<x-app-layout>

    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('reports.index') }}" placeholder="Search products..." />

        </div>

        {{-- 🎯 Filters --}}

        <form method="GET" action="{{ route('reports.index') }}" id="filterForm"
            class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-4">


            {{-- Preserve Search --}}
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif


            {{-- Start Date --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    Start Date
                </label>

                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="input input-bordered w-full" onchange="submitFilters()">
            </div>


            {{-- End Date --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    End Date
                </label>

                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="input input-bordered w-full" onchange="submitFilters()">
            </div>


            {{-- Level --}}
            <div>
                <label class="block text-sm font-medium mb-1">
                    Level
                </label>

                <select name="level" class="select select-bordered w-full" onchange="submitFilters()">

                    <option value="">All Levels</option>

                    <option value="normal" @selected(request('level') === 'normal')>
                        Normal
                    </option>

                    <option value="critical" @selected(request('level') === 'critical')>
                        Critical
                    </option>

                </select>
            </div>


            {{-- Export + Clear --}}
            <div class="flex gap-2 justify-end items-end">

                {{-- Export --}}
                <a href="{{ route('reports.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    Export
                </a>

                {{-- Clear --}}
                <a href="{{ route('reports.index') }}"
                    class="btn btn-outline btn-sm {{ !$hasFilters ? 'btn-disabled' : '' }}">
                    Clear Filters
                </a>

            </div>

        </form>


        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- Error Message --}}
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif


        {{-- 📋 Stock Report --}}
        <table class="table">

            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Level</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($movements as $movement)
                    <tr class="hover:bg-base-300">

                        {{-- Sr No --}}
                        <th>
                            {{ $movements->firstItem() + $loop->index }}
                        </th>


                        {{-- Product --}}
                        <td class="font-medium">
                            {{ $movement->product_name ?? '-' }}
                        </td>


                        {{-- Current Stock --}}
                        <td class="font-semibold">

                            {{ max(0, (int) $movement->quantity) }}

                        </td>


                        {{-- Last Movement Date --}}
                        <td>
                            {{ $movement->created_at ? \Carbon\Carbon::parse($movement->created_at)->format('d/m/Y h:i A') : '-' }}
                        </td>


                        {{-- Level --}}
                        <td>

                            @if ($movement->quantity > 5)
                                <span class="badge badge-success">
                                    Normal
                                </span>
                            @else
                                <span class="badge badge-error">
                                    Critical
                                </span>
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center text-gray-500">
                            No stock records found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>


        {{-- 📄 Pagination --}}
        <div class="pt-4">
            {{ $movements->links() }}
        </div>

    </div>

    <script>
        function submitFilters() {
            document.getElementById('filterForm').submit();
        }
    </script>

</x-app-layout>
