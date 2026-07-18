<div class='overflow-y-scroll'>
    <ul class="w-full menu menu-md bg-base-200 rounded-box">
        @foreach ($Menus as $menu)
        {{-- Logika Filter --}}
        @if ($menu->menu === 'Dashboard' && (auth()->guest() || !auth()->check()))
        @continue
        @endif
        @if ($menu->menu === 'Administrator' && (auth()->guest() || !auth()->user()->hasRole('administrator')))
        @continue
        @endif
        @if ($menu->menu === 'Laporan Insiden' && (auth()->guest() || !auth()->user()->hasRole('administrator')))
        @continue
        @endif
        @if ($menu->menu === 'Manhours' && (!auth()->check() || !auth()->user()?->can('viewAny', \App\Models\Manhour::class)))
        @continue
        @endif
        @if ($menu->menu === 'WPI' && !auth()->check())
        @continue
        @endif
        @if (
        $menu->menu === 'MCU Schedule' &&
        (auth()->guest() ||
        !auth()->user()->hasAnyRole(['administrator', 'Medical Staff'])))
        @continue
        @endif
        @if (
        $menu->menu === 'Event General' &&
        (auth()->guest() ||
        !auth()->user()->hasAnyRole(['administrator', 'Moderator'])))
        @continue
        @endif

        @php
        $menuEffects = 'transition-all duration-300 ease-in-out hover:-translate-x-1 hover:shadow-lg hover:z-10 rounded-lg';
        $isActiveMenu = Request::is($menu->request_route);
        @endphp

        {{-- LEVEL 1: Group dengan SubMenu --}}
        @if (count($menu->subMenus) > 0)
        <li wire:key="menu-group-{{ $menu->id }}">
            {{-- Tambahkan atribut "open" jika menu ini sedang aktif agar otomatis terbuka --}}
            <details {{ $isActiveMenu ? 'open' : '' }}>
                <summary class="{{ $menuEffects }} {{ $isActiveMenu ? 'active' : '' }}">
                    {{-- Ganti icon placeholder ini dengan pemanggil icon Anda, misal: <x-icon name="{{ $menu->icon }}" /> --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                    </svg>
                    {{ __($menu->menu) }}
                </summary>

                <ul>
                    @foreach ($menu->subMenus as $submenu)
                    @php
                    $isActiveSub = ($submenu->request_route != null) ? Request::is($submenu->request_route) : request()->routeIs($submenu->route);
                    @endphp

                    {{-- LEVEL 2: Extra SubMenu --}}
                    @if (count($submenu->ExtraSubMenu) > 0)
                    <li wire:key="submenu-group-{{ $submenu->id }}">
                        <details {{ $isActiveSub ? 'open' : '' }}>
                            <summary class="{{ $menuEffects }} {{ $isActiveSub ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                                </svg>
                                {{ __($submenu->menu) }}
                            </summary>

                            <ul>
                                @foreach ($submenu->ExtraSubMenu as $xsubmenu)
                                <li wire:key="xsubmenu-item-{{ $xsubmenu->id }}">
                                    <a href="{{ route($xsubmenu->route) }}"
                                        class="{{ $menuEffects }} {{ Request::is($xsubmenu->request_route) ? 'active' : '' }}">
                                        {{ __($xsubmenu->menu) }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </details>
                    </li>

                    {{-- LEVEL 2: SubMenu Biasa --}}
                    @else
                    <li wire:key="submenu-item-{{ $submenu->id }}">
                        <a href="{{ $submenu->route ? route($submenu->route) : '#' }}"
                            class="{{ $menuEffects }} {{ $isActiveSub ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            {{ __($submenu->menu) }}
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </details>
        </li>

        {{-- LEVEL 1: Menu Single --}}
        @else
        <li wire:key="menu-item-{{ $menu->id }}">
            <a href="{{ $menu->route ? route($menu->route) : '#' }}"
                class="{{ $menuEffects }} {{ $isActiveMenu ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                {{ __($menu->menu) }}
            </a>
        </li>
        @endif
        @endforeach
    </ul>
</div>