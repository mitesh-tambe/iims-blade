@props([
    'id',
    'title' => 'Overview',
    'description' => null,
])

<div class="bg-base-200 border border-base-300 rounded-box p-4 mb-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

        <div>
            <h2 class="text-lg font-semibold">
                {{ $title }}
            </h2>

            @if ($description)
                <p class="text-sm text-base-content/60">
                    {{ $description }}
                </p>
            @endif
        </div>

        {{-- Period Buttons --}}
        <div class="join">

            <button
                type="button"
                class="btn btn-sm join-item btn-primary"
                data-chart-period="weekly"
            >
                Weekly
            </button>

            <button
                type="button"
                class="btn btn-sm join-item"
                data-chart-period="monthly"
            >
                Monthly
            </button>

            <button
                type="button"
                class="btn btn-sm join-item"
                data-chart-period="yearly"
            >
                Yearly
            </button>

        </div>

    </div>

    {{-- Responsive Chart Container --}}
    <div class="relative w-full h-[300px] sm:h-[350px] lg:h-[400px]">

        <canvas id="{{ $id }}"></canvas>

    </div>

</div>