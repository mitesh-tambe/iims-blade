<x-app-layout>

    @php
        $filterParams = request()->only(['search', 'page']);
    @endphp

    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('stock-adjustments.index') }}" placeholder="Search adjustments..." />

            {{-- ➕ Create Adjustment --}}
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
                Create Stock Adjustment
            </a>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- 📋 Stock Adjustments Table --}}
        <table class="table">

            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Adjustment No.</th>
                    <th>Product</th>
                    <th>Adjustment Date</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>Created By</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($adjustments as $adjustment)
                    <tr class="hover:bg-base-300">

                        <th>{{ $adjustments->firstItem() + $loop->index }}</th>

                        <td class="font-medium">
                            {{ $adjustment->adjustment_no }}
                        </td>

                        <td>
                            {{ $adjustment->product->book_name ?? '-' }}
                        </td>

                        <td>
                            {{ $adjustment->adjustment_date ? $adjustment->adjustment_date->format('d/m/Y') : '-' }}
                        </td>

                        <td>
                            @if ($adjustment->quantity > 0)
                                <span class="text-success font-semibold">
                                    +{{ $adjustment->quantity }}
                                </span>
                            @elseif ($adjustment->quantity < 0)
                                <span class="text-error font-semibold">
                                    {{ $adjustment->quantity }}
                                </span>
                            @else
                                <span class="text-gray-500">
                                    0
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $adjustment->reason }}
                        </td>

                        <td>
                            {{ $adjustment->creator->name ?? '-' }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <a href="{{ route('stock-adjustments.show', $adjustment) }}" class="btn btn-xs btn-info">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            {{-- ❌ Delete --}}
                            <form action="{{ route('stock-adjustments.destroy', $adjustment) }}" method="POST"
                                class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this stock adjustment?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-xs btn-error">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center text-gray-500">
                            No stock adjustments found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

        {{-- 📄 Pagination --}}
        <div class="pt-4">
            {{ $adjustments->links() }}
        </div>

    </div>

</x-app-layout>
