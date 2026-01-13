<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('categories.index') }}" placeholder="Search categories..." />

            {{-- ➕ Create Category --}}
            <button class="btn btn-primary" onclick="create_category.showModal()">
                Create Category
            </button>
        </div>

        {{-- 📋 Categories Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Category Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id="categoriesTableBody">
                @forelse ($categories as $category)
                    <tr class="hover:bg-base-300" data-category-id="{{ $category->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Required for JS update --}}
                        <td class="category-name">
                            {{ $category->name }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info tooltip" data-tip="View"
                                onclick="openViewCategory('{{ $category->name }}')">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <button class="btn btn-xs btn-warning tooltip" data-tip="Edit"
                                onclick="openEditCategory({{ $category->id }}, @js($category->name))">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            {{-- 🗑 Delete --}}
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-xs btn-error tooltip" data-tip="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 📄 Pagination --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    {{-- 🔹 CREATE MODAL --}}
    @include('categories.partials.create-category-modal')

    {{-- 🔹 EDIT MODAL --}}
    @include('categories.partials.edit-category-modal')

    {{-- 🔹 VIEW MODAL --}}
    @include('categories.partials.view-category-modal')

    {{-- 🔔 TOAST --}}
    @include('components.toast')

    <script>
        function openViewCategory(name) {
            document.getElementById('view_category_name').value = name;
            view_category.showModal();
        }
    </script>
</x-app-layout>
