<x-layouts.app :title="'FIM Analysis Dashboard'">
    <div class="space-y-4">

        {{-- Header --}}
        <div class="bg-[#f0ecdd] border-2 border-gray-300 p-4 rounded-lg text-black">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold">FIM Analysis Dashboard ✱</h1>
                    <p class="text-sm md:text-base mt-1">
                        Hasil analisis File Integrity Monitoring dari Wazuh yang telah diklasifikasikan
                        menggunakan rule-based filtering dan LLM.
                    </p>
                </div>

                <form method="GET" action="{{ route('fim.dashboard') }}" class="flex flex-col sm:flex-row gap-2">
                    <input
                        type="date"
                        name="date"
                        value="{{ $date }}"
                        class="rounded-lg border-2 border-gray-300 bg-black px-3 py-2 text-sm text-white"
                    >

                    @if($classification)
                        <input type="hidden" name="classification" value="{{ $classification }}">
                    @endif

                    <button
                        type="submit"
                        class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white"
                    >
                        Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Total --}}
            <div class="bg-[#f0ecdd] p-4 rounded-lg relative overflow-hidden text-black">
                <div class="text-6xl font-bold mb-8">
                    {{ $summary['total'] }}
                </div>

                <p class="text-xl font-semibold mb-2">Total FIM Events</p>
                <p class="text-sm leading-relaxed">
                    Total hasil analisis FIM pada tanggal <b>{{ $date }}</b>
                    yang berhasil disimpan ke sistem.
                </p>

                <div class="mt-4">
                    <svg class="w-24 h-24 opacity-80" viewBox="0 0 100 60" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="50" cy="30" rx="40" ry="25"/>
                        <circle cx="50" cy="30" r="15"/>
                        <circle cx="50" cy="30" r="8" fill="currentColor"/>
                    </svg>
                </div>

                <div class="absolute bottom-4 left-4 flex items-center gap-2">
                    <div class="w-6 h-6 bg-black rounded-full flex items-center justify-center text-white text-xs font-bold">F</div>
                    <span class="font-semibold">FIM Monitoring</span>
                </div>
            </div>

            {{-- Mencurigakan --}}
            <div class="bg-yellow-400 p-4 rounded-lg text-black">
                <div class="text-6xl font-bold mb-6">
                    {{ $summary['mencurigakan'] }}
                </div>

                <div class="space-y-2">
                    <p class="text-xl font-semibold">Suspicious Events</p>
                    <p class="text-lg">
                        Event yang dikategorikan <b>mencurigakan</b> dan perlu ditinjau lebih lanjut.
                    </p>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <span class="font-medium">Need Review</span>
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            {{-- Aman & Berbahaya --}}
            <div class="md:col-span-2 grid grid-cols-2 gap-4">

                {{-- Aman --}}
                <div class="bg-green-500 text-white p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-3">
                        <div class="text-4xl font-bold">
                            {{ $summary['aman'] }}
                        </div>

                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m5-4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-4 8 4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <p class="text-base">
                        Safe<br>
                        Events
                    </p>
                    <p class="text-lg font-medium">
                        Tidak membutuhkan tindakan khusus
                    </p>
                </div>

                {{-- Berbahaya --}}
                <div class="bg-red-500 text-white p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-3">
                        <div class="text-4xl font-bold">
                            {{ $summary['berbahaya'] }}
                        </div>

                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <p class="text-base">
                        Dangerous<br>
                        Events
                    </p>
                    <p class="text-lg font-medium">
                        Membutuhkan investigasi segera
                    </p>
                </div>

                {{-- Analysis Source --}}
                <div class="col-span-2 bg-[#f0ecdd] p-4 rounded-lg text-black relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-5xl font-bold mb-3">
                                {{ $summary['llm'] }}/{{ $summary['rule_based'] }}
                            </div>
                            <p class="text-lg font-semibold">LLM / Rule-Based Analysis</p>
                            <p class="text-base">
                                Perbandingan event yang dianalisis oleh LLM dan event yang diklasifikasi
                                menggunakan rule-based filtering.
                            </p>
                            <p class="mb-2 text-2xl">
                                Hybrid Security Analysis
                            </p>
                        </div>

                        <svg class="absolute bottom-4 right-4 w-16 h-16 opacity-30" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a5 5 0 0 0-5 5v2H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V7a5 5 0 0 0-5-5zm-3 7V7a3 3 0 0 1 6 0v2H9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Bottom Section --}}
            <div class="md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Filter Classification --}}
                <div class="bg-[#f0ecdd] border-2 border-gray-300 p-4 rounded-lg text-black">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-2xl font-bold">
                                Event Filter <span class="text-2xl">✱</span>
                            </h2>
                            <p class="text-sm mt-1">
                                Filter data berdasarkan hasil klasifikasi.
                            </p>
                        </div>

                        <svg class="w-16 h-16 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M3 4h18M6 12h12M10 20h4" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <a
                            href="{{ route('fim.dashboard', ['date' => $date]) }}"
                            class="rounded-lg px-3 py-2 font-semibold text-center {{ !$classification ? 'bg-black text-white' : 'bg-white border border-gray-300 text-black' }}"
                        >
                            Semua
                        </a>

                        <a
                            href="{{ route('fim.dashboard', ['date' => $date, 'classification' => 'aman']) }}"
                            class="rounded-lg px-3 py-2 font-semibold text-center {{ $classification === 'aman' ? 'bg-green-600 text-white' : 'bg-white border border-gray-300 text-black' }}"
                        >
                            Aman
                        </a>

                        <a
                            href="{{ route('fim.dashboard', ['date' => $date, 'classification' => 'mencurigakan']) }}"
                            class="rounded-lg px-3 py-2 font-semibold text-center {{ $classification === 'mencurigakan' ? 'bg-yellow-400 text-black' : 'bg-white border border-gray-300 text-black' }}"
                        >
                            Mencurigakan
                        </a>

                        <a
                            href="{{ route('fim.dashboard', ['date' => $date, 'classification' => 'berbahaya']) }}"
                            class="rounded-lg px-3 py-2 font-semibold text-center {{ $classification === 'berbahaya' ? 'bg-red-600 text-white' : 'bg-white border border-gray-300 text-black' }}"
                        >
                            Berbahaya
                        </a>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-3 mt-4 flex justify-between items-center text-sm">
                        <span>Selected Date</span>
                        <span class="font-semibold">{{ $date }}</span>
                    </div>
                </div>

                {{-- Highest Risk --}}
                <div class="bg-[#f0ecdd] p-4 rounded-lg text-black">
                    @php
                        $highestRisk = $events->first();
                    @endphp

                    <h2 class="text-2xl font-bold text-black mb-4">Highest Risk Event</h2>

                    @if($highestRisk)
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-5xl font-bold text-red-700">
                                {{ $highestRisk->risk_score }}
                            </span>
                            <div>
                                <p class="font-semibold">{{ ucfirst($highestRisk->classification) }}</p>
                                <p class="text-sm opacity-80">{{ $highestRisk->analysis_source ?? '-' }}</p>
                            </div>
                        </div>

                        <p class="text-sm font-medium truncate" title="{{ $highestRisk->file_path }}">
                            {{ $highestRisk->file_path }}
                        </p>

                        <div class="flex justify-between items-center text-sm text-gray-700 border-t-2 pt-3 mt-4">
                            <div>
                                <p class="font-semibold">{{ $highestRisk->agent_name ?? '-' }}</p>
                                <p class="opacity-70">{{ $highestRisk->agent_ip ?? '-' }}</p>
                            </div>
                            <span class="opacity-70">Rule: {{ $highestRisk->rule_id ?? '-' }}</span>
                        </div>
                    @else
                        <div class="flex justify-center items-center h-32">
                            <p class="font-semibold">Belum ada data</p>
                        </div>
                    @endif
                </div>

                {{-- Analysis Composition --}}
                <div class="bg-[#d7e3d5] border-2 border-gray-300 p-4 rounded-lg relative text-black">
                    @php
                        $totalAnalysis = max(($summary['llm'] + $summary['rule_based']), 1);
                        $llmPercent = round(($summary['llm'] / $totalAnalysis) * 100, 1);
                        $ruleBasedPercent = round(($summary['rule_based'] / $totalAnalysis) * 100, 1);
                    @endphp

                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h3 class="text-lg font-semibold">
                                Analysis Composition —
                            </h3>
                            <p class="text-sm text-gray-700">LLM vs Rule-Based</p>
                        </div>

                        <span class="bg-black text-white text-xs px-3 py-1 rounded-full font-semibold">
                            {{ $llmPercent }}% LLM
                        </span>
                    </div>

                    <div class="space-y-4 mt-6">
                        <div>
                            <div class="flex justify-between text-sm font-semibold mb-1">
                                <span>LLM Analysis</span>
                                <span>{{ $summary['llm'] }}</span>
                            </div>
                            <div class="h-4 bg-white border border-gray-300 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: {{ $llmPercent }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm font-semibold mb-1">
                                <span>Rule-Based</span>
                                <span>{{ $summary['rule_based'] }}</span>
                            </div>
                            <div class="h-4 bg-white border border-gray-300 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500" style="width: {{ $ruleBasedPercent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between text-xs text-gray-600 mt-6">
                        <span>Automated</span>
                        <span>Hybrid</span>
                        <span>Reviewable</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Event Table --}}
        <div class="bg-[#f0ecdd] border-2 border-gray-300 rounded-lg text-black overflow-hidden">
            <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2 border-b-2 border-gray-300">
                <div>
                    <h2 class="text-2xl font-bold">FIM Analysis Results</h2>
                    <p class="text-sm">
                        Daftar event FIM yang telah diproses dan diklasifikasikan.
                    </p>
                </div>

                <div class="text-sm font-semibold">
                    Total shown: {{ $events->count() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-black text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Time</th>
                            <th class="px-4 py-3 text-left font-semibold">Agent</th>
                            <th class="px-4 py-3 text-left font-semibold">File Path</th>
                            <th class="px-4 py-3 text-left font-semibold">Event</th>
                            <th class="px-4 py-3 text-left font-semibold">Risk</th>
                            <th class="px-4 py-3 text-left font-semibold">Class</th>
                            <th class="px-4 py-3 text-left font-semibold">Source</th>
                            <th class="px-4 py-3 text-left font-semibold">Reason</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-300 bg-white">
                        @forelse($events as $event)
                            @include('partials.fim-analysis-result-row', ['event' => $event])
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data analisis FIM untuk tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t-2 border-gray-300 bg-white">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
