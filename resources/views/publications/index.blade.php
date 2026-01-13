<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('publications.index') }}" placeholder="Search publications..." />

            {{-- ➕ Create Publication --}}
            <button class="btn btn-primary" onclick="create_publication.showModal()">
                Create Publication
            </button>
        </div>

        {{-- 📋 Publications Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Publication Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id="publicationsTableBody">
                @forelse ($publications as $publication)
                    <tr class="hover:bg-base-300" data-publication-id="{{ $publication->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Needed for JS update --}}
                        <td class="publication-name">
                            {{ $publication->name }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info tooltip" data-tip="View"
                                onclick="openViewPublication('{{ $publication->name }}')">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <button class="btn btn-xs btn-warning tooltip" data-tip="Edit"
                                onclick="openEditPublication({{ $publication->id }}, @js($publication->name))">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            {{-- 🗑 Delete --}}
                            <form action="{{ route('publications.destroy', $publication->id) }}" method="POST"
                                class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this publication?')">
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
                            No publications found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 📄 Pagination --}}
    <div class="mt-4">
        {{ $publications->links() }}
    </div>

    {{-- 🔹 CREATE MODAL --}}
    @include('publications.partials.create-publication-modal')

    {{-- 🔹 EDIT MODAL --}}
    @include('publications.partials.edit-publication-modal')

    {{-- 🔹 VIEW MODAL --}}
    @include('publications.partials.view-publication-modal')

    {{-- 🔔 TOAST --}}
    @include('components.toast')

    <script>
        function openViewPublication(name) {
            document.getElementById('view_publication_name').value = name;
            view_publication.showModal();
        }
    </script>
</x-app-layout>
