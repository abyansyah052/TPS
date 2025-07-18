<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - TPS Inventory Systems</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .modal-header {
            background: var(--primary-color);
            color: white;
        }

        .modal-header .btn-close {
            filter: invert(1);
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

        .btn-check:checked + .btn {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-group .btn {
            border-radius: 5px !important;
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <!-- Navigation (Logo Only) -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="javascript:void(0)" style="cursor: default;">
                <i class="fas fa-ship me-2"></i>
                TPS Inventory Systems
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
