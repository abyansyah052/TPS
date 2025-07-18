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
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn {
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-success:hover {
            transform: translateY(-2px);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
        }

        .table {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
            border-radius: 25px;
        }

        .badge.bg-success {
            background-color: var(--success-color) !important;
        }

        .badge.bg-danger {
            background-color: var(--danger-color) !important;
        }

        .badge.bg-warning {
            background-color: var(--warning-color) !important;
        }

        .divisi-rtg {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
        }

        .divisi-me {
            background: rgba(52, 152, 219, 0.1);
            color: #2980b9;
        }

        .divisi-cc {
            background: rgba(241, 196, 15, 0.1);
            color: #f39c12;
        }

        .divisi-workshop {
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

        .pagination {
            margin-top: 2rem;
            justify-content: center;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border-color: #e9ecef;
            border-radius: 25px;
            margin: 0 2px;
            padding: 0.5rem 1rem;
        }

        .pagination .page-link:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .filters-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }

        .filter-group {
            margin-bottom: 1rem;
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .btn-group .btn {
            border-radius: 0;
            border-right-width: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            border-right-width: 1px;
        }

        .status-active {
            color: var(--success-color);
        }

        .status-inactive {
            color: var(--danger-color);
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .form-select:hover {
            border-color: var(--accent-color);
        }

        .btn-check:checked + .btn {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-group .btn {
            border-radius: 8px !important;
            margin-right: 4px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
            border: 2px solid var(--accent-color);
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .btn-group .btn:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .btn-group .btn-check:checked + .btn {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        }

        .photo-link {
            color: var(--accent-color);
            text-decoration: none;
        }

        .photo-link:hover {
            color: #2980b9;
            text-decoration: underline;
        }
    </style>
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
    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    @stack('scripts')
</body>
</html>
