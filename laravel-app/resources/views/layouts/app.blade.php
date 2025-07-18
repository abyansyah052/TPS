<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - TPS Inventory Systems</title>
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
            --border-radius: 10px;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: var(--primary-color);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: white !important;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .nav-link {
            color: rgba(255,255,255,0.7) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
        }

        .card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
        }

        .stat-card.success {
            background: linear-gradient(135deg, #2ecc71, var(--success-color));
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #f1c40f, var(--warning-color));
        }

        .stat-card.danger {
            background: linear-gradient(135deg, #e74c3c, var(--danger-color));
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            opacity: 0.9;
        }

        .search-box {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            transition: border-color 0.3s ease;
            width: 100%;
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
            border: 2px solid var(--accent-color);
            background: white;
            color: var(--accent-color);
            transition: all 0.3s ease;
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
            background: var(--primary-color);
            color: white;
            padding: 1.5rem 0;
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
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-ship me-2"></i>
                TPS Inventory Systems
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('catalog*') ? 'active' : '' }}" href="/catalog">Catalog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} TPS Inventory Systems. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    @yield('scripts')
</body>
</html>
