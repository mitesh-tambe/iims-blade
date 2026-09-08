<x-app-layout>

    <div class="overflow-x-auto space-y-4">

        {{-- 🔝 Top Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <form method="GET" action="{{ route('find-id.index') }}" class="w-full max-w-3xl">

                <div class="flex items-center gap-3">

                    <!-- Search Input -->
                    <div class="flex-1">
                        <label class="input flex items-center gap-2 w-full">

                            <!-- Search Icon -->
                            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">

                                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                                    stroke="currentColor">

                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="m21 21-4.35-4.35"></path>

                                </g>
                            </svg>

                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                                class="grow" />

                        </label>
                    </div>


                    <!-- Search By -->
                    <select name="search_by" class="select w-40">

                        <option value="product" {{ request('search_by', 'product') === 'product' ? 'selected' : '' }}>
                            Product
                        </option>

                        <option value="author" {{ request('search_by') === 'author' ? 'selected' : '' }}>
                            Author
                        </option>

                        <option value="category" {{ request('search_by') === 'category' ? 'selected' : '' }}>
                            Category
                        </option>

                        <option value="publication" {{ request('search_by') === 'publication' ? 'selected' : '' }}>
                            Publication
                        </option>

                        <option value="rack" {{ request('search_by') === 'rack' ? 'selected' : '' }}>
                            Rack
                        </option>

                        <option value="language" {{ request('search_by') === 'language' ? 'selected' : '' }}>
                            Language
                        </option>
                    </select>


                    <!-- Search Button -->
                    <button type="submit" class="btn btn-neutral">
                        Search
                    </button>

                    <!-- Reset Button -->
                    <a href="{{ route('find-id.index') }}" class="btn btn-outline">
                        Reset
                    </a>

                </div>

            </form>

        </div>

        {{-- Show Results --}}
        @if ($results->count())

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

                @foreach ($results as $result)
                    <div
                        class="group rounded-xl border border-base-300 bg-base-100 shadow-sm
                    hover:shadow-md hover:border-primary/40 transition-all duration-200">

                        <div class="p-5">

                            {{-- Header --}}
                            <div class="flex items-center justify-between gap-3">

                                <div class="flex items-center gap-2">

                                    {{-- Type Icon --}}
                                    <div
                                        class="flex h-10 w-10 items-center justify-center
                                    rounded-lg bg-primary/10 text-primary">

                                        @if ($searchBy === 'product')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m-8-4l8 4m0 0v10" />
                                            </svg>
                                        @elseif ($searchBy === 'author')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19a4 4 0 00-8 0m4-8a4 4 0 100-8 4 4 0 000 8zm5 8a4 4 0 00-3-3.87M17 11a3 3 0 100-6" />
                                            </svg>
                                        @elseif ($searchBy === 'category')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        @elseif ($searchBy === 'publication')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                            </svg>
                                        @elseif ($searchBy === 'rack')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 5h16M4 12h16M4 19h16M7 5v14M17 5v14" />
                                            </svg>
                                        @elseif ($searchBy === 'language')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5h12M9 3v2m3 14l-4-8-4 8m1.5-3h5M14 5h7m-3.5 0c0 6-2.5 10-6.5 13m3-6c2 2 4 3.5 6.5 4.5" />
                                            </svg>
                                        @endif

                                    </div>

                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide opacity-50">
                                            {{ ucfirst($searchBy) }}
                                        </p>

                                        <p class="font-semibold">
                                            Result
                                        </p>
                                    </div>

                                </div>

                                {{-- ID --}}
                                <div class="text-right">
                                    <p class="text-xs opacity-50">
                                        ID
                                    </p>

                                    <p class="text-lg font-bold text-primary">
                                        {{ $result->id }}
                                    </p>
                                </div>

                            </div>


                            {{-- Divider --}}
                            <div class="my-4 border-t border-base-300"></div>


                            {{-- Name / Details --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide opacity-50 mb-1">
                                    Name
                                </p>

                                <p class="text-lg font-semibold break-words">
                                    @if ($searchBy === 'product')
                                        {{ $result->book_name }}
                                    @else
                                        {{ $result->name }}
                                    @endif
                                </p>
                            </div>

                        </div>

                    </div>
                @endforeach

            </div>
        @elseif ($search)
            {{-- No Results --}}
            <div class="alert alert-warning">
                <span>
                    No {{ $searchBy }} found for
                    <strong>"{{ $search }}"</strong>.
                </span>
            </div>

        @endif

    </div>

</x-app-layout>
