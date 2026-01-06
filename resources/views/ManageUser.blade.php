<x-layouts.chatApp :title="__('Kelola Pengguna')">
    <div class="h-full w-full p-6 bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-slate-600 dark:text-white">Manajemen pengguna</h1>
                <div class="bg-gradient-to-r from-orange-400 to-amber-300 text-yellow-800 px-6 py-2 rounded-lg flex items-center gap-2 font-semibold shadow ">
                    Hi, {{ Auth::user()->name }}!
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex gap-6 mb-6 border border-slate-700/40">
                
            </div>

            <!-- Search & Filter Bar -->
            <div class="flex gap-4 mb-6 items-center">
                <div class="flex-1 relative">
                    <form method="GET" action="{{ route('manage-user') }}" class="flex-1 relative">
                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search"
                            class="w-full px-4 py-2 bg-white dark:bg-slate-900 shadow-lg backdrop-blur-md  
                                   border border-slate-700/40 placeholder-slate-400 dark:placeholder-slate-500 
                                   rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                        <flux:icon.magnifying-glass class="absolute right-3 top-2.5 w-5 h-5 text-gray-300" />
                    </form>
                </div>
                @php
                    $newDirection = ($direction === 'asc') ? 'desc' : 'asc';
                @endphp
                
                <a href="{{ route('manage-user', [
                        'search' => request('search'),
                        'sort' => 'name',
                        'direction' => $newDirection
                    ]) }}"
                    class="px-4 py-2 shadow-lg backdrop-blur-md bg-white dark:bg-slate-900 border border-slate-700/40 rounded-lg hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 hover:text-yellow-800 hover:border-transparent flex items-center gap-2">
                
                    <flux:icon.funnel class="w-5 h-5" />
                    
                    Sort by
                
                    {{-- ICON PANAH DARI FLUX --}}
                    @if ($direction === 'asc')
                        <flux:icon.chevron-up class="w-4 h-4" />
                    @else
                        <flux:icon.chevron-down class="w-4 h-4" />
                    @endif
                </a>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-slate-900 rounded-lg shadow-lg backdrop-blur-md border border-slate-700/40 overflow-x-auto">
                <table class="min-w-[900px] w-full">
                    <thead class="bg-white dark:bg-slate-900 border-b border-slate-700/40 text-slate-600 dark:text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium  w-[40%]">PENGGUNA</th>
                            <th class="px-6 py-3 text-left text-sm font-medium  w-[20%]">STATUS</th>
                            <th class="px-6 py-3 text-left text-sm font-medium  w-[20%]">ROLE</th>
                            <th class="px-6 py-3 text-right text-sm font-medium  w-[20%]">ACTIONS</th>
                        </tr>
                    </thead>
                
                    <tbody class="divide-y divide-gray-200">

                    @foreach ($users as $user)
                    
                    <tr>
                        <!-- USER -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $colors = ['bg-pink-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-red-500', 'bg-indigo-500'];

                                    $index = crc32($user->name) % count($colors);
                                    $userColor = $colors[$index];
                                @endphp

                                <div class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold {{ $userColor }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="leading-tight text-slate-600 dark:text-white">
                                    <p class="font-medium ">{{ $user->name }}</p>
                                    <p class="text-sm ">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                    
                        <!-- STATUS -->
                        <td class="px-6 py-4">
                            @if($user->active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full font-medium">
                                    Inactive
                                </span>
                            @endif
                        </td>
                    
                        <!-- ROLE -->
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('manage-user.update-role', $user) }}" 
                                  class="relative inline-block w-32 group">
                                @csrf
                            
                                <select name="is_admin" onchange="this.form.submit()"
                                    class="appearance-none w-full border border-gray-300 rounded 
                                           bg-white dark:bg-slate-900 text-slate-600 dark:text-white
                                           hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                                           hover:text-yellow-800 hover:border-0 text-sm px-3 py-1 pr-8">
                                    <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>User</option>
                                    <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>Admin</option>
                                </select>
                            
                                <!-- Icon ikut hover -->
                                <flux:icon.chevron-down
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 
                                           text-slate-500 stroke-2 pointer-events-none
                                           group-hover:text-yellow-800" />
                            </form>
                        </td>

                    
                        <!-- ACTIONS -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex gap-2 justify-end">
                    
                                <!-- Toggle status -->
                                <form method="POST" action="{{ route('manage-user.toggle-status', $user) }}">
                                    @csrf
                                    <button class="px-4 py-1 border border-gray-300 rounded hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 hover:text-yellow-800 hover:border-0 text-sm">
                                        {{ $user->active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                    
                                <!-- Delete -->
                                <form method="POST" action="{{ route('manage-user.delete', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-4 py-1 border border-red-500 text-red-500 rounded text-sm hover:bg-red-500 hover:text-white">
                                        Delete
                                    </button>
                                </form>
                    
                            </div>
                        </td>
                    
                    </tr>
                    
                    @endforeach
                    
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</x-layouts.app>