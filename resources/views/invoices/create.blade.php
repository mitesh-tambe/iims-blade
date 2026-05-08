<x-app-layout>
    <div class="flex justify-center">
        <form action="{{ route('invoices.store') }}" method="POST" class="w-full max-w-5xl">
            @csrf

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">
                <legend class="fieldset-legend text-lg font-semibold">
                    Invoice Details
                </legend>

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Invoice No --}}
                    <div>
                        <label class="label">Invoice No</label>

                        <input type="text" name="invoice_no" class="input input-bordered w-full"
                            value="{{ old('invoice_no') }}" placeholder="Enter invoice number" required />

                        @error('invoice_no')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Purchase Date --}}
                    <div>
                        <label class="label">Purchase Date</label>

                        <input type="date" name="purchase_date" class="input input-bordered w-full"
                            value="{{ old('purchase_date') }}" />
                    </div>

                    {{-- Vendor --}}
                    <div>
                        <label class="label">Vendor</label>

                        <select name="vendor_id" class="select select-bordered w-full">
                            <option value="">Select Vendor</option>

                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                    {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Total Amount --}}
                    <div>
                        <label class="label">Total Amount</label>

                        <input type="number" name="total_amount" class="input input-bordered w-full"
                            value="{{ old('total_amount') }}" placeholder="Total amount" required />
                    </div>

                </div>

                {{-- PRODUCTS --}}
                <div class="space-y-3 pt-4">

                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-lg">
                            Products and Quantities
                        </h3>

                        <button type="button" class="btn btn-sm btn-primary" onclick="addProductRow()">

                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>

                    <div id="productRows" class="space-y-3">

                        {{-- DEFAULT ROW --}}
                        <div class="product-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

                            {{-- Product --}}
                            <div class="md:col-span-5">
                                <label class="label">Product</label>

                                <select name="products[0][product_id]" class="product-select w-full" required>
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div class="md:col-span-2">
                                <label class="label">Qty</label>

                                <input type="number" name="products[0][quantity]" class="input input-bordered w-full"
                                    min="1" value="1" required />
                            </div>

                            {{-- Purchase Price --}}
                            <div class="md:col-span-3">
                                <label class="label">Purchase Price</label>

                                <input type="number" step="0.01" name="products[0][purchase_price]"
                                    class="input input-bordered w-full" placeholder="Price" required />
                            </div>

                            {{-- Remove --}}
                            <div class="md:col-span-2">
                                <button type="button" class="btn btn-error w-full" onclick="removeProductRow(this)">

                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary">
                        Save Invoice
                    </button>
                </div>

            </fieldset>
        </form>
    </div>

    <script>
        let productIndex = 1;

        function createTomSelect(selectElement) {

            new TomSelect(selectElement, {

                valueField: 'id',

                labelField: 'book_name',

                searchField: [
                    'book_name',
                    'isbn',
                    'barcode_no'
                ],

                create: false,

                preload: false,

                maxOptions: 20,

                placeholder: 'Search Product / ISBN / Barcode...',

                loadThrottle: 100,

                load: function(query, callback) {

                    if (!query.length) {
                        return callback();
                    }

                    // Barcode fast match
                    if (/^\d+$/.test(query)) {

                        fetch(`/products/search?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(json => {

                                callback(json);

                                // Auto select if exact single match
                                if (json.length === 1) {

                                    this.addOption(json[0]);

                                    this.setValue(json[0].id);
                                }

                            })
                            .catch(() => {
                                callback();
                            });

                        return;
                    }

                    // Normal search
                    fetch(`/products/search?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(json => {

                            callback(json);

                        })
                        .catch(() => {

                            callback();

                        });
                }

            });
        }

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.product-select').forEach(select => {

                createTomSelect(select);

            });

        });

        function addProductRow() {

            const container = document.getElementById('productRows');

            const row = document.createElement('div');

            row.className =
                'product-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end';

            row.innerHTML = `

            <div class="md:col-span-5">
                <label class="label">Product</label>

                <select name="products[${productIndex}][product_id]"
                    class="product-select w-full"
                    required>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="label">Qty</label>

                <input type="number"
                    name="products[${productIndex}][quantity]"
                    class="input input-bordered w-full"
                    min="1"
                    value="1"
                    required />
            </div>

            <div class="md:col-span-3">
                <label class="label">Purchase Price</label>

                <input type="number"
                    step="0.01"
                    name="products[${productIndex}][purchase_price]"
                    class="input input-bordered w-full"
                    placeholder="Price"
                    required />
            </div>

            <div class="md:col-span-2">
                <button type="button"
                    class="btn btn-error w-full"
                    onclick="removeProductRow(this)">

                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;

            container.appendChild(row);

            const newSelect = row.querySelector('.product-select');

            createTomSelect(newSelect);

            productIndex++;
        }

        function removeProductRow(button) {

            const rows = document.querySelectorAll('.product-row');

            if (rows.length === 1) {
                return;
            }

            button.closest('.product-row').remove();
        }
    </script>
</x-app-layout>
