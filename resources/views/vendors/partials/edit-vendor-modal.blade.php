{{-- Edit Vendor Modal --}}
<dialog id="edit_vendor" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Edit Vendor</h3>

        <form id="editVendorForm" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_vendor_id">

            <div>
                <label class="label">
                    <span class="label-text">Vendor Name</span>
                </label>
                <input type="text" id="edit_vendor_name" class="input input-bordered w-full" required />
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Phone</span>
                </label>
                <input type="text" id="edit_vendor_phone" class="input input-bordered w-full" />
            </div>

            {{-- email  --}}
            <div>
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" id="edit_vendor_email" class="input input-bordered w-full" />
            </div>

            {{-- address --}}
            <div>
                <label class="label">
                    <span class="label-text">Address</span>
                </label>
                <input type="text" id="edit_vendor_address" class="input input-bordered w-full" />
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="edit_vendor.close()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Update
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
    function openEditVendor(id, name, phone, email, address) {
        document.getElementById('edit_vendor_id').value = id;
        document.getElementById('edit_vendor_name').value = name;
        document.getElementById('edit_vendor_phone').value = phone;
        document.getElementById('edit_vendor_email').value = email;
        document.getElementById('edit_vendor_address').value = address;
        edit_vendor.showModal();
    }

    document.getElementById('editVendorForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = document.getElementById('edit_vendor_id').value;
        const name = document.getElementById('edit_vendor_name').value;
        const phone = document.getElementById('edit_vendor_phone').value;
        const email = document.getElementById('edit_vendor_email').value;
        const address = document.getElementById('edit_vendor_address').value;
        const token = document.querySelector('input[name="_token"]').value;

        const response = await fetch(`/vendors/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                name,
                phone,
                email,
                address
            })
        });

        const data = await response.json();

        edit_vendor.close();

        // Update table instantly
        const row = document.querySelector(`[data-vendor-id="${id}"]`);
        if (row) {
            row.querySelector('.vendor-name').innerText = data.vendor.name;
            row.querySelector('.vendor-phone').innerText = data.vendor.phone;
            row.querySelector('.vendor-email').innerText = data.vendor.email;
            row.querySelector('.vendor-address').innerText = data.vendor.address;
        }

        showToast(data.message);
    });
</script>
