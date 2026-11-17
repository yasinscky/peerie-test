<?php

namespace App\Services;

class NotionInstructionHtml
{
    public function convert(string $markdown): string
    {
        $markdown = $this->genderColons($markdown);
        $lines = preg_split("/\r\n|\n|\r/", $markdown) ?: [];
        $html = '';
        $count = count($lines);
        $index = 0;

        while ($index < $count) {
            if (trim($lines[$index]) === '') {
                $index++;

                continue;
            }

            if (preg_match('/^```/', $lines[$index])) {
                [$block, $index] = $this->takeCode($lines, $index);
                $html .= $block;

                continue;
            }

            if (preg_match('/^\|/', $lines[$index])) {
                [$block, $index] = $this->takeTable($lines, $index);
                $html .= $block;

                continue;
            }

            if (preg_match('/^(?:- |• |\d+\. )/', $lines[$index])) {
                [$block, $index] = $this->takeList($lines, $index);
                $html .= $block;

                continue;
            }

            if (preg_match('/^#{1,3}\s+/', $lines[$index])) {
                $html .= $this->heading($lines[$index]);
                $index++;

                continue;
            }

            if (preg_match('/^>\s?/', $lines[$index])) {
                [$block, $index] = $this->takeQuote($lines, $index);
                $html .= $block;

                continue;
            }

            if (preg_match('/^-{3,}\s*$/', trim($lines[$index]))) {
                $index++;

                continue;
            }

            [$block, $index] = $this->takeParagraph($lines, $index);
            $html .= $block;
        }

        return $this->stripDividers($this->stripTaskHeader($this->italicizeNotices($html)));
    }

    public function body(string $file): string
    {
        $secondHeading = strpos($file, "\n# ", 1);
        if ($secondHeading !== false) {
            return trim(substr($file, $secondHeading + 1));
        }

        $lines = preg_split("/\r\n|\n|\r/", $file) ?: [];
        $lastProperty = null;
        foreach ($lines as $index => $line) {
            if (preg_match('/^(?:Action|Cadence|CapacityAllowed|Category|Country|Documents|Document name|EffortMin|GlobalOrder|Industry|Instruction|Language|LocalPresence|Prerequisites|Preferred week of the month):\s*/', $line)) {
                $lastProperty = $index;
            }
        }
        if ($lastProperty === null) {
            return '';
        }

        return trim(implode("\n", array_slice($lines, $lastProperty + 1)));
    }

    private function genderColons(string $text): string
    {
        $suffixes = ['innen', 'die', 'in', 'e', 'r'];
        foreach ($suffixes as $suffix) {
            $boundary = $suffix === 'innen' ? '' : '\\b';
            $text = preg_replace('/(?<=\p{L})\*{1,3}(?='.$suffix.$boundary.')/u', ':', $text) ?? $text;
        }

        return $text;
    }

    public function normalizeExisting(string $html): string
    {
        $html = preg_replace('/<\/em>\*{1,3}(?=innen)/u', ':', $html) ?? $html;

        return $this->stripDividers($this->stripTaskHeader($this->italicizeNotices($this->genderColons($this->repairTables($html)))));
    }

    public function stripTaskHeader(string $html): string
    {
        $blocks = [];
        $rest = $html;
        while (preg_match('/\A(\s*)<(h1|h2|p)\b[^>]*>(.*?)<\/\2>/is', $rest, $matches)) {
            $blocks[] = [
                'gap' => $matches[1],
                'tag' => strtolower($matches[2]),
                'inner' => $matches[3],
                'html' => substr($matches[0], strlen($matches[1])),
            ];
            $rest = substr($rest, strlen($matches[0]));
        }
        if ($blocks === []) {
            return $html;
        }

        $introStart = in_array($blocks[0]['tag'], ['h1', 'h2'], true) ? 1 : 0;
        $introEnd = $introStart;
        while ($introEnd < count($blocks) && $blocks[$introEnd]['tag'] === 'p') {
            $introEnd++;
        }
        if ($introStart === $introEnd) {
            return $html;
        }

        $headerEnd = null;
        for ($index = $introStart; $index < $introEnd; $index++) {
            $isLabel = $this->paragraphHasTime($blocks[$index]['inner']) || $this->paragraphHasWhy($blocks[$index]['inner']);
            if ($isLabel) {
                $headerEnd = $index;

                continue;
            }
            if ($headerEnd !== null) {
                break;
            }
        }
        if ($headerEnd === null) {
            return $this->stripPreamble($html);
        }

        $removeTitle = $introStart === 1 && ! $this->isContentHeading($blocks[0]['inner']);
        $output = '';
        foreach ($blocks as $index => $block) {
            if ($index === 0 && $removeTitle) {
                continue;
            }
            if ($index >= $introStart && $index <= $headerEnd) {
                $stripped = $this->stripIntroParagraph($block['inner']);
                if ($stripped === null) {
                    continue;
                }
                $output .= $block['gap'].$stripped;

                continue;
            }
            $output .= $block['gap'].$block['html'];
        }

        return $this->stripPreamble($output.$rest);
    }

