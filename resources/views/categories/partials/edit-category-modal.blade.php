{{-- Edit Category Modal --}}
<dialog id="edit_category" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Edit Category</h3>

        <form id="editCategoryForm" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_category_id">

            <div>
                <label class="label">
                    <span class="label-text">Category Name</span>
                </label>
                <input type="text" id="edit_category_name" class="input input-bordered w-full" required />
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="edit_category.close()">
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
    function openEditCategory(id, name) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
        edit_category.showModal();
    }

    document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const id = document.getElementById('edit_category_id').value;
        const name = document.getElementById('edit_category_name').value;
        const token = document.querySelector('input[name="_token"]').value;

        const response = await fetch(`/categories/${id}`, {
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
            alert('Something went wrong');
            return;
        }

        const data = await response.json();

        edit_category.close();

        // 🔄 Update table row instantly
        const row = document.querySelector(`[data-category-id="${id}"]`);
        if (row) {
            row.querySelector('.category-name').innerText = data.category.name;
        }

        showToast(data.message);
    });
</script>
