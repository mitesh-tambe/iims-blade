<div class="drawer-side is-drawer-close:overflow-visible">
    <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>

    <div class="flex min-h-full flex-col items-start bg-base-200 is-drawer-close:w-14 is-drawer-open:w-64">

        <ul class="menu w-full grow">

            {{-- Homepage --}}
            <li>
                <a href="{{ route('dashboard') }}"
                    class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('dashboard') ? 'sidebar-active' : '' }}"
                    data-tip="Homepage">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                        stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                        class="my-1.5 inline-block size-4">

                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>

                        <path
                            d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z">
                        </path>
                    </svg>

                    <span class="is-drawer-close:hidden">Homepage</span>
                </a>
            </li>


            {{-- =====================================================
                 MASTERS
            ====================================================== --}}
            <li>
                <details @if (request()->routeIs('products.*') ||
                        request()->routeIs('authors.*') ||
                        request()->routeIs('categories.*') ||
                        request()->routeIs('publications.*') ||
                        request()->routeIs('racks.*')) open @endif>

                    <summary class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Master">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="my-1.5 inline-block size-4">

                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z">
                            </path>

                        </svg>

                        <span class="is-drawer-close:hidden">Masters</span>
                    </summary>

                    <ul>

                        {{-- Products --}}
                        <li>
                            <a href="{{ route('products.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('products.*') ? 'sidebar-active' : '' }}"
                                data-tip="Products">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">Products</span>
                            </a>
                        </li>


                        {{-- Authors --}}
                        <li>
                            <a href="{{ route('authors.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('authors.*') ? 'sidebar-active' : '' }}"
                                data-tip="Authors">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">Authors</span>
                            </a>
                        </li>


                        {{-- Categories --}}
                        <li>
                            <a href="{{ route('categories.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('categories.*') ? 'sidebar-active' : '' }}"
                                data-tip="Categories">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">Categories</span>
                            </a>
                        </li>


                        {{-- Publications --}}
                        <li>
                            <a href="{{ route('publications.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('publications.*') ? 'sidebar-active' : '' }}"
                                data-tip="Publications">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">Publications</span>
                            </a>
                        </li>


                        {{-- Racks --}}
                        <li>
                            <a href="{{ route('racks.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('racks.*') ? 'sidebar-active' : '' }}"
                                data-tip="Racks">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">Racks</span>
                            </a>
                        </li>

                    </ul>
                </details>
            </li>


            {{-- =====================================================
                 STOCK MANAGEMENT
            ====================================================== --}}
            <li>
                <details @if (request()->routeIs('vendors.*') || request()->routeIs('invoices.*')) open @endif>

                    <summary class="is-drawer-close:tooltip is-drawer-close:tooltip-right"
                        data-tip="Stock Management">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="my-1.5 inline-block size-4">

                            <path
                                d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                            </path>
                            <path d="M3.27 6.96 12 12.01l8.73-5.05"></path>
                            <path d="M12 22.08V12"></path>

                        </svg>

                        <span class="is-drawer-close:hidden">
                            Stock Management
                        </span>
                    </summary>

                    <ul>

                        {{-- Vendors --}}
                        <li>
                            <a href="{{ route('vendors.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('vendors.*') ? 'sidebar-active' : '' }}"
                                data-tip="Vendors">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">
                                    Vendors
                                </span>
                            </a>
                        </li>


                        {{-- Invoices --}}
                        <li>
                            <a href="{{ route('invoices.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('invoices.*') ? 'sidebar-active' : '' }}"
                                data-tip="Invoices">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">
                                    Invoices
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('stock-adjustments.index') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('stock-adjustments.*') ? 'sidebar-active' : '' }}"
                                data-tip="Stock Adjustments">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                                    stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                                    class="my-1.5 inline-block size-4">

                                    <path d="M20 7h-9"></path>
                                    <path d="M14 17H5"></path>
                                    <circle cx="17" cy="17" r="3"></circle>
                                    <circle cx="7" cy="7" r="3"></circle>

                                </svg>

                                <span class="is-drawer-close:hidden">
                                    Stock Adjustments
                                </span>
                            </a>
                        </li>

                    </ul>
                </details>
            </li>


            {{-- =====================================================
                 SALES
            ====================================================== --}}
            <li>
                <a href="{{ route('sales.index') }}"
                    class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('sales.*') ? 'sidebar-active' : '' }}"
                    data-tip="Sales">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="my-1.5 inline-block size-4">

                        <path d="M3 3v18h18"></path>
                        <path d="M18 17V9"></path>
                        <path d="M13 17V5"></path>
                        <path d="M8 17v-3"></path>

                    </svg>

                    <span class="is-drawer-close:hidden">
                        Sales
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('reports.index') }}"
                    class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('reports.*') ? 'sidebar-active' : '' }}"
                    data-tip="Reports">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="my-1.5 inline-block size-4">

                        <path d="M3 3v18h18"></path>
                        <path d="M18 17V9"></path>
                        <path d="M13 17V5"></path>
                        <path d="M8 17v-3"></path>

                    </svg>

                    <span class="is-drawer-close:hidden">
                        Reports
                    </span>
                </a>
            </li>


            {{-- =====================================================
                 USERS
            ====================================================== --}}
            @if (auth()->user()->email === 'admin@gmail.com')
                <li>
                    <a href="{{ route('users.index') }}"
                        class="is-drawer-close:tooltip is-drawer-close:tooltip-right {{ request()->routeIs('users.*') ? 'sidebar-active' : '' }}"
                        data-tip="Users">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="my-1.5 inline-block size-4">

                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>

                        </svg>

                        <span class="is-drawer-close:hidden">
                            Users
                        </span>
                    </a>
                </li>
            @endif


            {{-- =====================================================
                 LOGOUT
            ====================================================== --}}
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="is-drawer-close:tooltip is-drawer-close:tooltip-right"
                        data-tip="Logout">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="my-1.5 inline-block size-4">

                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>

                        </svg>

                        <span class="is-drawer-close:hidden">
                            Logout
                        </span>

                    </button>
                </form>
            </li>

        </ul>
    </div>
</div>


{{-- =====================================================
     SIDEBAR STATE
====================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const drawer = document.getElementById('my-drawer-4');

        if (!drawer) {
            return;
        }

        const storageKey = 'sidebar-open';

        // Restore sidebar state
        const savedState = localStorage.getItem(storageKey);

        if (savedState === 'open') {
            drawer.checked = true;
        }

        if (savedState === 'closed') {
            drawer.checked = false;
        }

        // Save sidebar state
        drawer.addEventListener('change', function() {

            localStorage.setItem(
                storageKey,
                this.checked ? 'open' : 'closed'
            );

        });

        // Keep sidebar open when navigating
        document
            .querySelectorAll('.drawer-side a[href]')
            .forEach(function(link) {

                link.addEventListener('click', function() {

                    localStorage.setItem(
                        storageKey,
                        'open'
                    );

                });

            });

    });
</script>
