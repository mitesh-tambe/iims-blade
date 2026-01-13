{{-- 👁 View Category Modal --}}
<dialog id="view_category" class="modal">
    <div class="modal-box">
        <!-- Close -->
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-lg font-bold mb-4">View Category</h3>

        <form class="space-y-4">
            <div>
                <label class="label">
                    <span class="label-text">Category Name</span>
                </label>
                <input type="text" id="view_category_name" class="input input-bordered w-full bg-base-200" readonly />
            </div>
        </form>
    </div>
</dialog>
