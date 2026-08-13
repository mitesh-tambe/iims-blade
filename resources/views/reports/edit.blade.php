<x-app-layout>
    <div class="flex justify-center">

        <form action="{{ route('invoices.update', $purchase->id) }}" method="POST" class="w-full max-w-5xl">

            @csrf
            @method('PUT')

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">

                <legend class="fieldset-legend text-lg font-semibold">
                    Edit Invoice
                </legend>

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Invoice No --}}
                    <div>
                        <label class="label">Invoice No</label>

                        <input type="text" name="invoice_no" class="input input-bordered w-full"
                            value="{{ old('invoice_no', $purchase->invoice_no) }}" placeholder="Enter invoice number"
                            required />

                        @error('invoice_no')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Purchase Date --}}
                    <div>
                        <label class="label">Purchase Date</label>

                        <input type="date" name="purchase_date" class="input input-bordered w-full"
                            value="{{ old('purchase_date', $purchase->purchase_date?->format('Y-m-d')) }}" />
                    </div>

                    {{-- Vendor --}}
                    <div>
                        <label class="label">Vendor</label>

                        <select name="vendor_id" class="select select-bordered w-full">

                            <option value="">
                                Select Vendor
                            </option>

                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                    {{ old('vendor_id', $purchase->vendor_id) == $vendor->id ? 'selected' : '' }}>

                                    {{ $vendor->name }}

                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- Total Amount --}}
                    <div>
                        <label class="label">Total Amount</label>

                        <input type="number" step="0.01" name="total_amount" class="input input-bordered w-full"
                            value="{{ old('total_amount', $purchase->total_amount) }}" placeholder="Total amount"
                            required />
                    </div>

                    {{-- Ref No --}}
                    <div>
                        <label class="label">Reference No</label>

                        <input type="text" name="ref_no" class="input input-bordered w-full"
                            value="{{ old('ref_no', $purchase->ref_no) }}" readonly />
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

                        @foreach ($purchase->items as $index => $item)
                            <div class="product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end"
                                data-product-id="{{ $item->product_id }}">

                                {{-- Product --}}
                                <div class="md:col-span-5">

                                    <label class="label">Product</label>

                                    <select name="products[{{ $index }}][product_id]"
                                        class="product-select w-full" required>

                                        <option value="{{ $item->product_id }}" selected>
                                            {{ $item->product?->book_name }}
                                        </option>

                                    </select>

                                </div>

                                {{-- Quantity --}}
                                <div class="md:col-span-2">

                                    <label class="label">Qty</label>

                                    <input type="number" name="products[{{ $index }}][quantity]"
                                        class="quantity-input input input-bordered w-full" min="1"
                                        value="{{ $item->quantity }}" required />

                                </div>

                                {{-- Purchase Price --}}
                                <div class="md:col-span-3">

                                    <label class="label">Purchase Price</label>

                                    <input type="number" step="0.01"
                                        name="products[{{ $index }}][purchase_price]"
                                        class="purchase-price input input-bordered w-full"
                                        value="{{ $item->cost_price }}" placeholder="Price" required />

                                </div>

                                {{-- Edit Product --}}
                                <div class="md:col-span-1">

                                    <button type="button" class="btn btn-warning w-full edit-product-btn">

                                        <i class="fa-solid fa-pen"></i>

                                    </button>

                                </div>

                                {{-- Remove --}}
                                <div class="md:col-span-1">

                                    <button type="button" class="btn btn-error w-full"
                                        onclick="removeProductRow(this)">

                                        <i class="fa-solid fa-trash"></i>

                                    </button>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>

                {{-- SUBMIT --}}
                <div class="pt-4 flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        Update Invoice
                    </button>

                    <a href="{{ route('invoices.index') }}" class="btn btn-neutral">

                        Cancel

                    </a>

                </div>

            </fieldset>

        </form>

    </div>

    <script>
        let productIndex = {{ $purchase->items->count() }};

        function createTomSelect(selectElement) {

            const tom = new TomSelect(selectElement, {

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

                    fetch(`/products/search?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(json => {

                            callback(json);

                            if (json.length === 1 && /^\d+$/.test(query)) {

                                this.addOption(json[0]);

                                this.setValue(json[0].id);

                            }

                        })
                        .catch(() => callback());

                },

                render: {
                    option: function(item, escape) {

                        return `
                    <div>
    <strong>${escape(item.book_name)}</strong>

    <div class="flex items-center gap-4 text-xs text-gray-500">
        <span>₹ ${item.mrp ?? 0}</span>

        <span>
            ${escape(item.author?.name ?? '-')}
        </span>

        <span>
            ${escape(item.publication?.name ?? '-')}
        </span>
    </div>
</div>
                `;
                    }
                },

                onItemAdd: function(value) {

                    const row = selectElement.closest('.product-row');

                    row.dataset.productId = value;

                    const selected = this.options[value];

                    const qtyInput =
                        row.querySelector('.quantity-input');

                    const priceInput =
                        row.querySelector('.purchase-price');

                    const qty =
                        parseFloat(qtyInput.value || 1);

                    const mrp =
                        parseFloat(selected.mrp || 0);

                    // row total price
                    priceInput.value =
                        (qty * mrp).toFixed(2);

                    qtyInput.dataset.lastQty = qty;

                    calculateTotal();

                }

            });

            const row = selectElement.closest('.product-row');

            const qtyInput =
                row.querySelector('.quantity-input');

            const priceInput =
                row.querySelector('.purchase-price');

            qtyInput.dataset.lastQty =
                qtyInput.value;

            const editBtn =
                row.querySelector('.edit-product-btn');

            editBtn.addEventListener('click', function() {

                const productId =
                    row.dataset.productId;

                if (!productId) {

                    alert('Please select product first');

                    return;
                }

                window.open(
                    `/products/${productId}/edit`,
                    '_blank'
                );

            });

            qtyInput.addEventListener('input', function() {

                const newQty =
                    parseFloat(this.value || 1);

                const oldQty =
                    parseFloat(this.dataset.lastQty || 1);

                const currentPrice =
                    parseFloat(priceInput.value || 0);

                const unitPrice =
                    currentPrice / oldQty;

                const newPrice =
                    unitPrice * newQty;

                priceInput.value =
                    newPrice.toFixed(2);

                this.dataset.lastQty =
                    newQty;

                calculateTotal();

            });

            priceInput.addEventListener('input', function() {

                calculateTotal();

            });

        }

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.product-select')
                .forEach(select => {

                    createTomSelect(select);

                });

            calculateTotal();

        });

        function addProductRow() {

            const container =
                document.getElementById('productRows');

            const row =
                document.createElement('div');

            row.className =
                'product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end';

            row.innerHTML = `

        <div class="md:col-span-5">

            <label class="label">Product</label>

            <select
                name="products[${productIndex}][product_id]"
                class="product-select w-full"
                required>
            </select>

        </div>

        <div class="md:col-span-2">

            <label class="label">Qty</label>

            <input
                type="number"
                name="products[${productIndex}][quantity]"
                class="input input-bordered w-full quantity-input"
                min="1"
                value="1"
                required />

        </div>

        <div class="md:col-span-3">

            <label class="label">Purchase Price</label>

            <input
                type="number"
                step="0.01"
                name="products[${productIndex}][purchase_price]"
                class="input input-bordered w-full purchase-price"
                placeholder="Price"
                required />

        </div>

        <div class="md:col-span-1">

            <button
                type="button"
                class="btn btn-warning w-full edit-product-btn">

                <i class="fa-solid fa-pen"></i>

            </button>

        </div>

        <div class="md:col-span-1">

            <button
                type="button"
                class="btn btn-error w-full"
                onclick="removeProductRow(this)">

                <i class="fa-solid fa-trash"></i>

            </button>

        </div>
        `;

            container.appendChild(row);

            createTomSelect(
                row.querySelector('.product-select')
            );

            productIndex++;

        }

        function removeProductRow(button) {

            const rows =
                document.querySelectorAll('.product-row');

            if (rows.length === 1) return;

            button.closest('.product-row').remove();

            calculateTotal();

        }

        function calculateTotal() {

            let total = 0;

            document.querySelectorAll(
                '.purchase-price'
            ).forEach(input => {

                total +=
                    parseFloat(input.value || 0);

            });

            document.querySelector(
                'input[name="total_amount"]'
            ).value = total.toFixed(2);

        }
    </script>

</x-app-layout>
