<x-layouts.app :title="__('Dashboard')">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- critical section-->
            <div class="bg-orange-600 p-4 rounded-lg relative overflow-hidden text-black">
                <div class="text-6xl font-bold mb-8">
                    @if($critical['ok'])
                        {{ $critical['count'] }}
                        @if($critical['count'] > 0)
                            😱
                        @else
                            😎
                        @endif
                    @else
                        😵
                    @endif
                </div>
                @if(! $critical['ok'])
                    <p class="text-lg font-medium mb-8">
                        ⚠ {{ $critical['error'] }}
                    </p>
                @else
                    <p class="text-xl font-semibold mb-2">Critical Security Alerts</p>
                    <p class="text-sm leading-relaxed">
                        Total alert dengan tingkat keparahan <b>Critical (rule.level ≥ 15)</b>
                        yang terdeteksi oleh Wazuh dalam <b>24 jam terakhir</b>.
                    </p>
                @endif
                
                <div class="mb-3">
                    <svg class="w-24 h-24" viewBox="0 0 100 60" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="10" y1="5" x2="50" y2="15" stroke-width="3"/>
                        <line x1="50" y1="15" x2="90" y2="5" stroke-width="3"/>
                        <ellipse cx="50" cy="35" rx="40" ry="20"/>
                        <circle cx="50" cy="35" r="15"/>
                        <circle cx="50" cy="35" r="8" fill="currentColor"/>
                    </svg>
                </div>
                
                <div class="absolute bottom-4 left-4 flex items-center gap-2">
                    <div class="w-6 h-6 bg-black rounded-full flex items-center justify-center text-white text-xs font-bold">L</div>
                    <span class="font-semibold">Critical Risk Event</span>
                    <p class="text-sm font-medium">Security Events (24h)</p>
                </div>
            </div>

            <!-- high severity section -->
            <div class="bg-yellow-400 p-4 rounded-lg text-black">
                <div class="text-6xl font-bold mb-6">
                    @if($high['ok'])
                        {{ $high['count'] }}
                        @if($high['count'] > 0)
                            😱
                        @else
                            😎
                        @endif
                    @else
                        😵
                    @endif
                </div>
                <div class="space-y-2 ">
                    @if(! $high['ok'])
                        <p class="text-lg">
                            ⚠ {{ $high['error'] }}
                        </p>
                    @else
                        <p class="text-xl font-semibold">High Severity Alerts</p>
                        <p class="text-lg">
                            Total alert dengan tingkat keparahan <b>High (rule.level 12 - 14)</b>
                            yang terdeteksi oleh Wazuh dalam <b>24 jam terakhir</b>.
                        </p>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-auto">
                    <span class="font-medium">Recent Alerts</span>
                     <svg class="w-24 h-24" viewBox="0 0 100 60" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="50" cy="30" rx="40" ry="25"/>
                        <circle cx="50" cy="30" r="15"/>
                        <circle cx="50" cy="30" r="8" fill="currentColor"/>
                        <line x1="35" y1="10" x2="35" y2="0"/>
                        <line x1="42" y1="12" x2="42" y2="2"/>
                        <line x1="50" y1="5" x2="50" y2="-5"/>
                        <line x1="58" y1="12" x2="58" y2="2"/>
                        <line x1="65" y1="10" x2="65" y2="0"/>
                    </svg>
                </div>
            </div>

            <!-- medium severity section -->
            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                <!-- Hospitals In Cities -->
                <div class="bg-blue-500 text-white p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-3">
                        <div class="text-4xl font-bold">
                            @if($medium['ok'])
                                {{ $medium['count'] }}
                                @if($medium['count'] > 0)
                                    😱
                                @else
                                    😎
                                @endif
                            @else
                                😵
                            @endif
                        </div>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    @if(! $medium['ok'])
                        <p class="mb-3 text-2xl">
                            ⚠ {{ $medium['error'] }}
                        </p>
                    @else
                        <p class="text-base">
                            Medium Severity<br>
                            Alerts
                        </p>
                        <p class="mb-3 text-2xl">
                            Requires Monitoring
                        </p>
                    @endif
                </div>

                <!-- Low Severity -->
                <div class="bg-green-500 text-white p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-3">
                        <div class="text-4xl font-bold">
                            @if($low['ok'])
                                {{ $low['count'] }}
                                @if($low['count'] > 0)
                                    😱
                                @else
                                    😎
                                @endif
                            @else
                                😵
                            @endif
                        </div>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    @if(! $low['ok'])
                        <p class="text-lg font-medium">
                            ⚠ {{ $low['error'] }}
                        </p>
                    @else
                        <p class="text-base">
                            Low Severity<br>
                            Alerts
                        </p>
                        <p class="text-lg font-medium">
                            Informational & Minor Issues
                        </p>
                    @endif
                </div>

                <div class="col-span-2 bg-purple-300 p-4 rounded-lg text-black relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-5xl font-bold mb-3">57/80</div>
                            <p class="text-lg font-semibold">Bed Occupancy Rate</p>
                            <p class="text-base">60.4%^</p>
                            <p class="mb-2 text-2xl">...</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <path d="M8 14s1.5 2 4 2 4-2 4-2" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="9" cy="9" r="1" fill="currentColor"/>
                                <circle cx="15" cy="9" r="1" fill="currentColor"/>
                            </svg>
                            <span class="text-4xl font-bold">2%</span>
                        </div>
                        <svg class="absolute bottom-4 right-4 w-16 h-16 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 7h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h1v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9h1a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Premium Healthcare Center -->
                <div class="bg-[#f0ecdd] border-2 border-gray-300 p-4 rounded-lg text-black">
                    <div class="flex items-center gap-2 mb-3">
                        <h2 class="text-2xl font-bold">The Premium</h2>
                        <span class="text-2xl">✱</span>
                    </div>
                    <h2 class="text-2xl font-bold mb-3">Heathcare —<br>Center</h2>
                    <div class="border-t-2 border-gray-300 pt-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold">Lifecare</p>
                            <p class="text-sm">International</p>
                        </div>
                        <span class="text-sm">(Global)</span>
                    </div>
                </div>

                <!-- Patient Growth -->
                <div class="bg-[#8f8104] p-4 rounded-lg text-black">
                    <p class="text-lg font-semibold mb-3">Patient Growth</p>
                    <div class="text-6xl font-bold mb-3">^35%</div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold">Lifecare</p>
                            <p class="text-sm">International</p>
                        </div>
                        <span class="text-sm">(People)</span>
                    </div>
                </div>


                <!-- Data Analytics Chart -->
                <div class="bg-[#d7e3d5] border-2 border-gray-300 p-4 rounded-lg relative text-black">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold">Data Analytics —</h3>
                        <div class="flex gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            </svg>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M7 17L17 7M17 7H7M17 7v10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="relative h-32 mb-3">
                        <span class="absolute -top-2 right-8 bg-orange-500 text-white text-xs px-3 py-1 rounded-full font-semibold">+42.85 %</span>
                        <svg class="w-full h-full" viewBox="0 0 350 100" preserveAspectRatio="none">
                            <polyline 
                                points="0,60 50,70 100,50 150,40 200,30 250,45 300,35 350,40" 
                                fill="none" 
                                stroke="currentColor" 
                                stroke-width="2"
                            />
                        </svg>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-600">
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                        <span>Sun</span>
                    </div>
                </div>

            </div>

        </div>
</x-layouts.app>
