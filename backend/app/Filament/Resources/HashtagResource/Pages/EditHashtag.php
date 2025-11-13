<?php

namespace App\Filament\Resources\HashtagResource\Pages;

use App\Filament\Resources\HashtagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditHashtag extends EditRecord
{
    protected static string $resource = HashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeHashtagData($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Преобразуем объекты с 'tag' в строки для отображения в форме
        if (isset($data['hashtag_blocks']) && is_array($data['hashtag_blocks'])) {
            foreach ($data['hashtag_blocks'] as &$block) {
                if (isset($block['tags']) && is_array($block['tags'])) {
                    $block['tags'] = array_map(function ($tag) {
                        if (is_array($tag) && isset($tag['tag'])) {
                            return $tag['tag'];
                        }
                        return $tag;
                    }, $block['tags']);
                }
            }
        }
        
        return $data;
    }

    protected function normalizeHashtagData(array $data): array
    {
        // Нормализуем хештеги в блоках (добавляем # если отсутствует)
        if (isset($data['hashtag_blocks']) && is_array($data['hashtag_blocks'])) {
            foreach ($data['hashtag_blocks'] as &$block) {
                if (isset($block['tags']) && is_array($block['tags'])) {
                    $block['tags'] = array_map(function ($tag) {
                        if (is_string($tag)) {
                            $tag = trim($tag);
                            // Если хештег не начинается с #, добавляем его
                            if (!empty($tag) && !Str::startsWith($tag, '#')) {
                                return '#' . $tag;
                            }
                            return $tag;
                        }
                        // Если это объект с 'tag' ключом, извлекаем его
                        if (is_array($tag) && isset($tag['tag'])) {
                            $tagStr = trim($tag['tag']);
                            if (!empty($tagStr) && !Str::startsWith($tagStr, '#')) {
                                return '#' . $tagStr;
                            }
                            return $tagStr;
                        }
                        return $tag;
                    }, $block['tags']);
                    $block['tags'] = array_values(array_filter($block['tags']));
                }
                
                // Нормализуем хештеги в категориях
                if (isset($block['categories']) && is_array($block['categories'])) {
                    foreach ($block['categories'] as &$category) {
                        if (isset($category['tags']) && is_array($category['tags'])) {
                            $category['tags'] = array_map(function ($tag) {
                                if (is_string($tag)) {
                                    $tag = trim($tag);
                                    if (!empty($tag) && !Str::startsWith($tag, '#')) {
                                        return '#' . $tag;
                                    }
                                    return $tag;
                                }
                                return $tag;
                            }, $category['tags']);
                            $category['tags'] = array_values(array_filter($category['tags']));
                        }
                    }
                }
            }
        }

        // Нормализуем общий список хештегов
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = array_map(function ($tag) {
                if (is_string($tag)) {
                    $tag = trim($tag);
                    if (!empty($tag) && !Str::startsWith($tag, '#')) {
                        return '#' . $tag;
                    }
                    return $tag;
                }
                return $tag;
            }, $data['tags']);
            $data['tags'] = array_values(array_unique(array_filter($data['tags'])));
        }

        return $data;
    }
}
