<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceFileResource\Pages;
use App\Models\ResourceFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ResourceFileResource extends Resource
{
    protected static ?string $model = ResourceFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Resource Files';
    protected static ?string $modelLabel = 'Resource File';
    protected static ?string $pluralModelLabel = 'Resource Files';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Content Bank Template'),
                        Forms\Components\Select::make('language')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                            ])
                            ->default('en')
                            ->required()
                            ->native(false),
                        Forms\Components\FileUpload::make('file')
                            ->label('File')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->directory(function ($get, $record) {
                                $lang = $get('language') ?? ($record?->language ?? 'en');
                                return 'resources/' . $lang;
                            })
                            ->visibility('private')
                            ->required(fn ($context) => $context === 'create')
                            ->disk('local')
                            ->deletable(false)
                            ->downloadable(false)
                            ->helperText('Upload a .docx file. File will be stored in resources/{language} folder.')
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('original_filename')
                    ->label('Filename')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (ResourceFile $record) {
                        if ($record->file_path && Storage::disk('local')->exists($record->file_path)) {
                            Storage::disk('local')->delete($record->file_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->file_path && Storage::disk('local')->exists($record->file_path)) {
                                    Storage::disk('local')->delete($record->file_path);
                                }
                            }
                        }),
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
            'index' => Pages\ListResourceFiles::route('/'),
            'create' => Pages\CreateResourceFile::route('/create'),
            'edit' => Pages\EditResourceFile::route('/{record}/edit'),
        ];
    }
}
