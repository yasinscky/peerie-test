<?php

namespace App\Filament\Resources\HashtagResource\Pages;

use App\Filament\Resources\HashtagResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateHashtag extends CreateRecord
{
    protected static string $resource = HashtagResource::class;
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeHashtagData($data);
    }

    protected function normalizeHashtagData(array $data): array
    {
        if (isset($data['hashtag_blocks']) && is_array($data['hashtag_blocks'])) {
            foreach ($data['hashtag_blocks'] as &$block) {
                if (isset($block['tags']) && is_array($block['tags'])) {
                    $block['tags'] = array_map(function ($tag) {
                        if (is_string($tag)) {
                            $tag = trim($tag);
                            if (!empty($tag) && !Str::startsWith($tag, '#')) {
                                return '#' . $tag;
                            }
                            return $tag;
                        }
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