    private function stripPreamble(string $html): string
    {
        $guard = 0;
        while ($guard < 12 && preg_match('/\A(\s*)<(h1|h2|p)\b[^>]*>(.*?)<\/\2>/is', $html, $matches)) {
            $guard++;
            $tag = strtolower($matches[2]);
            $dropFollowingParagraph = false;
            $drop = false;
            if ($tag === 'h1' && ! $this->isContentHeading($matches[3])) {
                $drop = true;
            } elseif ($this->isTimeLine($this->plainLine($matches[3])) || $this->paragraphHasTime($matches[3]) || $this->isWhyLine($this->plainLine($matches[3])) || $this->paragraphHasWhy($matches[3])) {
                $drop = true;
                $dropFollowingParagraph = $tag !== 'p';
            } elseif ($tag === 'p' && preg_match('/\A'.preg_quote($matches[0], '/').'\s*<(h1|h2)\b[^>]*>(.*?)<\/\1>/is', $html, $next) && ($this->isWhyLine($this->plainLine($next[2])) || $this->paragraphHasWhy($next[2]))) {
                $drop = true;
            }
            if (! $drop) {
                break;
            }
            $html = substr($html, strlen($matches[0]));
            if ($dropFollowingParagraph && preg_match('/\A\s*<p\b[^>]*>.*?<\/p>/is', $html, $paragraph)) {
                $html = substr($html, strlen($paragraph[0]));
            }
        }

        return $html;
    }

    public function stripDividers(string $html): string
    {
        return preg_replace('/<p>\s*-{3,}\s*<\/p>/i', '', $html) ?? $html;
    }

    public function repairTables(string $html): string
    {
        if (! str_contains(strtolower($html), '<table')) {
            return $html;
        }

        return preg_replace_callback(
            '/<table\b[^>]*>.*?<\/table>/is',
            fn (array $matches): string => $this->ensureTableBorder($this->repairTableHtml($matches[0])),
            $html
        ) ?? $html;
    }

    private function stripIntroParagraph(string $inner): ?string
    {
        $lines = preg_split('/<br\s*\/?>/i', $inner) ?: [];
        $hasLabel = false;
        foreach ($lines as $line) {
            $plain = $this->plainLine($line);
            if ($this->isTimeLine($plain) || $this->isWhyLine($plain)) {
                $hasLabel = true;
                break;
            }
        }
        $kept = [];
        $passedLabel = false;
        foreach ($lines as $line) {
            $plain = $this->plainLine($line);
            if ($plain === '') {
                continue;
            }
            if ($this->isTimeLine($plain) || $this->isWhyLine($plain)) {
                $passedLabel = true;

                continue;
            }
            if (! $hasLabel || ! $passedLabel) {
                continue;
            }
            $kept[] = trim($line);
        }
        if ($kept === []) {
            return null;
        }

        return '<p>'.implode('<br>', $kept).'</p>';
    }

    private function paragraphHasTime(string $inner): bool
    {
        $lines = preg_split('/<br\s*\/?>/i', $inner) ?: [];
        foreach ($lines as $line) {
            if ($this->isTimeLine($this->plainLine($line))) {
                return true;
            }
        }

        return false;
    }

    private function paragraphHasWhy(string $inner): bool
    {
        $lines = preg_split('/<br\s*\/?>/i', $inner) ?: [];
        foreach ($lines as $line) {
            if ($this->isWhyLine($this->plainLine($line))) {
                return true;
            }
        }

        return false;
    }

    private function isContentHeading(string $inner): bool
    {
        $plain = $this->plainLine($inner);

        return (bool) preg_match('/^(?:step|schritt)\s*\d+|before you start|bevor du|how to|so geht|so beanspruch/iu', $plain);
    }

    private function isTimeLine(string $plain): bool
    {
        return (bool) preg_match('/^(?:time needed|time required|time commitment|benötigte zeit|zeitbedarf|zeitaufwand)\b/iu', $plain);
    }

    private function isWhyLine(string $plain): bool
    {
        return (bool) preg_match('/^(?:why this matters|why it matters|warum das wichtig ist)\b/iu', $plain);
    }

