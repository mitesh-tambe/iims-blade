<x-app-layout>
    <div class="flex justify-center">
        <div class="w-full max-w-5xl">
            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">
                {{-- HEADER --}}
                <div class="flex items-center justify-between">
                    <legend class="fieldset-legend text-lg font-semibold">
                        Stock Adjustment Details
                    </legend>
                </div>

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Adjustment No --}}
                    <div>
                        <label class="label">Adjustment No</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->adjustment_no ?? '-' }}" readonly />
                    </div>

                    {{-- Adjustment Date --}}
                    <div>
                        <label class="label">Adjustment Date</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->adjustment_date
                                ? \Carbon\Carbon::parse($stockAdjustment->adjustment_date)->format('d/m/Y')
                                : '-' }}"
                            readonly />
                    </div>

                    {{-- Product --}}
                    <div>
                        <label class="label">Product</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->product?->book_name ?? '-' }}" readonly />
                    </div>

                    {{-- Adjustment Type --}}
                    <div>
                        <label class="label">Adjustment Type</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->type ?? '-' }}" readonly />
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="label">Quantity</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->quantity ?? '-' }}" readonly />
                    </div>

                    {{-- Unit Cost --}}
                    <div>
                        <label class="label">Unit Cost</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->unit_cost ? '₹ ' . number_format($stockAdjustment->unit_cost, 2) : '-' }}"
                            readonly />
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="label">Reason</label>

                        <input type="text" class="input input-bordered w-full"
                            value="{{ $stockAdjustment->reason ?? '-' }}" readonly />
                    </div>

                    {{-- Remarks --}}
                    <div class="md:col-span-2">
                        <label class="label">Remarks</label>
                        <textarea class="textarea textarea-bordered w-full" rows="4" readonly>{{ $stockAdjustment->remarks ?? '-' }}</textarea>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-2 pt-4">
                    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-ghost">
                        Back
                    </a>
                </div>
            </fieldset>
        </div>
    </div>
</x-app-layout>
