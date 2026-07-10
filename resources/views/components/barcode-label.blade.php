@props(['product'])

<div class="label-item">

    {{-- shop name --}}
    <div class="shop-name">
        IDEAL BOOK CO.
    </div>

    {{-- Book Name --}}
    <div class="book-name">
        {{ $product->book_name }}
    </div>

    {{-- Barcode --}}
    <div class="barcode">

        @if (!empty($product->barcode_no))
            {!! DNS1D::getBarcodeSVG($product->barcode_no, 'C128', 1.2, 28, 'black', false) !!}
        @else
            <div style="font-size:10px; text-align:center;">
                Barcode Not Available
            </div>
        @endif
    </div>

    {{-- Barcode Number --}}
    <div class="barcode-number">
        {{ $product->barcode_no ?? 'N/A' }}
    </div>

    {{-- Bottom --}}
    <div class="bottom-row">

        <span>
            Rack: {{ $product->rack->name ?? 'N/A' }}
        </span>

        <span>
            ₹{{ number_format($product->mrp, 0) }}
        </span>

    </div>

</div>
