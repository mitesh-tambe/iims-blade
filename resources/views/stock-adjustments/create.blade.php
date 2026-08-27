<x-app-layout>
    <div class="flex justify-center">
        <form action="{{ route('stock-adjustments.store') }}" method="POST" class="w-full max-w-5xl">
            @csrf

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <legend class="fieldset-legend text-lg font-semibold">
                        Stock Adjustment Details
                    </legend>
                    <a href="{{ route('products.create') }}" class="btn btn-primary" target="_blank"
                        rel="noopener noreferrer">
                        Create New Product
                    </a>
                </div>

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Adjustment No --}}
                    <div>
                        <label class="label">Adjustment No</label>
                        <input type="text" name="adjustment_no" class="input input-bordered w-full"
                            value="{{ old('adjustment_no', $adjustment_no ?? '') }}" placeholder="Adjustment number"
                            readonly />
                        @error('adjustment_no')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Adjustment Date --}}
                    <div>
                        <label class="label">Adjustment Date</label>
                        <input type="date" name="adjustment_date" class="input input-bordered w-full"
                            value="{{ old('adjustment_date', now()->format('Y-m-d')) }}" required />
                        @error('adjustment_date')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product --}}
                    <div>
                        <label class="label">Product</label>

                        <select id="product_id" name="product_id" required></select>

                        @error('product_id')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Adjustment Type --}}
                    <div>
                        <label class="label">Adjustment Type</label>
                        <select name="adjustment_type" class="select select-bordered w-full" required>
                            <option value="">Select Type</option>

                            <option value="add" {{ old('adjustment_type') === 'add' ? 'selected' : '' }}>
                                Add Stock
                            </option>

                            <option value="remove" {{ old('adjustment_type') === 'remove' ? 'selected' : '' }}>
                                Remove Stock
                            </option>
                        </select>

                        @error('adjustment_type')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="label">Quantity</label>
                        <input type="number" name="quantity" class="input input-bordered w-full"
                            value="{{ old('quantity') }}" min="1" placeholder="Enter quantity" required />
                        @error('quantity')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="label">Reason</label>
                        <select name="reason" class="select select-bordered w-full" required>
                            <option value="">Select Reason</option>
                            <option value="Purchase Correction"
                                {{ old('reason') === 'Purchase Correction' ? 'selected' : '' }}>
                                Purchase Correction
                            </option>

                            <option value="Sales Correction"
                                {{ old('reason') === 'Sales Correction' ? 'selected' : '' }}>
                                Sales Correction
                            </option>

                            <option value="Damaged" {{ old('reason') === 'Damaged' ? 'selected' : '' }}>
                                Damaged
                            </option>

                            <option value="Expired" {{ old('reason') === 'Expired' ? 'selected' : '' }}>
                                Expired
                            </option>

                            <option value="Lost / Missing" {{ old('reason') === 'Lost / Missing' ? 'selected' : '' }}>
                                Lost / Missing
                            </option>

                            <option value="Found Stock" {{ old('reason') === 'Found Stock' ? 'selected' : '' }}>
                                Found Stock
                            </option>

                            <option value="Physical Stock Count"
                                {{ old('reason') === 'Physical Stock Count' ? 'selected' : '' }}>
                                Physical Stock Count
                            </option>

                            <option value="Opening Stock" {{ old('reason') === 'Opening Stock' ? 'selected' : '' }}>
                                Opening Stock
                            </option>

                            <option value="Return" {{ old('reason') === 'Return' ? 'selected' : '' }}>
                                Return
                            </option>

                            <option value="Other" {{ old('reason') === 'Other' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>

                        @error('reason')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remarks --}}
                    <div class="md:col-span-2">
                        <label class="label">Remarks</label>

                        <textarea name="remarks" class="textarea textarea-bordered w-full" rows="4"
                            placeholder="Enter additional remarks...">{{ old('remarks') }}</textarea>

                        @error('remarks')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-2 pt-4">
                    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Save Adjustment
                    </button>
                </div>
            </fieldset>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const productSelect = new TomSelect('#product_id', {
                valueField: 'id',
                labelField: 'book_name',
                placeholder: 'Search product...',
                preload: false,
                maxOptions: 20,

                // Laravel is already doing the search.
                // Don't filter the returned results again.
                score: function() {
                    return function() {
                        return 1;
                    };
                },

                load: function(query, callback) {
                    if (!query || query.length < 2) {
                        callback();
                        return;
                    }
                    fetch(
                            `{{ route('stock-adjustments.product-search') }}?q=${encodeURIComponent(query)}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                        .then(response => {

                            if (!response.ok) {
                                throw new Error(`HTTP error: ${response.status}`);
                            }

                            return response.json();
                        })
                        .then(data => {

                            console.log('Stock adjustment products:', data);

                            callback(data);
                        })
                        .catch(error => {

                            console.error('Product search error:', error);

                            callback();
                        });
                },

                render: {

                    option: function(item, escape) {

                        return `
                        <div class="py-2 px-2">
                            <div class="font-medium">
                                ${escape(item.book_name ?? '')}
                            </div>

                            <div class="text-sm text-gray-500 mt-1">
                                ISBN: ${escape(item.isbn ?? '-')}
                                &nbsp; | &nbsp;
                                Barcode: ${escape(item.barcode_no ?? '-')}
                                &nbsp; | &nbsp;
                                Available: ${escape(String(item.available_stock ?? 0))}
                            </div>
                        </div>
                    `;
                    },

                    item: function(item, escape) {

                        return `
                        <div>
                            ${escape(item.book_name ?? '')}
                        </div>
                    `;
                    }
                },
                onItemAdd: function(value) {
                    const item = this.options[value];
                    console.log('Selected product:', item);
                }
            });

        });
    </script>
</x-app-layout>
