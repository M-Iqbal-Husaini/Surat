@php
    use Illuminate\Support\Facades\Route;

    function menu_classes(string $activeWhen) : string {
        return request()->routeIs($activeWhen)
            ? 'bg-[#EEF4FF] text-[#3D64FF] font-semibold'
            : 'text-slate-600 hover:bg-[#EEF4FF] font-semibold';
    }

    function icon_classes(string $activeWhen) : string {
        return request()->routeIs($activeWhen)
            ? 'text-[#3D64FF]'
            : 'text-slate-500 group-hover:text-slate-600';
    }

    function first_existing_route(array $candidates): ?string
    {
        if (!auth()->check()) return null;

        $user = auth()->user();

        // PRIORITAS ROUTE SESUAI ROLE
        $rolePriority = [];

        if ($user->hasRole('admin'))            $rolePriority[] = 'admin.';
        if ($user->hasRole('verifikator'))      $rolePriority[] = 'verifikator.';
        if ($user->hasRole('sekretaris_direktur'))  $rolePriority[] = 'sekretaris-direktur.';
        if ($user->hasRole('pimpinan'))         $rolePriority[] = 'pimpinan.';
        if ($user->hasRole('pembuat_surat'))    $rolePriority[] = 'pembuat-surat.';

        // Cari route yang cocok dengan role dulu
        foreach ($rolePriority as $prefix) {
            foreach ($candidates as $route) {
                if (str_starts_with($route, $prefix) && Route::has($route)) {
                    return $route;
                }
            }
        }

        // Fallback (jaga-jaga)
        foreach ($candidates as $route) {
            if (Route::has($route)) return $route;
        }

        return null;
    }

    function menu_icon_svg($name) {
        return match ($name) {
            'home' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25' /></svg>",
            'inbox' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75' /></svg>",
            'send' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z' /></svg>",
            'list' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z' /></svg>",
            'template' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z' /></svg>",
            'users' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z' /></svg>",
            'chev' => "<svg class='w-5 h-5' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5' /></svg>",
            'monitor' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25' /></svg>",
            'struc' => "<svg class='w-6 h-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z' /></svg>",
            default => '',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE CHECK
    |--------------------------------------------------------------------------
    */
    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');

    /*
    |--------------------------------------------------------------------------
    | MENU CONFIG
    |--------------------------------------------------------------------------
    */
    $menus = [
        [
            'type'   => 'link',
            'label'  => 'Dashboard',
            'routes' => ['dashboard'],
            'active' => 'dashboard',
            'icon'   => 'home',
            'can'    => null,
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | ADMIN: Monitoring Surat (TANPA DISPOSISI)
    |--------------------------------------------------------------------------
    */
    if ($isAdmin) {
        $menus[] = [
            'type'   => 'group',
            'label'  => 'Monitoring Surat',
            'icon'   => 'monitor',
            'active' => ['admin.surat-masuk.*', 'admin.surat-keluar.*'],
            'can'    => null,
            'items'  => [
                [
                    'type'   => 'link',
                    'label'  => 'Surat Masuk',
                    'routes' => ['admin.surat-masuk.index'],
                    'active' => 'admin.surat-masuk.*',
                    'icon'   => 'inbox',
                    'can'    => 'view surat masuk',
                ],
                [
                    'type'   => 'link',
                    'label'  => 'Surat Keluar',
                    'routes' => ['admin.surat-keluar.index'],
                    'active' => 'admin.surat-keluar.*',
                    'icon'   => 'send',
                    'can'    => 'view surat keluar',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | NON-ADMIN: Surat Masuk & Keluar
    |--------------------------------------------------------------------------
    */
    if (!$isAdmin) {
        $menus[] = [
            'type'   => 'link',
            'label'  => 'Surat Masuk',
            'routes' => [
                'pembuat-surat.surat-masuk.index',
                'sekretaris-direktur.surat-masuk.index',
                'verifikator.surat-masuk.index',
                'pimpinan.surat-masuk.index',
            ],
            'active' => '*.surat-masuk.*',
            'icon'   => 'inbox',
            'can'    => 'view surat masuk',
        ];

        $menus[] = [
            'type'   => 'link',
            'label'  => 'Surat Keluar',
            'routes' => [
                'pembuat-surat.surat-keluar.index',
                'sekretaris-direktur.surat-keluar.index',
                'verifikator.surat-keluar.index',
                'pimpinan.surat-keluar.index',
            ],
            'active' => '*.surat-keluar.*',
            'icon'   => 'send',
            'can'    => 'view surat keluar',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DISPOSISI — HANYA PEMBUAT & VERIFIKATOR
    |--------------------------------------------------------------------------
    */
    if (
        !$isAdmin &&
        auth()->check() &&
        auth()->user()->hasAnyRole(['pembuat_surat', 'verifikator'])
    ) {
        $menus[] = [
            'type'   => 'link',
            'label'  => 'Disposisi',
            'routes' => [
                'pembuat-surat.disposisi.index',
                'verifikator.disposisi.index',
            ],
            'active' => '*.disposisi.*',
            'icon'   => 'list',
            'can'    => 'view disposisi',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN MASTER DATA
    |--------------------------------------------------------------------------
    */
    if ($isAdmin) {
        $menus[] = [
            'type'   => 'link',
            'label'  => 'Template Surat',
            'routes' => ['admin.template-surat.index'],
            'active' => 'admin.template-surat.*',
            'icon'   => 'template',
            'can'    => 'manage templates',
        ];

        $menus[] = [
            'type'   => 'link',
            'label'  => 'Struktur Unit',
            'routes' => ['admin.struktur-unit.index'],
            'active' => 'admin.struktur-unit.*',
            'icon'   => 'struc',
            'can'    => 'manage struktur unit',
        ];

        $menus[] = [
            'type'   => 'link',
            'label'  => 'Jalur Surat',
            'routes' => ['admin.jalur-surat.index'],
            'active' => 'admin.jalur-surat.*',
            'icon'   => 'list',
            'can'    => 'manage jalur surat',
        ];

        $menus[] = [
            'type'   => 'link',
            'label'  => 'Akun User',
            'routes' => ['admin.users.index'],
            'active' => 'admin.users.*',
            'icon'   => 'users',
            'can'    => 'manage users',
        ];
    }
@endphp

{{-- SIDEBAR --}}
<aside
    class="fixed inset-y-0 left-0 z-40 bg-white border-r border-slate-200
           transform transition-all duration-200 ease-in-out
           -translate-x-full lg:translate-x-0 flex flex-col"
    :class="[
        sidebarCollapsed ? 'lg:w-20 w-72' : 'lg:w-72 w-72',
        (sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0')
    ]"
    x-cloak
>
    {{-- HEADER LOGO --}}
    <div class="flex items-center justify-between h-16 px-6 pt-2">
        <div class="flex items-center space-x-3">
            <div class="h-8 w-8 flex items-center justify-center">
                <img src="{{ asset('images/Logo-Polbeng.png') }}" alt="Logo" class="h-8 w-8 object-contain">
            </div>
            <div x-show="!sidebarCollapsed" x-transition.opacity.duration.150>
                <div class="text-[20px] font-bold text-slate-900 leading-tight">E-SURAT</div>
            </div>
        </div>

        <button class="lg:hidden p-1.5 rounded-lg hover:bg-slate-100" @click="sidebarOpen=false">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- MENU LABEL --}}
    <div class="h-6 mt-4 mb-3 px-6 flex items-center">
        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150
              class="text-[12px] font-medium text-slate-400 tracking-[0.16em]">
            MENU
        </span>
        <button x-show="sidebarCollapsed" x-transition.opacity.duration.150 class="w-full flex justify-center items-center h-6 hover:bg-slate-100 rounded-lg"> <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 16 16"> <circle cx="2.5" cy="8" r="1.5" /> <circle cx="8" cy="8" r="1.5" /> <circle cx="13.5" cy="8" r="1.5" /> </svg> </button>
    </div>

    {{-- MENU --}}
    <nav class="px-3 space-y-1 flex-1">
        @foreach ($menus as $m)
            @php
                $type = $m['type'] ?? 'link';

                // permission filter aman
                $allowed = true;
                $can = $m['can'] ?? null;
                if (!is_null($can)) {
                    $allowed = auth()->check() && auth()->user()->can($can);
                }
            @endphp

            {{-- LINK --}}
            @if ($type === 'link')
                @php
                    $routeName = isset($m['routes']) ? first_existing_route($m['routes']) : null;
                @endphp

                @if ($allowed && $routeName)
                    <a href="{{ route($routeName) }}"
                       class="group flex items-center h-11 rounded-xl text-[15px] transition-colors
                              {{ menu_classes($m['active']) }}"
                       :class="sidebarCollapsed ? 'justify-center' : 'pl-5 pr-3'">

                        <span class="{{ icon_classes($m['active']) }}" :class="sidebarCollapsed ? '' : 'mr-3'">
                            {!! menu_icon_svg($m['icon']) !!}
                        </span>

                        <span x-show="!sidebarCollapsed">{{ $m['label'] }}</span>
                    </a>
                @endif
            @endif

            {{-- GROUP (submenu) --}}
            @if ($type === 'group' && $allowed)
                @php
                    // group aktif jika salah satu pattern aktif
                    $groupActive = false;
                    foreach (($m['active'] ?? []) as $pat) {
                        if (request()->routeIs($pat)) { $groupActive = true; break; }
                    }
                @endphp

                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" class="space-y-1">
                    <button
                            type="button"
                            class="w-full group flex items-center h-11 rounded-xl text-[15px] transition-colors
                                {{ $groupActive ? 'bg-[#EEF4FF] text-[#3D64FF] font-semibold' : 'text-slate-600 hover:bg-[#EEF4FF] font-semibold' }}"
                            :class="sidebarCollapsed ? 'justify-center' : 'pl-5 pr-3'"
                            @click="
                                if (sidebarCollapsed) {
                                    sidebarCollapsed = false;    // buka sidebar (desktop)
                                    open = true;                 // pastikan submenu terbuka
                                } else {
                                    open = !open;                // normal toggle
                                }
                            "
                        >

                        <span class="{{ $groupActive ? 'text-[#3D64FF]' : 'text-slate-500 group-hover:text-slate-600' }}"
                              :class="sidebarCollapsed ? '' : 'mr-3'">
                            {!! menu_icon_svg($m['icon']) !!}
                        </span>

                        <span x-show="!sidebarCollapsed" class="flex-1 text-left">
                            {{ $m['label'] }}
                        </span>

                        <span x-show="!sidebarCollapsed" class="text-slate-600 transition-transform" :class="open ? 'rotate-180' : ''">
                            {!! menu_icon_svg('chev') !!}
                        </span>
                    </button>

                    <div x-show="open && !sidebarCollapsed" x-cloak class="pl-3 space-y-1">
                        @foreach (($m['items'] ?? []) as $it)
                            @php
                                $itAllowed = true;
                                $itCan = $it['can'] ?? null;
                                if (!is_null($itCan)) {
                                    $itAllowed = auth()->check() && auth()->user()->can($itCan);
                                }
                                $itRoute = isset($it['routes']) ? first_existing_route($it['routes']) : null;
                            @endphp

                            @if ($itAllowed && $itRoute)
                                <a href="{{ route($itRoute) }}"
                                   class="group flex items-center h-10 rounded-xl text-[15px] transition-colors
                                          {{ menu_classes($it['active']) }} pl-11 pr-3">

                                    <span class="{{ icon_classes($it['active']) }} mr-3">
                                        {!! menu_icon_svg($it['icon']) !!}
                                    </span>

                                    <span>{{ $it['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>
</aside>
