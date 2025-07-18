@extends('layouts.management')

@section('content')
<div class="container mt-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="/catalog" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Catalog
        </a>
    </div>

    <!-- Material Management Section -->
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>Material Management</h4>
                    <p class="text-muted mb-0">Manage material quantities and photos</p>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="downloadTemplate()">
                            <i class="fas fa-download me-2"></i>Download Template
                        </button>
                        <button type="button" class="btn btn-success" onclick="showUploadModal()">
                            <i class="fas fa-upload me-2"></i>Update Data
                        </button>
                        <button type="button" class="btn btn-warning" onclick="showExportModal()">
                            <i class="fas fa-file-export me-2"></i>Export Data
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control search-box" placeholder="Search materials...">
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="divisionFilter" id="all" value="" checked>
                        <label class="btn btn-outline-primary btn-sm" for="all">All</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="rtg" value="RTG">
                        <label class="btn btn-outline-primary btn-sm" for="rtg">RTG</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="me" value="ME">
                        <label class="btn btn-outline-primary btn-sm" for="me">ME</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="cc" value="CC">
                        <label class="btn btn-outline-primary btn-sm" for="cc">CC</label>
                        
                        <input type="radio" class="btn-check" name="divisionFilter" id="lain" value="others">
                        <label class="btn btn-outline-primary btn-sm" for="lain">Others</label>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="ACTIVE">Active</option>
                        <option value="INACTIVE">Inactive</option>
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
                                <th>Qty</th>
                                <th>Photo</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="materialsTableBody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Materials pagination">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- Populated dynamically -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Material Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" id="fileInput" name="file" accept=".xlsx,.xls" required>
                        <div class="form-text">Please upload the completed template file.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadData()">
                    <i class="fas fa-upload me-2"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Material Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">Enter Password</label>
                        <input type="password" class="form-control" id="passwordInput" name="password" required>
                        <div class="form-text">Password required for data export.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="exportData()">
                    <i class="fas fa-file-export me-2"></i>Export
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Material Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editMaterialSap" class="form-label">Material SAP</label>
                            <input type="text" class="form-control" id="editMaterialSap" readonly>
                            <div class="form-text">Material SAP cannot be edited</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editDivisi" class="form-label">Division</label>
                            <select class="form-select" id="editDivisi" name="divisi" required>
                                <option value="RTG">RTG</option>
                                <option value="ME">ME</option>
                                <option value="CC">CC</option>
                                <option value="LAIN">LAIN</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" rows="2" readonly></textarea>
                        <div class="form-text">Description cannot be edited</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editUnit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="editUnit" name="base_unit_measure" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editQty" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="editQty" name="qty" min="0" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editLokasiSistem" class="form-label">System Location</label>
                            <input type="text" class="form-control" id="editLokasiSistem" name="lokasi_sistem">
                        </div>
                        <div class="col-md-6">
                            <label for="editLokasiFisik" class="form-label">Physical Location</label>
                            <input type="text" class="form-control" id="editLokasiFisik" name="lokasi_fisik">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editPenempatan" class="form-label">Placement</label>
                            <select class="form-select" id="editPenempatan" name="penempatan_alat">
                                <option value="">Select Placement</option>
                                <option value="CONTROL_SYSTEM">CONTROL_SYSTEM</option>
                                <option value="COOLING_SYSTEM">COOLING_SYSTEM</option>
                                <option value="ELECTRICAL_SYSTEM">ELECTRICAL_SYSTEM</option>
                                <option value="ENGINE">ENGINE</option>
                                <option value="FASTENERS">FASTENERS</option>
                                <option value="FUEL_SYSTEM">FUEL_SYSTEM</option>
                                <option value="GENERAL_MAINTENANCE">GENERAL_MAINTENANCE</option>
                                <option value="HOIST_SYSTEM">HOIST_SYSTEM</option>
                                <option value="HYDRAULIC_SYSTEM">HYDRAULIC_SYSTEM</option>
                                <option value="TROLLEY_SYSTEM">TROLLEY_SYSTEM</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editPhoto" class="form-label">Photo URL</label>
                            <input type="url" class="form-control" id="editPhoto" name="photo" placeholder="https://example.com/image.jpg">
                            <div class="form-text">Enter image URL or leave empty</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateMaterial()">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .search-box {
        border-radius: 25px;
        border: 2px solid #e9ecef;
        padding: 10px 20px;
        transition: border-color 0.3s ease;
    }

    .search-box:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .table-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background: linear-gradient(135deg, #2c3e50, #34495e);
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
        color: #27ae60;
    }

    .status-inactive {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    .divisi-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .divisi-rtg {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .divisi-me {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
    }

    .divisi-cc {
        background: rgba(46, 204, 113, 0.1);
        color: #27ae60;
    }

    .divisi-lain {
        background: rgba(149, 165, 166, 0.1);
        color: #34495e;
    }

    .qty-badge {
        background: #17a2b8;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

<script>
    let currentPage = 1;
    let currentFilters = {
        divisi: '',
        status: '',
        search: '',
        penempatan: ''
    };
    let currentMaterials = [];

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - initializing...'); // Debug log
        loadMaterials();
        setupEventListeners();
        
        // Load placement options
        loadPlacementOptions();
    });

    function setupEventListeners() {
        // Search input with better event handling
        const searchInput = document.getElementById('searchInput');
        console.log('Search input element:', searchInput); // Debug log
        
        if (searchInput) {
            console.log('Setting up search input listener...'); // Debug log
            searchInput.addEventListener('input', debounce(function(e) {
                console.log('Search input triggered! Value:', e.target.value); // Debug log
                currentFilters.search = e.target.value;
                currentPage = 1;
                loadMaterials();
            }, 300));
            
            // Test event listener
            searchInput.addEventListener('keyup', function(e) {
                console.log('Keyup event - Search value:', e.target.value); // Debug log
            });
        } else {
            console.error('Search input element not found!'); // Debug log
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function(e) {
                console.log('Status filter changed:', e.target.value); // Debug log
                currentFilters.status = e.target.value;
                currentPage = 1;
                loadMaterials();
            });
        }

        // Placement filter
        const placementFilter = document.getElementById('placementFilter');
        if (placementFilter) {
            placementFilter.addEventListener('change', function(e) {
                console.log('Placement filter changed:', e.target.value); // Debug log
                currentFilters.penempatan = e.target.value;
                currentPage = 1;
                loadMaterials();
            });
        }

        // Division filter
        document.querySelectorAll('input[name="divisionFilter"]').forEach(radio => {
            radio.addEventListener('change', function(e) {
                console.log('Division filter changed:', e.target.value); // Debug log
                if (e.target.value === 'others') {
                    currentFilters.divisi = 'Lain';
                } else {
                    currentFilters.divisi = e.target.value;
                }
                currentPage = 1;
                loadMaterials();
            });
        });
    }

    async function loadMaterials() {
        const tableBody = document.getElementById('materialsTableBody');
        tableBody.innerHTML = '<tr><td colspan="11" class="text-center">Loading...</td></tr>';
        
        try {
            // Build the query parameters properly
            const params = new URLSearchParams();
            params.append('page', currentPage);
            
            // Only add non-empty filters
            if (currentFilters.search && currentFilters.search.trim()) {
                params.append('search', currentFilters.search.trim());
            }
            if (currentFilters.divisi) {
                params.append('divisi', currentFilters.divisi);
            }
            if (currentFilters.status) {
                params.append('status', currentFilters.status);
            }
            if (currentFilters.penempatan) {
                params.append('penempatan', currentFilters.penempatan);
            }

            console.log('Loading materials with params:', params.toString()); // Debug log

            const response = await fetch(`/api/management/materials?${params}`);
            const data = await response.json();
            
            console.log('Response data:', data); // Debug log
            
            if (data.data) {
                renderMaterials(data.data);
                renderPagination(data);
            } else {
                tableBody.innerHTML = '<tr><td colspan="11" class="text-center text-danger">Error loading data</td></tr>';
            }
        } catch (error) {
            console.error('Error loading materials:', error);
            tableBody.innerHTML = '<tr><td colspan="11" class="text-center text-danger">Error loading data</td></tr>';
        }
    }

    function renderMaterials(materials) {
        const tableBody = document.getElementById('materialsTableBody');
        
        // Store materials data for edit functionality
        currentMaterials = materials;
        
        if (materials.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="11" class="text-center">No materials found</td></tr>';
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
                    <td><span class="qty-badge">${material.qty || 0}</span></td>
                    <td>${material.photo ? `<a href="${material.photo}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-image"></i> View</a>` : '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-success" onclick="editMaterial(${material.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        tableBody.innerHTML = rows;
    }

    function renderPagination(data) {
        const pagination = document.getElementById('pagination');
        
        if (data.last_page <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '';
        
        // Previous button
        if (data.current_page > 1) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page - 1})">Previous</a></li>`;
        }
        
        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            if (i === data.current_page) {
                paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
            } else if (i === data.current_page - 3 || i === data.current_page + 3) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Next button
        if (data.current_page < data.last_page) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page + 1})">Next</a></li>`;
        }

        pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
        currentPage = page;
        loadMaterials();
    }

    function getDivisionClass(divisi) {
        switch(divisi) {
            case 'RTG': return 'divisi-rtg';
            case 'ME': return 'divisi-me';
            case 'CC': return 'divisi-cc';
            default: return 'divisi-lain';
        }
    }

    function downloadTemplate() {
        window.location.href = '/management/download-template';
    }

    function showUploadModal() {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
    }

    async function uploadData() {
        const fileInput = document.getElementById('fileInput');
        if (!fileInput.files[0]) {
            alert('Please select a file');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        try {
            const response = await fetch('/management/upload-data', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                loadMaterials();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('Error uploading file: ' + error.message);
        }
    }

    function showExportModal() {
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        modal.show();
    }

    async function exportData() {
        const password = document.getElementById('passwordInput').value;
        if (!password) {
            alert('Please enter password');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('password', password);

            const response = await fetch('/management/export-data', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                // Create download link
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'material_export_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                
                bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
                document.getElementById('passwordInput').value = '';
            } else {
                const result = await response.json();
                alert('Error: ' + (result.message || 'Invalid password'));
            }
        } catch (error) {
            alert('Error exporting data: ' + error.message);
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async function loadPlacementOptions() {
        try {
            // Load unique placement values for filter
            const response = await fetch('/api/management/materials?get_placements=1');
            const data = await response.json();
            
            if (data.placements) {
                const placementFilter = document.getElementById('placementFilter');
                data.placements.forEach(placement => {
                    if (placement) {
                        const option = document.createElement('option');
                        option.value = placement;
                        option.textContent = placement.replace(/_/g, ' ');
                        placementFilter.appendChild(option);
                    }
                });
            }
        } catch (error) {
            console.error('Error loading placement options:', error);
        }
    }

    // Edit Material Functions
    function editMaterial(id) {
        // Find material from current materials data
        const material = currentMaterials.find(m => m.id === id);
        if (!material) {
            alert('Material not found');
            return;
        }

        // Populate form
        document.getElementById('editId').value = material.id;
        document.getElementById('editMaterialSap').value = material.material_sap || '';
        document.getElementById('editDescription').value = material.material_description || '';
        document.getElementById('editDivisi').value = material.divisi || '';
        document.getElementById('editUnit').value = material.base_unit_measure || '';
        document.getElementById('editStatus').value = material.status || '';
        document.getElementById('editQty').value = material.qty || 0;
        document.getElementById('editLokasiSistem').value = material.lokasi_sistem || '';
        document.getElementById('editLokasiFisik').value = material.lokasi_fisik || '';
        document.getElementById('editPenempatan').value = material.penempatan_alat || '';
        document.getElementById('editPhoto').value = material.photo || '';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }

    async function updateMaterial() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        const id = document.getElementById('editId').value;

        try {
            const response = await fetch(`/api/management/materials/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    divisi: formData.get('divisi'),
                    base_unit_measure: formData.get('base_unit_measure'),
                    status: formData.get('status'),
                    lokasi_sistem: formData.get('lokasi_sistem'),
                    lokasi_fisik: formData.get('lokasi_fisik'),
                    penempatan_alat: formData.get('penempatan_alat'),
                    photo: formData.get('photo'),
                    qty: formData.get('qty')
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('Material updated successfully!');
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                loadMaterials(); // Reload the table
            } else {
                alert('Error: ' + (result.message || 'Failed to update material'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error updating material');
        }
    }
</script>
@endsection
