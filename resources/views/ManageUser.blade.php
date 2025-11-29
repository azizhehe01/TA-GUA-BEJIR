<x-layouts.chatApp :title="__('Kelola Pengguna')">
    <div class="h-full w-full p-6 bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-slate-600 dark:text-white">Manajemen pengguna</h1>
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-orange-400 to-amber-300 text-yellow-800 px-6 py-2 rounded-lg flex items-center gap-2 font-semibold shadow ">
                    <span>+</span> Tambah Pengguna
                </a>
            </div>

            <!-- Tabs -->
            <div class="flex gap-6 mb-6 border border-slate-700/40">
                
            </div>

            <!-- Search & Filter Bar -->
            <div class="flex gap-4 mb-6 items-center">
                <div class="flex-1 relative">
                    <input type="text" placeholder="Search" class="w-full px-4 py-2 bg-white dark:bg-slate-900 shadow-lg backdrop-blur-md  border border-slate-700/40 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <flux:icon.magnifying-glass class="absolute right-3 top-2.5 w-5 h-5 text-gray-300 " />
                </div>
                <button class="px-4 py-2 shadow-lg backdrop-blur-md bg-white dark:bg-slate-900 border border-slate-700/40 rounded-lg hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 hover:text-yellow-800 hover:border-transparent flex items-center gap-2">
                    <flux:icon.funnel class="w-5 h-5" />
                    Sort by
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-slate-900 rounded-lg shadow-lg backdrop-blur-md border border-slate-700/40 overflow-hidden">
                <table class="w-full table-fixed">
                    <thead class="bg-white dark:bg-slate-900 border-b border-slate-700/40 text-slate-600 dark:text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium  w-[40%]">USER</th>
                            <th class="px-6 py-3 text-left text-sm font-medium  w-[20%]">STATUS</th>
                            <th class="px-6 py-3 text-left text-sm font-medium  w-[20%]">ROLE</th>
                            <th class="px-6 py-3 text-right text-sm font-medium  w-[20%]">ACTIONS</th>
                        </tr>
                    </thead>
                
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <!-- USER -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-pink-500 text-white flex items-center justify-center font-bold">
                                        TW
                                    </div>
                                    <div class="leading-tight text-slate-600 dark:text-white">
                                        <p class="font-medium ">Tom Williams</p>
                                        <p class="text-sm ">tomwilliams@vendor.com</p>
                                    </div>
                                </div>
                            </td>
                        
                            <!-- STATUS -->
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                                    Active
                                </span>
                            </td>
                        
                            <!-- ROLE -->
                            <td class="px-6 py-4">
                                <button class="flex items-center gap-1 text-slate-600 dark:text-white  hover:text-orange-400">
                                    Owner
                                    <flux:icon.chevron-down class="w-4 h-4" />
                                </button>
                            </td>
                        
                            <!-- ACTIONS -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end text-slate-600 dark:text-white">
                                    <button class="px-4 py-1 border border-gray-300 rounded hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 hover:text-yellow-800 hover:border-0 text-sm">
                                        Deactivate
                                    </button>
                                    <button class="px-4 py-1 border border-gray-300 rounded hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 hover:text-yellow-800 hover:border-0 text-sm">
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>