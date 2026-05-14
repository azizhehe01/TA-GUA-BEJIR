<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <style>
        .chat-box {
            scrollbar-width: none;
        }
        .chat-box::-webkit-scrollbar {
            display: none;
        }
    </style>
    <body class="min-h-screen bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-slate-950">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>
            

            <flux:navlist variant="outline">
                <flux:navlist.item icon="sparkles" :href="route('AskAI')" :current="request()->routeIs('AskAI') && !request()->has('id')" wire:navigate>
                    <b>{{ __('Chat Baru') }}</b>
                </flux:navlist.item>
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home"  :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                @if(auth()->user() && auth()->user()->is_admin)
                    <flux:navlist.group :heading="__('Manage User')" class="grid">
                        <flux:navlist.item 
                            icon="user-plus"
                            :href="route('manage-user')"
                            :current="request()->routeIs('manage-user')"
                            wire:navigate>
                            {{ __('Kelola user') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif

                <flux:navlist.group :heading="__('History chat kamu ada disini')" class="grid">
                    {{-- Container Scrollable --}}
                    <div class="max-h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach(auth()->user()->conversations()->orderBy('updated_at', 'desc')->get() as $chat)
                            <div class="group relative flex items-center">

                                {{-- Item Chat --}}
                                <flux:navlist.item 
                                    :href="route('AskAI', ['id' => $chat->id])" 
                                    :current="request()->query('id') == $chat->id"
                                    wire:navigate
                                    class="flex-1 pr-8"
                                >
                                    {{ Str::limit($chat->title, 20) }}
                                </flux:navlist.item>

                                <div class="absolute right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:modal.trigger name="delete-chat-{{ $chat->id }}">
                                        <button type="button" class="text-slate-400 hover:text-red-500">
                                            <flux:icon.trash variant="micro" />
                                        </button>
                                    </flux:modal.trigger>
                                </div>

                                {{-- Modal --}}
                                <flux:modal 
                                    name="delete-chat-{{ $chat->id }}" 
                                    class="min-w-[26rem] p-4 !bg-transparent border-0 shadow-none [&_[aria-label='Close_modal']]:!hidden"
                                >
                                    <div class="!bg-white dark:!bg-white rounded-3xl px-8 py-9 flex flex-col items-center text-center shadow-xl">
                                
                                        {{-- Illustration --}}
                                        <img 
                                            src="{{ asset('images/delete-chat.png') }}" 
                                            alt="Delete Chat"
                                            class="w-50 h-50 object-contain mb-4 select-none"
                                        >
                                
                                        {{-- Title --}}
                                        <h2 class="text-xl font-bold text-zinc-800">
                                            Hapus Riwayat Chat?
                                        </h2>
                                
                                        {{-- Subtitle --}}
                                        <p class="text-sm leading-relaxed text-zinc-500 mt-2 max-w-xs">
                                            Yakin mau hapus chat ini?  
                                            Tindakan ini tidak bisa dibatalkan.
                                        </p>
                                
                                        {{-- Indicator --}}
                                        <div class="flex gap-1.5 mt-5">
                                            <div class="w-2 h-2 rounded-full bg-zinc-800"></div>
                                            <div class="w-2 h-2 rounded-full bg-zinc-300"></div>
                                            <div class="w-2 h-2 rounded-full bg-zinc-300"></div>
                                        </div>
                                
                                        {{-- Buttons --}}
                                        <div class="flex items-center justify-between w-full mt-8 px-2">
                                
                                            <flux:modal.close>
                                                <button 
                                                    type="button"
                                                    class="text-sm font-semibold text-zinc-600 hover:text-black transition-colors"
                                                >
                                                    Cancel
                                                </button>
                                            </flux:modal.close>
                                
                                            <form action="{{ route('chat.destroy', $chat->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                
                                                <button 
                                                    type="submit"
                                                    class="text-sm font-semibold text-red-500 hover:text-red-600 transition-colors"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                
                                        </div>
                                    </div>
                                </flux:modal>

                            </div>
                        @endforeach
                    </div>
                </flux:navlist.group>

            </flux:navlist>

            
            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/azizhehe01/TA-GUA-BEJIR.git" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                    data-test="sidebar-menu-button"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        
    </body>
</html>
