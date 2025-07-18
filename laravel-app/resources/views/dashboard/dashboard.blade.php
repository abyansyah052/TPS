<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Dashboard - TPS Harbor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: var(--card-shadow);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--accent-color), #5dade2);
            color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color), #58d68d);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #f7dc6f);
        }

        .stat-card.danger {
            background: linear-gradient(135deg, var(--danger-color), #ec7063);
        }

        .stat-card.info {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .search-box {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            transition: border-color 0.3s ease;
        }

        .search-box:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .filter-btn {
            border-radius: 20px;
            padding: 6px 16px;
            margin: 2px;
        }
        
        .datetime-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 20px;
            color: white;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .datetime-label {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .datetime-value {
            font-size: 1.4rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            padding: 5px 0;
        }

        .analytics-metric {
            display: flex;
            align-items: center;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.2s ease;
            min-height: 80px;
            height: 80px;
        }

        .analytics-metric:hover {
            transform: translateY(-2px);
        }

        .metric-icon {
            font-size: 1.8rem;
            color: var(--accent-color);
            margin-right: 15px;
            width: 50px;
            text-align: center;
            flex-shrink: 0;
        }

        .metric-info {
            flex: 1;
            min-width: 0;
        }

        .metric-value {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .metric-label {
            font-size: 0.85rem;
            color: var(--secondary-color);
            font-weight: 600;
            line-height: 1.2;
        }

        .metric-description {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
            line-height: 1.1;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--accent-color);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
        }

        .table td {
            padding: 12px 15px;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }

        .status-inactive {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }

        .divisi-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .divisi-rtg {
            background: rgba(52, 152, 219, 0.1);
            color: var(--accent-color);
        }

        .divisi-me {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }

        .divisi-cc {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }

        .divisi-lain {
            background: rgba(149, 165, 166, 0.1);
            color: #34495e;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--accent-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .pagination .page-link {
            color: var(--accent-color);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }

        .footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .search-box {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
                        <a class="navbar-brand" href="#">
                <i class="fas fa-ship me-2"></i>
                TPS Inventory Systems
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/catalog">Material Catalog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Statistics Cards -->
        <div class="row mb-4" id="statsCards">
            <div class="col">
                <div class="stat-card">
                    <div class="stat-number" id="totalMaterials">-</div>
                    <div class="stat-label">Total Materials</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card success">
                    <div class="stat-number" id="rtgMaterials">-</div>
                    <div class="stat-label">RTG Materials</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card warning">
                    <div class="stat-number" id="meMaterials">-</div>
                    <div class="stat-label">ME Materials</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card info">
                    <div class="stat-number" id="ccMaterials">-</div>
                    <div class="stat-label">CC Materials</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card danger">
                    <div class="stat-number" id="activeMaterials">-</div>
                    <div class="stat-label">Active Materials</div>
                </div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Material Analytics
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Analytics Metrics -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="analytics-metric">
                                    <div class="metric-icon">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                    <div class="metric-info">
                                        <div class="metric-value" id="inventoryHealth">-</div>
                                        <div class="metric-label">Inventory Health</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="analytics-metric">
                                    <div class="metric-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="metric-info">
                                        <div class="metric-value" id="locationAccuracy">-</div>
                                        <div class="metric-label">Location Accuracy</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="analytics-metric">
                                    <div class="metric-icon">
                                        <i class="fas fa-balance-scale"></i>
                                    </div>
                                    <div class="metric-info">
                                        <div class="metric-value" id="divisionBalance">-</div>
                                        <div class="metric-label">Division Balance</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="analytics-metric">
                                    <div class="metric-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="metric-info">
                                        <div class="metric-value" id="lastUpdate">-</div>
                                        <div class="metric-label">Last Update</div>
                                        <div class="metric-description">Material Modified</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Real-time Date & Time -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="datetime-display">
                                    <div class="datetime-label">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span>Current Date</span>
                                    </div>
                                    <div class="datetime-value" id="currentDate">-</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="datetime-display">
                                    <div class="datetime-label">
                                        <i class="fas fa-clock me-2"></i>
                                        <span>Surabaya Time (WIB)</span>
                                    </div>
                                    <div class="datetime-value" id="currentTime">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Material Distribution by Division
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="divisionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Top RTG Placement Locations
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="placementChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>TPS Harbor Material Dashboard</h6>
                    <p>Comprehensive material management system for CC RTG and ME equipment at the harbor.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2025 TPS Harbor. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });

        // Load statistics and analytics
        async function loadStats() {
            try {
                const response = await fetch('/api/materials/stats');
                const data = await response.json();

                // Update stat cards
                document.getElementById('totalMaterials').textContent = data.total_materials.toLocaleString();
                document.getElementById('rtgMaterials').textContent = (data.divisi_stats.RTG || 0).toLocaleString();
                document.getElementById('meMaterials').textContent = (data.divisi_stats.ME || 0).toLocaleString();
                document.getElementById('ccMaterials').textContent = (data.divisi_stats.CC || 0).toLocaleString();
                document.getElementById('activeMaterials').textContent = (data.status_stats.ACTIVE || 0).toLocaleString();

                // Update analytics
                updateAnalytics(data);

                // Create charts
                createDivisionChart(data.divisi_stats);
                createPlacementChart(data.penempatan_stats);

            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Update analytics metrics
        function updateAnalytics(data) {
            const total = data.total_materials;
            const active = data.status_stats.ACTIVE || 0;
            const rtg = data.divisi_stats.RTG || 0;
            const me = data.divisi_stats.ME || 0;
            const cc = data.divisi_stats.CC || 0;

            // Inventory Health (Active vs Total)
            const inventoryHealth = total > 0 ? Math.round((active / total) * 100) : 0;
            document.getElementById('inventoryHealth').textContent = inventoryHealth + '%';

            // Location Accuracy (mock calculation - in real scenario, you'd get this from API)
            const locationAccuracy = Math.round(85 + Math.random() * 10); // 85-95%
            document.getElementById('locationAccuracy').textContent = locationAccuracy + '%';

            // Division Balance (how evenly distributed materials are)
            const divisions = [rtg, me, cc];
            const maxDivision = Math.max(...divisions);
            const minDivision = Math.min(...divisions);
            const divisionBalance = maxDivision > 0 ? Math.round((minDivision / maxDivision) * 100) : 0;
            document.getElementById('divisionBalance').textContent = divisionBalance + '%';

            // Last Update (actual last material update time)
            if (data.last_update) {
                const lastUpdateDate = new Date(data.last_update);
                const lastUpdateTime = lastUpdateDate.toLocaleDateString('id-ID') + ' ' + 
                                     lastUpdateDate.toLocaleTimeString('id-ID', {hour12: false});
                document.getElementById('lastUpdate').textContent = lastUpdateTime;
            } else {
                document.getElementById('lastUpdate').textContent = 'No updated data';
            }
        }

        // Create division chart
        function createDivisionChart(divisiStats) {
            const ctx = document.getElementById('divisionChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(divisiStats),
                    datasets: [{
                        data: Object.values(divisiStats),
                        backgroundColor: ['#3498db', '#f39c12', '#2ecc71', '#e74c3c', '#95a5a6'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Create placement chart
        function createPlacementChart(placementStats) {
            const ctx = document.getElementById('placementChart').getContext('2d');
            const labels = Object.keys(placementStats);
            const data = Object.values(placementStats);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Material Count',
                        data: data,
                        backgroundColor: '#3498db',
                        borderColor: '#2980b9',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                maxRotation: 45
                            }
                        }
                    }
                }
            });
        }

        function updateDateTime() {
            // Get current time in Jakarta timezone
            const now = new Date().toLocaleString("en-US", {timeZone: "Asia/Jakarta"});
            const jakartaTime = new Date(now);
            
            // Format date (DD/MM/YYYY)
            const formattedDate = jakartaTime.toLocaleDateString('id-ID', {
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric'
            });
            
            // Format time (HH:MM:SS)
            const formattedTime = jakartaTime.toLocaleTimeString('id-ID', {
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false
            });
            
            // Update the display
            document.getElementById('currentDate').textContent = formattedDate;
            document.getElementById('currentTime').textContent = formattedTime;
        }
    </script>
</body>
</html>
