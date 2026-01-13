<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('authors.index') }}" placeholder="Search authors..." />

            {{-- ➕ Create Author --}}
            <button class="btn btn-primary" onclick="create_author.showModal()">
                Create Author
            </button>
        </div>

        {{-- 📋 Authors Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Author Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id='authorsTableBody'>
                @forelse ($authors as $author)
                    <tr class="hover:bg-base-300" data-author-id="{{ $author->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Needed for JS update --}}
                        <td class="author-name">
                            {{ $author->name }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info tooltip" data-tip="View"
                                onclick="openViewAuthor('{{ $author->name }}')">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <button class="btn btn-xs btn-warning tooltip" data-tip="Edit"
                                onclick="openEditAuthor({{ $author->id }}, @js($author->name))">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            <form action="{{ route('authors.destroy', $author->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this author?')">
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
                            No authors found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $authors->links() }}
    </div>

    {{-- 🔹 CREATE MODAL --}}
    @include('authors.partials.create-author-modal')

    {{-- 🔹 EDIT MODAL --}}
    @include('authors.partials.edit-author-modal')

    {{-- 🔹 VIEW MODAL --}}
    @include('authors.partials.view-author-modal')

    {{-- 🔔 TOAST --}}
    @include('components.toast')

    <script>
        function openViewAuthor(name) {
            document.getElementById('view_author_name').value = name;
            view_author.showModal();
        }
    </script>

</x-app-layout>
