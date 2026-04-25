<div class="flex flex-wrap items-center gap-3">
    @php
        $canUpdatePresence = $canUpdatePresence ?? false;
        $statusAccentColors = [
            \App\Enums\PresenceStatus::PRESENT->value => 'var(--color-success)',
            \App\Enums\PresenceStatus::SICK->value => 'var(--color-warning)',
            \App\Enums\PresenceStatus::PERMISSION->value => 'var(--color-primary)',
            \App\Enums\PresenceStatus::LATE->value => 'var(--color-secondary)',
            \App\Enums\PresenceStatus::ABSENT->value => 'var(--color-danger)',
        ];
    @endphp

    @foreach ($statuses as $statusKey => $statusLabel)
        @php
            $accentColor = $statusAccentColors[$statusKey] ?? 'var(--color-primary)';
        @endphp

        <label wire:key="presence-status-{{ $getRecord()->id }}-{{ $statusKey }}" class="flex items-center gap-1.5 cursor-pointer hover:opacity-80 transition">
            <input
                type="radio"
                name="presence-status-{{ $getRecord()->id }}"
                value="{{ $statusKey }}"
                @if ($canUpdatePresence)
                    wire:change="updatePresenceStatus({{ $getRecord()->id }}, '{{ $statusKey }}')"
                @endif
                @disabled(! $canUpdatePresence)
                @checked($getState() === $statusKey)
                class="fi-checkbox-input h-4 w-4 rounded border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0 focus:ring-primary-600 dark:bg-white/5 dark:ring-white/20 dark:focus:ring-primary-500"
                style="accent-color: {{ $accentColor }};"
            >
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ $statusLabel }}
            </span>
        </label>
    @endforeach
</div>
