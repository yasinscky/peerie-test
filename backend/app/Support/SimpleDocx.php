<?php

namespace App\Support;

use ZipArchive;

class SimpleDocx
{
    public static function fromBlocks(string $title, array $blocks): string
    {
        $paragraphs = [self::heading($title, 32)];

        foreach ($blocks as $block) {
            $paragraphs[] = self::heading($block['title'], 24);
            foreach ($block['items'] as $item) {
                $paragraphs[] = self::run($item['label'], true);
                $value = trim((string) ($item['value'] ?? ''));
                $paragraphs[] = self::run($value !== '' ? $value : '—', false);
                $paragraphs[] = self::emptyParagraph();
            }
        }

        $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body>'
            . implode('', $paragraphs)
            . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1134" w:right="1134" w:bottom="1134" w:left="1134"/></w:sectPr>'
            . '</w:body></w:document>';

        $tmp = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            return '';
        }
        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rels());
        $zip->addFromString('word/_rels/document.xml.rels', self::documentRels());
        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();

        $binary = file_get_contents($tmp);
        @unlink($tmp);

        return $binary !== false ? $binary : '';
    }

    private static function heading(string $text, int $size): string
    {
        $escaped = self::escape($text);

        return '<w:p><w:pPr><w:spacing w:after="160"/></w:pPr><w:r>'
            . '<w:rPr><w:b/><w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/><w:color w:val="1C1A1B"/></w:rPr>'
            . '<w:t xml:space="preserve">' . $escaped . '</w:t></w:r></w:p>';
    }

    private static function run(string $text, bool $bold): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $text) ?: [''];
        $parts = [];
        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $parts[] = '<w:br/>';
            }
            $parts[] = '<w:t xml:space="preserve">' . self::escape($line) . '</w:t>';
        }

        $boldXml = $bold ? '<w:b/>' : '';

        return '<w:p><w:pPr><w:spacing w:after="40"/></w:pPr><w:r>'
            . '<w:rPr>' . $boldXml . '<w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
            . implode('', $parts)
            . '</w:r></w:p>';
    }

    private static function emptyParagraph(): string
    {
        return '<w:p><w:pPr><w:spacing w:after="80"/></w:pPr></w:p>';
    }

    private static function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '</Types>';
    }

    private static function rels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>';
    }

    private static function documentRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';
    }
}
