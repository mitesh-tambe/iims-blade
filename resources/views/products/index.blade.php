<x-app-layout>
    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- 🔍 Search --}}
            <x-search-bar action="{{ route('products.index') }}" placeholder="Search products..." />

            {{-- ➕ Create Product --}}
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                Create Product
            </a>
        </div>

        {{-- 🎯 Filters --}}
        <form method="GET" action="{{ route('products.index') }}" id="filterForm"
            class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-4">

            {{-- Preserve search --}}
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            {{-- Author --}}
            <select name="author_id" class="select select-bordered w-full" onchange="submitFilters()">
                <option value="">All Authors</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>

            {{-- Language --}}
            <select name="language_id" class="select select-bordered w-full" onchange="submitFilters()">
                <option value="">All Languages</option>
                @foreach ($languages as $language)
                    <option value="{{ $language->id }}" {{ request('language_id') == $language->id ? 'selected' : '' }}>
                        {{ $language->name }}
                    </option>
                @endforeach
            </select>

            {{-- Category --}}
            <select name="category_id" class="select select-bordered w-full" onchange="submitFilters()">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            {{-- Rack --}}
            <select name="rack_no" class="select select-bordered w-full" onchange="submitFilters()">
                <option value="">All Racks</option>
                @foreach ($racks as $rack)
                    <option value="{{ $rack }}" {{ request('rack_no') == $rack ? 'selected' : '' }}>
                        {{ $rack }}
                    </option>
                @endforeach
            </select>
        </form>

        @php
            $hasFilters = request()->except('page') !== [];
        @endphp

        <div class="flex justify-end mt-3">
            <a href="{{ route('products.index') }}"
                class="btn btn-outline btn-sm {{ !$hasFilters ? 'btn-disabled' : '' }}">
                Clear Filters
            </a>
        </div>

        {{-- 📋 Products Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Book Name</th>
                    <th>ISBN</th>
                    <th>Pages</th>
                    <th>MRP</th>
                    <th>Author</th>
                    <th>Language</th>
                    <th>Category</th>
                    <th>Rack No.</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($products as $product)
                    <tr class="hover:bg-base-300" data-product-id="{{ $product->id }}">
                        <th>{{ $loop->iteration }}</th>

                        <td class="font-medium">
                            {{ $product->book_name }}
                        </td>

                        <td>{{ $product->isbn ?? '-' }}</td>

                        <td>{{ $product->book_pages }}</td>

                        <td>₹ {{ number_format($product->mrp, 2) }}</td>

                        <td>{{ $product->author->name ?? '-' }}</td>

                        <td>{{ $product->language->name ?? '-' }}</td>

                        <td>{{ $product->category->name ?? '-' }}</td>

                        <td>{{ $product->rack_no ?? '-' }}</td>

                        <td class="text-right space-x-1">
                            {{-- 👁 View --}}
                            <button type="button" class="btn btn-xs btn-info"
                                onclick='openViewProduct(@json($product))'>
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            {{-- ✏️ Edit --}}
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-xs btn-warning">
                                <i class="fa-solid fa-pencil"></i>
                            </a>

                            {{-- ❌ Delete --}}
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-error">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 📄 Pagination --}}
        <div class="pt-4">
            {{ $products->links() }}
        </div>

    </div>

    @include('products.partials.view-product-modal')

    <script>
        function openViewProduct(product) {

            // BASIC DETAILS
            document.getElementById('view_book_name').textContent = product.book_name ?? '-';
            document.getElementById('view_isbn').textContent = product.isbn ?? '-';
            document.getElementById('view_edition').textContent = product.edition ?? '-';
            document.getElementById('view_book_pages').textContent = product.book_pages ?? '-';
            document.getElementById('view_barcode_no').textContent = product.barcode_no ?? '-';
            document.getElementById('view_mrp').textContent = product.mrp ?? '-';
            document.getElementById('view_rack_no').textContent = product.rack_no ?? '-';

            // DISCOUNTS
            document.getElementById('view_disc_company').textContent =
                (product.disc_from_company ?? 0) + ' %';

            document.getElementById('view_amt_company').textContent =
                product.amt_company ?? '0.00';

            document.getElementById('view_disc_customer').textContent =
                (product.disc_for_customer ?? 0) + ' %';

            document.getElementById('view_amt_customer').textContent =
                product.amt_customer ?? '0.00';

            // RELATIONS (IMPORTANT: use loaded relations)
            document.getElementById('view_author').textContent =
                product.author?.name ?? '-';

            document.getElementById('view_publication').textContent =
                product.publication?.name ?? '-';

            document.getElementById('view_language').textContent =
                product.language?.name ?? '-';

            document.getElementById('view_category').textContent =
                product.category?.name ?? '-';

            // OPEN MODAL
            view_product.showModal();
        }
    </script>

    <script>
        function submitFilters() {
            document.getElementById('filterForm').submit();
        }
    </script>

</x-app-layout>
