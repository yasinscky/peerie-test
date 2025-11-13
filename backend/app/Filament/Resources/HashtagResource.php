<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HashtagResource\Pages;
use App\Filament\Resources\HashtagResource\RelationManagers;
use App\Models\Hashtag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HashtagResource extends Resource
{
    protected static ?string $model = Hashtag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';
    protected static ?string $navigationLabel = 'Хештеги';
    protected static ?string $modelLabel = 'Хештег';
    protected static ?string $pluralModelLabel = 'Хештеги';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('industry')
                            ->label('Индустрия')
                            ->options([
                                'beauty' => 'Beauty',
                                'physio' => 'Physio',
                                'coaching' => 'Coaching',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('country')
                            ->label('Страна')
                            ->options([
                                'ie' => 'Ireland (IE)',
                                'uk' => 'United Kingdom (UK)',
                                'de' => 'Germany (DE)',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('language')
                            ->label('Язык')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                            ])
                            ->default('en')
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Beauty Salon - IRL'),
                    ]),
                
                Forms\Components\Section::make('Интро')
                    ->schema([
                        Forms\Components\TextInput::make('intro_title')
                            ->label('Заголовок интро')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('intro_description')
                            ->label('Описание интро')
                            ->rows(3),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Блоки хештегов')
                    ->description('Настройка категорий хештегов: Local, Broad, Industry, Niche, Branded')
                    ->schema([
                        Forms\Components\Repeater::make('hashtag_blocks')
                            ->label('Блоки')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Заголовок блока')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: 1 – Local'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Описание блока')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Forms\Components\TagsInput::make('tags')
                                    ->label('Хештеги')
                                    ->placeholder('Добавьте хештег и нажмите Enter')
                                    ->helperText('Введите хештеги с # или без, они автоматически добавятся с #')
                                    ->separator(',')
                                    ->splitKeys(['Tab', ','])
                                    ->columnSpanFull(),
                                Forms\Components\Repeater::make('categories')
                                    ->label('Категории (опционально, только для Industry блока)')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Название категории')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Хештеги категории')
                                            ->placeholder('Добавьте хештеги категории')
                                            ->separator(',')
                                            ->splitKeys(['Tab', ',']),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(5)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новый блок')
                            ->addActionLabel('Добавить блок')
                            ->reorderable(true)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Все хештеги')
                    ->description('Автоматически генерируется из блоков, можно редактировать вручную')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Все хештеги')
                            ->placeholder('Добавьте хештег и нажмите Enter')
                            ->separator(',')
                            ->splitKeys(['Tab', ','])
                            ->helperText('Этот список объединяет все хештеги из блоков выше'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('industry')
                    ->label('Индустрия')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beauty' => 'success',
                        'physio' => 'info',
                        'coaching' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('Страна')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ie' => '🇮🇪 IE',
                        'uk' => '🇬🇧 UK',
                        'de' => '🇩🇪 DE',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Язык')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en' => '🇬🇧 EN',
                        'de' => '🇩🇪 DE',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('industry')
                    ->label('Индустрия')
                    ->options([
                        'beauty' => 'Beauty',
                        'physio' => 'Physio',
                        'coaching' => 'Coaching',
                    ]),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Страна')
                    ->options([
                        'ie' => 'Ireland (IE)',
                        'uk' => 'United Kingdom (UK)',
                        'de' => 'Germany (DE)',
                    ]),
                Tables\Filters\SelectFilter::make('language')
                    ->label('Язык')
                    ->options([
                        'en' => 'English',
                        'de' => 'Deutsch',
                    ]),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListHashtags::route('/'),
            'create' => Pages\CreateHashtag::route('/create'),
            'edit' => Pages\EditHashtag::route('/{record}/edit'),
        ];
    }
}
