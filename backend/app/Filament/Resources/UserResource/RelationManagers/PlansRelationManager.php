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

    protected static ?string $title = 'Планы пользователя';
    protected static ?string $modelLabel = 'План';
    protected static ?string $pluralModelLabel = 'Планы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Название плана')
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
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->default('Без названия'),
                Tables\Columns\TextColumn::make('business_type')
                    ->label('Тип бизнеса')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ecommerce' => 'E-commerce',
                        'service' => 'Услуги',
                        'saas' => 'SaaS',
                        'content' => 'Контент',
                        default => $state ?? 'Не указан',
                    }),
                Tables\Columns\TextColumn::make('language')
                    ->label('Язык')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ru' => 'Русский',
                        'en' => 'Английский',
                        default => $state ?? 'Не указан',
                    }),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Задач')
                    ->counts('tasks')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('business_type')
                    ->label('Тип бизнеса')
                    ->options([
                        'ecommerce' => 'E-commerce',
                        'service' => 'Услуги',
                        'saas' => 'SaaS',
                        'content' => 'Контент',
                    ]),
                Tables\Filters\SelectFilter::make('language')
                    ->label('Язык')
                    ->options([
                        'ru' => 'Русский',
                        'en' => 'Английский',
                    ]),
            ])
            ->headerActions([
                // Создание планов через анкету на фронтенде
            ])
            ->actions([
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

