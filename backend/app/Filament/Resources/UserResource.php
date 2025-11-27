<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Main information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->helperText('Leave empty to keep password unchanged when editing'),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText('Administrators have access to the admin panel')
                            ->default(false),
                    ]),
                
                Forms\Components\Section::make('Additional information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email verified')
                            ->displayFormat('d.m.Y H:i')
                            ->helperText('Set date if email is verified'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Registration date')
                            ->content(fn (User $record): ?string => $record->created_at?->format('d.m.Y H:i'))
                            ->hidden(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Placeholder::make('plans_count')
                            ->label('Plans count')
                            ->content(fn (User $record): string => $record->plans()->count())
                            ->hidden(fn (string $context): bool => $context === 'create'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('latestPlan'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('latestPlan.country')
                    ->label('Country')
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? '—'))
                    ->badge(),
                Tables\Columns\TextColumn::make('latestPlan.language')
                    ->label('Language')
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? '—'))
                    ->badge(),
                Tables\Columns\TextColumn::make('latestPlan.business_type')
                    ->label('Industry')
                    ->formatStateUsing(function (?string $state): string {
                        if ($state === null) {
                            return '—';
                        }

                        return match ($state) {
                            'any' => 'Any',
                            'beauty' => 'Beauty',
                            'physio' => 'Physio',
                            'coaching' => 'Coaching',
                            default => 'Any',
                        };
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('plans_count')
                    ->label('Plans')
                    ->counts('plans')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('email_verified')
                    ->label('Email verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('has_plans')
                    ->label('Has plans')
                    ->query(fn (Builder $query): Builder => $query->has('plans')),
                Tables\Filters\Filter::make('created_at')
                    ->label('Registration period')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->before(function (Tables\Actions\DeleteAction $action, User $record) {
                        if (auth()->id() === $record->id) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Нельзя удалить свою учетную запись')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->before(function (Tables\Actions\DeleteBulkAction $action, $records) {
                            if ($records->contains('id', auth()->id())) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('You cannot delete your own account')
                                    ->title('You cannot delete your own account')
                                    ->send();
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

