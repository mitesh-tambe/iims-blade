{{-- Edit Rack Modal --}}
<dialog id="edit_rack" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Edit Rack</h3>

        <form id="editRackForm" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_rack_id">

            <div>
                <label class="label">
                    <span class="label-text">Rack Name</span>
                </label>
                <input type="text" id="edit_rack_name" class="input input-bordered w-full" required />
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="edit_rack.close()">
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
    function openEditRack(id, name) {
        document.getElementById('edit_rack_id').value = id;
        document.getElementById('edit_rack_name').value = name;
        edit_rack.showModal();
    }

    document.getElementById('editRackForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = document.getElementById('edit_rack_id').value;
        const name = document.getElementById('edit_rack_name').value;
        const token = document.querySelector('input[name="_token"]').value;

        const response = await fetch(`/racks/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                name
            })
        });

        const data = await response.json();

        edit_rack.close();

        // Update table instantly
        const row = document.querySelector(`[data-rack-id="${id}"]`);
        if (row) {
            row.querySelector('.rack-name').innerText = data.rack.name;
        }

        showToast(data.message);
    });
</script>
