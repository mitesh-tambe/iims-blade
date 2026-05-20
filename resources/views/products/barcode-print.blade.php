<!DOCTYPE html>
<html>

<head>

    <title>Barcode Labels</title>

    <style>
        /* Thermal Sticker Size */
        @page {
            size: 50mm 25mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Single Label */
        .label-item {

            width: 50mm;
            height: 25mm;

            box-sizing: border-box;

            padding: 1.5mm 2mm;

            overflow: hidden;

            display: flex;
            flex-direction: column;

            page-break-after: always;
        }

        /* Shop Name */
        .shop-name, .barcode-number {

            text-align: center;

            font-size: 7px;

            line-height: 1;

            margin-bottom: 1mm;
        }

        /* Book Name */
        .book-name {

            font-size: 7px;

            font-weight: bold;

            line-height: 1.1;

            min-height: 7mm;

            overflow: hidden;

            word-break: break-word;

            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;

            margin-bottom: 0.3mm;
        }

        /* Barcode Section */
        .barcode {

            text-align: center;

            /* margin-top: 0.3mm; */
        }

        /* Barcode SVG */
        .barcode svg {

            width: 100%;

            height: 7mm;
        }

        /* Bottom Row */
        .bottom-row {

            display: flex;

            justify-content: space-between;

            align-items: center;

            font-size: 7px;

            margin-top: auto;
        }

        @media print {

            html,
            body {

                width: 50mm;
                height: 25mm;

                margin: 0;
                padding: 0;
            }

            .label-item {
                border: none;
            }
        }
    </style>

</head>

<body onload="window.print()">

    @for ($i = 0; $i < $qty; $i++)
        <x-barcode-label :product="$product" />
    @endfor

</body>

</html>
