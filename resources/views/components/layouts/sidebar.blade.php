@php
use App\Models\Menu;
$menus = Menu::whereNull('parent_id')
    ->where('is_active', true)
    ->orderBy('order')
    ->with(['children' => function ($query) {
        $query->where('is_active', true)->orderBy('order');
    }])
    ->get();
@endphp

<div class="sidebar">
    <h2 class="text-center text-xl font-bold py-4 border-b border-gray-700">RBAC Panel</h2>

    @foreach ($menus as $menu)
        <div>
            <a href="{{ $menu->route ? route($menu->route) : '#' }}"
               class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs($menu->route) ? 'bg-sky-600' : '' }}">
                @if($menu->icon)
                    <i class="{{ $menu->icon }} mr-2"></i>
                @endif
                {{ $menu->name }}
            </a>

            {{-- Jika punya sub-menu --}}
            @if ($menu->children->count())
                <div class="ml-4 border-l border-gray-600 pl-3">
                    @foreach ($menu->children as $child)
                        <a href="{{ $child->route ? route($child->route) : '#' }}"
                           class="block px-3 py-1 text-sm hover:bg-slate-700 {{ request()->routeIs($child->route) ? 'bg-sky-500' : '' }}">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
