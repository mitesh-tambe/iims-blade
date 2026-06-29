<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('sales.index') }}" placeholder="Search..." />
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- 📋 Sales Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Invoice No</th>
                    <th>Sale Date</th>
                    <th>Total Amount</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id='salesTableBody'>
                @forelse ($sales as $sale)
                    <tr class="hover:bg-base-300" data-sale-id="{{ $sale->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Needed for JS update --}}
                        <td class="invoice_no">
                            {{ $sale->invoice_no }}
                        </td>

                        <td class="sale_date">
                            {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}
                        </td>

                        <td class="total_amount">
                            {{ $sale->total_amount }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-xs btn-info" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            {{-- ✏️ Edit --}}
                            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-xs btn-warning">
                                <i class="fa-solid fa-pencil"></i>
                            </a>

                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this sale record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-error tooltip" data-tip="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500">
                            No sales found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>


    @if (session('print_bill'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                setTimeout(() => {

                    window.open(
                        @json(session('print_bill')),
                        '_blank'
                    );

                }, 100);

            });
        </script>
    @endif

</x-app-layout>
