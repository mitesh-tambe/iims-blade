@props([
    'action' => '',
    'name' => 'search',
    'placeholder' => 'Search...',
    'value' => request()->get('search'),
])

<form method="GET" action="{{ $action }}" class="join w-full max-w-md">
    <div class="w-full">
        <label class="input join-item flex items-center gap-2 w-full">
            <!-- Search Icon -->
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </g>
            </svg>

            <input type="text" name="{{ $name }}" value="{{ $value }}"
                placeholder="{{ $placeholder }}" class="grow" />
        </label>
    </div>

    <button type="submit" class="btn btn-neutral join-item">
        Search
    </button>
</form>
