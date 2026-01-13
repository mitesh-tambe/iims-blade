{{-- Edit Author Modal --}}
<dialog id="edit_author" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Edit Author</h3>

        <form id="editAuthorForm" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_author_id">

            <div>
                <label class="label">
                    <span class="label-text">Author Name</span>
                </label>
                <input type="text" id="edit_author_name" class="input input-bordered w-full" required />
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="edit_author.close()">
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
    function openEditAuthor(id, name) {
        document.getElementById('edit_author_id').value = id;
        document.getElementById('edit_author_name').value = name;
        edit_author.showModal();
    }

    document.getElementById('editAuthorForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = document.getElementById('edit_author_id').value;
        const name = document.getElementById('edit_author_name').value;
        const token = document.querySelector('input[name="_token"]').value;

        const response = await fetch(`/authors/${id}`, {
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

        edit_author.close();

        // Update table instantly
        const row = document.querySelector(`[data-author-id="${id}"]`);
        if (row) {
            row.querySelector('.author-name').innerText = data.author.name;
        }

        showToast(data.message);
    });
</script>
