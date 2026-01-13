{{-- Create Category Modal --}}
<dialog id="create_category" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Create Category</h3>

        <form id="createCategoryForm" class="space-y-4">
            @csrf

            <div>
                <label class="label">
                    <span class="label-text">Category Name</span>
                </label>
                <input type="text" id="category_name" class="input input-bordered w-full"
                    placeholder="Enter category name" required />

                <p id="category_name_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="create_category.close()">
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

        const form = document.getElementById('createCategoryForm');
        const tableBody = document.getElementById('categoriesTableBody'); // may be null

        const nameInput = document.getElementById('category_name');
        const errorEl = document.getElementById('category_name_error');

        if (!form) return; // ❗ ONLY check form

        // 🔹 Clear error while typing (GUARDED)
        if (nameInput && errorEl) {
            nameInput.addEventListener('input', () => {
                errorEl.textContent = '';
                errorEl.classList.add('hidden');
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = nameInput.value.trim();
            if (!name) return;

            try {
                const response = await fetch("{{ route('categories.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                            .value,
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({
                        name
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();

                    // 🔴 Show validation error (GUARDED)
                    if (errorData.errors?.name && errorEl) {
                        errorEl.textContent = errorData.errors.name[0];
                        errorEl.classList.remove('hidden');
                    }

                    throw new Error('Validation failed');
                }

                const data = await response.json();

                /* 🔥 DISPATCH EVENT FOR TOM SELECT */
                window.dispatchEvent(new CustomEvent('category-created', {
                    detail: {
                        id: data.category.id,
                        name: data.category.name
                    }
                }));

                // 🔹 Close modal & reset
                create_category.close();
                form.reset();

                /* 🧠 ONLY update table IF it exists */
                if (tableBody) {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-base-300');
                    row.dataset.categoryId = data.category.id;

                    row.innerHTML = `
                        <th>1</th>
                        <td class="category-name">${data.category.name}</td>
                        <td class="text-right space-x-1">

                            <a href="#" class="btn btn-xs btn-info tooltip" data-tip="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <button
                                class="btn btn-xs btn-warning tooltip"
                                data-tip="Edit"
                                onclick="openEditCategory(${data.category.id}, '${data.category.name.replace(/'/g, "\\'")}')">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            <form class="inline">
                                <button type="button"
                                    class="btn btn-xs btn-error tooltip"
                                    data-tip="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    `;

                    tableBody.prepend(row);

                    [...tableBody.querySelectorAll('tr')].forEach((tr, index) => {
                        const th = tr.querySelector('th');
                        if (th) th.textContent = index + 1;
                    });
                }

                if (window.showToast) {
                    showToast(data.message, 'success');
                }

            } catch (error) {
                console.error(error);
                if (window.showToast) {
                    showToast('Failed to create category', 'error');
                }
            }
        });
    });
</script>
