<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body>
    <div class="min-h-screen bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800 ">
        <!-- Main Container -->         
            <!-- Header Navigation -->
            <nav class="flex items-center justify-between p-6 md:p-8 ">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/icon-siem-aziz.png') }}" 
                             alt="logo siem-aziz" 
                             class="w-full h-full object-contain">
                    </div>
                </div>
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#"
                       class="font-medium text-slate-700 dark:text-slate-200 transition 
                              hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                              hover:bg-clip-text hover:text-transparent">
                        Tentang Kami
                    </a>
                    <a href="#"
                       class="font-medium text-slate-700 dark:text-slate-200 transition 
                              hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                              hover:bg-clip-text hover:text-transparent">
                        Lainnya 
                    </a>
                    <a href="#"
                       class="font-medium text-slate-700 dark:text-slate-200 transition 
                              hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                              hover:bg-clip-text hover:text-transparent">
                        Layanan
                    </a>
                </div>
                <!-- Buttons -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('register') }}"
                           class="hidden md:block px-6 py-2 border-2 border-orange-400 text-orange-400 rounded-full font-semibold hover:bg-purple-50 transition">
                            SIGN UP
                        </a>

                        <a href="{{ route('login') }}"
                           class="px-6 py-2 bg-gradient-to-r from-orange-400 to-amber-300 text-white rounded-full font-semibold hover:bg-purple-700 shadow transition">
                            LOGIN
                        </a>
                    @endguest
                    <button onclick="toggleMenu()" class="md:hidden p-2 text-orange-400">
                        <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                </div>
            </nav>
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden flex flex-col gap-4 p-4 ">  
                <a href="#"
                   class="font-medium text-slate-700 transition 
                          hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                          hover:bg-clip-text hover:text-transparent">
                    Tentang Kami
                </a>
                <a href="#"
                   class="font-medium text-slate-700 transition 
                          hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                          hover:bg-clip-text hover:text-transparent">
                    Layanan
                </a>
                <a href="#"
                   class="font-medium text-slate-700 transition 
                          hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-200 
                          hover:bg-clip-text hover:text-transparent">
                    Lainnya
                </a>
                @guest
                <button class="w-full px-6 py-2 border-2 border-orange-400 text-orange-400 rounded-full font-semibold hover:bg-orange-50 transition">
                    SIGN UP
                </button>
                @endguest
            </div>
            <!-- Hero Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 p-6 md:p-8 lg:p-12">
                
                <!-- Left Content -->
                <div class="flex flex-col justify-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-slate-600 dark:text-slate-200 mb-6 leading-tight">
                        Your Smart Assistant<br>for <span class="text-blue-500">Wazuh</span><span class="text-yellow-500">.</span>
                    </h1>
                    <p class="text-gray-600 dark:text-slate-200 text-base leading-relaxed mb-8">
                        Dengan integrasi Wazuh dan kecerdasan buatan, kamu dapat memantau keamanan sistem,
                        menganalisis alert, serta mendapatkan insight penting hanya dengan sekali klik.
                    </p>
                    @auth
                    <a href="{{ url('/dashboard') }}"
                       class="relative overflow-hidden px-8 py-3 rounded-full font-bold 
                              text-orange-400 dark:text-orange-400 bg-white shadow
                              transition-all duration-300 group w-fit border-0 group-hover:border-0 inline-block">
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-200 
                                   opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full">
                        </span>
                        <span class="relative group-hover:text-white">
                            Dashboard
                        </span>
                    </a>
                    @endauth
                    <p class="text-gray-600 dark:text-slate-200 text-sm mt-12 leading-relaxed">
                        Tingkatkan kecepatan respon & visibilitas keamanan dengan bantuan AI yang siap membantu kapan saja.
                    </p>
                </div>
                <!-- Right Illustration -->
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/gambar-orang.svg') }}" alt="ini ilustrasi orang lagi di depan pc ya bjir" class="w-100vh h-auto object-contain">
                </div>
            </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
