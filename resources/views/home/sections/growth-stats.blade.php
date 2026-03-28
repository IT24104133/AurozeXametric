@php
    $showSection = $homepageSettings['show_growth_stats_section'] ?? true;
    $showChart = $homepageSettings['show_growth_chart'] ?? true;
    $showStats = $homepageSettings['show_platform_stats'] ?? true;
    $growthTitle = $homepageSettings['growth_section_title'] ?? 'Growth & Statistics';
    $growthSubtitle = $homepageSettings['growth_section_subtitle'] ?? null;
    $statsTitle = $homepageSettings['stats_section_title'] ?? 'By The Numbers';
    $statsSubtitle = $homepageSettings['stats_section_subtitle'] ?? null;
    $chartId = $chartId ?? 'dailyGrowthChart';
    $labelsJson = json_encode($growthLabels ?? []);
    $valuesJson = json_encode($growthValues ?? []);
    $maxValue = max(1, (int) ($maxGrowthValue ?? 1));
@endphp

@if($showSection)
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-sm font-semibold text-indigo-700 mb-4">
                <span class="text-lg">📈</span>
                Growth & Stats
            </div>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">{{ $growthTitle }}</h2>
            @if($growthSubtitle)
                <p class="mt-3 text-slate-600">{{ $growthSubtitle }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            @if($showChart)
                <div class="lg:col-span-3 bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">New Registrations</h3>
                            <p class="text-sm text-slate-500">From {{ $growthStartDate->toDateString() }} to {{ $growthEndDate->toDateString() }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" data-chart-toggle="bar"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                                Bar
                            </button>
                            <button type="button" data-chart-toggle="line"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                                Line
                            </button>
                        </div>
                    </div>

                    <div class="relative h-[220px] lg:h-[320px]">
                        <canvas id="{{ $chartId }}" aria-label="Daily growth chart" role="img"></canvas>
                    </div>

                    @if(($totalNewUsers ?? 0) === 0)
                        <div class="mt-4 text-center text-sm text-slate-500">No new registrations in this range.</div>
                    @endif
                </div>
            @endif

            @if($showStats)
                <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $statsTitle }}</h3>
                        @if($statsSubtitle)
                            <p class="text-sm text-slate-500 mt-2">{{ $statsSubtitle }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($statsCards as $card)
                            <div class="rounded-2xl border border-slate-200 p-4 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg">
                                        @switch($card['icon'])
                                            @case('graduation-cap') 🎓 @break
                                            @case('user') 👤 @break
                                            @case('file-text') 📄 @break
                                            @case('book-open') 📚 @break
                                            @case('infinity') ♾️ @break
                                            @default 📌
                                        @endswitch
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold uppercase text-slate-500">{{ $card['label'] }}</p>
                                        <p class="text-2xl font-extrabold text-slate-900">
                                            {{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}
                                        </p>
                                    </div>
                                </div>
                                @if(!empty($card['description']))
                                    <p class="text-xs text-slate-500 mt-2">{{ $card['description'] }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">No stats cards enabled.</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Chart || !@json($showChart)) return;

        const ctx = document.getElementById('{{ $chartId }}');
        if (!ctx) return;

        const labels = {!! $labelsJson !!};
        const values = {!! $valuesJson !!};

        const createGradient = (context) => {
            const chart = context.chart;
            const {ctx, chartArea} = chart;
            if (!chartArea) return 'rgba(79, 70, 229, 0.15)';
            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
            return gradient;
        };

        const baseDataset = {
            label: 'New Users',
            data: values,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.6)',
            borderWidth: 2,
            borderRadius: 6,
            maxBarThickness: 28,
            tension: 0.35,
            fill: false
        };

        const chartConfig = (type) => ({
            type,
            data: {
                labels,
                datasets: [
                    {
                        ...baseDataset,
                        backgroundColor: type === 'line' ? createGradient : baseDataset.backgroundColor,
                        fill: type === 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => `Date: ${items[0].label}`,
                            label: (item) => `New users: ${item.formattedValue}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        suggestedMax: {{ $maxValue }}
                    }
                }
            }
        });

        let chart = new Chart(ctx, chartConfig('bar'));

        const toggles = ctx.closest('section')?.querySelectorAll('[data-chart-toggle]') || [];
        toggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const type = toggle.getAttribute('data-chart-toggle');
                chart.destroy();
                chart = new Chart(ctx, chartConfig(type));
            });
        });
    });
</script>
@endif