<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $description = $getState();
        $hasContent = filled($description);
    @endphp

    <div x-data="{ open: false }" class="space-y-3">
        @if ($hasContent)
            <button
                type="button"
                class="fi-btn fi-btn-size-md fi-btn-color-primary inline-flex items-center gap-1.5"
                @click="open = !open"
            >
                <svg class="fi-btn-icon h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12h.01M12 12h.01M9 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.932L3 20l1.084-3.252C3.379 15.59 3 13.846 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span x-text="open ? 'Hide instruction preview' : 'Open instruction preview'"></span>
            </button>

            <div
                x-show="open"
                x-cloak
                class="mt-3 rounded-xl border border-gray-200 bg-white shadow-sm"
            >
                <div class="p-4 max-h-[60vh] overflow-y-auto">
                    <h2 class="text-sm font-semibold text-gray-900 mb-3">
                        Instruction preview (dashboard)
                    </h2>

                    <div class="prose max-w-none text-sm leading-relaxed text-gray-800">
                        {!! $description !!}
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                Fill in the "Instruction" field to see how it will look in the dashboard.
            </div>
        @endif
    </div>
</x-dynamic-component>
