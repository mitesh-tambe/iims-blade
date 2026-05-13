{{-- Edit User Modal --}}
<dialog id="edit_user" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Edit User</h3>

        <form id="editUserForm" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_user_id">

            <div>
                <label class="label">
                    <span class="label-text">User Name</span>
                </label>
                <input type="text" id="edit_user_name" class="input input-bordered w-full" required />
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" id="edit_user_email" class="input input-bordered w-full" required />
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="edit_user.close()">
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
    function openEditUser(id, name, email) {
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_user_name').value = name;
        document.getElementById('edit_user_email').value = email;
        edit_user.showModal();
    }

    document.getElementById('editUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = document.getElementById('edit_user_id').value;
        const name = document.getElementById('edit_user_name').value;
        const email = document.getElementById('edit_user_email').value;
        const token = document.querySelector('input[name="_token"]').value;

        const response = await fetch(`/users/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                name,
                email
            })
        });

        const data = await response.json();

        edit_user.close();

        // Update table instantly
        const row = document.querySelector(`[data-user-id="${id}"]`);
        if (row) {
            row.querySelector('.user-name').innerText = data.user.name;
            row.querySelector('.user-email').innerText = data.user.email;

            // Update edit button onclick value
            const editButton = row.querySelector('.btn-warning');

            if (editButton) {

                editButton.setAttribute(
                    'onclick',
                    `openEditUser(${data.user.id}, '${data.user.name.replace(/'/g, "\\'")}', '${data.user.email}')`
                );

            }
        }

        showToast(data.message);
    });
</script>