    private function plainLine(string $html): string
    {
        $plain = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $plain = str_replace("\u{00A0}", ' ', $plain);

        return trim(preg_replace('/\s+/u', ' ', $plain) ?? $plain);
    }

    private function italicizeNotices(string $html): string
    {
        return preg_replace_callback(
            '/<(p|li)>(.*?)<\/\1>/isu',
            function (array $matches): string {
                $plain = trim(html_entity_decode(strip_tags($matches[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if (! preg_match('/^(Disclaimer|Hinweis|Rechtlicher Hinweis|Legal disclaimer|Tool disclaimer|Third-party tools disclaimer)\s*:/iu', $plain)) {
                    return $matches[0];
                }

                return '<'.$matches[1].'><em>'.e($plain).'</em></'.$matches[1].'>';
            },
            $html
        ) ?? $html;
    }

    private function heading(string $line): string
    {
        if (! preg_match('/^(#{1,3})\s+(.*)$/', trim($line), $matches)) {
            return '';
        }

        $level = strlen($matches[1]);
        $text = $this->inline($matches[2]);

        return "<h{$level}>{$text}</h{$level}>";
    }

    private function takeParagraph(array $lines, int $index): array
    {
        $parts = [];
        $count = count($lines);
        while ($index < $count && trim($lines[$index]) !== '' && ! $this->startsBlock($lines[$index])) {
            $parts[] = $this->inline(trim($lines[$index]));
            $index++;
        }

        return ['<p>'.implode('<br>', $parts).'</p>', $index];
    }

    private function takeList(array $lines, int $index): array
    {
        $ordered = (bool) preg_match('/^\d+\.\s+/', $lines[$index]);
        $tag = $ordered ? 'ol' : 'ul';
        $items = '';
        $count = count($lines);
        while ($index < $count && preg_match($ordered ? '/^\d+\.\s+(.*)$/' : '/^(?:- |• )(.*)$/', $lines[$index], $matches)) {
            $items .= '<li>'.$this->inline(trim($matches[1])).'</li>';
            $index++;
        }

        return ["<{$tag}>{$items}</{$tag}>", $index];
    }

    private function takeQuote(array $lines, int $index): array
    {
        $parts = [];
        $count = count($lines);
        while ($index < $count && preg_match('/^>\s?(.*)$/', $lines[$index], $matches)) {
            $parts[] = $this->inline(trim($matches[1]));
            $index++;
        }

        return ['<blockquote><p>'.implode('<br>', $parts).'</p></blockquote>', $index];
    }

    private function takeCode(array $lines, int $index): array
    {
        $index++;
        $code = [];
        $count = count($lines);
        while ($index < $count && ! preg_match('/^```/', $lines[$index])) {
            $code[] = $lines[$index];
            $index++;
        }
        if ($index < $count) {
            $index++;
        }

        return ['<pre><code>'.e(implode("\n", $code)).'</code></pre>', $index];
    }

    private function takeTable(array $lines, int $index): array
    {
        $rows = [];
        $count = count($lines);
        while ($index < $count && preg_match('/^\|/', $lines[$index])) {
            $rows[] = $lines[$index];
            $index++;
        }

        $parsedRows = [];
        $columns = 0;
        foreach ($rows as $row) {
            $cells = array_map('trim', explode('|', trim($row, "| \t")));
            if ($this->isSeparatorRow($cells)) {
                continue;
            }
            $parsedRows[] = $cells;
            $columns = max($columns, count($cells));
        }

        $html = '<table>';
        foreach ($parsedRows as $rowIndex => $cells) {
            $cells = array_pad($cells, $columns, '');
            $tag = $rowIndex === 0 ? 'th' : 'td';
            $html .= '<tr>';
            foreach ($cells as $cell) {
                $html .= "<{$tag}>".($cell === '' ? '&nbsp;' : $this->inline($cell))."</{$tag}>";
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return [$html, $index];
    }

    private function repairTableHtml(string $html): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $table = $document->getElementsByTagName('table')->item(0);
        if (! $table instanceof \DOMElement || ! $this->tableNeedsRepair($table)) {
            return $html;
        }

        $grid = [];
        $rowIndex = 0;
        foreach ($table->getElementsByTagName('tr') as $row) {
            $column = 0;
            foreach ($row->childNodes as $cell) {
                if (! $cell instanceof \DOMElement || ! in_array($cell->nodeName, ['td', 'th'], true)) {
                    continue;
                }
                while (isset($grid[$rowIndex][$column])) {
                    $column++;
                }
                $rowspan = max(1, (int) $cell->getAttribute('rowspan'));
                $colspan = max(1, (int) $cell->getAttribute('colspan'));
                for ($rowOffset = 0; $rowOffset < $rowspan; $rowOffset++) {
                    for ($columnOffset = 0; $columnOffset < $colspan; $columnOffset++) {
                        $grid[$rowIndex + $rowOffset][$column + $columnOffset] = $rowOffset === 0 && $columnOffset === 0
                            ? $this->cellSnapshot($cell)
                            : ['tag' => $cell->nodeName, 'html' => '', 'attributes' => []];
                    }
                }
                $column += $colspan;
            }
            $rowIndex++;
        }

        $width = 0;
        foreach ($grid as $row) {
            $width = max($width, count($row));
        }

        while ($table->firstChild) {
            $table->removeChild($table->firstChild);
        }
        $body = $document->createElement('tbody');
        foreach ($grid as $row) {
            $tableRow = $document->createElement('tr');
            for ($column = 0; $column < $width; $column++) {
                $snapshot = $row[$column] ?? ['tag' => 'td', 'html' => '', 'attributes' => []];
                $cell = $document->createElement($snapshot['tag'] === 'th' ? 'th' : 'td');
                foreach ($snapshot['attributes'] as $name => $value) {
                    $cell->setAttribute($name, $value);
                }
                $this->fillCell($document, $cell, $snapshot['html']);
                $tableRow->appendChild($cell);
            }
            $body->appendChild($tableRow);
        }
        if (! $table->hasAttribute('border')) {
            $table->setAttribute('border', '1');
        }
        $table->appendChild($body);

        return $document->saveHTML($table) ?: $html;
    }

    private function ensureTableBorder(string $html): string
    {
        if (preg_match('/<table\b[^>]*\bborder\s*=/i', $html)) {
            return $html;
        }

        return preg_replace('/<table\b/i', '<table border="1"', $html, 1) ?? $html;
    }

    private function tableNeedsRepair(\DOMElement $table): bool
    {
        $counts = [];
        foreach ($table->getElementsByTagName('tr') as $row) {
            $count = 0;
            foreach ($row->childNodes as $cell) {
                if (! $cell instanceof \DOMElement || ! in_array($cell->nodeName, ['td', 'th'], true)) {
                    continue;
                }
                $count++;
                if ((int) $cell->getAttribute('rowspan') > 1 || (int) $cell->getAttribute('colspan') > 1) {
                    return true;
                }
            }
            $counts[] = $count;
        }

        return count(array_unique($counts)) > 1;
    }

    private function cellSnapshot(\DOMElement $cell): array
    {
        $attributes = [];
        foreach (['style', 'class', 'align', 'valign'] as $name) {
            if ($cell->hasAttribute($name)) {
                $attributes[$name] = $cell->getAttribute($name);
            }
        }

        $html = '';
        foreach ($cell->childNodes as $child) {
            $html .= $cell->ownerDocument?->saveHTML($child) ?? '';
        }

        return [
            'tag' => $cell->nodeName,
            'html' => $html,
            'attributes' => $attributes,
        ];
    }

    private function fillCell(\DOMDocument $document, \DOMElement $cell, string $html): void
    {
        $plain = trim(str_replace(["\u{00A0}", '&nbsp;'], '', strip_tags($html)));
        if ($plain === '') {
            $cell->appendChild($document->createTextNode("\u{00A0}"));

            return;
        }

        $source = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $source->loadHTML('<?xml encoding="UTF-8"><div id="cell">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $wrapper = $source->getElementsByTagName('div')->item(0);
        if (! $wrapper) {
            $cell->appendChild($document->createTextNode($plain));

            return;
        }
        foreach (iterator_to_array($wrapper->childNodes) as $child) {
            $cell->appendChild($document->importNode($child, true));
        }
    }

    private function isSeparatorRow(array $cells): bool
    {
        if ($cells === []) {
            return false;
        }

        foreach ($cells as $cell) {
            if (! preg_match('/^:?-{3,}:?$/', $cell)) {
                return false;
            }
        }

        return true;
    }

    private function startsBlock(string $line): bool
    {
        return (bool) preg_match('/^(?:#{1,3}\s+|```|\||>\s?|- |• |\d+\.\s+|-{3,}\s*$)/', $line);
    }

    private function inline(string $text): string
    {
        $text = e($text);
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>', $text) ?? $text;
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*(.+?)\*(?!\*)/', '<em>$1</em>', $text) ?? $text;

        return $text;
    }
}
