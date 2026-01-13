<x-app-layout>
    <div class="p-6">
        {{-- <h1 class="text-2xl font-bold mb-6">Dashboard</h1> --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Products --}}
            <a href="{{ route('products.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-box text-4xl text-primary"></i>
                    <h2 class="card-title mt-2">Products</h2>
                    <p class="text-3xl font-bold">{{ $productsCount }}</p>
                </div>
            </a>

            {{-- Authors --}}
            <a href="{{ route('authors.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-user-pen text-4xl text-secondary"></i>
                    <h2 class="card-title mt-2">Authors</h2>
                    <p class="text-3xl font-bold">{{ $authorsCount }}</p>
                </div>
            </a>

            {{-- Publications --}}
            <a href="{{ route('publications.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-book-open text-4xl text-accent"></i>
                    <h2 class="card-title mt-2">Publications</h2>
                    <p class="text-3xl font-bold">{{ $publicationsCount }}</p>
                </div>
            </a>

            {{-- Categories --}}
            <a href="{{ route('categories.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                <div class="card-body items-center text-center">
                    <i class="fa-solid fa-tags text-4xl text-info"></i>
                    <h2 class="card-title mt-2">Categories</h2>
                    <p class="text-3xl font-bold">{{ $categoriesCount }}</p>
                </div>
            </a>

        </div>
    </div>
</x-app-layout>
