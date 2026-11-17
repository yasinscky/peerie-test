<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CabinetNotification;
use App\Models\NotificationRead;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $items = $this->visible($user, $this->language($request, $user))->get();

        return response()->json([
            'success' => true,
            'notifications' => $items->map(fn (CabinetNotification $item) => $this->payload($item))->values(),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        $now = now();
        $items = $this->visible($user, $this->language($request, $user))->get();

        foreach ($items as $item) {
            NotificationRead::query()->updateOrCreate(
                [
                    'notification_id' => $item->id,
                    'user_id' => $user->id,
                ],
                ['read_at' => $now]
            );
        }

        return response()->json([
            'success' => true,
            'notifications' => $items->map(fn (CabinetNotification $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'body' => $item->body,
                'published_at' => $item->published_at?->toIso8601String(),
                'read' => true,
            ])->values(),
        ]);
    }

    private function language(Request $request, User $user): string
    {
        $language = (string) $request->query('language', $user->language ?: 'en');

        return in_array($language, ['en', 'de'], true) ? $language : 'en';
    }

    private function visible(User $user, string $language)
    {
        return CabinetNotification::query()
            ->visibleTo($user, $language)
            ->with(['reads' => fn ($query) => $query->where('user_id', $user->id)])
            ->orderByDesc('published_at')
            ->limit(30);
    }

    private function payload(CabinetNotification $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'body' => $item->body,
            'published_at' => $item->published_at?->toIso8601String(),
            'read' => $item->reads->isNotEmpty(),
        ];
    }
}
