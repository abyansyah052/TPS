@extends('layouts.catalog')

@section('content')
<div class="container mt-4">
    <!-- Material Catalog Section -->
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h4 class="mb-0"><i class="fas fa-box me-2"></i>Material Catalog</h4>
                </div>
                <div class="col-auto">
                    <a href="/management" class="btn btn-primary">
                        <i class="fas fa-cogs me-2"></i>Material Management
                    </a>
                </div>
            </div>
            
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control search-box" placeholder="Search materials...">
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group" aria-label="Division filter">
                        <input type="radio" class="btn-check" name="divisionFilter" id="all" value="all" checked>
                        <label class="btn btn-outline-primary" for="all">All</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="rtg" value="rtg">
                        <label class="btn btn-outline-primary" for="rtg">RTG</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="me" value="me">
                        <label class="btn btn-outline-primary" for="me">ME</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="cc" value="cc">
                        <label class="btn btn-outline-primary" for="cc">CC</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="others" value="others">
                        <label class="btn btn-outline-primary" for="others">Others</label>
                    </div>
                </div>
            </div>

            <!-- Status and Placement Filters -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="placementFilter">
                        <option value="">All Placements</option>
                        <!-- Populated dynamically -->
                    </select>
                </div>
            </div>

            <!-- Materials Table -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Division</th>
                                <th>Material SAP</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>System Location</th>
                                <th>Physical Location</th>
                                <th>Placement</th>
                                <th>Photo</th>
                            </tr>
                        </thead>
                        <tbody id="materialsTableBody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination" id="pagination">
                        <!-- Populated dynamically -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentFilters = {
        search: '',
        divisi: '',
        status: '',
        penempatan: ''
    };

    // Load initial data
    loadMaterials();

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = e.target.value;
            currentPage = 1;
            loadMaterials();
        }, 300);
    });

    // Division filter buttons (radio buttons)
    const divisionRadios = document.querySelectorAll('input[name="divisionFilter"]');
    divisionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const filter = this.value;
                currentFilters.divisi = filter === 'all' ? '' : filter.toUpperCase();
                currentPage = 1;
                loadMaterials();
            }
        });
    });

    // Status filter
    const statusFilter = document.getElementById('statusFilter');
    statusFilter.addEventListener('change', function() {
        currentFilters.status = this.value.toUpperCase();
        currentPage = 1;
        loadMaterials();
    });

    // Placement filter
    const placementFilter = document.getElementById('placementFilter');
    placementFilter.addEventListener('change', function() {
        currentFilters.penempatan = this.value;
        currentPage = 1;
        loadMaterials();
    });

    function loadMaterials() {
        // Show loading indicator
        const tableBody = document.getElementById('materialsTableBody');
        tableBody.innerHTML = '<tr><td colspan="9" class="text-center">Loading...</td></tr>';

        // Build query parameters
        const params = new URLSearchParams();
        if (currentFilters.search) params.append('search', currentFilters.search);
        if (currentFilters.divisi) params.append('divisi', currentFilters.divisi);
        if (currentFilters.status) params.append('status', currentFilters.status);
        if (currentFilters.penempatan) params.append('penempatan', currentFilters.penempatan);
        params.append('page', currentPage);

        // Fetch data from API
        fetch(`/api/materials?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                displayMaterials(data.data);
                updatePagination(data);
                updatePlacementFilter();
            })
            .catch(error => {
                console.error('Error loading materials:', error);
                tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>';
            });
    }

    function displayMaterials(materials) {
        const tableBody = document.getElementById('materialsTableBody');
        
        if (materials.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="9" class="text-center">No materials found</td></tr>';
            return;
        }

        const rows = materials.map(material => {
            const divisiClass = getDivisionClass(material.divisi);
            const statusClass = material.status === 'ACTIVE' ? 'status-active' : 'status-inactive';
            
            return `
                <tr>
                    <td><span class="divisi-badge ${divisiClass}">${material.divisi}</span></td>
                    <td>${material.material_sap || '-'}</td>
                    <td>${material.material_description || '-'}</td>
                    <td>${material.base_unit_measure || '-'}</td>
                    <td><span class="status-badge ${statusClass}">${material.status}</span></td>
                    <td>${material.lokasi_sistem || '-'}</td>
                    <td>${material.lokasi_fisik || '-'}</td>
                    <td>${material.penempatan_alat || '-'}</td>
                    <td>${material.photo ? `<a href="${material.photo}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-image"></i> View</a>` : '-'}</td>
                </tr>
            `;
        }).join('');

        tableBody.innerHTML = rows;
    }

    function getDivisionClass(divisi) {
        switch(divisi) {
            case 'RTG': return 'divisi-rtg';
            case 'ME': return 'divisi-me';
            case 'CC': return 'divisi-cc';
            default: return 'divisi-lain';
        }
    }

    function updatePagination(data) {
        const pagination = document.getElementById('pagination');
        let paginationHTML = '';

        // Previous button
        if (data.current_page > 1) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page - 1})">Previous</a></li>`;
        }

        // Page numbers
        const startPage = Math.max(1, data.current_page - 2);
        const endPage = Math.min(data.last_page, data.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const active = i === data.current_page ? 'active' : '';
            paginationHTML += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
        }

        // Next button
        if (data.current_page < data.last_page) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page + 1})">Next</a></li>`;
        }

        pagination.innerHTML = paginationHTML;
    }

    function updatePlacementFilter() {
        // Load unique placements for RTG division
        fetch('/api/materials?divisi=RTG')
            .then(response => response.json())
            .then(data => {
                const placements = [...new Set(data.data
                    .filter(m => m.penempatan_alat && m.penempatan_alat !== 'NULL')
                    .map(m => m.penempatan_alat)
                )].sort();

                const placementFilter = document.getElementById('placementFilter');
                placementFilter.innerHTML = '<option value="">All Placements</option>';
                
                placements.forEach(placement => {
                    placementFilter.innerHTML += `<option value="${placement}">${placement}</option>`;
                });
            });
    }

    // Make changePage function global
    window.changePage = function(page) {
        currentPage = page;
        loadMaterials();
    }
});
</script>
@endsection
