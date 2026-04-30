<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('vendors.index') }}" placeholder="Search vendors..." />

            {{-- ➕ Create Vendor --}}
            <button class="btn btn-primary" onclick="create_vendor.showModal()">
                Create Vendor
            </button>
        </div>

        {{-- 📋 vendors Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Vendor Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id='vendorsTableBody'>
                @forelse ($vendors as $vendor)
                    <tr class="hover:bg-base-300" data-vendor-id="{{ $vendor->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Needed for JS update --}}
                        <td class="vendor-name">
                            {{ $vendor->name }}
                        </td>

                        <td class="vendor-phone">
                            {{ $vendor->phone }}
                        </td>

                        <td class="vendor-email">
                            {{ $vendor->email }}
                        </td>

                        <td class="vendor-address">
                            {{ $vendor->address }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info tooltip" data-tip="View"
                                onclick='openViewVendor(@json($vendor))'>
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <button class="btn btn-xs btn-warning tooltip" data-tip="Edit"
                                onclick="openEditVendor({{ $vendor->id }}, @js($vendor->name), @js($vendor->phone), @js($vendor->email), @js($vendor->address))">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this vendor?')">
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
                            No vendors found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- <div class="mt-4">
        {{ $vendors->links() }}
    </div> --}}

    {{-- 🔹 CREATE MODAL --}}
    @include('vendors.partials.create-vendor-modal')

    {{-- 🔹 EDIT MODAL --}}
    @include('vendors.partials.edit-vendor-modal')

    {{-- 🔹 VIEW MODAL --}}
    @include('vendors.partials.view-vendor-modal')

    {{-- 🔔 TOAST --}}
    @include('components.toast')

    <script>
        function openViewVendor(vendor) {
            document.getElementById('view_vendor_name').value = vendor.name;
            document.getElementById('view_vendor_phone').value = vendor.phone;
            document.getElementById('view_vendor_email').value = vendor.email;
            document.getElementById('view_vendor_address').value = vendor.address;
            view_vendor.showModal();
        }
    </script>

</x-app-layout>
