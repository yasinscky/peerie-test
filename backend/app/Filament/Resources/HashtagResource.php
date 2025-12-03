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
    protected static ?string $navigationLabel = 'Hashtags';
    protected static ?string $modelLabel = 'Hashtag';
    protected static ?string $pluralModelLabel = 'Hashtags';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Main information')
                    ->schema([
                        Forms\Components\Select::make('industry')
                            ->label('Industry')
                            ->options([
                                'beauty' => 'Beauty',
                                'physio' => 'Physio',
                                'coaching' => 'Coaching',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('country')
                            ->label('Country')
                            ->options([
                                'ie' => 'Ireland (IE)',
                                'uk' => 'United Kingdom (UK)',
                                'de' => 'Germany (DE)',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('language')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                            ])
                            ->default('en')
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Example: Beauty Salon - IRL'),
                    ]),
                
                Forms\Components\Section::make('Intro')
                    ->schema([
                        Forms\Components\TextInput::make('intro_title')
                            ->label('Intro title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('intro_description')
                            ->label('Intro description')
                            ->rows(3),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Hashtag blocks')
                    ->description('Configure hashtag categories: Local, Broad, Industry, Niche, Branded')
                    ->schema([
                        Forms\Components\Repeater::make('hashtag_blocks')
                            ->label('Blocks')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Block title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Example: 1 – Local'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Block description')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Forms\Components\TagsInput::make('tags')
                                    ->label('Hashtags')
                                    ->placeholder('Add hashtag and press Enter')
                                    ->helperText('You can enter tags with or without #, it will be added automatically')
                                    ->separator(',')
                                    ->splitKeys(['Tab', ','])
                                    ->columnSpanFull(),
                                Forms\Components\Repeater::make('categories')
                                    ->label('Categories (optional, only for Industry block)')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Category name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Category hashtags')
                                            ->placeholder('Add category hashtags')
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
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New block')
                            ->addActionLabel('Add block')
                            ->reorderable(true)
                            ->required(),
                    ]),

                Forms\Components\Section::make('All hashtags')
                    ->description('Automatically generated from blocks, can be edited manually')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('All hashtags')
                            ->placeholder('Add hashtag and press Enter')
                            ->separator(',')
                            ->splitKeys(['Tab', ','])
                            ->helperText('This list merges all hashtags from the blocks above'),
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
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('industry')
                    ->label('Industry')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beauty' => 'success',
                        'physio' => 'info',
                        'coaching' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ie' => '🇮🇪 IE',
                        'uk' => '🇬🇧 UK',
                        'de' => '🇩🇪 DE',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en' => '🇬🇧 EN',
                        'de' => '🇩🇪 DE',
                        default => $state,
                    })
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('industry')
                    ->label('Industry')
                    ->options([
                        'beauty' => 'Beauty',
                        'physio' => 'Physio',
                        'coaching' => 'Coaching',
                    ]),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Country')
                    ->options([
                        'ie' => 'Ireland (IE)',
                        'uk' => 'United Kingdom (UK)',
                        'de' => 'Germany (DE)',
                    ]),
                Tables\Filters\SelectFilter::make('language')
                    ->label('Language')
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
