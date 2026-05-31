<div class="account-sidebar rounded-3 overflow-hidden shadow-sm">

    {{-- User Header --}}
    <div class="text-center p-4" style="background:linear-gradient(135deg,#1a3a1a,#2d6a2d);">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
             style="width:68px;height:68px;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.35);">
            <span style="font-size:26px;font-weight:700;color:#fff;line-height:1;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
        </div>
        <div class="text-white fw-semibold fs-16 lh-1 mb-1">{{ Auth::user()->name }}</div>
        <div class="fs-13" style="color:rgba(255,255,255,0.65);">{{ Auth::user()->email }}</div>
    </div>

    {{-- Navigation --}}
    <div class="bg-white p-3">
        @php
            $route = request()->routeIs('user.index') ? 'dashboard'
                   : (request()->routeIs('user.orders*') ? 'orders'
                   : (request()->routeIs('wishlist.*') ? 'wishlist' : ''));
        @endphp

        @php
            $navItems = [
                ['icon' => 'fa-gauge-high',         'label' => 'Dashboard',       'route' => 'user.index',    'key' => 'dashboard'],
                ['icon' => 'fa-bag-shopping',        'label' => 'My Orders',       'route' => 'user.orders',   'key' => 'orders'],
                ['icon' => 'fa-heart',               'label' => 'Wishlist',        'route' => 'wishlist.index','key' => 'wishlist'],
            ];
        @endphp

        <ul class="list-unstyled mb-0">
            @foreach($navItems as $item)
            <li class="mb-1">
                <a href="{{ route($item['route']) }}"
                   class="d-flex align-items-center gap-3 px-3 py-2 rounded-2 text-decoration-none fw-500 fs-14 {{ $route === $item['key'] ? 'text-white' : 'text-dark' }}"
                   style="{{ $route === $item['key'] ? 'background:#2d6a2d;' : '' }} transition:background 0.2s;">
                    <i class="fa-solid {{ $item['icon'] }} fs-14" style="width:16px;text-align:center;color:{{ $route === $item['key'] ? '#fff' : '#4CAF50' }};"></i>
                    {{ $item['label'] }}
                </a>
            </li>
            @endforeach

            <li class="mt-2 pt-2 border-top">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="d-flex align-items-center gap-3 px-3 py-2 rounded-2 w-100 border-0 bg-transparent text-start fw-500 fs-14"
                            style="color:#c0392b;cursor:pointer;">
                        <i class="fa-solid fa-right-from-bracket fs-14" style="width:16px;text-align:center;color:#c0392b;"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
