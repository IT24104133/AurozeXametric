@php
    $chartId = $chartId ?? 'dailyGrowthChart';
    $labelsJson = json_encode($growthLabels ?? []);
    $valuesJson = json_encode($growthValues ?? []);
    $maxValue = max(1, (int) ($maxGrowthValue ?? 1));
@endphp

<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-sm font-semibold text-indigo-700 mb-4">
                    <span class="text-lg">📈</span>
                    Daily Growth
                </div>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">New Registrations</h2>
                <p class="mt-3 text-slate-600">From {{ $growthStartDate->toDateString() }} to {{ $growthEndDate->toDateString() }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-chart-toggle="bar"
                        class="px-4 py-2 rounded-full text-xs font-bold border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                    Bar
                </button>
                <button type="button" data-chart-toggle="line"
                        class="px-4 py-2 rounded-full text-xs font-bold border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                    Line
                </button>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="relative h-[220px] lg:h-[320px]">
                <canvas id="{{ $chartId }}" aria-label="Daily growth chart" role="img"></canvas>
            </div>

            @if(($totalNewUsers ?? 0) === 0)
                <div class="mt-4 text-center text-sm text-slate-500">No new registrations in this range.</div>
            @endif
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Chart) return;

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
                        ticks: {
                            stepSize: 1
                        },
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