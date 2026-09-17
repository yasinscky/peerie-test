<?php

namespace App\Services;

class WorksheetRegistry
{
    public static function kinds(): array
    {
        return array_keys(self::definitions());
    }

    public static function get(string $kind): ?array
    {
        return self::definitions()[$kind] ?? null;
    }

    public static function kindFromTitle(?string $title): ?string
    {
        $normalized = str_replace(['-', '–', '—'], ' ', mb_strtolower((string) $title));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        foreach (self::definitions() as $kind => $definition) {
            foreach ($definition['title_needles'] as $needle) {
                if (str_contains($normalized, $needle)) {
                    return $kind;
                }
            }
        }

        return null;
    }

    public static function slugToKind(string $slug): ?string
    {
        $kind = str_replace('-', '_', $slug);

        return isset(self::definitions()[$kind]) ? $kind : null;
    }

    public static function kindToSlug(string $kind): string
    {
        return str_replace('_', '-', $kind);
    }

    private static function definitions(): array
    {
        return [
            'buyer_persona' => [
                'layout' => 'fields',
                'filename' => 'Buyer-Persona',
                'group' => 'brand',
                'title_needles' => ['buyer persona'],
                'titles' => ['en' => 'Buyer Persona Template', 'de' => 'Buyer Persona Vorlage'],
                'field_keys' => [
                    'name', 'portrait', 'age_range', 'location_work_model', 'role_industry', 'seniority_team',
                    'income_band', 'life_situation', 'pain_1', 'pain_2', 'pain_3', 'pain_4', 'pain_5',
                    'short_term_goal', 'deeper_motivation', 'success_criteria', 'platforms', 'search_queries',
                    'content_preferences', 'budget', 'value_perception', 'decision_process', 'objections', 'persona_summary',
                ],
                'sections' => [
                    1 => ['name', 'portrait', 'age_range', 'location_work_model', 'role_industry', 'seniority_team', 'income_band', 'life_situation'],
                    2 => ['pain_1', 'pain_2', 'pain_3', 'pain_4', 'pain_5'],
                    3 => ['short_term_goal', 'deeper_motivation', 'success_criteria'],
                    4 => ['platforms', 'search_queries', 'content_preferences'],
                    5 => ['budget', 'value_perception', 'decision_process', 'objections'],
                    6 => ['persona_summary'],
                ],
                'step_titles' => [
                    'en' => [1 => 'Step 1: Demographics', 2 => 'Step 2: Pain points', 3 => 'Step 3: Goals & motives', 4 => 'Step 4: Online behaviour & channels', 5 => 'Step 5: Willingness to pay & decision path', 6 => 'Step 6: Write the persona'],
                    'de' => [1 => 'Schritt 1: Demografie', 2 => 'Schritt 2: Schmerzpunkte', 3 => 'Schritt 3: Ziele & Motive', 4 => 'Schritt 4: Online-Verhalten & Kanäle', 5 => 'Schritt 5: Zahlungsbereitschaft & Entscheidungsweg', 6 => 'Schritt 6: Persona schreiben'],
                ],
                'labels' => [
                    'en' => [
                        'name' => 'Name', 'portrait' => '2–3 sentences about your ideal person', 'age_range' => 'Age range',
                        'location_work_model' => 'Location & work model', 'role_industry' => 'Role & industry',
                        'seniority_team' => 'Seniority & team size', 'income_band' => 'Income band', 'life_situation' => 'Life situation',
                        'pain_1' => 'Pain point 1', 'pain_2' => 'Pain point 2', 'pain_3' => 'Pain point 3', 'pain_4' => 'Pain point 4 (optional)', 'pain_5' => 'Pain point 5 (optional)',
                        'short_term_goal' => 'Short-term goal (3–6 months)', 'deeper_motivation' => 'Deeper motivation', 'success_criteria' => 'Success criteria',
                        'platforms' => 'Platforms', 'search_queries' => 'Search queries', 'content_preferences' => 'Content preferences',
                        'budget' => 'Budget', 'value_perception' => 'Value perception', 'decision_process' => 'Decision process', 'objections' => 'Objections', 'persona_summary' => 'Persona write-up',
                    ],
                    'de' => [
                        'name' => 'Name', 'portrait' => '2–3 Sätze zur idealen Person', 'age_range' => 'Altersspanne',
                        'location_work_model' => 'Ort & Arbeitsmodell', 'role_industry' => 'Rolle & Branche',
                        'seniority_team' => 'Seniorität & Teamgröße', 'income_band' => 'Einkommensband', 'life_situation' => 'Lebenssituation',
                        'pain_1' => 'Schmerzpunkt 1', 'pain_2' => 'Schmerzpunkt 2', 'pain_3' => 'Schmerzpunkt 3', 'pain_4' => 'Schmerzpunkt 4 (optional)', 'pain_5' => 'Schmerzpunkt 5 (optional)',
                        'short_term_goal' => 'Kurzfristiges Ziel (3–6 Monate)', 'deeper_motivation' => 'Tiefere Motivation', 'success_criteria' => 'Erfolgskriterien',
                        'platforms' => 'Plattformen', 'search_queries' => 'Suchanfragen', 'content_preferences' => 'Content-Vorlieben',
                        'budget' => 'Budget', 'value_perception' => 'Wertwahrnehmung', 'decision_process' => 'Entscheidung', 'objections' => 'Einwände', 'persona_summary' => 'Persona-Text',
                    ],
                ],
            ],
            'brand_identity' => [
                'layout' => 'fields',
                'filename' => 'Brand-Identity',
                'group' => 'brand',
                'title_needles' => ['brand identity', 'markenidentit', 'unternehmensmarke', 'personal/ company brand', 'personal or company brand', 'company brand'],
                'titles' => ['en' => 'Brand Identity Template', 'de' => 'Markenidentität-Vorlage'],
                'field_keys' => [
                    'why_started', 'change_to_create', 'unique_approach', 'brand_story',
                    'core_values', 'personality_traits', 'communication_style',
                    'colours', 'fonts', 'imagery_style', 'visual_notes',
                ],
                'sections' => [
                    1 => ['why_started', 'change_to_create', 'unique_approach', 'brand_story'],
                    2 => ['core_values'],
                    3 => ['personality_traits', 'communication_style'],
                    4 => ['colours', 'fonts', 'imagery_style', 'visual_notes'],
                ],
                'step_titles' => [
                    'en' => [1 => 'Step 1: Brand story', 2 => 'Step 2: Core values', 3 => 'Step 3: Brand personality', 4 => 'Step 4: Visual identity'],
                    'de' => [1 => 'Schritt 1: Markengeschichte', 2 => 'Schritt 2: Kernwerte', 3 => 'Schritt 3: Markenpersönlichkeit', 4 => 'Schritt 4: Visuelle Identität'],
                ],
                'labels' => [
                    'en' => [
                        'why_started' => 'Why you started', 'change_to_create' => 'The change you want to create',
                        'unique_approach' => 'What makes your approach different', 'brand_story' => 'Brand story',
                        'core_values' => 'Core values', 'personality_traits' => 'Personality traits',
                        'communication_style' => 'How these traits show up', 'colours' => 'Colours',
                        'fonts' => 'Fonts', 'imagery_style' => 'Imagery style', 'visual_notes' => 'Visual notes',
                    ],
                    'de' => [
                        'why_started' => 'Warum du gestartet hast', 'change_to_create' => 'Die Veränderung, die du schaffen willst',
                        'unique_approach' => 'Was deinen Ansatz unterscheidet', 'brand_story' => 'Markengeschichte',
                        'core_values' => 'Kernwerte', 'personality_traits' => 'Persönlichkeitsmerkmale',
                        'communication_style' => 'Wie sich das in der Kommunikation zeigt', 'colours' => 'Farben',
                        'fonts' => 'Schriften', 'imagery_style' => 'Bildstil', 'visual_notes' => 'Visuelle Notizen',
                    ],
                ],
            ],
            'content_bank' => [
                'layout' => 'categories',
                'filename' => 'Content-Bank',
                'group' => 'planning',
                'title_needles' => ['content bank', 'contentbank'],
                'titles' => ['en' => 'Content Bank Template', 'de' => 'Content-Bank-Vorlage'],
                'category_keys' => ['quick_wins', 'education', 'results', 'behind_scenes', 'myths', 'promo', 'seasonal'],
                'field_keys' => [],
                'sections' => [],
                'step_titles' => ['en' => [], 'de' => []],
                'labels' => [
                    'en' => [
                        'quick_wins' => 'Quick wins', 'education' => 'Education & tips', 'results' => 'Client results & testimonials',
                        'behind_scenes' => 'Behind the scenes', 'myths' => 'Myth-busting', 'promo' => 'Promotional', 'seasonal' => 'Seasonal',
                    ],
                    'de' => [
                        'quick_wins' => 'Schnelle Erfolge', 'education' => 'Bildung & Tipps', 'results' => 'Kundenergebnisse & Testimonials',
                        'behind_scenes' => 'Hinter den Kulissen', 'myths' => 'Mythen aufklären', 'promo' => 'Werbung', 'seasonal' => 'Saisonal',
                    ],
                ],
            ],
            'content_audit' => [
                'layout' => 'rows',
                'filename' => 'Content-Audit',
                'group' => 'planning',
                'title_needles' => ['content audit'],
                'titles' => ['en' => 'Content Audit Template', 'de' => 'Content-Audit-Vorlage'],
                'row_keys' => ['date', 'platform', 'topic', 'format', 'category', 'engagement', 'notes'],
                'field_keys' => ['insights_topics', 'insights_formats', 'insights_ignored', 'gaps'],
                'sections' => [
                    1 => [],
                    2 => [],
                    3 => ['insights_topics', 'insights_formats', 'insights_ignored'],
                    4 => ['gaps'],
                ],
                'step_titles' => [
                    'en' => [3 => 'Step 3: What’s working', 4 => 'Step 4: Gaps & opportunities'],
                    'de' => [3 => 'Schritt 3: Was funktioniert', 4 => 'Schritt 4: Lücken & Chancen'],
                ],
                'labels' => [
                    'en' => [
                        'date' => 'Date', 'platform' => 'Platform', 'topic' => 'Topic / title', 'format' => 'Format',
                        'category' => 'Category', 'engagement' => 'Engagement', 'notes' => 'Notes',
                        'insights_topics' => 'Topics that resonate', 'insights_formats' => 'Formats that perform',
                        'insights_ignored' => 'What’s getting ignored', 'gaps' => 'Content gaps',
                    ],
                    'de' => [
                        'date' => 'Datum', 'platform' => 'Plattform', 'topic' => 'Thema / Titel', 'format' => 'Format',
                        'category' => 'Kategorie', 'engagement' => 'Engagement', 'notes' => 'Notizen',
                        'insights_topics' => 'Themen, die ankommen', 'insights_formats' => 'Formate, die funktionieren',
                        'insights_ignored' => 'Was ignoriert wird', 'gaps' => 'Content-Lücken',
                    ],
                ],
            ],
            'content_planning' => [
                'layout' => 'fields',
                'filename' => 'Monthly-Content-Planning',
                'group' => 'planning',
                'title_needles' => ['content planning', 'content calendar', 'monats content', 'plan content for the month', 'plane deinen content'],
                'titles' => ['en' => 'Monthly Content Planning Template', 'de' => 'Monats-Content-Planning-Vorlage'],
                'field_keys' => [
                    'active_channels', 'time_per_week', 'monthly_goal', 'whats_happening', 'audience_need',
                    'monthly_theme', 'hero_pieces', 'ctas', 'channel_map',
                ],
                'sections' => [
                    1 => ['active_channels', 'time_per_week'],
                    2 => ['monthly_goal', 'whats_happening', 'audience_need', 'monthly_theme'],
                    3 => ['hero_pieces', 'ctas'],
                    4 => ['channel_map'],
                ],
                'step_titles' => [
                    'en' => [1 => 'Step 1: Channels & time', 2 => 'Step 2: Monthly focus', 3 => 'Step 3: Key pieces & CTAs', 4 => 'Step 4: Channel map'],
                    'de' => [1 => 'Schritt 1: Kanäle & Zeit', 2 => 'Schritt 2: Monatlicher Fokus', 3 => 'Schritt 3: Key Pieces & CTAs', 4 => 'Schritt 4: Kanalplan'],
                ],
                'labels' => [
                    'en' => [
                        'active_channels' => 'Active channels', 'time_per_week' => 'Time available per week',
                        'monthly_goal' => 'Main goal this month', 'whats_happening' => 'What’s happening this month',
                        'audience_need' => 'What your audience needs now', 'monthly_theme' => 'Monthly theme',
                        'hero_pieces' => 'Key content pieces', 'ctas' => 'CTAs for the month', 'channel_map' => 'How content maps across channels',
                    ],
                    'de' => [
                        'active_channels' => 'Aktive Kanäle', 'time_per_week' => 'Zeit pro Woche',
                        'monthly_goal' => 'Hauptziel diesen Monat', 'whats_happening' => 'Was diesen Monat passiert',
                        'audience_need' => 'Was deine Zielgruppe jetzt braucht', 'monthly_theme' => 'Monatsthema',
                        'hero_pieces' => 'Wichtige Content-Stücke', 'ctas' => 'CTAs für den Monat', 'channel_map' => 'Verteilung auf die Kanäle',
                    ],
                ],
            ],
        ];
    }
}
