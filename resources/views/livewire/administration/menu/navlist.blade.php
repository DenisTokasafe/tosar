<div class='overflow-y-scroll'>
    <flux:navlist variant="outline">
        <flux:navlist.group class="grid">
            @foreach ($Menus as $menu)
                {{-- Logika Filter (Dashboard, Admin, Manhours) tetap sama --}}
                @if ($menu->menu === 'Dashboard' && (auth()->guest() || !auth()->check()))
                    @continue
                @endif
                @if ($menu->menu === 'Administrator' && (auth()->guest() || !auth()->user()->hasRole('administrator')))
                    @continue
                @endif
                @if ($menu->menu === 'Manhours' && (!auth()->check() || !auth()->user()?->can('viewAny', \App\Models\Manhour::class)))
                    @continue
                @endif
                @if ($menu->menu === 'WPI' && !auth()->check())
                    @continue
                @endif
                {{-- LEVEL 1: Group dengan SubMenu --}}
                @if (count($menu->subMenus) > 0)
                    <flux:navlist.group-list wire:key="menu-group-{{ $menu->id }}" expandable
                        route='{{ $menu->request_route }}' heading="{{ $menu->menu }}" class="grid">
                        @foreach ($menu->subMenus as $submenu)
                            {{-- LEVEL 2: Extra SubMenu --}}
                            @if (count($submenu->ExtraSubMenu) > 0)
                                <flux:navlist.group-list wire:key="submenu-group-{{ $submenu->id }}" expandable
                                    route='{{ $submenu->request_route }}' heading="{{ $submenu->menu }}" class="grid">
                                    @foreach ($submenu->ExtraSubMenu as $xsubmenu)
                                        <flux:menu.item wire:key="xsubmenu-item-{{ $xsubmenu->id }}"
                                            :href="route($xsubmenu->route)"
                                            :current="Request::is($xsubmenu->request_route)" wire:navigate>
                                            {{ $xsubmenu->menu }}
                                        </flux:menu.item>
                                    @endforeach
                                </flux:navlist.group-list>

                                {{-- LEVEL 2: SubMenu Biasa --}}
                            @elseif(!$submenu->route)
                                <flux:menu.item wire:key="submenu-item-{{ $submenu->id }}"
                                    :current="(($submenu->request_route!=null)? Request::is($submenu->request_route ):Request::is($submenu->route ))"
                                    icon="{{ $submenu->icon }}" wire:navigate>
                                    {{ $submenu->menu }}
                                </flux:menu.item>
                            @else
                                <flux:menu.item wire:key="submenu-item-{{ $submenu->id }}"
                                    :href="route($submenu->route)"
                                    :current="(($submenu->request_route!=null)? Request::is($submenu->request_route ):request()->routeIs($submenu->route ))"
                                    icon="{{ $submenu->icon }}" wire:navigate>
                                    {{ $submenu->menu }}
                                </flux:menu.item>
                            @endif
                        @endforeach
                    </flux:navlist.group-list>

                    {{-- LEVEL 1: Menu Single --}}
                @elseif(!$menu->route)
                    <flux:navlist.item wire:key="menu-item-{{ $menu->id }}" icon="{{ $menu->icon ?: 'ban' }}"
                        :current="Request::is($menu->request_route)" wire:navigate>
                        {{ $menu->menu }}
                    </flux:navlist.item>
                @else
                    <flux:navlist.item wire:key="menu-item-{{ $menu->id }}" icon="{{ $menu->icon }}"
                        :href="route($menu->route)" :current="Request::is($menu->request_route)" wire:navigate>
                        {{ $menu->menu }}
                    </flux:navlist.item>
                @endif
            @endforeach
        </flux:navlist.group>
    </flux:navlist>
</div>
