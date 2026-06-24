<x-app-layout>
    <div class="flex justify-center">

        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    Edit Bill
                </h3>
            </div>
            <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Invoice No --}}
                    <div>
                        <label class="label">Invoice No</label>

                        <input type="text" name="invoice_no" class="input input-bordered w-full"
                            value="{{ old('invoice_no', $sale->invoice_no) }}" required />

                        @error('invoice_no')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="label">Date</label>

                        <input type="date" name="sale_date" class="input input-bordered w-full"
                            value="{{ old('sale_date', \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d')) }}" />
                    </div>

                    {{-- Total --}}
                    <div>
                        <label class="label">Total Amt</label>

                        <input type="text" name="total_amount" class="input input-bordered w-full"
                            value="{{ old('total_amount', $sale->total_amount) }}" required />

                        @error('total_amount')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>


                <div class="space-y-3 pt-4">

                    <div class="flex items-center justify-between">

                        <h4 class="font-semibold text-gray-800">
                            Products and Quantities
                        </h4>

                        <button type="button" class="btn btn-sm btn-primary" onclick="addProductRow()">

                            <i class="fa-solid fa-plus"></i>
                        </button>

                    </div>

                    <div id="productRows" class="space-y-3">

                        @foreach ($sale->saleItems as $index => $item)
                            <div class="product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end"
                                data-product-id="{{ $item->product_id }}">

                                {{-- Product --}}
                                <div class="md:col-span-5">
                                    <label class="label">Product</label>

                                    <select name="products[{{ $index }}][product_id]"
                                        class="product-select w-full" required>

                                        <option value="{{ $item->product->id }}" selected>
                                            {{ $item->product->book_name }}
                                        </option>

                                    </select>
                                </div>


                                {{-- Qty --}}
                                <div class="md:col-span-2">
                                    <label class="label">Qty</label>

                                    <input type="number" name="products[{{ $index }}][quantity]"
                                        class="quantity-input input input-bordered w-full" min="1"
                                        value="{{ $item->quantity }}" required />
                                </div>


                                {{-- MRP --}}
                                <div class="md:col-span-3">
                                    <label class="label">MRP</label>

                                    <input type="number" step="0.01"
                                        name="products[{{ $index }}][purchase_price]"
                                        class="purchase-price input input-bordered w-full"
                                        value="{{ $item->selling_price }}" required />
                                </div>


                                {{-- Edit --}}
                                <div class="md:col-span-1">

                                    <button type="button" class="btn btn-warning w-full edit-product-btn">

                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                </div>


                                {{-- Delete --}}
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


                <div class="pt-4">

                    <button type="submit" class="btn btn-primary">

                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

    <script>
        let productIndex = {{ $sale->saleItems->count() }};

        function createTomSelect(selectElement) {

            const row = selectElement.closest('.product-row');

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
                    <div class="text-xs text-gray-500">
                        ₹ ${item.mrp ?? 0}
                    </div>
                </div>
            `;
                    }
                },

                onInitialize: function() {

                    const input = this.control_input;

                    input.addEventListener('keydown', (e) => {

                        if (e.key === 'Enter' || e.key === 'Tab') {
                            e.preventDefault();
                        }

                    });

                    // preload existing edit data WITHOUT triggering itemAdd
                    if (selectElement.options.length > 0) {

                        const selectedOption = selectElement.options[0];

                        this.addOption({
                            id: selectedOption.value,
                            book_name: selectedOption.text
                        });

                        this.setValue(selectedOption.value, true);
                    }

                },

                onItemAdd: function(value) {

                    const selected = this.options[value];

                    row.dataset.productId = value;

                    // prevent edit page overwrite
                    if (!selected.mrp) return;

                    const qtyInput =
                        row.querySelector('.quantity-input');

                    const priceInput =
                        row.querySelector('.purchase-price');

                    const qty =
                        parseFloat(qtyInput.value || 1);

                    const mrp =
                        parseFloat(selected.mrp || 0);

                    priceInput.value =
                        (qty * mrp).toFixed(2);

                    calculateTotal();

                    setTimeout(() => {

                        this.control_input.value = '';

                        this.focus();

                    }, 50);

                }

            });

            selectElement.tomselect = tom;

            const editBtn =
                row.querySelector('.edit-product-btn');

            editBtn.addEventListener('click', function() {

                const productId = row.dataset.productId;

                if (!productId) {
                    alert('Please select product first');
                    return;
                }

                window.open(
                    `/products/${productId}/edit?generate_barcode=1`,
                    '_blank'
                );
            });

            row.querySelector('.quantity-input')
                .addEventListener('input', function() {

                    const product =
                        tom.options[tom.getValue()];

                    if (!product || !product.mrp) return;

                    const qty =
                        parseFloat(this.value || 1);

                    const mrp =
                        parseFloat(product.mrp || 0);

                    row.querySelector('.purchase-price')
                        .value =
                        (qty * mrp).toFixed(2);

                    calculateTotal();
                });

            row.querySelector('.purchase-price')
                .addEventListener('input', function() {

                    calculateTotal();

                });
        }

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.product-select').forEach(select => {

                createTomSelect(select);

            });

            calculateTotal();
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

</x-app-layout>
