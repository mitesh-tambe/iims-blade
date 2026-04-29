<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('racks.index') }}" placeholder="Search racks..." />

            {{-- ➕ Create Rack --}}
            <button class="btn btn-primary" onclick="create_rack.showModal()">
                Create Rack
            </button>
        </div>

        {{-- 📋 Racks Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Rack Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id='racksTableBody'>
                @forelse ($racks as $rack)
                    <tr class="hover:bg-base-300" data-rack-id="{{ $rack->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Needed for JS update --}}
                        <td class="rack-name">
                            {{ $rack->name }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info tooltip" data-tip="View"
                                onclick="openViewRack('{{ $rack->name }}')">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <button class="btn btn-xs btn-warning tooltip" data-tip="Edit"
                                onclick="openEditRack({{ $rack->id }}, @js($rack->name))">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            <form action="{{ route('racks.destroy', $rack->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this rack?')">
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
                            No racks found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $racks->links() }}
    </div>

    {{-- 🔹 CREATE MODAL --}}
    @include('racks.partials.create-rack-modal')

    {{-- 🔹 EDIT MODAL --}}
    @include('racks.partials.edit-rack-modal')

    {{-- 🔹 VIEW MODAL --}}
    @include('racks.partials.view-rack-modal')

    {{-- 🔔 TOAST --}}
    @include('components.toast')

    <script>
        function openViewRack(name) {
            document.getElementById('view_rack_name').value = name;
            view_rack.showModal();
        }
    </script>

</x-app-layout>
