<x-app-layout>

    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('invoices.index') }}" placeholder="Search invoices..." />

            {{-- ➕ Create Invoice --}}
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                Create Invoice
            </a>
        </div>

        {{-- show success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- 📋 Invoices Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Invoice No.</th>
                    <th>Vendor Name</th>
                    <th>Purchase Date</th>
                    <th>Total Amount</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($purchases as $purchase)
                    <tr class="hover:bg-base-300" data-purchase-id="{{ $purchase->id }}">
                        <th>{{ $loop->iteration }}</th>

                        <td class="font-medium">
                            {{ $purchase->invoice_no }}
                        </td>

                        <td>{{ $purchase->vendor->name ?? '-' }}</td>

                        <td>
                            {{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : '-' }}
                        </td>

                        <td>₹ {{ number_format($purchase->total_amount, 2) }}</td>


                        <td class="text-right space-x-1">
                            {{-- 👁 View --}}
                            <a href="{{ route('invoices.show', ['invoice' => $purchase->id]) }}"
                                class="btn btn-xs btn-info">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            @if (auth()->user()->email === 'admin@gmail.com' || ($purchase->purchase_date && $purchase->purchase_date->isToday()))
                                {{-- ✏️ Edit --}}
                                <a href="{{ route('invoices.edit', ['invoice' => $purchase->id]) }}"
                                    class="btn btn-xs btn-warning">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>

                                {{-- ❌ Delete --}}
                                <form action="{{ route('invoices.destroy', ['invoice' => $purchase->id]) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-error">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 📄 Pagination --}}
        {{-- <div class="pt-4">
            {{ $products->links() }}
        </div> --}}
    </div>
</x-app-layout>
