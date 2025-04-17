@extends('layouts.app')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Finansal Raporlar</h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <!-- Filtre Formu -->
            <form method="GET" action="{{ route('reports.index') }}" class="mb-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700">Dönem</label>
                        <select name="period" id="period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Aylık</option>
                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yıllık</option>
                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Özel</option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $period != 'custom' ? 'disabled' : '' }}>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Bitiş Tarihi</label>
                        <input type="date" name="end_date" id="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" {{ $period != 'custom' ? 'disabled' : '' }}>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            Filtrele
                        </button>
                    </div>
                </div>
            </form>

            <!-- Özet Bilgiler -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500">Toplam Gelir</h4>
                    <p class="mt-1 text-lg font-semibold text-green-600">{{ number_format($summary['income'], 2) }} {{ auth()->user()->currency }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500">Toplam Gider</h4>
                    <p class="mt-1 text-lg font-semibold text-red-600">{{ number_format($summary['expense'], 2) }} {{ auth()->user()->currency }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500">Bakiye</h4>
                    <p class="mt-1 text-lg font-semibold {{ $summary['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($summary['balance'], 2) }} {{ auth()->user()->currency }}</p>
                </div>
            </div>

            <!-- Grafikler -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Pasta Grafiği (Kategori Dağılımı) -->
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500 mb-4">Kategori Bazlı Harcama Dağılımı</h4>
                    <canvas id="categoryChart" height="200"></canvas>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500 mb-4">Gelir ve Gider Trendi</h4>
                    <canvas id="trendChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pasta Grafiği
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: @json($categoryBreakdown['labels']),
                datasets: [{
                    data: @json($categoryBreakdown['data']),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#C9CBCF', '#7BC225', '#FF5733', '#C70039'
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return `${label}: ${value.toFixed(2)} {{ auth()->user()->currency }}`;
                            }
                        }
                    }
                }
            }
        });

        // Çizgi Grafiği
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($trendData['labels']),
                datasets: [
                    {
                        label: 'Gelir',
                        data: @json($trendData['income']),
                        borderColor: '#36A2EB',
                        fill: false,
                    },
                    {
                        label: 'Gider',
                        data: @json($trendData['expense']),
                        borderColor: '#FF6384',
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                return `${label}: ${value.toFixed(2)} {{ auth()->user()->currency }}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Ay'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Miktar'
                        },
                        beginAtZero: true
                    }
                }
            }
        });


        document.getElementById('period').addEventListener('change', function() {
            const isCustom = this.value === 'custom';
            document.getElementById('start_date').disabled = !isCustom;
            document.getElementById('end_date').disabled = !isCustom;
        });
    </script>
@endsection
