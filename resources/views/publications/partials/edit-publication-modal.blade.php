{{-- Edit publication Modal --}}
<dialog id="edit_publication" class="modal">
    <div class="modal-box">

        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Edit Publication</h3>

        <form id="editPublicationForm" class="space-y-4">
            @csrf

            <input type="hidden" id="edit_publication_id">

            <div>
                <label class="label">
                    <span class="label-text">Publication Name</span>
                </label>
                <input type="text" id="edit_publication_name" class="input input-bordered w-full" required />
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="edit_publication.close()">
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
    document.addEventListener('DOMContentLoaded', () => {

        // Open modal
        window.openEditPublication = function(id, name) {
            document.getElementById('edit_publication_id').value = id;
            document.getElementById('edit_publication_name').value = name;
            edit_publication.showModal();
        };

        // Submit edit form
        const form = document.getElementById('editPublicationForm');

        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = document.getElementById('edit_publication_id').value;
            const name = document.getElementById('edit_publication_name').value;
            const token = document.querySelector('input[name="_token"]').value;

            const response = await fetch(`/publications/${id}`, {
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

            if (!response.ok) {
                console.error(await response.text());
                alert('Something went wrong');
                return;
            }

            const data = await response.json();

            // Close modal
            edit_publication.close();

            // 🔄 Update table instantly
            const row = document.querySelector(`[data-publication-id="${id}"]`);
            if (row) {
                row.querySelector('.publication-name').innerText = data.publication.name;
            }

            // 🔔 Toast
            showToast(data.message);
        });
    });
</script>
