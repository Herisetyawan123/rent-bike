<!-- start sidebar -->
<div id="sideBar"
    class="relative flex flex-col flex-wrap bg-white border-r border-gray-300 p-6 flex-none w-64 md:-ml-64 md:fixed md:top-0 md:z-30 md:h-screen md:shadow-xl animated faster">

    <div class="flex flex-col">
        <div class="text-right hidden md:block mb-4">
            <button id="sideBarHideBtn">
                <i class="fad fa-times-circle"></i>
            </button>
        </div>

        @can('dashboard.view')
        <p class="uppercase text-xs text-gray-600 mb-4 tracking-wider">Dashboard</p>
        <a href="{{ route('admin-vendor.dashboard') }}"
            class="mb-3 capitalize font-medium text-sm transition ease-in-out duration-500 {{ request()->routeIs('admin-vendor.dashboard') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-home text-xs mr-2"></i>
            Dashboard
        </a>
        @endcan

        @canany(['bike.view', 'bike.create'])
        <p class="uppercase text-xs text-gray-600 mb-4 mt-4 tracking-wider">Penyewaan</p>
        @endcanany

        @can('bike.view')
        <a href="{{ route('admin-vendor.motors.index') }}"
            class="mb-3 capitalize font-medium text-sm transition duration-300 {{ request()->routeIs('admin-vendor.motors.index') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-motorcycle text-xs mr-2"></i>
            Daftar Motor
        </a>
        @endcan

        @can('bike.create')
        <a href="{{ route('admin-vendor.motors.create') }}"
            class="mb-3 capitalize font-medium text-sm transition duration-300 {{ request()->routeIs('admin-vendor.motors.create') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-plus text-xs mr-2"></i>
            Tambah Motor
        </a>
        @endcan

        @can('transaksi.view')
        <a href="{{ route('admin-vendor.transactions.index') }}"
            class="mb-3 capitalize font-medium text-sm transition duration-300 {{ request()->routeIs('admin-vendor.transactions.index') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-clipboard-list text-xs mr-2"></i>
            Transaksi
        </a>
        @endcan

        @can('kontrak.view')
        <a href="{{ route('admin-vendor.contracts.index') }}"
            class="mb-3 capitalize font-medium text-sm transition duration-300 {{ request()->routeIs('admin-vendor.contracts.index') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-file-signature text-xs mr-2"></i>
            Kontrak Peminjaman
        </a>
        @endcan

        @canany(['profile.view', 'profile.edit'])
        <p class="uppercase text-xs text-gray-600 mb-4 mt-4 tracking-wider">Akun Vendor</p>
        @endcanany

        @can('profile.edit')
        <a href="{{ route('admin-vendor.edit') }}"
            class="mb-3 capitalize font-medium text-sm transition duration-300 {{ request()->routeIs('admin-vendor.profiles.index') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-user-cog text-xs mr-2"></i>
            Profil Saya
        </a>
        @endcan

        {{-- Hidden: Notifikasi --}}
        @can('notifikasi.view')
        <a href="{{ route('admin-vendor.notifications.index') }}"
            class="mb-3 capitalize font-medium text-sm transition duration-300 {{ request()->routeIs('admin-vendor.notifications.index') ? 'text-teal-600' : 'hover:text-teal-600' }}">
            <i class="fad fa-bell text-xs mr-2"></i>
            Notifikasi
        </a>
        @endcan
    </div>
</div>
<!-- end sidebar -->
