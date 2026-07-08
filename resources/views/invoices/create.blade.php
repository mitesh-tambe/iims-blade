<x-app-layout>
    <div class="flex justify-center">
        <form action="{{ route('invoices.store') }}" method="POST" class="w-full max-w-5xl">
            @csrf

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <legend class="fieldset-legend text-lg font-semibold">
                        Invoice Details
                    </legend>

                    <a href="{{ route('products.create') }}" class="btn btn-primary" target="_blank"
                        rel="noopener noreferrer">
                        Create New Product
                    </a>
                </div>

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
                            value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : '') }}">
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

                    {{-- ref no --}}
                    <div>
                        <label class="label">Reference No</label>

                        <input type="text" name="ref_no" class="input input-bordered w-full"
                            value="{{ $ref_no }}" placeholder="Enter reference number" required readonly />
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
                        <div class="product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end">

                            {{-- Product --}}
                            <div class="md:col-span-5">
                                <label class="label">Product</label>

                                <select name="products[0][product_id]" class="product-select w-full" required>
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div class="md:col-span-2">
                                <label class="label">Qty</label>

                                {{-- <input type="number" name="products[0][quantity]" class="input input-bordered w-full"
                                    min="1" value="1" required /> --}}

                                <input type="number" name="products[0][quantity]"
                                    class="quantity-input input input-bordered w-full" min="1" value="1"
                                    required />
                            </div>

                            {{-- Purchase Price --}}
                            <div class="md:col-span-3">
                                <label class="label">Purchase Price</label>

                                {{-- <input type="number" step="0.01" name="products[0][purchase_price]"
                                    class="input input-bordered w-full" placeholder="Price" required /> --}}

                                <input type="number" step="0.01" name="products[0][purchase_price]"
                                    class="purchase-price input input-bordered w-full" placeholder="Price" required />
                            </div>

                            {{-- genrate barcode button --}}
                            <div class="md:col-span-1">
                                <button type="button" class="btn btn-info w-full generate-barcode-btn">
                                    <i class="fa-solid fa-barcode"></i>
                                </button>
                            </div>

                            <div class="md:col-span-1">
                                <button type="button" class="btn btn-warning w-full edit-product-btn">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>

                            {{-- Remove --}}
                            <div class="md:col-span-1">
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

    {{-- <script>
        let productIndex = 1;

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
                        .catch(() => {
                            callback();
                        });
                },

                render: {
                    option: function(item, escape) {
                        return `
                    <div>
                        <strong>${escape(item.book_name)}</strong>
                        <div class="text-xs text-gray-500">
                            ₹ ${item.mrp ?? 0}
                        </div>
                    </div>
                `;
                    }
                },

                onItemAdd: function(value) {

                    const row = selectElement.closest('.product-row');

                    row.dataset.productId = value;

                    const selected = this.options[value];

                    const qtyInput = row.querySelector('.quantity-input');

                    const priceInput = row.querySelector('.purchase-price');

                    const qty = parseFloat(qtyInput.value || 1);

                    const mrp = parseFloat(selected.mrp || 0);

                    priceInput.value = (qty * mrp).toFixed(2);

                    calculateTotal();
                }
            });

            const row = selectElement.closest('.product-row');

            const editBtn = row.querySelector('.edit-product-btn');

            editBtn.addEventListener('click', function() {

                const productId = row.dataset.productId;

                if (!productId) {
                    alert('Please select product first');
                    return;
                }

                window.open(`/products/${productId}/edit?generate_barcode=1`, '_blank');
            });

            row.querySelector('.quantity-input').addEventListener('input', function() {

                const product = tom.options[tom.getValue()];

                if (!product) return;

                const qty = parseFloat(this.value || 1);

                const mrp = parseFloat(product.mrp || 0);

                row.querySelector('.purchase-price').value =
                    (qty * mrp).toFixed(2);

                calculateTotal();
            });

            row.querySelector('.purchase-price').addEventListener('input', function() {

                calculateTotal();

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
                'product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end';

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
                    class="input input-bordered w-full quantity-input"
                    min="1"
                    value="1"
                    required />
            </div>

            <div class="md:col-span-3">
                <label class="label">Purchase Price</label>

                <input type="number"
                    step="0.01"
                    name="products[${productIndex}][purchase_price]"
                    class="input input-bordered w-full purchase-price"
                    placeholder="Price"
                    required />
            </div>

            <div class="md:col-span-1">
            <button type="button" class="btn btn-info w-full generate-barcode-btn">
                <i class="fa-solid fa-barcode"></i>
            </button>
            </div>

            <div class="md:col-span-1">
                <button type="button" class="btn btn-warning w-full edit-product-btn">
                <i class="fa-solid fa-pen"></i>
            </button>
            </div>

            <div class="md:col-span-1">
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

        function calculateTotal() {

            let total = 0;

            document.querySelectorAll('.purchase-price').forEach(input => {

                total += parseFloat(input.value || 0);

            });

            document.querySelector('input[name="total_amount"]').value =
                total.toFixed(2);
        }


        window.addEventListener('storage', async function(event) {

            if (event.key !== 'product_updated') {
                return;
            }

            const data = JSON.parse(event.newValue);

            const productId = data.id;

            document.querySelectorAll('.product-row').forEach(async row => {

                if (row.dataset.productId != productId) {
                    return;
                }

                try {

                    const response = await fetch(`/products/${productId}/json`);

                    const product = await response.json();

                    const tomSelect =
                        row.querySelector('.product-select').tomselect;

                    tomSelect.clearOptions();

                    tomSelect.addOption(product);

                    tomSelect.refreshOptions(false);

                    tomSelect.setValue(product.id, true);

                    const qty =
                        parseFloat(row.querySelector('.quantity-input').value || 1);

                    row.querySelector('.purchase-price').value =
                        (qty * parseFloat(product.mrp)).toFixed(2);

                    calculateTotal();

                    console.log('storage fired', event);

                } catch (e) {

                    console.error(e);

                }
            });
        });
    </script> --}}

    <script>
        let productIndex = 1;

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

                            // auto select barcode result
                            if (json.length === 1 && /^\d+$/.test(query)) {

                                this.addOption(json[0]);

                                this.setValue(json[0].id);
                            }

                        })
                        .catch(() => {
                            callback();
                        });
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

                // FIX FOR BARCODE SCANNER
                onInitialize: function() {

                    const input = this.control_input;

                    input.addEventListener('keydown', (e) => {

                        // scanner sends Enter/Tab after barcode
                        if (e.key === 'Enter' || e.key === 'Tab') {
                            e.preventDefault();
                        }
                    });

                    // focus first product field
                    setTimeout(() => {
                        this.focus();
                    }, 100);
                },

                onItemAdd: function(value) {

                    const row = selectElement.closest('.product-row');

                    row.dataset.productId = value;

                    const selected = this.options[value];

                    const qtyInput = row.querySelector('.quantity-input');

                    const priceInput = row.querySelector('.purchase-price');

                    const qty = parseFloat(qtyInput.value || 1);

                    const mrp = parseFloat(selected.mrp || 0);

                    priceInput.value = (qty * mrp).toFixed(2);

                    calculateTotal();

                    // keep cursor ready for next scan
                    setTimeout(() => {

                        this.control_input.value = '';

                        this.focus();

                    }, 50);
                }
            });

            // store instance
            selectElement.tomselect = tom;

            const row = selectElement.closest('.product-row');

            const editBtn = row.querySelector('.edit-product-btn');

            editBtn.addEventListener('click', function() {

                const productId = row.dataset.productId;

                if (!productId) {
                    alert('Please select product first');
                    return;
                }

                window.open(`/products/${productId}/edit?generate_barcode=1`, '_blank');
            });

            row.querySelector('.quantity-input').addEventListener('input', function() {

                const product = tom.options[tom.getValue()];

                if (!product) return;

                const qty = parseFloat(this.value || 1);

                const mrp = parseFloat(product.mrp || 0);

                row.querySelector('.purchase-price').value =
                    (qty * mrp).toFixed(2);

                calculateTotal();
            });

            row.querySelector('.purchase-price').addEventListener('input', function() {

                calculateTotal();

            });
        }

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.product-select').forEach(select => {

                createTomSelect(select);

            });

            // focus first product field initially
            setTimeout(() => {

                const firstSelect =
                    document.querySelector('.product-select').tomselect;

                if (firstSelect) {
                    firstSelect.focus();
                }

            }, 200);

        });

        function addProductRow() {

            const container = document.getElementById('productRows');

            const row = document.createElement('div');

            row.className =
                'product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end';

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
                class="input input-bordered w-full quantity-input"
                min="1"
                value="1"
                required />
        </div>

        <div class="md:col-span-3">
            <label class="label">Purchase Price</label>

            <input type="number"
                step="0.01"
                name="products[${productIndex}][purchase_price]"
                class="input input-bordered w-full purchase-price"
                placeholder="Price"
                required />
        </div>

        <div class="md:col-span-1">
        <button type="button" class="btn btn-info w-full generate-barcode-btn">
            <i class="fa-solid fa-barcode"></i>
        </button>
        </div>

        <div class="md:col-span-1">
            <button type="button" class="btn btn-warning w-full edit-product-btn">
            <i class="fa-solid fa-pen"></i>
        </button>
        </div>

        <div class="md:col-span-1">
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

            setTimeout(() => {
                newSelect.tomselect.focus();
            }, 100);

            productIndex++;
        }

        function removeProductRow(button) {

            const rows = document.querySelectorAll('.product-row');

            if (rows.length === 1) {
                return;
            }

            button.closest('.product-row').remove();
        }

        function calculateTotal() {

            let total = 0;

            document.querySelectorAll('.purchase-price').forEach(input => {

                total += parseFloat(input.value || 0);

            });

            document.querySelector('input[name="total_amount"]').value =
                total.toFixed(2);
        }


        window.addEventListener('storage', async function(event) {

            if (event.key !== 'product_updated') {
                return;
            }

            const data = JSON.parse(event.newValue);

            const productId = data.id;

            document.querySelectorAll('.product-row').forEach(async row => {

                if (row.dataset.productId != productId) {
                    return;
                }

                try {

                    const response = await fetch(`/products/${productId}/json`);

                    const product = await response.json();

                    const tomSelect =
                        row.querySelector('.product-select').tomselect;

                    tomSelect.clearOptions();

                    tomSelect.addOption(product);

                    tomSelect.refreshOptions(false);

                    tomSelect.setValue(product.id, true);

                    const qty =
                        parseFloat(row.querySelector('.quantity-input').value || 1);

                    row.querySelector('.purchase-price').value =
                        (qty * parseFloat(product.mrp)).toFixed(2);

                    calculateTotal();

                    console.log('storage fired', event);

                } catch (e) {

                    console.error(e);

                }
            });
        });
    </script>

    <script>
        // Barcode Generate Button Click
        document.addEventListener('click', function(e) {

            const button = e.target.closest('.generate-barcode-btn');

            if (!button) return;

            // Current Row
            const row = button.closest('.product-row');

            if (!row) return;

            // Product Select
            const productSelect =
                row.querySelector('.product-select');

            // Quantity Input
            const quantityInput =
                row.querySelector('.quantity-input');

            // Values
            const productId = productSelect.value;

            const qty = parseInt(quantityInput.value);

            // Validation
            if (!productId) {

                alert('Please select product');

                return;
            }

            if (!qty || qty <= 0) {

                alert('Please enter valid quantity');

                quantityInput.focus();

                return;
            }

            // Open Barcode Print Page
            window.open(
                `/products/${productId}/barcode-print?qty=${qty}`,
                '_blank'
            );

        });
    </script>
</x-app-layout>
