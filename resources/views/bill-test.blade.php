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

            width: 65%;

            padding-right: 4px;
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
                Ph: 1234567890
            </div>

            <div class="small-text">
                GSTIN: 27AAAAA0000A1Z5
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
                Bill No: 1045
            </div>

            <div>
                Date: 22/05/2026
            </div>

            <div>
                Time: 11:48 AM
            </div>

            <div>
                Customer: Walk In Customer
            </div>

        </div>

        <div class="divider"></div>

        {{-- ITEMS --}}
        <table>

            <thead>

                <tr>
                    <th>Item</th>
                    <th class="qty">Qty</th>
                    <th class="price">MRP</th>
                    <th class="amount">Amt</th>
                </tr>

            </thead>

            <tbody>

                <tr>
                    <td>TRAFFIC ENGINEERING AND TRANSPORT PLANNING</td>
                    <td class="qty">1</td>
                    <td class="price">850</td>
                    <td class="amount">850</td>
                </tr>

                <tr>
                    <td>ADVANCED STRUCTURAL ANALYSIS FOR CIVIL ENGINEERS</td>
                    <td class="qty">1</td>
                    <td class="price">720</td>
                    <td class="amount">720</td>
                </tr>

                <tr>
                    <td>DESIGN OF REINFORCED CONCRETE STRUCTURES</td>
                    <td class="qty">2</td>
                    <td class="price">560</td>
                    <td class="amount">1120</td>
                </tr>

                <tr>
                    <td>GEOTECHNICAL ENGINEERING FOUNDATION DESIGN</td>
                    <td class="qty">1</td>
                    <td class="price">640</td>
                    <td class="amount">640</td>
                </tr>

                <tr>
                    <td>HIGHWAY ENGINEERING AND TRANSPORT MANAGEMENT</td>
                    <td class="qty">1</td>
                    <td class="price">580</td>
                    <td class="amount">580</td>
                </tr>

                <tr>
                    <td>ENVIRONMENTAL ENGINEERING WATER TREATMENT</td>
                    <td class="qty">3</td>
                    <td class="price">430</td>
                    <td class="amount">1290</td>
                </tr>

                <tr>
                    <td>ESTIMATION COSTING AND VALUATION HANDBOOK</td>
                    <td class="qty">1</td>
                    <td class="price">390</td>
                    <td class="amount">390</td>
                </tr>

                <tr>
                    <td>BUILDING CONSTRUCTION MATERIALS AND METHODS</td>
                    <td class="qty">2</td>
                    <td class="price">510</td>
                    <td class="amount">1020</td>
                </tr>

                <tr>
                    <td>HYDROLOGY AND WATER RESOURCE ENGINEERING</td>
                    <td class="qty">1</td>
                    <td class="price">470</td>
                    <td class="amount">470</td>
                </tr>

                <tr>
                    <td>CONSTRUCTION PROJECT MANAGEMENT TECHNIQUES</td>
                    <td class="qty">1</td>
                    <td class="price">760</td>
                    <td class="amount">760</td>
                </tr>

            </tbody>

        </table>

        <div class="divider"></div>

        {{-- TOTALS --}}
        <div class="totals">

            <div class="totals-row">
                <span>Subtotal</span>
                <span>₹7840</span>
            </div>

            <div class="totals-row">
                <span>CGST</span>
                <span>₹392</span>
            </div>

            <div class="totals-row">
                <span>SGST</span>
                <span>₹392</span>
            </div>

            <div class="divider"></div>

            <div class="totals-row grand-total">
                <span>Total</span>
                <span>₹8624</span>
            </div>

        </div>

        <div class="divider"></div>

        {{-- PAYMENT --}}
        <div class="small-text">

            Payment Mode: CASH

        </div>

        <div class="small-text">

            Total Savings: ₹250

        </div>

        {{-- FOOTER --}}
        <div class="footer">

            THANK YOU! VISIT AGAIN!

        </div>

    </div>

</body>

</html>
