@extends('admin.layouts.admin_master')

@section('title', 'Standards Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/taxonomy-manager.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .standard-type-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }

    .type-unit {
        background: rgba(59, 130, 246, 0.1);
        color: var(--blue);
    }

    .type-grade {
        background: rgba(245, 158, 11, 0.1);
        color: var(--accent-amber);
    }
</style>
@endsection

@section('content')
<div class="taxonomy-manager">
    <div class="taxonomy-header">
        <h1><i class="fas fa-tags"></i> Standards Management</h1>
        <p>Manage Unit of Measures and Quality Grades for products</p>
    </div>

    <div class="search-container">
        <input type="text"
               id="globalSearch"
               class="search-input"
               placeholder="Search standards...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <!-- Standards Table -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fas fa-balance-scale"></i> System Standards</h3>
            <div class="table-actions">
                <button class="action-btn btn-add" onclick="openAddStandardForm()">
                    <i class="fas fa-plus"></i> Add Standard
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Standard Value</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="standardsList">
                    <tr>
                        <td colspan="7" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Loading standards...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-container" id="addFormContainer" style="display: none;">
        <div id="formContent"></div>
    </div>
</div>

<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="editFormContent"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let standards = [];
    let searchTerm = '';

    document.addEventListener('DOMContentLoaded', function() {
        loadStandards();

        document.getElementById('globalSearch').addEventListener('input', function(e) {
            searchTerm = e.target.value.toLowerCase();
            filterStandards();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('close') || e.target.id === 'editModal') {
                closeModal();
            }
        });
    });

    async function loadStandards() {
        try {
            const response = await fetch('{{ route("admin.taxonomy.standards.data") }}');
            standards = await response.json();
            renderStandards();
        } catch (error) {
            console.error('Error loading standards:', error);
            Swal.fire('Error', 'Failed to load standards data', 'error');
        }
    }

    function filterStandards() {
        if (!searchTerm.trim()) {
            renderStandards();
            return;
        }

        const filtered = standards.filter(std =>
            std.standard_value.toLowerCase().includes(searchTerm) ||
            std.description?.toLowerCase().includes(searchTerm) ||
            std.standard_type.toLowerCase().includes(searchTerm)
        );

        renderStandards(filtered);
    }

    function renderStandards(data = standards) {
        const container = document.getElementById('standardsList');

        if (data.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-tags"></i>
                        <p>No standards found</p>
                        <button class="action-btn btn-add" onclick="openAddStandardForm()">
                            <i class="fas fa-plus"></i> Add First Standard
                        </button>
                    </td>
                </tr>
            `;
            return;
        }

        container.innerHTML = data.map((std, index) => {
            return `
                <tr>
                    <td>${index + 1}</td>
                    <td style="font-weight: 500;">${std.standard_value}</td>
                    <td>
                        <span class="standard-type-badge ${std.standard_type === 'unit_of_measure' ? 'type-unit' : 'type-grade'}">
                            ${std.standard_type === 'unit_of_measure' ? 'Unit' : 'Grade'}
                        </span>
                    </td>
                    <td class="description-cell">${std.description || '-'}</td>
                    <td>${std.display_order}</td>
                    <td><span class="status-badge ${std.is_active ? 'status-active' : 'status-inactive'}">
                        ${std.is_active ? 'Active' : 'Inactive'}
                    </span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-edit" onclick="editStandard(${std.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" onclick="deleteStandard(${std.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function openAddStandardForm() {
        const formContainer = document.getElementById('addFormContainer');
        const formContent = document.getElementById('formContent');

        formContent.innerHTML = `
            <h3><i class="fas fa-plus-circle"></i> Add System Standard</h3>

            <form id="standardForm" onsubmit="saveStandard(event)">
                <div class="form-group">
                    <label class="form-label">Standard Type *</label>
                    <select class="form-select" name="standard_type" required>
                        <option value="">Select Type</option>
                        <option value="unit_of_measure">Unit of Measure</option>
                        <option value="quality_grade">Quality Grade</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Standard Value *</label>
                    <input type="text" class="form-input" name="standard_value" required
                           placeholder="e.g., KG, Grade A, Jars, Export Quality">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" name="description"
                              placeholder="Detailed description or criteria" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Display Order</label>
                    <input type="number" class="form-input" name="display_order" value="0" min="0">
                </div>

                <div class="step-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Standard
                    </button>
                    <button type="button" class="btn-secondary" onclick="closeForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        `;

        formContainer.style.display = 'block';
        formContainer.scrollIntoView({ behavior: 'smooth' });
    }

    async function saveStandard(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const data = {
            standard_type: formData.get('standard_type'),
            standard_value: formData.get('standard_value'),
            description: formData.get('description'),
            display_order: formData.get('display_order')
        };

        try {
            const response = await fetch('{{ route("admin.taxonomy.standards.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Standard added successfully',
                    showConfirmButton: false,
                    timer: 2000
                });

                closeForm();
                loadStandards();
            } else {
                throw new Error(result.message || 'Failed to save standard');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    async function editStandard(id) {
        try {
            const response = await fetch('{{ route("admin.taxonomy.standards.edit", ":id") }}'.replace(':id', id));
            const standard = await response.json();

            const formContent = document.getElementById('editFormContent');
            formContent.innerHTML = `
                <h3><i class="fas fa-edit"></i> Edit Standard</h3>
                <form onsubmit="updateStandard(${id}, event)">
                    <div class="form-group">
                        <label class="form-label">Standard Type</label>
                        <select class="form-select" name="standard_type" required disabled>
                            <option value="${standard.standard_type}">
                                ${standard.standard_type === 'unit_of_measure' ? 'Unit of Measure' : 'Quality Grade'}
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Standard Value *</label>
                        <input type="text" class="form-input" name="standard_value"
                               value="${standard.standard_value}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" name="description" rows="3">${standard.description || ''}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-input" name="display_order"
                               value="${standard.display_order}" min="0">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="is_active">
                            <option value="1" ${standard.is_active ? 'selected' : ''}>Active</option>
                            <option value="0" ${!standard.is_active ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>

                    <div class="step-buttons">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Standard
                        </button>
                        <button type="button" class="btn-secondary" onclick="closeModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            `;

            document.getElementById('editModal').style.display = 'block';
        } catch (error) {
            console.error('Error editing standard:', error);
            Swal.fire('Error', 'Failed to load standard for editing', 'error');
        }
    }

    async function updateStandard(id, e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const data = {
            standard_value: formData.get('standard_value'),
            description: formData.get('description'),
            display_order: formData.get('display_order'),
            is_active: formData.get('is_active')
        };

        try {
            const response = await fetch('{{ route("admin.taxonomy.standards.update", ":id") }}'.replace(':id', id), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Standard updated successfully',
                    showConfirmButton: false,
                    timer: 1500
                });

                closeModal();
                loadStandards();
            } else {
                throw new Error(result.message || 'Failed to update standard');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    async function deleteStandard(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the standard!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch('{{ route("admin.taxonomy.standards.delete", ":id") }}'.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Standard has been deleted successfully',
                    showConfirmButton: false,
                    timer: 1500
                });

                loadStandards();
            } else {
                throw new Error(data.message || 'Failed to delete standard');
            }
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }

    function closeForm() {
        document.getElementById('addFormContainer').style.display = 'none';
        document.getElementById('formContent').innerHTML = '';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('editFormContent').innerHTML = '';
    }
</script>
@endsection
