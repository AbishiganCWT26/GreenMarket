@extends('admin.layouts.admin_master')

@section('title', 'Product Taxonomy & Category Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/taxonomy-manager.css') }}">
{{-- Assuming category manager styles are minimal or included in taxonomy-manager.css --}}
@endsection

@section('content')

<div class="card-panel">
    <h2><i class="fas fa-seedling"></i> Product Taxonomy & Category Management</h2>
    <p>Manage the hierarchical structure (Taxonomy Tree) and the flat list of categories (Category List) for your products.</p>
</div>

---

<div class="flex-row">
    {{-- Taxonomy Structure Panel (From original file 1) --}}
    <div class="card-panel taxonomy-structure-panel">
        <h3><i class="fas fa-sitemap"></i> Current Taxonomy Tree (Hierarchical View)</h3>
        <p class="text-secondary">Main Category > Sub-Category > Specific Product</p>
        <ul class="taxonomy-tree">
            {{-- Main Category Example 1 --}}
            <li class="category-main">
                <i class="fas fa-folder-open"></i> **Vegetables**
                <div class="actions">
                    <button class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </div>
                <ul>
                    {{-- Sub-Category Example --}}
                    <li class="category-sub">
                        <i class="fas fa-folder"></i> Leafy Greens
                        <div class="actions">
                            <button class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></button>
                        </div>
                        <ul>
                            {{-- Specific Product Example --}}
                            <li class="category-product">Spinach (Organic)</li>
                            <li class="category-product">Cabbage (Local Variety)</li>
                        </ul>
                    </li>
                    <li class="category-sub">
                        <i class="fas fa-folder"></i> Root Vegetables
                        <div class="actions">
                            <button class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></button>
                        </div>
                        <ul>
                            <li class="category-product">Carrot (Premium Grade)</li>
                            <li class="category-product">Beetroot</li>
                        </ul>
                    </li>
                </ul>
            </li>
            {{-- Main Category Example 2 --}}
             <li class="category-main">
                <i class="fas fa-folder-open"></i> **Fruits**
                <div class="actions">
                    <button class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </div>
            </li>
        </ul>
    </div>

    {{-- Add/Edit Form (From original file 1) --}}
    <div class="card-panel taxonomy-form-panel">
        <h3><i class="fas fa-plus"></i> Add New Taxonomy Item</h3>
        <form action="{{ route('admin.taxonomy.create') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="item_type">Type of Item</label>
                <select id="item_type" name="item_type" required onchange="toggleParentField(this.value)">
                    <option value="main">Main Category</option>
                    <option value="sub">Sub-Category</option>
                    <option value="product">Specific Product</option>
                </select>
            </div>

            <div class="form-group" id="parent-category-group" style="display: none;">
                <label for="parent_id">Select Parent Category</label>
                <select id="parent_id" name="parent_id">
                    <option value="">-- Select Parent --</option>
                    {{-- Options populated via AJAX based on item_type selection --}}
                    <option value="veg">Vegetables (Main)</option>
                    <option value="leafy">Leafy Greens (Sub)</option>
                </select>
                <small class="text-danger">Mandatory for Sub-Categories and Specific Products.</small>
            </div>

            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" placeholder="e.g., Leafy Greens, Organic Tomatoes" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Taxonomy Item</button>
        </form>
    </div>
</div>

---

{{-- Flat Category List (Integrated from original file 2) --}}
<div class="card-panel mt-4">
    <h3><i class="fas fa-list-alt"></i> Category List (Flat View)</h3>
    <a class="btn btn-success mb-3" href="{{ route('admin.taxonomy.create') }}"><i class="fas fa-plus"></i> Add New Category/Item</a>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Description</th>
                <th>Order</th>
                <th>Status</th>
                <th>Actions</th> {{-- Added Actions column for consistency --}}
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->category_name }}</td>
                <td>{{ $c->description }}</td>
                <td>{{ $c->display_order }}</td>
                <td>{{ $c->is_active ? 'Active' : 'Inactive' }}</td>
                <td>
                    <button class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Placeholder to handle the case where $categories might not be defined if the original file 1 logic is used --}}
    @empty($categories)
        <p class="text-warning">No categories found to display in the flat list. Ensure the '$categories' variable is passed to this view.</p>
    @endempty
</div>

@endsection

@section('scripts')
<script>
    function toggleParentField(type) {
        const parentGroup = document.getElementById('parent-category-group');
        const parentSelect = document.getElementById('parent_id');

        // Rules: Sub-category and Product need a parent. Main category does not.
        if (type === 'sub' || type === 'product') {
            parentGroup.style.display = 'block';
            parentSelect.setAttribute('required', 'required');
            // In a real app, an AJAX call would update parentSelect options here, fetching parents
            // based on the selected item type (e.g., if adding a 'product', show only 'sub-categories' as parents).
            console.log('Fetching possible parent categories for type: ' + type);
        } else {
            parentGroup.style.display = 'none';
            parentSelect.removeAttribute('required');
        }
    }

    // Initialize the script based on the default selected option (Main Category)
    document.addEventListener('DOMContentLoaded', () => {
        toggleParentField(document.getElementById('item_type').value);
    });
</script>
@endsection
