<x-app-layout>
    <div class="flex justify-center">
        <form action="{{ route('stock-adjustments.store') }}" method="POST" class="w-full max-w-5xl">
            @csrf

            {{-- SESSION ERROR --}}
            @if (session('error'))
                <div class="alert alert-error mb-4">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- SESSION SUCCESS --}}
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <div>
                        <p class="font-semibold">
                            Please fix the following errors:
                        </p>

                        <ul class="list-disc list-inside mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <legend class="fieldset-legend text-lg font-semibold">
                        Stock Adjustment Details
                    </legend>
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

                    <div>
                        <label class="label">Invoice</label>
                        <select id="purchase_id" name="purchase_id" required>
                            <option value="">Select Invoice...</option>
                        </select> @error('purchase_id')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product --}}
                    <div>
                        <label class="label">Product</label>

                        <select id="product_id" name="product_id" required disabled>
                        </select>

                        @error('product_id')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Adjustment Type --}}
                    <div>
                        <label class="label">Adjustment Type</label>
                        <select id="adjustment_type" name="adjustment_type" class="select select-bordered w-full"
                            required>
                            <option value="">Select Type</option>

                            <option value="add" {{ old('adjustment_type') === 'add' ? 'selected' : '' }}>
                                Add Stock
                            </option>

                            <option value="remove" {{ old('adjustment_type') === 'remove' ? 'selected' : '' }}>
                                Remove Stock
                            </option>

                            <option value="price_change"
                                {{ old('adjustment_type') === 'price_change' ? 'selected' : '' }}>
                                Change Price
                            </option>
                        </select>

                        @error('adjustment_type')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="label">Quantity</label>
                        <input id="quantity" type="number" name="quantity" class="input input-bordered w-full"
                            value="{{ old('quantity') }}" min="1" placeholder="Enter quantity" />
                        @error('quantity')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- MRP --}}
                    <div>
                        <label class="label">MRP</label>
                        <input id="unit_cost" type="number" name="unit_cost" class="input input-bordered w-full"
                            value="{{ old('unit_cost') }}" min="0" step="0.01" placeholder="Enter MRP" />
                        @error('unit_cost')
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
            let latestInvoiceSearch = '';
            let latestProductSearch = '';
            let latestProductPurchaseId = '';

            // =========================================================
            // INVOICE TOM SELECT
            // =========================================================
            const purchaseSelect = new TomSelect('#purchase_id', {
                valueField: 'id',
                labelField: 'invoice_no',
                searchField: [
                    'invoice_no',
                    'ref_no',
                    'vendor_name'
                ],
                placeholder: 'Search invoice...',
                preload: false,
                maxOptions: 20,
                cache: false,
                shouldLoad: function(query) {
                    return query.trim().length >= 1;
                },
                score: function() {
                    return function() {
                        return 1;
                    };
                },
                onType: function(query) {
                    console.log('Invoice typing:', query);
                    latestInvoiceSearch = query;
                    if (!query || query.trim().length === 0) {
                        this.clearOptions();
                        this.refreshOptions(false);
                        return;
                    }
                    this.clearOptions();
                    this.refreshOptions(false);
                },
                load: function(query, callback) {
                    const currentSearch = query;
                    latestInvoiceSearch = query;
                    console.log('Searching invoices:', query);

                    if (!query || query.trim().length === 0) {
                        callback([]);
                        return;
                    }

                    const url =
                        `{{ route('stock-adjustments.purchase-search') }}` +
                        `?q=${encodeURIComponent(query)}`;

                    fetch(url, {
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
                            console.log('Invoice search response:', data);

                            if (currentSearch !== latestInvoiceSearch) {
                                console.log('Ignoring old invoice response:', currentSearch);
                                callback([]);
                                return;
                            }

                            if (this.control_input.value !== currentSearch) {
                                console.log('Invoice search changed:', currentSearch);
                                callback([]);
                                return;
                            }

                            if (!Array.isArray(data) || data.length === 0) {
                                console.log('No invoices found for:', currentSearch);
                                callback([]);
                                return;
                            }

                            console.log('Invoice results:', data);
                            callback(data);
                        })
                        .catch(error => {
                            console.error('Invoice search error:', error);
                            callback([]);
                        });
                },
                render: {
                    option: function(item, escape) {
                        return `
                    <div class="py-2 px-2">
                        <div class="font-medium">
                            ${escape(item.invoice_no ?? '')}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            Date:
                            ${escape(item.purchase_date ?? '-')}
                            &nbsp; | &nbsp;
                            Vendor:
                            ${escape(item.vendor_name ?? '-')}
                            &nbsp; | &nbsp;
                            Ref:
                            ${escape(item.ref_no ?? '-')}
                        </div>
                    </div>
                `;
                    },
                    item: function(item, escape) {
                        return `
                    <div>
                        ${escape(item.invoice_no ?? '')}
                    </div>
                `;
                    }
                },
                onChange: function(purchaseId) {
                    console.log('Selected invoice:', purchaseId);

                    latestProductSearch = '';
                    latestProductPurchaseId = purchaseId || '';

                    productSelect.clear();
                    productSelect.clearOptions();
                    productSelect.clear();
                    productSelect.refreshOptions(false);

                    if (!purchaseId) {
                        productSelect.disable();
                        productSelect.settings.placeholder = 'Select invoice first...';
                        productSelect.control_input.placeholder = 'Select invoice first...';
                        return;
                    }

                    productSelect.enable();
                    productSelect.settings.placeholder = 'Search product from selected invoice...';
                    productSelect.control_input.placeholder = 'Search product...';
                    productSelect.focus();
                }
            });

            // =========================================================
            // PRODUCT TOM SELECT
            // =========================================================
            const productSelect = new TomSelect('#product_id', {
                valueField: 'id',
                labelField: 'book_name',
                searchField: [
                    'book_name',
                    'isbn',
                    'barcode_no'
                ],
                placeholder: 'Select invoice first...',
                preload: false,
                maxOptions: 20,
                cache: false,
                shouldLoad: function(query) {
                    return query.trim().length >= 1;
                },
                score: function() {
                    return function() {
                        return 1;
                    };
                },
                onType: function(query) {
                    console.log('Product typing:', query);
                    latestProductSearch = query;

                    if (!query || query.trim().length === 0) {
                        this.clearOptions();
                        this.refreshOptions(false);
                        return;
                    }

                    this.clearOptions();
                    this.refreshOptions(false);
                },
                load: function(query, callback) {
                    const purchaseId = purchaseSelect.getValue();
                    const currentSearch = query;
                    const currentPurchaseId = purchaseId;

                    latestProductSearch = query;
                    latestProductPurchaseId = purchaseId;

                    console.log(
                        'Product search:',
                        query,
                        'Invoice:',
                        purchaseId
                    );

                    if (!purchaseId) {
                        callback([]);
                        return;
                    }

                    if (!query || query.trim().length === 0) {
                        callback([]);
                        return;
                    }

                    const url =
                        `{{ route('stock-adjustments.product-search') }}` +
                        `?q=${encodeURIComponent(query)}` +
                        `&purchase_id=${encodeURIComponent(purchaseId)}`;

                    fetch(url, {
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
                            console.log('Product search response:', data);

                            if (
                                currentSearch !== latestProductSearch ||
                                currentPurchaseId !== latestProductPurchaseId
                            ) {
                                console.log(
                                    'Ignoring old product response:',
                                    currentSearch,
                                    currentPurchaseId
                                );
                                callback([]);
                                return;
                            }

                            if (this.control_input.value !== currentSearch) {
                                console.log(
                                    'Product search changed:',
                                    currentSearch
                                );
                                callback([]);
                                return;
                            }

                            if (!Array.isArray(data) || data.length === 0) {
                                console.log(
                                    'No products found for:',
                                    currentSearch
                                );
                                callback([]);
                                return;
                            }

                            console.log(
                                'Products found:',
                                data
                            );

                            callback(data);
                        })
                        .catch(error => {
                            console.error(
                                'Product search error:',
                                error
                            );
                            callback([]);
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
                            ISBN:
                            ${escape(item.isbn ?? '-')}
                            &nbsp; | &nbsp;
                            Barcode:
                            ${escape(item.barcode_no ?? '-')}
                            MRP:
                            ${escape(item.mrp ?? '-')}
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
                    console.log(
                        'Selected product:',
                        item
                    );
                }
            });

            // =========================================================
            // INITIAL STATE
            // =========================================================
            const initialPurchaseId = purchaseSelect.getValue();

            if (!initialPurchaseId) {
                productSelect.disable();
                productSelect.settings.placeholder = 'Select invoice first...';
                productSelect.control_input.placeholder = 'Select invoice first...';
            } else {
                productSelect.enable();
                productSelect.settings.placeholder = 'Search product from selected invoice...';
                productSelect.control_input.placeholder = 'Search product...';
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const adjustmentType = document.getElementById('adjustment_type');
            const quantityInput = document.getElementById('quantity');
            const unitCostInput = document.getElementById('unit_cost');
            function updateAdjustmentFields() {
                const type = adjustmentType.value;
                if (type === 'price_change') {

                    // Quantity is not applicable for price change
                    quantityInput.value = '';
                    quantityInput.disabled = true;
                    quantityInput.required = false;

                    // MRP is required and enabled
                    unitCostInput.disabled = false;
                    unitCostInput.required = true;

                } else if (type === 'add' || type === 'remove') {

                    // Quantity is enabled and required
                    quantityInput.disabled = false;
                    quantityInput.required = true;

                    // MRP is not applicable for Add / Remove
                    unitCostInput.value = '';
                    unitCostInput.disabled = true;
                    unitCostInput.required = false;

                } else {

                    // No adjustment type selected
                    quantityInput.disabled = false;
                    quantityInput.required = false;

                    unitCostInput.disabled = false;
                    unitCostInput.required = false;
                }
            }
            adjustmentType.addEventListener('change', updateAdjustmentFields);

            // Handle old value after validation error
            updateAdjustmentFields();
        });
    </script>
    
</x-app-layout>
