<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3>Users</h3>
        </div>

        {{-- 📋 Users Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody id='usersTableBody'>
                @forelse ($users as $user)
                    <tr class="hover:bg-base-300" data-user-id="{{ $user->id }}">
                        <th>{{ $loop->iteration }}</th>

                        {{-- ✅ Needed for JS update --}}
                        <td class="user-name">
                            {{ $user->name }}
                        </td>
                        <td class="user-email">
                            {{ $user->email }}
                        </td>

                        <td class="text-right space-x-1">

                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info tooltip" data-tip="View"
                                onclick="openViewUser(@js($user->name), @js($user->email))">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <button class="btn btn-xs btn-warning tooltip" data-tip="Edit"
                                onclick="openEditUser({{ $user->id }}, @js($user->name), @js($user->email))">
                                <i class="fa-solid fa-pencil"></i>
                            </button>

                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                        <td colspan="4" class="text-center text-gray-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- 
    <div class="mt-4">
        {{ $users->links() }}
    </div> --}}


    {{-- 🔹 EDIT MODAL --}}
    @include('users.partials.edit-user-modal')

    {{-- 🔹 VIEW MODAL --}}
    @include('users.partials.view-user-modal')

    {{-- 🔔 TOAST --}}
    @include('components.toast')

    <script>
        function openViewUser(name, email) {
            document.getElementById('view_user_name').value = name;
            document.getElementById('view_user_email').value = email;
            view_user.showModal();
        }
    </script>

</x-app-layout>
