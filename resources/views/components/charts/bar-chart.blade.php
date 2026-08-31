@props(['id', 'title' => 'Overview', 'description' => null, 'chartData' => null, 'chart-data' => null])

@php
    // Fallback to capture either attribute key format
    $finalChartData = $chartData ?? ($attributes->get('chart-data') ?? ($attributes->get('chartData') ?? []));
@endphp

<div class="bg-base-200 border border-base-300 rounded-box p-4 mb-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold">
                {{ $title }}
            </h2>
            @if ($description)
                <p class="text-sm text-base-content/60">
                    {{ $description }}
                </p>
            @endif
        </div>

        {{-- Period Buttons --}}
        <div class="join" id="{{ $id }}-period-container">
            <button type="button" class="btn btn-sm join-item btn-primary" data-period="weekly">
                Weekly
            </button>
            <button type="button" class="btn btn-sm join-item" data-period="monthly">
                Monthly
            </button>
            <button type="button" class="btn btn-sm join-item" data-period="yearly">
                Yearly
            </button>
        </div>
    </div>

    {{-- Responsive Chart Container --}}
    <div class="relative w-full h-[300px] sm:h-[350px] lg:h-[400px]">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>

<script>
    (function initChart_{{ Str::slug($id, '_') }}() {
        const renderChart = () => {
            const canvas = document.getElementById('{{ $id }}');
            const periodContainer = document.getElementById('{{ $id }}-period-container');
            const allChartData = {{ Illuminate\Support\Js::from($finalChartData) }};

            if (!canvas) return;

            // Wait for Chart.js to load via Vite if not yet available
            if (typeof window.Chart === 'undefined') {
                setTimeout(renderChart, 50);
                return;
            }

            if (!allChartData || typeof allChartData !== 'object' || !allChartData.weekly) {
                console.warn('Chart Data missing or invalid for {{ $id }}', allChartData);
                return;
            }

            // Clean up old instance on re-renders
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            // Create Chart Instance
            const chartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: allChartData.weekly.labels,
                    datasets: allChartData.weekly.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });

            // Handle Weekly/Monthly/Yearly Switch Buttons
            if (periodContainer) {
                const buttons = periodContainer.querySelectorAll('button[data-period]');
                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        const selectedPeriod = button.getAttribute('data-period');
                        if (!allChartData[selectedPeriod]) return;

                        buttons.forEach(btn => btn.classList.remove('btn-primary'));
                        button.classList.add('btn-primary');

                        chartInstance.data.labels = allChartData[selectedPeriod].labels;
                        chartInstance.data.datasets = allChartData[selectedPeriod].datasets;
                        chartInstance.update();
                    });
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', renderChart);
        } else {
            renderChart();
        }
    })();
</script>
