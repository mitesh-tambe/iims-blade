{{-- Create Author Modal --}}
<dialog id="create_author" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Create Author</h3>

        <form id="createAuthorForm" class="space-y-4">
            @csrf

            <div>
                <label class="label">
                    <span class="label-text">Author Name</span>
                </label>
                <input type="text" id="author_name" class="input input-bordered w-full"
                    placeholder="Enter author name" required />
                <p id="author_name_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="create_author.close()">
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

        const form = document.getElementById('createAuthorForm');
        const tableBody = document.getElementById('authorsTableBody'); // may be null

        const nameInput = document.getElementById('author_name');
        const errorEl = document.getElementById('author_name_error');

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

            const nameInput = document.getElementById('author_name');
            const name = nameInput.value.trim();
            if (!name) return;

            try {
                const response = await fetch("{{ route('authors.store') }}", {
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

                    // 🔴 Show validation error
                    if (errorData.errors?.name && errorEl) {
                        errorEl.textContent = errorData.errors.name[0];
                        errorEl.classList.remove('hidden');
                    }

                    throw new Error('Validation failed');
                }

                const data = await response.json();

                /* 🔥 DISPATCH EVENT FOR TOM SELECT */
                window.dispatchEvent(new CustomEvent('author-created', {
                    detail: {
                        id: data.author.id,
                        name: data.author.name
                    }
                }));

                // Close modal & reset
                create_author.close();
                form.reset();

                /* 🧠 ONLY update table IF it exists */
                if (tableBody) {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-base-300');
                    row.dataset.authorId = data.author.id;

                    row.innerHTML = `
                    <th>1</th>
                    <td class="author-name">${data.author.name}</td>
                    <td class="text-right space-x-1">
                        <a href="#" class="btn btn-xs btn-info">View</a>
                        <button class="btn btn-xs btn-warning"
                            onclick="openEditAuthor(${data.author.id}, '${data.author.name.replace(/'/g, "\\'")}')">
                            Edit
                        </button>
                        <button type="button" class="btn btn-xs btn-error">Delete</button>
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
                    showToast('Failed to create author', 'error');
                }
            }
        });
    });
</script>
