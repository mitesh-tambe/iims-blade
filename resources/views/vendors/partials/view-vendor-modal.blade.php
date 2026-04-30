{{-- 👁 View Vendor Modal --}}
<dialog id="view_vendor" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">View Vendor</h3>

        <form class="space-y-4">
            <div>
                <label class="label">
                    <span class="label-text">Vendor Name</span>
                </label>
                <input type="text" id="view_vendor_name" class="input input-bordered w-full bg-base-200" readonly />
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Phone</span>
                </label>
                <input type="text" id="view_vendor_phone" class="input input-bordered w-full bg-base-200" readonly />
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" id="view_vendor_email" class="input input-bordered w-full bg-base-200" readonly />
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Address</span>
                </label>
                <input type="text" id="view_vendor_address" class="input input-bordered w-full bg-base-200" readonly />
            </div>
        </form>
    </div>
</dialog>
