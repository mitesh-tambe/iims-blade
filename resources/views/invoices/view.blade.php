<x-app-layout>

    <div class="flex justify-center">

        <div class="w-full max-w-5xl">

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">

                <legend class="fieldset-legend text-lg font-semibold">
                    Invoice Details
                </legend>

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Invoice No --}}
                    <div>
                        <label class="label">Invoice No</label>

                        <input type="text" class="input input-bordered w-full" value="{{ $purchase->invoice_no }}"
                            readonly />
                    </div>

                    {{-- Purchase Date --}}
                    <div>
                        <label class="label">Purchase Date</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : '-' }}"
                            readonly />
                    </div>

                    {{-- Vendor --}}
                    <div>
                        <label class="label">Vendor</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $purchase->vendor?->name ?? '-' }}" readonly />
                    </div>

                    {{-- Total Amount --}}
                    <div>
                        <label class="label">Total Amount</label>

                        <input type="text" class="input input-bordered w-full"
                            value="₹ {{ number_format($purchase->total_amount, 2) }}" readonly />
                    </div>

                    {{-- Ref No --}}
                    <div>
                        <label class="label">Reference No</label>

                        <input type="text" class="input input-bordered w-full" value="{{ $purchase->ref_no }}"
                            readonly />
                    </div>

                </div>

                {{-- PRODUCTS --}}
                <div class="space-y-3 pt-4">

                    <div class="flex items-center justify-between">

                        <h3 class="font-semibold text-lg">
                            Products
                        </h3>

                    </div>

                    <div class="overflow-x-auto">

                        <table class="table table-zebra">

                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>Book Name</th>
                                    <th>Barcode</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Rack</th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse($purchase->items as $index => $item)
                                    <tr>

                                        <td>
                                            {{ $index + 1 }}
                                        </td>

                                        <td>
                                            {{ $item->product?->book_name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->product?->barcode_no ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->quantity }}
                                        </td>

                                        <td>
                                            ₹ {{ number_format($item->cost_price, 2) }}
                                        </td>

                                        <td>
                                            ₹ {{ number_format($item->cost_price * $item->quantity, 2) }}
                                        </td>

                                        <td>
                                            {{ $item->product?->rack_no ?? '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center text-gray-500">
                                            No products found.
                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="pt-4 flex justify-end gap-2">

                    <a href="{{ route('invoices.index') }}" class="btn btn-neutral">

                        Back

                    </a>

                    <button type="button" class="btn btn-primary" onclick="window.print()">

                        Print

                    </button>

                </div>

            </fieldset>

        </div>

    </div>

</x-app-layout>
