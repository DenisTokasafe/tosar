<div class='overflow-y-scroll'>
    <flux:navlist variant="outline">
        <flux:navlist.group class="grid">

            @foreach ($Menus as $menu)

            {{-- Skip Dashboard jika guest --}}
            @if($menu->menu === 'Dashboard' && (auth()->guest() || !auth()->user()->hasRole('administrator')))
            @continue
            @endif
            {{-- Skip Administration jika bukan administrator --}}
            @if($menu->menu === 'Administrator' && (auth()->guest() || !auth()->user()->hasRole('administrator')))
            @continue
            @endif

            {{-- Skip Manhours kalau user tidak punya izin --}}
            @if($menu->menu === 'Manhours' && (!auth()->check() || !auth()->user()?->can('viewAny', \App\Models\Manhour::class)))
            @continue
            @endif

            @if(count($menu->SubMenu) > 0)
            <flux:navlist.group-list expandable route='{{ $menu->request_route }}' heading="{{ $menu->menu }}" class="grid ">
                @foreach ($menu->SubMenu as $submenu)
                @if(count($submenu->ExtraSubMenu) > 0)
                <flux:navlist.group-list expandable route='{{ $submenu->request_route }}' heading="{{ $submenu->menu }}" class="grid ">
                    @foreach ($submenu->ExtraSubMenu as $xsubmenu)
                    <flux:menu.item :href="route($xsubmenu->route)" :current="Request::is($xsubmenu->request_route)" wire:navigate>
                        {{ $xsubmenu->menu }}
                    </flux:menu.item>
                    @endforeach
                </flux:navlist.group-list>
                @elseif(!$submenu->route)

                <flux:menu.item :current="(($submenu->request_route!=null)? Request::is($submenu->request_route ):Request::is($submenu->route ))" icon="{{ $submenu->icon }}" wire:navigate>
                    {{ $submenu->menu }}
                </flux:menu.item>
                @else
                <flux:menu.item :href="route($submenu->route)" :current="(($submenu->request_route!=null)? Request::is($submenu->request_route ):request()->routeIs($submenu->route ))" icon="{{ $submenu->icon }}" wire:navigate>
                    {{ $submenu->menu }}
                </flux:menu.item>
                @endif
                @endforeach
            </flux:navlist.group-list>
            @elseif(!$menu->route)
            <flux:navlist.item icon="{{ $menu->icon ?: 'ban' }}" :current="Request::is($menu->request_route)" wire:navigate>
                {{ $menu->menu }}
            </flux:navlist.item>
            @else
            <flux:navlist.item icon="{{ $menu->icon }}" :href="route($menu->route)" :current="Request::is($menu->request_route )" wire:navigate>
                {{ $menu->menu }}
            </flux:navlist.item>
            @endif

            @endforeach


        </flux:navlist.group>
    </flux:navlist>




</div>
