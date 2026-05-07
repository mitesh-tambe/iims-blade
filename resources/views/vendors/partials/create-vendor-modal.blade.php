{{-- Create Vendor Modal --}}
<dialog id="create_vendor" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Create Vendor</h3>

        <form id="createVendorForm" class="space-y-4">
            @csrf

            {{-- Name --}}
            <div>
                <label class="label">
                    <span class="label-text">Vendor Name</span>
                </label>
                <input type="text" id="vendor_name" name="name" class="input input-bordered w-full"
                    placeholder="Enter vendor name" required />
                <p id="vendor_name_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            {{-- Phone --}}
            <div>
                <label class="label">
                    <span class="label-text">Phone</span>
                </label>
                <input type="text" id="vendor_phone" name="phone" class="input input-bordered w-full"
                    placeholder="Enter phone number" required />
                <p id="vendor_phone_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            {{-- Email --}}
            <div>
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" id="vendor_email" name="email" class="input input-bordered w-full"
                    placeholder="Enter email address" />
                <p id="vendor_email_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            {{-- Address --}}
            <div>
                <label class="label">
                    <span class="label-text">Address</span>
                </label>
                <textarea id="vendor_address" name="address" class="textarea textarea-bordered w-full" placeholder="Enter address"></textarea>
                <p id="vendor_address_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            {{-- pan_no  --}}
            <div>
                <label class="label">
                    <span class="label-text">PAN No.</span>
                </label>
                <input type="text" id="vendor_pan_no" name="pan_no" class="input input-bordered w-full" placeholder="Enter PAN number" />
                <p id="vendor_pan_no_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            {{-- gst_no  --}}
            <div>
                <label class="label">
                    <span class="label-text">GST No.</span>
                </label>
                <input type="text" id="vendor_gst_no" name="gst_no" class="input input-bordered w-full" placeholder="Enter GST number" />
                <p id="vendor_gst_no_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('create_vendor').close()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const form = document.getElementById('createVendorForm');
        const tableBody = document.getElementById('vendorsTableBody');

        if (!form) return;

        const fields = ['name', 'phone', 'email', 'address', 'pan_no', 'gst_no'];

        // 🔹 Clear errors on input
        fields.forEach(field => {
            const input = document.getElementById(`vendor_${field}`);
            const error = document.getElementById(`vendor_${field}_error`);

            if (input && error) {
                input.addEventListener('input', () => {
                    error.textContent = '';
                    error.classList.add('hidden');
                });
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                name: document.getElementById('vendor_name').value.trim(),
                phone: document.getElementById('vendor_phone').value.trim(),
                email: document.getElementById('vendor_email').value.trim(),
                address: document.getElementById('vendor_address').value.trim(),
                pan_no: document.getElementById('vendor_pan_no').value.trim(),
                gst_no: document.getElementById('vendor_gst_no').value.trim()
            };

            try {
                const response = await fetch("{{ route('vendors.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                            .value,
                        "Accept": "application/json",
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                // ❌ Validation errors
                if (!response.ok) {

                    fields.forEach(field => {
                        const errorEl = document.getElementById(`vendor_${field}_error`);
                        if (data.errors?.[field] && errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.remove('hidden');
                        }
                    });

                    throw new Error('Validation failed');
                }

                // ✅ Dispatch event (TomSelect etc.)
                window.dispatchEvent(new CustomEvent('vendor-created', {
                    detail: {
                        id: data.vendor.id,
                        name: data.vendor.name
                    }
                }));

                // ✅ Close modal
                document.getElementById('create_vendor').close();
                form.reset();

                // ✅ Update table (safe rendering)
                if (tableBody) {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-base-300');
                    row.dataset.vendorId = data.vendor.id;

                    row.innerHTML = `
                    <th>1</th>
                    <td class="vendor-name"></td>
                    <td>${data.vendor.phone ?? ''}</td>
                    <td>${data.vendor.email ?? ''}</td>
                    <td class="text-right space-x-1">
                        <a href="#" class="btn btn-xs btn-info">View</a>
                        <button class="btn btn-xs btn-warning edit-btn">Edit</button>
                        <button type="button" class="btn btn-xs btn-error">Delete</button>
                    </td>
                `;

                    // 🔐 SAFE text injection
                    row.querySelector('.vendor-name').textContent = data.vendor.name;

                    // attach edit event safely
                    row.querySelector('.edit-btn').addEventListener('click', () => {
                        openEditVendor(data.vendor.id, data.vendor.name);
                    });

                    tableBody.prepend(row);

                    // 🔄 Re-index
                    [...tableBody.querySelectorAll('tr')].forEach((tr, index) => {
                        const th = tr.querySelector('th');
                        if (th) th.textContent = index + 1;
                    });
                }

                if (window.showToast) {
                    showToast(data.message || 'Vendor created', 'success');
                }

            } catch (error) {
                console.error(error);
                if (window.showToast) {
                    showToast('Failed to create vendor', 'error');
                }
            }
        });
    });
</script>
