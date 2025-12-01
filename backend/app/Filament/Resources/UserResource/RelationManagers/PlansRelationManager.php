<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PlansRelationManager extends RelationManager
{
    protected static string $relationship = 'plans';

    protected static ?string $title = 'User plans';
    protected static ?string $modelLabel = 'Plan';
    protected static ?string $pluralModelLabel = 'Plans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Plan title')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->default('Untitled'),
                Tables\Columns\TextColumn::make('questionnaire_summary')
                    ->label('Questionnaire')
                    ->wrap()
                    ->limit(120)
                    ->formatStateUsing(fn (?string $state): string => $state ?? '—'),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->badge(),
                Tables\Columns\TextColumn::make('completed_tasks')
                    ->label('Completed tasks')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $record->completed_tasks . ' / ' . $record->total_tasks),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label('Language')
                    ->options([
                        'en' => 'English',
                        'de' => 'German',
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('viewQuestionnaire')
                    ->label('View questionnaire')
                    ->icon('heroicon-o-rectangle-stack')
                    ->modalHeading('Questionnaire data')
                    ->modalWidth('2xl')
                    ->form([
                        Forms\Components\KeyValue::make('questionnaire_data')
                            ->label('Questionnaire data')
                            ->disabled(),
                    ])
                    ->fillForm(function ($record): array {
                        return [
                            'questionnaire_data' => $record->questionnaire_data ?? [],
                        ];
                    })
                    ->modalSubmitAction(false),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

