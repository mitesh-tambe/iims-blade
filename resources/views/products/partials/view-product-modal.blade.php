{{-- View Product Modal --}}
<dialog id="view_product" class="modal">
    <div class="modal-box w-11/12 max-w-5xl">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Product Details</h3>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">✕</button>
            </form>
        </div>

        {{-- BASIC DETAILS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="label font-semibold">Book Name</label>
                <p id="view_book_name" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">ISBN</label>
                <p id="view_isbn" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Edition</label>
                <p id="view_edition" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Book Pages</label>
                <p id="view_book_pages" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Barcode</label>
                <p id="view_barcode_no" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">MRP</label>
                <p id="view_mrp" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

        </div>

        {{-- DISCOUNTS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">

            <div>
                <label class="label font-semibold">Disc % (Company)</label>
                <p id="view_disc_company" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Amt (Company)</label>
                <p id="view_amt_company" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Disc % (Customer)</label>
                <p id="view_disc_customer" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Amt (Customer)</label>
                <p id="view_amt_customer" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

        </div>

        {{-- RELATIONS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

            <div>
                <label class="label font-semibold">Author</label>
                <p id="view_author" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Publication</label>
                <p id="view_publication" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Language</label>
                <p id="view_language" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Category</label>
                <p id="view_category" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

            <div>
                <label class="label font-semibold">Rack No.</label>
                <p id="view_rack_no" class="bg-base-200 p-2 rounded text-sm"></p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="modal-action flex flex-col sm:flex-row sm:items-center gap-3">
            {{-- Label Quantity --}}
            <div class="flex items-center gap-2">

                <label class="font-semibold text-sm whitespace-nowrap">
                    Labels Qty
                </label>

                <input type="number" id="barcode_qty" class="input input-bordered input-sm w-24" min="1"
                    placeholder="Qty">

            </div>

            {{-- Generate Barcode --}}
            <button type="button" class="btn btn-secondary" id="generate_barcode_btn" disabled>
                Generate Barcode
            </button>
            <form method="dialog">
                <button class="btn btn-primary">Close</button>
            </form>
        </div>

    </div>
</dialog>

<script>
    let currentProductId = null;

    const barcodeQtyInput =
        document.getElementById('barcode_qty');

    const generateBarcodeBtn =
        document.getElementById('generate_barcode_btn');

    // Enable / Disable button
    barcodeQtyInput.addEventListener('input', function() {

        const qty = parseInt(this.value);

        if (qty > 0) {

            generateBarcodeBtn.disabled = false;

        } else {

            generateBarcodeBtn.disabled = true;
        }
    });

    // Generate Barcode Print Page
    generateBarcodeBtn.addEventListener('click', function() {

        const qty = barcodeQtyInput.value;

        if (!qty || qty <= 0) {
            return;
        }

        window.open(
            `/products/${currentProductId}/barcode-print?qty=${qty}`,
            '_blank'
        );
    });
</script>
