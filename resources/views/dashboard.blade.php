<x-app-layout>
    <div class="p-6">
        {{-- <h1 class="text-2xl font-bold mb-6">Dashboard</h1> --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

            {{-- Products --}}
            <a href="{{ route('products.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-box text-4xl text-primary"></i>
                    <h2 class="card-title mt-2">Products</h2>
                    <p class="text-3xl font-bold">{{ $productsCount }}</p>
                </div>
            </a>

            {{-- Authors --}}
            <a href="{{ route('authors.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-user-pen text-4xl text-secondary"></i>
                    <h2 class="card-title mt-2">Authors</h2>
                    <p class="text-3xl font-bold">{{ $authorsCount }}</p>
                </div>
            </a>

            {{-- Publications --}}
            <a href="{{ route('publications.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-book-open text-4xl text-accent"></i>
                    <h2 class="card-title mt-2">Publications</h2>
                    <p class="text-3xl font-bold">{{ $publicationsCount }}</p>
                </div>
            </a>

            {{-- Categories --}}
            <a href="{{ route('categories.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-tags text-4xl text-info"></i>
                    <h2 class="card-title mt-2">Categories</h2>
                    <p class="text-3xl font-bold">{{ $categoriesCount }}</p>
                </div>
            </a>

            {{-- Racks --}}
            <a href="{{ route('racks.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-warehouse text-4xl text-warning"></i>
                    <h2 class="card-title mt-2">Racks</h2>
                    <p class="text-3xl font-bold">{{ $racksCount }}</p>
                </div>
            </a>
        </div>
    </div>

    <section class="py-8">
        <div class="container mx-auto px-5">

            <div class="grid grid-cols-1 lg:grid-cols-[3fr_2fr] gap-6">

                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Generate Bill
                        </h3>
                    </div>
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Invoice No --}}
                            <div>
                                <label class="label">Invoice No *</label>

                                <input type="text" name="invoice_no" class="input input-bordered w-full"
                                    value="{{ old('invoice_no', $invoiceNo) }}" placeholder="Enter invoice number"
                                    required readonly />

                                @error('invoice_no')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sale Date --}}
                            <div>
                                <label class="label">Date *</label>

                                <input type="date" name="sale_date" class="input input-bordered w-full"
                                    value="{{ auth()->user()->email == 'admin@gmail.com' ? old('sale_date') : now()->toDateString() }}"
                                    {{ auth()->user()->email == 'admin@gmail.com' ? '' : 'readonly' }} required />
                            </div>

                            <div>
                                <label class="label">Customer Name</label>

                                <input type="text" name="name" class="input input-bordered w-full"
                                    value="{{ old('name') }}" placeholder="Enter customer name" />

                                @error('name')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="label">Contact No</label>

                                <input type="number" name="phone" class="input input-bordered w-full"
                                    value="{{ old('phone') }}" placeholder="Enter contact number" />

                                @error('phone')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="label">Payment mode *</label>

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
                                <label class="label">Total Amt *</label>

                                <input type="text" name="total_amount" class="input input-bordered w-full"
                                    value="{{ old('total_amount') }}" required />

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

                                {{-- DEFAULT ROW --}}
                                <div class="product-row grid grid-cols-1 md:grid-cols-13 gap-3 items-end">

                                    {{-- Product --}}
                                    <div class="md:col-span-5">
                                        <label class="label">Product</label>

                                        <select name="products[0][product_id]" class="product-select w-full" required>
                                        </select>
                                    </div>

                                    {{-- Quantity --}}
                                    <div class="md:col-span-1">
                                        <label class="label">Qty</label>

                                        <input type="number" name="products[0][quantity]"
                                            class="quantity-input input input-bordered w-full" min="1"
                                            value="1" required />
                                    </div>

                                    {{-- Discount --}}
                                    <div class="md:col-span-1">
                                        <label class="label">Disc %</label>

                                        <input type="number" name="products[0][discount]"
                                            class="discount-input input input-bordered w-full" />
                                    </div>

                                    {{-- Mrp --}}
                                    <div class="md:col-span-2">
                                        <label class="label">MRP</label>

                                        <input type="number" step="0.01" name="products[0][purchase_price]"
                                            class="purchase-price input input-bordered w-full" placeholder="Price"
                                            required />
                                    </div>

                                    {{-- amount --}}
                                    <div class="md:col-span-2">
                                        <label class="label">Amt</label>

                                        <input type="number" step="0.01" name="products[0][net_amount]"
                                            class="net_amount input input-bordered w-full" placeholder="Amt" readonly
                                            required />
                                    </div>

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
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Recent Transactions
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    {{-- sr.no. --}}
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        #</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Invoice No.</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Sale Date</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Total Amt</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $sale->invoice_no }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sale->total_amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

                onInitialize: function() {

                    const input = this.control_input;

                    input.addEventListener('keydown', (e) => {

                        if (e.key === 'Enter' || e.key === 'Tab') {
                            e.preventDefault();
                        }
                    });

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

                    calculateRowAmount(row);

                    setTimeout(() => {

                        this.control_input.value = '';

                        this.focus();

                    }, 50);
                }
            });

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

            row.querySelector('.quantity-input')
                .addEventListener('input', function() {

                    const product = tom.options[tom.getValue()];

                    if (!product) return;

                    const qty = parseFloat(this.value || 1);

                    const mrp = parseFloat(product.mrp || 0);

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

            const netAmountInput =
                row.querySelector('.net_amount');

            const discountAmount =
                (mrp * discount) / 100;

            const netAmount =
                mrp - discountAmount;

            netAmountInput.value =
                netAmount.toFixed(2);

            calculateTotal();
        }

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.product-select').forEach(select => {

                createTomSelect(select);

            });

            setTimeout(() => {

                const firstSelect =
                    document.querySelector('.product-select')?.tomselect;

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
                placeholder="Price"
                required />
        </div>

        <div class="md:col-span-2">
            <label class="label">Amt</label>

            <input type="number"
                step="0.01"
                name="products[${productIndex}][net_amount]"
                class="input input-bordered w-full net_amount"
                placeholder="Amt"
                readonly />
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

            calculateTotal();
        }

        function calculateTotal() {

            let total = 0;

            document.querySelectorAll('.net_amount').forEach(input => {

                total += parseFloat(input.value || 0);

            });

            document.querySelector('input[name="total_amount"]').value = Math.round(total).toFixed(2);
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

                    calculateRowAmount(row);

                } catch (e) {

                    console.error(e);

                }
            });
        });
    </script>
</x-app-layout>
