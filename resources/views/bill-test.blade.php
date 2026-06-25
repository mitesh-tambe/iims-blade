<!DOCTYPE html>
<html>

<head>

    <title>Thermal Bill Test</title>

    <style>
        html,
        body {

            width: 80mm;

            margin: 0 auto;
            padding: 0;

            background: #fff;

            font-family: monospace;

            overflow-x: hidden;
        }

        body {
            display: block;
        }

        /* Main Receipt */
        .receipt {

            width: 72mm;

            padding: 3mm;

            box-sizing: border-box;
        }

        /* Center */
        .text-center {
            text-align: center;
        }

        /* Shop Name */
        .shop-name {

            font-size: 20px;

            font-weight: bold;

            margin-bottom: 2px;

            line-height: 1.2;
        }

        /* Small Text */
        .small-text {

            font-size: 11px;

            line-height: 1.4;
        }

        /* Divider */
        .divider {

            border-top: 1px dashed #000;

            margin: 6px 0;
        }

        /* Bill Information */
        .bill-info {

            font-size: 12px;

            line-height: 1.8;
        }

        /* Table */
        table {

            width: 100%;

            border-collapse: collapse;

            font-size: 11px;

            table-layout: fixed;
        }

        /* Header */
        th {

            text-align: left;

            border-bottom: 1px dashed #000;

            padding-bottom: 3px;

            font-size: 11px;
        }

        /* Cell */
        td {

            padding: 3px 0;

            vertical-align: top;

            word-wrap: break-word;
        }

        /* Book Name Column */
        th:first-child,
        td:first-child {

            width: 55%;

            padding-right: 4px;
        }

        /* Discount */
        .discount {

            width: 10%;

            text-align: center;

            white-space: nowrap;
        }

        /* Qty */
        .qty {

            width: 10%;

            text-align: center;

            white-space: nowrap;
        }

        /* Price */
        .price {

            width: 12%;

            text-align: right;

            white-space: nowrap;
        }

        /* Amount */
        .amount {

            width: 13%;

            text-align: right;

            white-space: nowrap;
        }

        /* Totals */
        .totals {

            font-size: 13px;

            line-height: 1.8;
        }

        .totals-row {

            display: flex;

            justify-content: space-between;
        }

        .grand-total {

            font-size: 18px;

            font-weight: bold;
        }

        /* Footer */
        .footer {

            text-align: center;

            margin-top: 10px;

            font-size: 12px;

            line-height: 1.5;
        }

        /* Print */
        @media print {

            html,
            body {

                width: 80mm;

                margin: 0;
                padding: 0;

                overflow: hidden;
            }

            .receipt {

                width: 72mm;

                padding: 3mm;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

</head>

<body onload="window.print()">

    <div class="receipt">

        {{-- SHOP HEADER --}}
        <div class="text-center">

            <div class="shop-name">
                IDEAL BOOK CO.
            </div>

            <div class="small-text">
                {{-- 7, Abbas Mansion,<br> --}}
                Chhabildas Road, Dadar (W), Mumbai
            </div>

            <div class="small-text">
                Ph: +91 72082 26020
            </div>

        </div>

        <div class="divider"></div>

        {{-- TITLE --}}
        <div class="text-center">

            <strong>TAX INVOICE</strong>

        </div>

        <div class="divider"></div>

        {{-- BILL INFO --}}
        <div class="bill-info">

            <div>
                Bill No: {{ $sale->invoice_no }}
            </div>

            <div>
                Date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}
            </div>

            <div>
                Time: {{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}
            </div>

            <div>
                Customer: {{ $sale->customer?->name ?: 'Walk In Customer' }}
            </div>

            <div>
                Contact No: {{ $sale->customer?->phone ?: '-' }}
            </div>
        </div>

        <div class="divider"></div>

        {{-- ITEMS --}}
        <table>

            <thead>

                <tr>
                    <th>Item</th>
                    <th class="qty">Qty</th>
                    <th class="discount">%</th>
                    <th class="price">MRP</th>
                    <th class="amount">Amt</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($sale->saleItems as $item)
                    <tr>
                        <td>{{ $item->product->book_name }}</td>

                        <td class="qty">{{ $item->quantity }}</td>

                        <td class="discount">{{ $item->discount }}</td>

                        <td class="price">{{ number_format($item->mrp, 0) }}</td>

                        <td class="amount">{{ number_format($item->selling_price, 0) }}</td>
                    </tr>
                @endforeach

            </tbody>

        </table>

        <div class="divider"></div>

        {{-- TOTALS --}}

        <div class="totals-row grand-total">
            <span>Total</span>
            <span>₹ {{ $sale->total_amount }}</span>
        </div>

    </div>

    <div class="divider"></div>

    {{-- PAYMENT --}}
    <div class="small-text">

        Payment Mode: CASH

    </div>

    <div class="small-text">

        Created by: {{ $sale->creator?->name ?? '-' }}

    </div>

    {{-- FOOTER --}}
    <div class="footer">

        THANK YOU! VISIT AGAIN!

    </div>

    </div>

</body>

</html>
