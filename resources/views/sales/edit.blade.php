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
                            value="{{ old('invoice_no', $sale->invoice_no) }}" placeholder="Enter invoice number"
                            required readonly />

                        @error('invoice_no')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sale Date --}}
                    <div>
                        <label class="label">Date</label>

                        <input type="date" name="sale_date" class="input input-bordered w-full"
                            value="{{ old('sale_date', optional($sale->sale_date)->format('Y-m-d')) }}">
                    </div>

                    <div>
                        <label class="label">Customer Name</label>

                        <input type="text" name="name" class="input input-bordered w-full"
                            value="{{ old('name', $sale->customer?->name) }}" placeholder="Enter customer name"
                            required />

                        @error('name')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label">Contact No</label>

                        <input type="number" name="phone" class="input input-bordered w-full"
                            value="{{ old('phone', $sale->customer?->phone) }}" placeholder="Enter contact no"
                            required />

                        @error('phone')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label">Payment mode</label>

                        <select name="payment_mode" class="select select-bordered w-full" required>

                            <option value="">Select</option>

                            <option value="Cash"
                                {{ old('payment_mode', $sale->payment_mode ?? '') == 'Cash' ? 'selected' : '' }}>
                                Cash
                            </option>

                            <option value="UPI"
                                {{ old('payment_mode', $sale->payment_mode ?? '') == 'UPI' ? 'selected' : '' }}>
                                UPI
                            </option>

                            <option value="Cheque"
                                {{ old('payment_mode', $sale->payment_mode ?? '') == 'Cheque' ? 'selected' : '' }}>
                                Cheque
                            </option>

                            <option value="NEFT"
                                {{ old('payment_mode', $sale->payment_mode ?? '') == 'NEFT' ? 'selected' : '' }}>
                                NEFT
                            </option>

                        </select>

                        @error('payment_mode')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label">Total Amt</label>

                        <input type="text" name="total_amount" class="input input-bordered w-full"
                            value="{{ old('total_amount', $sale->total_amount) }}" required>

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
                                <div class="md:col-span-1">
                                    <label class="label">Qty</label>

                                    <input type="number" name="products[{{ $index }}][quantity]"
                                        class="quantity-input input input-bordered w-full"
                                        value="{{ old("products.$index.quantity", $item->quantity) }}" min="1"
                                        required>
                                </div>

                                {{-- Discount --}}
                                <div class="md:col-span-1">
                                    <label class="label">Disc %</label>

                                    <input type="number" name="products[{{ $index }}][discount]"
                                        class="discount-input input input-bordered w-full"
                                        value="{{ old("products.$index.discount", $item->discount) }}">
                                </div>

                                {{-- MRP --}}
                                <div class="md:col-span-2">
                                    <label class="label">MRP</label>

                                    <input type="number" step="0.01"
                                        name="products[{{ $index }}][purchase_price]"
                                        class="purchase-price input input-bordered w-full"
                                        value="{{ old("products.$index.purchase_price", $item->selling_price) }}"
                                        required>
                                </div>

                                {{-- Net Amount --}}
                                <div class="md:col-span-2">
                                    <label class="label">Amt</label>

                                    <input type="number" step="0.01"
                                        name="products[{{ $index }}][net_amount]"
                                        class="net_amount input input-bordered w-full"
                                        value="{{ old("products.$index.net_amount", $item->net_amount) }}" readonly>
                                </div>

                                {{-- Edit button --}}
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

                            // barcode auto select
                            if (
                                json.length === 1 &&
                                /^\d+$/.test(query)
                            ) {
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

                        if (
                            e.key === 'Enter' ||
                            e.key === 'Tab'
                        ) {
                            e.preventDefault();
                        }

                    });

                },

                onItemAdd: function(value) {

                    // skip first render of existing edit rows
                    if (row.dataset.skipInitial === '1') {
                        row.dataset.skipInitial = '0';
                        return;
                    }

                    row.dataset.productId = value;

                    const selected = this.options[value];

                    if (!selected) return;

                    const qty = parseFloat(
                        row.querySelector('.quantity-input').value || 1
                    );

                    const mrp =
                        parseFloat(selected.mrp || 0);

                    row.querySelector('.purchase-price').value =
                        (qty * mrp).toFixed(2);

                    calculateRowAmount(row);

                    setTimeout(() => {

                        this.control_input.value = '';
                        this.focus();

                    }, 50);

                }

            });

            selectElement.tomselect = tom;


            // EDIT PAGE EXISTING PRODUCTS
            if (selectElement.options.length > 0) {

                const option =
                    selectElement.options[0];

                tom.addOption({

                    id: option.value,
                    book_name: option.text,
                    mrp: parseFloat(
                        row.querySelector('.purchase-price').value || 0
                    )

                });

                row.dataset.skipInitial = '1';

                tom.setValue(option.value, true);

                row.dataset.productId = option.value;
            }


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
                    `/products/${productId}/edit?generate_barcode=1`,
                    '_blank'
                );

            });


            row.querySelector('.quantity-input')
                .addEventListener('input', function() {

                    const product =
                        tom.options[tom.getValue()];

                    if (!product) return;

                    const qty =
                        parseFloat(this.value || 1);

                    const mrp =
                        parseFloat(product.mrp || 0);

                    row.querySelector('.purchase-price').value =
                        (qty * mrp).toFixed(2);

                    calculateRowAmount(row);

                });


            row.querySelector('.discount-input')
                ?.addEventListener('input', function() {

                    calculateRowAmount(row);

                });


            row.querySelector('.purchase-price')
                .addEventListener('input', function() {

                    calculateRowAmount(row);

                });


            calculateRowAmount(row);

        }


        function calculateRowAmount(row) {

            const mrp =
                parseFloat(
                    row.querySelector('.purchase-price')?.value || 0
                );

            const discount =
                parseFloat(
                    row.querySelector('.discount-input')?.value || 0
                );

            const discountAmount =
                (mrp * discount) / 100;

            const net =
                mrp - discountAmount;

            row.querySelector('.net_amount').value =
                net.toFixed(2);

            calculateTotal();

        }


        function calculateTotal() {

            let total = 0;

            document
                .querySelectorAll('.net_amount')
                .forEach(input => {

                    total += parseFloat(
                        input.value || 0
                    );

                });

            document.querySelector(
                'input[name="total_amount"]'
            ).value = total.toFixed(2);

        }


        document.addEventListener(
            'DOMContentLoaded',
            function() {

                document
                    .querySelectorAll('.product-select')
                    .forEach(select => {

                        createTomSelect(select);

                    });

                calculateTotal();

            }
        );


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

            <select name="products[${productIndex}][product_id]"
                class="product-select w-full"
                required>
            </select>
        </div>

        <div class="md:col-span-1">
            <label class="label">Qty</label>

            <input type="number"
                name="products[${productIndex}][quantity]"
                class="input input-bordered w-full quantity-input"
                min="1"
                value="1"
                required />
        </div>

        <div class="md:col-span-1">
            <label class="label">Disc %</label>

            <input type="number"
                name="products[${productIndex}][discount]"
                class="input input-bordered w-full discount-input"
                value="0"/>
        </div>

        <div class="md:col-span-2">
            <label class="label">MRP</label>

            <input type="number"
                step="0.01"
                name="products[${productIndex}][purchase_price]"
                class="input input-bordered w-full purchase-price"
                required />
        </div>

        <div class="md:col-span-2">
            <label class="label">Amt</label>

            <input type="number"
                name="products[${productIndex}][net_amount]"
                class="input input-bordered w-full net_amount"
                readonly />
        </div>

        <div class="md:col-span-1">
            <button type="button"
                class="btn btn-warning w-full edit-product-btn">
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

            createTomSelect(
                row.querySelector('.product-select')
            );

            productIndex++;

        }


        function removeProductRow(button) {

            if (
                document.querySelectorAll('.product-row').length === 1
            ) return;

            button.closest('.product-row').remove();

            calculateTotal();

        }
    </script>

</x-app-layout>
