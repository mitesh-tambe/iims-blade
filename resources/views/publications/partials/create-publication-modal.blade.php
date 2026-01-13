{{-- Create Publication Modal --}}
<dialog id="create_publication" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">Create Publication</h3>

        <form id="createPublicationForm" class="space-y-4">
            @csrf

            <div>
                <label class="label">
                    <span class="label-text">Publication Name</span>
                </label>

                <input type="text" id="publication_name" class="input input-bordered w-full"
                    placeholder="Enter publication name" required />

                {{-- 🔴 Validation Error --}}
                <p id="publication_name_error" class="mt-1 text-sm text-error hidden"></p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-ghost" onclick="create_publication.close()">
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

        const form = document.getElementById('createPublicationForm');
        const tableBody = document.getElementById('publicationsTableBody'); // may be null

        const nameInput = document.getElementById('publication_name');
        const errorEl = document.getElementById('publication_name_error');

        if (!form) return; // ❗ ONLY check form (same as Author)

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
                const response = await fetch("{{ route('publications.store') }}", {
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
                window.dispatchEvent(new CustomEvent('publication-created', {
                    detail: {
                        id: data.publication.id,
                        name: data.publication.name
                    }
                }));

                // 🔹 Close modal & reset
                create_publication.close();
                form.reset();

                /* 🧠 ONLY update table IF it exists */
                if (tableBody) {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-base-300');
                    row.dataset.publicationId = data.publication.id;

                    row.innerHTML = `
                        <th>1</th>
                        <td class="publication-name">${data.publication.name}</td>
                        <td class="text-right space-x-1">

                            <a href="#" class="btn btn-xs btn-info tooltip" data-tip="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <button
                                class="btn btn-xs btn-warning tooltip"
                                data-tip="Edit"
                                onclick="openEditPublication(${data.publication.id}, '${data.publication.name.replace(/'/g, "\\'")}')">
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
                    showToast(data.message || 'Publication created successfully!', 'success');
                }

            } catch (error) {
                console.error(error);
                if (window.showToast) {
                    showToast('Failed to create publication', 'error');
                }
            }
        });
    });
</script>
