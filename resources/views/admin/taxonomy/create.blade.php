@extends('admin.layouts.admin_master')

@section('title', 'Create Category or Standard')

@section('content')

<h2>Create New Taxonomy Item</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.taxonomy.create') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="category">Main Category</option>
                    <option value="subcategory">Sub Category</option>
                    <option value="standard">System Standard</option>
                </select>
            </div>

            <div class="form-group">
                <label>Name / Value</label>
                <input type="text" name="value" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Description (optional)</label>
                <textarea name="description" rows="3" class="form-control"></textarea>
            </div>

            <button class="btn btn-primary">Create</button>
        </form>
    </div>
</div>

@endsection
