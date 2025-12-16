<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentIdeaResource\Pages;
use App\Models\ContentIdea;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContentIdeaResource extends Resource
{
    protected static ?string $model = ContentIdea::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationLabel = 'Content Ideas';
    protected static ?string $modelLabel = 'Content Idea';
    protected static ?string $pluralModelLabel = 'Content Ideas';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content Information')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->default(now()),
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Healthy Habits Checklist'),
                        Forms\Components\Select::make('language')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                            ])
                            ->default('en')
                            ->required()
                            ->native(false),
                    ]),
                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\Textarea::make('caption')
                            ->label('Caption')
                            ->required()
                            ->rows(5)
                            ->placeholder('Enter the caption text here...')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hashtags')
                            ->label('Hashtags')
                            ->rows(3)
                            ->placeholder('#health #wellness #habits')
                            ->helperText('Enter hashtags separated by spaces or commas')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tips')
                            ->label('Tips')
                            ->rows(4)
                            ->placeholder('Enter tips here (optional)...')
                            ->helperText('Optional tips section')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en' => '🇬🇧 English',
                        'de' => '🇩🇪 Deutsch',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'en' => 'success',
                        'de' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('caption')
                    ->label('Caption')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('hashtags')
                    ->label('Has Hashtags')
                    ->boolean()
                    ->getStateUsing(fn (ContentIdea $record): bool => !empty($record->hashtags)),
                Tables\Columns\IconColumn::make('tips')
                    ->label('Has Tips')
                    ->boolean()
                    ->getStateUsing(fn (ContentIdea $record): bool => !empty($record->tips)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label('Language')
                    ->options([
                        'en' => 'English',
                        'de' => 'Deutsch',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Date from'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Date until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn ($query, $date) => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn ($query, $date) => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentIdeas::route('/'),
            'create' => Pages\CreateContentIdea::route('/create'),
            'edit' => Pages\EditContentIdea::route('/{record}/edit'),
        ];
    }
}
