<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\CabinetNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = CabinetNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $modelLabel = 'Notification';

    protected static ?string $pluralModelLabel = 'Notifications';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Notification')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->label('Text')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('language')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                            ])
                            ->default('en')
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish at')
                            ->default(now())
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->helperText('Clients see this from this date.'),
                        Forms\Components\Select::make('user_id')
                            ->label('Client')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn (User $record): string => trim($record->name.' · '.$record->email))
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->nullable()
                            ->placeholder('All clients')
                            ->helperText('Leave empty to show this to every client.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'de' ? 'DE' : 'EN'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Client')
                    ->placeholder('All clients')
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
