<x-app-layout>
    <div class="flex justify-center">
        <form action="{{ route('products.update', $product->id) }}" method="POST" class="w-full max-w-3xl">
            @csrf
            @method('PUT')

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-6 space-y-4">
                <legend class="fieldset-legend text-lg font-semibold">
                    Edit Product
                </legend>

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Book Name --}}
                    <div>
                        <label class="label">Book Name</label>
                        <input type="text" name="book_name" class="input input-bordered w-full"
                            value="{{ old('book_name', $product->book_name) }}"
                            placeholder="Enter book name" required />
                        @error('book_name')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ISBN --}}
                    <div>
                        <label class="label">ISBN</label>
                        <input type="text" name="isbn" class="input input-bordered w-full"
                            value="{{ old('isbn', $product->isbn) }}"
                            placeholder="ISBN number" />
                    </div>

                    {{-- Edition --}}
                    <div>
                        <label class="label">Edition</label>
                        <input type="number" name="edition" class="input input-bordered w-full"
                            value="{{ old('edition', $product->edition) }}"
                            placeholder="Edition (optional)" />
                    </div>

                    {{-- Book Pages --}}
                    <div>
                        <label class="label">Book Pages</label>
                        <input type="number" step="0.01" name="book_pages" class="input input-bordered w-full"
                            value="{{ old('book_pages', $product->book_pages) }}"
                            placeholder="Total pages" required />
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="label">Barcode No</label>
                        <input type="text" name="barcode_no" class="input input-bordered w-full"
                            value="{{ old('barcode_no', $product->barcode_no) }}"
                            placeholder="Barcode number" />
                    </div>

                    {{-- MRP --}}
                    <div>
                        <label class="label">MRP</label>
                        <input type="number" name="mrp" class="input input-bordered w-full"
                            value="{{ old('mrp', $product->mrp) }}"
                            placeholder="MRP" required />
                    </div>
                </div>

                {{-- DISCOUNT SECTION --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">

                    {{-- Disc % Company --}}
                    <div>
                        <label class="label">Disc % (Company)</label>
                        <input type="number" name="disc_from_company" class="input input-bordered w-full"
                            value="{{ old('disc_from_company', $product->disc_from_company) }}"
                            placeholder="%" />
                    </div>

                    {{-- Amt Company --}}
                    <div>
                        <label class="label">Amt</label>
                        <input type="number" name="amt_company"
                            class="input input-bordered w-full bg-base-200"
                            value="{{ old('amt_company', $product->amt_company) }}"
                            placeholder="Amt" />
                    </div>

                    {{-- Disc % Customer --}}
                    <div>
                        <label class="label">Disc % (Customer)</label>
                        <input type="number" name="disc_for_customer" class="input input-bordered w-full"
                            value="{{ old('disc_for_customer', $product->disc_for_customer) }}"
                            placeholder="%" />
                    </div>

                    {{-- Amt Customer --}}
                    <div>
                        <label class="label">Amt</label>
                        <input type="number" name="amt_customer"
                            class="input input-bordered w-full bg-base-200"
                            value="{{ old('amt_customer', $product->amt_customer) }}"
                            placeholder="Amt" />
                    </div>

                </div>

                {{-- AUTHOR --}}
                <div>
                    <label class="label">Author</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="author_id" id="authorSelect" class="select select-bordered w-full" required>
                            <option value="">Select Author</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    {{ old('author_id', $product->author_id) == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button" class="btn btn-outline btn-primary w-full"
                            onclick="create_author.showModal()">
                            + Add Author
                        </button>
                    </div>
                </div>

                {{-- PUBLICATION --}}
                <div>
                    <label class="label">Publication</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="publication_id" id="publicationSelect" class="select select-bordered w-full" required>
                            <option value="">Select Publication</option>
                            @foreach ($publications as $publication)
                                <option value="{{ $publication->id }}"
                                    {{ old('publication_id', $product->publication_id) == $publication->id ? 'selected' : '' }}>
                                    {{ $publication->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button" class="btn btn-outline btn-primary w-full"
                            onclick="create_publication.showModal()">
                            + Add Publication
                        </button>
                    </div>
                </div>

                {{-- LANGUAGE --}}
                <div>
                    <label class="label">Language</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="language_id" id="languageSelect" class="select select-bordered w-full" required>
                            <option value="">Select Language</option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}"
                                    {{ old('language_id', $product->language_id) == $language->id ? 'selected' : '' }}>
                                    {{ $language->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button" class="btn btn-outline btn-primary w-full"
                            onclick="create_language.showModal()">
                            + Add Language
                        </button>
                    </div>
                </div>

                {{-- CATEGORY --}}
                <div>
                    <label class="label">Category</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="category_id" id="categorySelect" class="select select-bordered w-full">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button" class="btn btn-outline btn-primary w-full"
                            onclick="create_category.showModal()">
                            + Add Category
                        </button>
                    </div>
                </div>

                {{-- RACK NO. --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Rack No.</label>
                        <input type="text" name="rack_no" class="input input-bordered w-full"
                            value="{{ old('rack_no', $product->rack_no) }}" placeholder="Rack Number" required />
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary">
                        Update Product
                    </button>
                </div>

            </fieldset>
        </form>
    </div>

    {{-- MODALS --}}
    @include('authors.partials.create-author-modal')
    @include('publications.partials.create-publication-modal')
    @include('languages.partials.create-language-modal')
    @include('categories.partials.create-category-modal')

    {{-- TOAST --}}
    @include('components.toast')

    {{-- JS (IDENTICAL LOGIC AS CREATE) --}}
    @include('products.partials.product-form-js')

</x-app-layout>
