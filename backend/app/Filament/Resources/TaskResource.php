<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Задачи';
    protected static ?string $modelLabel = 'Задача';
    protected static ?string $pluralModelLabel = 'Задачи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('external_id')
                            ->label('Внешний ID')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Уникальный идентификатор из внешней системы'),
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->required()
                            ->rows(3),
                        Forms\Components\Select::make('category')
                            ->label('Категория')
                            ->options([
                                'SEO' => 'SEO',
                                'Content' => 'Контент',
                                'Social Media' => 'Социальные сети',
                                'Local SEO' => 'Локальное SEO',
                                'Email Marketing' => 'Email-маркетинг',
                                'Paid Ads' => 'Платная реклама',
                                'E-commerce' => 'E-commerce',
                                'SaaS' => 'SaaS',
                                'Analytics' => 'Аналитика',
                            ])
                            ->required(),
                        Forms\Components\Select::make('dependencies')
                            ->label('Зависимости')
                            ->options(fn () => Task::orderBy('title')->pluck('title', 'id'))
                            ->multiple()
                            ->searchable()
                            ->helperText('Задачи, которые должны быть выполнены перед этой (ID задач сохраняются в JSON)'),
                    ]),
                
                Forms\Components\Section::make('Параметры выполнения')
                    ->schema([
                        Forms\Components\TextInput::make('duration_hours')
                            ->label('Длительность (часы)')
                            ->numeric()
                            ->required()
                            ->min(1)
                            ->max(40),
                        Forms\Components\Select::make('frequency')
                            ->label('Частота')
                            ->options([
                                'once' => 'Однократно',
                                'weekly' => 'Еженедельно',
                                'monthly' => 'Ежемесячно',
                                'quarterly' => 'Ежеквартально',
                            ])
                            ->required(),
                        Forms\Components\Select::make('difficulty_level')
                            ->label('Уровень сложности')
                            ->options([
                                'beginner' => 'Начальный',
                                'intermediate' => 'Средний',
                                'advanced' => 'Продвинутый',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('global_order')
                            ->label('Глобальный порядок')
                            ->numeric()
                            ->helperText('Порядок сортировки для глобальных задач'),
                    ]),
                
                Forms\Components\Section::make('Условия применения')
                    ->schema([
                        Forms\Components\Select::make('business_type')
                            ->label('Тип бизнеса')
                            ->options([
                                'any' => 'Любой',
                                'ecommerce' => 'E-commerce',
                                'service' => 'Услуги',
                                'saas' => 'SaaS',
                                'content' => 'Контент',
                            ])
                            ->required(),
                        Forms\Components\Select::make('language')
                            ->label('Язык')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                                'ru' => 'Русский',
                                'any' => 'Любой',
                            ])
                            ->default('en')
                            ->required(),
                        Forms\Components\Toggle::make('is_local')
                            ->label('Для локального бизнеса'),
                        Forms\Components\Toggle::make('requires_website')
                            ->label('Требует наличие сайта'),
                        Forms\Components\Toggle::make('is_global')
                            ->label('Глобальная задача')
                            ->helperText('Если включено, задача доступна для всех стран'),
                    ]),

                Forms\Components\Section::make('Целевая аудитория')
                    ->schema([
                        Forms\Components\Select::make('target_countries')
                            ->label('Целевые страны')
                            ->options([
                                'ie' => 'Ireland (IE)',
                                'uk' => 'United Kingdom (UK)',
                                'de' => 'Germany (DE)',
                            ])
                            ->multiple()
                            ->native(false)
                            ->helperText('Если не выбрано и задача не глобальная, используется фильтр по стране плана'),
                        Forms\Components\Select::make('target_industries')
                            ->label('Целевые индустрии')
                            ->options([
                                'beauty' => 'Beauty',
                                'physio' => 'Physio',
                                'coaching' => 'Coaching',
                            ])
                            ->multiple()
                            ->native(false)
                            ->helperText('Индустрии, для которых предназначена задача'),
                        Forms\Components\TagsInput::make('allowed_capacities')
                            ->label('Разрешенные мощности')
                            ->placeholder('Введите количество часов в неделю')
                            ->helperText('Количество часов маркетинга в неделю, для которых доступна задача (например: 5, 10, 20)')
                            ->separator(',')
                            ->splitKeys(['Tab', ',']),
                        Forms\Components\Select::make('local_presence_options')
                            ->label('Опции локального присутствия')
                            ->options([
                                'yes' => 'Да (только для локального бизнеса)',
                                'no' => 'Нет (только для нелокального бизнеса)',
                            ])
                            ->multiple()
                            ->native(false)
                            ->helperText('Ограничения по типу локального присутствия'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Условия и правила')
                    ->schema([
                        Forms\Components\Repeater::make('conditions')
                            ->label('Условия')
                            ->schema([
                                Forms\Components\TextInput::make('field')
                                    ->label('Поле')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('operator')
                                    ->label('Оператор')
                                    ->options([
                                        'equals' => 'Равно',
                                        'not_equals' => 'Не равно',
                                        'greater_than' => 'Больше',
                                        'less_than' => 'Меньше',
                                        'in' => 'В списке',
                                        'not_in' => 'Не в списке',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->label('Значение')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['field'] ?? '') . ' ' . ($state['operator'] ?? '') . ' ' . ($state['value'] ?? ''))
                            ->helperText('Дополнительные условия для показа задачи'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('external_id')
                    ->label('Внешний ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Категория')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_hours')
                    ->label('Часы')
                    ->suffix('ч')
                    ->sortable(),
                Tables\Columns\TextColumn::make('difficulty_level')
                    ->label('Сложность')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beginner' => 'success',
                        'intermediate' => 'warning',
                        'advanced' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('business_type')
                    ->label('Тип бизнеса')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_global')
                    ->label('Глобальная')
                    ->boolean(),
                Tables\Columns\TextColumn::make('global_order')
                    ->label('Порядок')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('requires_website')
                    ->label('Нужен сайт')
                    ->boolean(),
                Tables\Columns\TextColumn::make('target_countries')
                    ->label('Страны')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? implode(', ', $state) : '—')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('target_industries')
                    ->label('Индустрии')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? implode(', ', $state) : '—')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Категория'),
                Tables\Filters\SelectFilter::make('business_type')
                    ->label('Тип бизнеса'),
                Tables\Filters\SelectFilter::make('difficulty_level')
                    ->label('Сложность'),
                Tables\Filters\TernaryFilter::make('is_global')
                    ->label('Глобальная задача')
                    ->placeholder('Все')
                    ->trueLabel('Только глобальные')
                    ->falseLabel('Только не глобальные'),
                Tables\Filters\SelectFilter::make('language')
                    ->label('Язык')
                    ->options([
                        'en' => 'English',
                        'de' => 'Deutsch',
                        'ru' => 'Русский',
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
            ->defaultSort('global_order', 'asc');
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
