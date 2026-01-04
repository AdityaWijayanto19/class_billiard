@extends('layouts.admin')

@section('title', 'Sales Analytics - Billiard Class')

@section('content')
    <div class="space-y-10 animate-in fade-in duration-700">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8">
            <div class="space-y-1">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Sales Analytics
                </h1>
                <p class="text-sm text-slate-500 dark:text-gray-400 font-medium">
                    Analisis penjualan menu berdasarkan data pesanan pelanggan
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Live Data</span>
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-white/10 mx-2"></div>
                <span class="text-xs font-medium text-slate-400">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>

        <!-- SALES ANALYTICS CONTENT -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-gray-500">
                    Grafik Penjualan Menu
                </h2>
                <div class="flex items-center gap-3">
                    <select id="dateRange" class="text-xs bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/10 rounded-md px-3 py-1.5 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-[#fa9a08]/50">
                        <option value="all">Semua Waktu</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="year">Tahun Ini</option>
                    </select>
                </div>
            </div>

            <!-- Sales Chart Card -->
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Grafik Penjualan Menu</h3>
                        <p class="text-xs text-slate-500 dark:text-gray-400">Statistik penjualan berdasarkan menu yang dipesan</p>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-[#fa9a08]/10 border border-[#fa9a08]/20 rounded-md">
                        <i class="ri-bar-chart-line text-[#fa9a08]"></i>
                        <span class="text-xs font-bold text-[#fa9a08]">Analytics</span>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="relative" style="height: 400px;">
                    <canvas id="menuSalesChart"></canvas>
                </div>

                <!-- Summary Stats -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-slate-200 dark:border-white/5">
                    <div class="bg-slate-50 dark:bg-white/5 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                                <i class="ri-restaurant-line text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-gray-400 font-medium">Per Item</p>
                                <p class="text-lg font-bold text-slate-900 dark:text-white" id="totalMenuCount">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-white/5 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                                <i class="ri-shopping-cart-line text-green-500"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-gray-400 font-medium">Total Terjual</p>
                                <p class="text-lg font-bold text-slate-900 dark:text-white" id="totalQuantity">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-white/5 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#fa9a08]/10 rounded-lg flex items-center justify-center">
                                <i class="ri-money-dollar-circle-line text-[#fa9a08]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-gray-400 font-medium">Total Revenue</p>
                                <p class="text-lg font-bold text-slate-900 dark:text-white" id="totalRevenue">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Orderan -->
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Detail Orderan</h3>
                        <p class="text-xs text-slate-500 dark:text-gray-400">Rincian menu yang telah terjual</p>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-green-500/10 border border-green-500/20 rounded-md">
                        <i class="ri-list-check text-green-500"></i>
                        <span class="text-xs font-bold text-green-500">Live</span>
                    </div>
                </div>

                <!-- Detail List -->
                <div id="menuDetailsList" class="space-y-2">
                    <div class="text-center py-8 text-slate-500 dark:text-gray-400">
                        <i class="ri-inbox-line text-4xl mb-2"></i>
                        <p>Belum ada data orderan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        let menuSalesChart = null;

        // Function to get date range
        function getDateRange(range) {
            const today = new Date();
            let startDate = null;
            let endDate = today.toISOString().split('T')[0];

            switch(range) {
                case 'today':
                    startDate = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(today.getDate() - 7);
                    startDate = weekAgo.toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(today.getMonth() - 1);
                    startDate = monthAgo.toISOString().split('T')[0];
                    break;
                case 'year':
                    const yearAgo = new Date(today);
                    yearAgo.setFullYear(today.getFullYear() - 1);
                    startDate = yearAgo.toISOString().split('T')[0];
                    break;
                default:
                    startDate = null;
                    endDate = null;
            }

            return { startDate, endDate };
        }

        // Function to load chart data
        async function loadChartData(range = 'all') {
            try {
                const { startDate, endDate } = getDateRange(range);
                let url = '/admin/menu-sales-data';
                const params = new URLSearchParams();
                
                if (startDate) params.append('start_date', startDate);
                if (endDate) params.append('end_date', endDate);
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }

                const response = await fetch(url);
                const data = await response.json();

                if (response.ok && data) {
                    updateChart(data);
                    updateSummary(data);
                    updateMenuDetails(data);
                } else {
                    console.error('Error loading chart data:', data);
                }
            } catch (error) {
                console.error('Error fetching chart data:', error);
            }
        }

        // Function to update chart
        function updateChart(data) {
            const ctx = document.getElementById('menuSalesChart');
            if (!ctx) return;

            // Destroy existing chart if exists
            if (menuSalesChart) {
                menuSalesChart.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e2e8f0' : '#0f172a';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

            // Warna untuk setiap kategori
            // Biru: Makanan, Oranye: Minuman, Abu-abu: Cemilan
            const categoryColors = {
                'Makanan': {
                    backgroundColor: 'rgba(59, 130, 246, 0.8)', // Biru
                    borderColor: 'rgba(59, 130, 246, 1)'
                },
                'Minuman': {
                    backgroundColor: 'rgba(251, 146, 60, 0.8)', // Oranye
                    borderColor: 'rgba(251, 146, 60, 1)'
                },
                'Cemilan': {
                    backgroundColor: 'rgba(107, 114, 128, 0.8)', // Abu-abu
                    borderColor: 'rgba(107, 114, 128, 1)'
                }
            };

            menuSalesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels, // ['Makanan', 'Minuman', 'Cemilan']
                    datasets: [
                        {
                            label: 'Jumlah Terjual (Item)',
                            data: data.quantities,
                            backgroundColor: [
                                categoryColors['Makanan'].backgroundColor,
                                categoryColors['Minuman'].backgroundColor,
                                categoryColors['Cemilan'].backgroundColor
                            ],
                            borderColor: [
                                categoryColors['Makanan'].borderColor,
                                categoryColors['Minuman'].borderColor,
                                categoryColors['Cemilan'].borderColor
                            ],
                            borderWidth: 2,
                            borderRadius: 6,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Revenue (Rp)',
                            data: data.revenues,
                            backgroundColor: [
                                categoryColors['Makanan'].backgroundColor.replace('0.8', '0.5'),
                                categoryColors['Minuman'].backgroundColor.replace('0.8', '0.5'),
                                categoryColors['Cemilan'].backgroundColor.replace('0.8', '0.5')
                            ],
                            borderColor: [
                                categoryColors['Makanan'].borderColor,
                                categoryColors['Minuman'].borderColor,
                                categoryColors['Cemilan'].borderColor
                            ],
                            borderWidth: 2,
                            borderRadius: 6,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: textColor,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                padding: 15,
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1a1a1a' : '#fff',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.datasetIndex === 1) {
                                            label += 'Rp ' + parseInt(context.parsed.y).toLocaleString('id-ID');
                                        } else {
                                            label += context.parsed.y + ' item';
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Jumlah Terjual',
                                color: textColor,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: textColor,
                                precision: 0
                            },
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Revenue (Rp)',
                                color: textColor,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return 'Rp ' + parseInt(value).toLocaleString('id-ID');
                                }
                            },
                            grid: {
                                drawOnChartArea: false,
                                color: gridColor
                            }
                        }
                    }
                }
            });
        }

        // Function to update summary stats
        function updateSummary(data) {
            const menuCountEl = document.getElementById('totalMenuCount');
            const quantityEl = document.getElementById('totalQuantity');
            const revenueEl = document.getElementById('totalRevenue');

            if (menuCountEl) menuCountEl.textContent = data.menu_count || 0;
            if (quantityEl) quantityEl.textContent = (data.total_items || 0).toLocaleString('id-ID');
            if (revenueEl) revenueEl.textContent = 'Rp ' + parseInt(data.total_revenue || 0).toLocaleString('id-ID');
        }

        // Function to update menu details
        function updateMenuDetails(data) {
            const detailsList = document.getElementById('menuDetailsList');
            if (!detailsList) return;

            if (!data.menu_details || data.menu_details.length === 0) {
                detailsList.innerHTML = `
                    <div class="text-center py-8 text-slate-500 dark:text-gray-400">
                        <i class="ri-inbox-line text-4xl mb-2"></i>
                        <p>Belum ada data orderan</p>
                    </div>
                `;
                return;
            }

            // Group by category with colors
            const categoryColors = {
                'Makanan': 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                'Minuman': 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20',
                'Cemilan': 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-500/20'
            };

            let html = '';
            data.menu_details.forEach(item => {
                const categoryClass = categoryColors[item.category] || 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20';
                html += `
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-lg border border-slate-200 dark:border-white/5">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 rounded text-xs font-bold border ${categoryClass}">
                                ${item.category}
                            </span>
                            <span class="text-sm font-medium text-slate-900 dark:text-white">
                                ${item.quantity}x ${item.name}
                            </span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">
                            ${item.quantity} item
                        </span>
                    </div>
                `;
            });

            detailsList.innerHTML = html;
        }

        // Realtime update interval
        let updateInterval = null;

        // Function to start realtime updates
        function startRealtimeUpdates() {
            // Clear existing interval
            if (updateInterval) {
                clearInterval(updateInterval);
            }

            // Update every 3 seconds
            updateInterval = setInterval(() => {
                const dateRange = document.getElementById('dateRange')?.value || 'all';
                loadChartData(dateRange);
            }, 3000);
        }

        // Function to stop realtime updates
        function stopRealtimeUpdates() {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        }

        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadChartData('all');
            startRealtimeUpdates();

            // Date range selector change
            const dateRangeSelect = document.getElementById('dateRange');
            if (dateRangeSelect) {
                dateRangeSelect.addEventListener('change', function() {
                    loadChartData(this.value);
                    // Restart realtime updates with new filter
                    startRealtimeUpdates();
                });
            }

            // Stop updates when page is hidden
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopRealtimeUpdates();
                } else {
                    startRealtimeUpdates();
                }
            });
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            stopRealtimeUpdates();
        });
    </script>
    @endpush
@endsection

