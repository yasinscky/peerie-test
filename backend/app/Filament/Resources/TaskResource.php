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
    protected static ?string $navigationLabel = 'Tasks';
    protected static ?string $modelLabel = 'Task';
    protected static ?string $pluralModelLabel = 'Tasks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action_id')
                    ->label('1. ActionID')
                    ->numeric()
                    ->helperText('Numeric part of ID (prefix is set automatically from category)'),

                Forms\Components\TextInput::make('global_order')
                    ->label('2. GlobalOrder')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->helperText('Only positive numbers allowed'),

                Forms\Components\Select::make('category')
                    ->label('3. Category')
                    ->options([
                        'Goals' => 'Goals',
                        'Digital Marketing Foundations' => 'Digital Marketing Foundations',
                        'Local SEO' => 'Local SEO',
                        'Content' => 'Content',
                        'Social Media' => 'Social Media',
                        'Website' => 'Website',
                        'Email Marketing' => 'Email Marketing',
                        'Paid Advertising' => 'Paid Advertising',
                        'CRM' => 'CRM',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('4. Action')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Section::make('5. Prerequisites')
                    ->schema([
                        Forms\Components\Repeater::make('prerequisites')
                            ->label('Prerequisites')
                            ->schema([
                                Forms\Components\Select::make('condition')
                                    ->label('Condition')
                                    ->options([
                                        'business_goals_defined' => 'Business goals defined',
                                        'marketing_goals_defined' => 'Marketing goals defined',
                                        'google_business_claimed' => 'Google Business Profile claimed & filled out',
                                        'core_directories_claimed' => 'Apple Business and Bing Places',
                                        'industry_directories_claimed' => 'Industry directories claimed',
                                        'business_directories_claimed' => 'Business directories claimed',
                                        'has_website' => 'Website in place',
                                        'email_marketing_tool' => 'Email marketing tool in place',
                                        'crm_pipeline' => 'CRM or simple pipeline to track leads',
                                        'has_primary_social_channel' => 'Primary social media channel',
                                        'has_secondary_social_channel' => 'Secondary social media channel',
                                    ])
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('value')
                                    ->label('Value')
                                    ->options([
                                        'no' => 'No (when answer is No)',
                                        'yes' => 'Yes (when answer is Yes)',
                                    ])
                                    ->required()
                                    ->default('no')
                                    ->helperText('If value is \"No\", task is shown when the answer is No. If \"Yes\", task is shown when the answer is Yes.'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $condition = $state['condition'] ?? null;
                                $value = $state['value'] ?? null;
                                
                                if (!$condition) {
                                    return 'New prerequisite';
                                }
                                
                                $conditionLabels = [
                                    'business_goals_defined' => 'Business goals defined',
                                    'marketing_goals_defined' => 'Marketing goals defined',
                                    'google_business_claimed' => 'Google Business Profile',
                                    'core_directories_claimed' => 'Apple Business and Bing Places',
                                    'industry_directories_claimed' => 'Industry directories',
                                    'business_directories_claimed' => 'Business directories',
                                    'has_website' => 'Website in place',
                                    'email_marketing_tool' => 'Email marketing tool',
                                    'crm_pipeline' => 'CRM pipeline',
                                    'has_primary_social_channel' => 'Primary social channel',
                                    'has_secondary_social_channel' => 'Secondary social channel',
                                ];
                                
                                $label = $conditionLabels[$condition] ?? $condition;
                                $valueLabel = $value === 'no' ? 'No (when answer is No)' : 'Yes (when answer is Yes)';
                                
                                return $label . ': ' . $valueLabel;
                            })
                            ->helperText('Add prerequisites based on questionnaire answers. If condition is "No", task will be shown.'),
                    ])
                    ->collapsible(),

                Forms\Components\Select::make('frequency')
                    ->label('6. Cadence')
                    ->options([
                        'once' => 'Once',
                        'weekly' => 'Weekly',
                        'bi_weekly' => 'Bi-weekly',
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'half_yearly' => 'Half-yearly',
                        'yearly' => 'Yearly',
                    ])
                    ->required(),

                Forms\Components\Select::make('target_countries')
                    ->label('7. Country')
                    ->options([
                        'UK' => 'United Kingdom (UK)',
                        'IRE' => 'Ireland (IRE)',
                        'DE' => 'Germany (DE)',
                    ])
                    ->multiple()
                    ->native(false)
                    ->helperText('Select one or multiple countries'),

                Forms\Components\Select::make('language')
                    ->label('8. Language')
                    ->options([
                        'en' => 'English',
                        'de' => 'Deutsch',
                    ])
                    ->default('en')
                    ->required(),

                Forms\Components\Select::make('target_industries')
                    ->label('9. Industries')
                    ->options([
                        'all' => 'All industries',
                        'beauty' => 'Beauty',
                        'physio' => 'Physio',
                        'coaching' => 'Coaching',
                    ])
                    ->multiple()
                    ->native(false)
                    ->helperText('Select one or multiple industries or choose "All industries" for tasks valid for any industry.'),

                Forms\Components\Select::make('allowed_capacities')
                    ->label('10. CapacityAllowed')
                    ->options([
                        2 => '2 hours',
                        4 => '4 hours',
                        6 => '6 hours',
                    ])
                    ->multiple()
                    ->native(false)
                    ->helperText('Select one or multiple capacities'),

                Forms\Components\Select::make('duration_minutes')
                    ->label('11. EffortMin')
                    ->options([
                        15 => '15 minutes',
                        30 => '30 minutes',
                        45 => '45 minutes',
                        60 => '60 minutes',
                        90 => '90 minutes',
                    ])
                    ->required(),

                Forms\Components\Select::make('local_presence_options')
                    ->label('12. LocalPresence')
                    ->options([
                        'any' => 'Any (show for all businesses)',
                        'yes' => 'Only for local businesses',
                        'no' => 'Only for non-local businesses',
                    ])
                    ->default('any')
                    ->native(false)
                    ->required(),

                Forms\Components\Select::make('template')
                    ->label('13. Template')
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ])
                    ->native(false)
                    ->required(),

                Forms\Components\RichEditor::make('description')
                    ->label('14. Instruction')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action_id')
                    ->label('ActionID')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, Task $record) {
                        $number = is_numeric($state) ? (int) $state : null;

                        if ($number === null) {
                            return '—';
                        }

                        $prefix = match ($record->category) {
                            'Goals' => 'G',
                            'Digital Marketing Foundations' => 'F',
                            'Local SEO' => 'SEO',
                            'Content' => 'CON',
                            'Social Media' => 'SM',
                            'Website' => 'WEB',
                            'Email Marketing' => 'EM',
                            'Paid Advertising' => 'PA',
                            'CRM' => 'CRM',
                            default => '',
                        };

                        $formattedNumber = str_pad((string) $number, 3, '0', STR_PAD_LEFT);

                        return $prefix !== ''
                            ? $prefix . '-' . $formattedNumber
                            : $formattedNumber;
                    }),
                Tables\Columns\TextColumn::make('global_order')
                    ->label('GlobalOrder')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Action')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->extraAttributes(['style' => 'min-width:420px; white-space:normal;'])
                    ->extraHeaderAttributes(['style' => 'min-width:420px;']),
                Tables\Columns\TextColumn::make('prerequisites')
                    ->label('Prerequisites')
                    ->formatStateUsing(function ($state, Task $record) {
                        $prerequisites = $record->prerequisites ?? [];

                        if (!is_array($prerequisites) || empty($prerequisites)) {
                            return '—';
                        }

                        $conditionLabels = [
                            'business_goals_defined' => 'Business goals',
                            'marketing_goals_defined' => 'Marketing goals',
                            'google_business_claimed' => 'Google Business',
                            'core_directories_claimed' => 'Apple Business and Bing Places',
                            'industry_directories_claimed' => 'Industry directories',
                            'business_directories_claimed' => 'Business directories',
                            'has_website' => 'Website',
                            'email_marketing_tool' => 'Email tool',
                            'crm_pipeline' => 'CRM',
                            'has_primary_social_channel' => 'Primary social',
                            'has_secondary_social_channel' => 'Secondary social',
                        ];

                        $conditions = [];

                        foreach ($prerequisites as $prerequisite) {
                            if (!isset($prerequisite['condition'], $prerequisite['value'])) {
                                continue;
                            }

                            $label = $conditionLabels[$prerequisite['condition']] ?? $prerequisite['condition'];
                            $valueLabel = $prerequisite['value'] === 'no' ? 'No' : 'Yes';
                            $conditions[] = $label . ': ' . $valueLabel;
                        }

                        return count($conditions) > 0 ? implode(', ', $conditions) : '—';
                    })
                    ->badge()
                    ->separator(',')
                    ->wrap()
                    ->extraAttributes(['style' => 'min-width:260px; white-space:normal;'])
                    ->extraHeaderAttributes(['style' => 'min-width:260px;']),
                Tables\Columns\TextColumn::make('description')
                    ->label('Instruction')
                    ->limit(80)
                    ->wrap()
                    ->extraAttributes(['style' => 'min-width:360px; white-space:normal;'])
                    ->extraHeaderAttributes(['style' => 'min-width:360px;']),
                Tables\Columns\TextColumn::make('frequency')
                    ->label('Cadence')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'once' => 'Once',
                        'weekly' => 'Weekly',
                        'bi_weekly' => 'Bi-weekly',
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'half_yearly' => 'Half-yearly',
                        'yearly' => 'Yearly',
                        default => $state ?? '—',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_countries')
                    ->label('Country')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && count($state) > 0) {
                            $labels = array_map(function ($code) {
                                $code = strtolower((string) $code);

                                return match ($code) {
                                    'uk' => 'UK',
                                    'ire', 'ie' => 'IRE',
                                    'de' => 'DE',
                                    default => strtoupper($code),
                                };
                            }, $state);

                            return implode(', ', $labels);
                        }

                        if (is_string($state) && $state !== '') {
                            return strtoupper($state);
                        }

                        return '—';
                    })
                    ->wrap()
                    ->extraAttributes(['style' => 'min-width:260px; white-space:nowrap;'])
                    ->extraHeaderAttributes(['style' => 'min-width:260px; text-align:left;']),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'en' => 'EN',
                        'de' => 'DE',
                        default => $state ?? '—',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_industries')
                    ->label('Industry')
                    ->formatStateUsing(function ($state) {
                        if (is_string($state) && $state !== '') {
                            $decoded = json_decode($state, true);

                            if (is_array($decoded)) {
                                $state = $decoded;
                            } else {
                                $state = array_map('trim', explode(',', $state));
                            }
                        }

                        if (is_array($state) && count($state) > 0) {
                            $normalized = array_map(static fn ($value) => strtolower((string) $value), $state);
                            $normalized = array_values(array_unique($normalized));

                            if (in_array('all', $normalized, true) && count($normalized) === 1) {
                                return 'All industries';
                            }

                            $filtered = array_filter($normalized, static fn ($value) => $value !== 'all');

                            if (count($filtered) === 0) {
                                return 'All industries';
                            }

                            $labels = array_map(static function ($value) {
                                return match ($value) {
                                    'beauty' => 'Beauty',
                                    'physio' => 'Physio',
                                    'coaching' => 'Coaching',
                                    default => (string) $value,
                                };
                            }, $filtered);

                            return implode(', ', $labels);
                        }

                        return 'All industries';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('allowed_capacities')
                    ->label('CapacityAllowed')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && count($state) > 0) {
                            $labels = array_map(function ($value) {
                                return (string) $value . 'h';
                            }, $state);

                            return implode(', ', $labels);
                        }

                        if ($state !== null && $state !== '') {
                            return (string) $state . 'h';
                        }

                        return '—';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('EffortMin')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' min' : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('local_presence_options')
                    ->label('LocalPresence')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && count($state) > 0) {
                            return $state[0];
                        }
                        return $state ?? '—';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('template')
                    ->label('Template')
                    ->formatStateUsing(fn ($state) => $state ?? '—')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category'),
                Tables\Filters\TernaryFilter::make('is_global')
                    ->label('Global task')
                    ->placeholder('All')
                    ->trueLabel('Only global')
                    ->falseLabel('Only non-global'),
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
            ->defaultSort('global_order', 'asc')
            ->groups([
                Tables\Grouping\Group::make('category')
                    ->label('Category')
                    ->collapsible(),
            ]);
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
