<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TaxonomyController extends Controller
{
    public function index()
    {
        return view('admin.taxonomy_management');
    }

    public function getCategories()
    {
        $categories = DB::table('product_categories')
            ->select('product_categories.*',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.category_id = product_categories.id) as product_count'),
                DB::raw('(SELECT COUNT(*) FROM product_subcategories WHERE product_subcategories.category_id = product_categories.id) as subcategory_count')
            )
            ->orderBy('display_order')
            ->get();

        return response()->json($categories);
    }

    public function getSubcategories()
    {
        $subcategories = DB::table('product_subcategories')
            ->select('product_subcategories.*',
                'product_categories.category_name',
                DB::raw('(SELECT COUNT(*) FROM product_examples WHERE product_examples.subcategory_id = product_subcategories.id) as product_example_count'),
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.subcategory_id = product_subcategories.id) as product_count')
            )
            ->join('product_categories', 'product_subcategories.category_id', '=', 'product_categories.id')
            ->orderBy('product_subcategories.display_order')
            ->get();

        return response()->json($subcategories);
    }

    public function getProducts()
    {
        $products = DB::table('product_examples')
            ->select('product_examples.*',
                'product_subcategories.subcategory_name',
                'product_categories.category_name',
                'product_categories.id as category_id'
            )
            ->join('product_subcategories', 'product_examples.subcategory_id', '=', 'product_subcategories.id')
            ->join('product_categories', 'product_subcategories.category_id', '=', 'product_categories.id')
            ->orderBy('product_examples.display_order')
            ->get();

        return response()->json($products);
    }

    public function getStandards()
    {
        $standards = DB::table('system_standards')
            ->orderBy('standard_type')
            ->orderBy('display_order')
            ->get();

        return response()->json($standards);
    }

    public function getSubcategoriesByCategory($categoryId)
    {
        $subcategories = DB::table('product_subcategories')
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return response()->json($subcategories);
    }

    public function saveMainCategory(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:100|unique:product_categories,category_name',
                'description' => 'nullable|string',
                'display_order' => 'integer|min:0',
                'subcategories' => 'required|array|min:1',
                'subcategories.*' => 'required|string|max:100',
                'products' => 'required|array|min:2',
                'products.*.name' => 'required|string|max:200',
                'products.*.subcategory_index' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $iconFilename = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                if ($file->getClientOriginalExtension() !== 'png') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only PNG images are allowed'
                    ], 422);
                }

                $filename = 'category_' . time() . '_' . uniqid() . '.png';
                $path = $request->file('image')->storeAs('taxonomy-icons', $filename, 'public_assets');

                if (!$path) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image'
                    ], 500);
                }

                $iconFilename = $filename;
            }

            $categoryId = DB::table('product_categories')->insertGetId([
                'category_name' => $request->category_name,
                'description' => $request->description,
                'icon_filename' => $iconFilename,
                'display_order' => $request->display_order ?? 0,
                'created_by_user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $subcategoryIds = [];
            foreach ($request->subcategories as $index => $subcategoryName) {
                $subId = DB::table('product_subcategories')->insertGetId([
                    'category_id' => $categoryId,
                    'subcategory_name' => $subcategoryName,
                    'display_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $subcategoryIds[$index] = $subId;
            }

            foreach ($request->products as $productData) {
                $subcategoryIndex = $productData['subcategory_index'];

                if (isset($subcategoryIds[$subcategoryIndex])) {
                    DB::table('product_examples')->insert([
                        'subcategory_id' => $subcategoryIds[$subcategoryIndex],
                        'product_name' => $productData['name'],
                        'display_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Main category created successfully with hierarchy'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveSubCategory(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:product_categories,id',
                'subcategory_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'display_order' => 'integer|min:0',
                'products' => 'required|array|min:2',
                'products.*' => 'required|string|max:200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subcategoryId = DB::table('product_subcategories')->insertGetId([
                'category_id' => $request->category_id,
                'subcategory_name' => $request->subcategory_name,
                'description' => $request->description,
                'display_order' => $request->display_order ?? 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($request->products as $productName) {
                DB::table('product_examples')->insert([
                    'subcategory_id' => $subcategoryId,
                    'product_name' => $productName,
                    'display_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sub-category created successfully with products'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save sub-category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'product_name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('product_examples')->insert([
            'subcategory_id' => $request->subcategory_id,
            'product_name' => $request->product_name,
            'description' => $request->description,
            'display_order' => $request->display_order ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully'
        ]);
    }

    public function editCategory($id)
    {
        $category = DB::table('product_categories')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json($category);
    }

    public function editSubcategory($id)
    {
        $subcategory = DB::table('product_subcategories')->find($id);

        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'message' => 'Sub-category not found'
            ], 404);
        }

        return response()->json($subcategory);
    }

    public function editProduct($id)
    {
        $product = DB::table('product_examples')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json($product);
    }

    public function updateCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100|unique:product_categories,category_name,' . $id,
            'description' => 'nullable|string',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = DB::table('product_categories')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $updateData = [
            'category_name' => $request->category_name,
            'description' => $request->description,
            'display_order' => $request->display_order,
            'is_active' => $request->is_active,
            'updated_at' => now()
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            if ($file->getClientOriginalExtension() !== 'png') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only PNG images are allowed'
                ], 422);
            }

            $filename = 'category_' . time() . '_' . uniqid() . '.png';
            $path = $request->file('image')->storeAs('taxonomy-icons', $filename, 'public_assets');

            if ($path) {
                if ($category->icon_filename) {
                    Storage::disk('public_assets')->delete('taxonomy-icons/' . $category->icon_filename);
                }
                $updateData['icon_filename'] = $filename;
            }
        }

        DB::table('product_categories')
            ->where('id', $id)
            ->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }

    public function updateSubcategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_name' => 'required|string|max:100|unique:product_subcategories,subcategory_name,' . $id,
            'description' => 'nullable|string',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $subcategory = DB::table('product_subcategories')->find($id);

        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'message' => 'Sub-category not found'
            ], 404);
        }

        DB::table('product_subcategories')
            ->where('id', $id)
            ->update([
                'category_id' => $request->category_id,
                'subcategory_name' => $request->subcategory_name,
                'description' => $request->description,
                'display_order' => $request->display_order,
                'is_active' => $request->is_active,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub-category updated successfully'
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:200|unique:product_examples,product_name,' . $id,
            'description' => 'nullable|string',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = DB::table('product_examples')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        DB::table('product_examples')
            ->where('id', $id)
            ->update([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'display_order' => $request->display_order,
                'is_active' => $request->is_active,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    }

    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();

            $category = DB::table('product_categories')->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $productCount = DB::table('products')->where('category_id', $id)->count();

            if ($productCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with associated products'
                ], 400);
            }

            $subcategories = DB::table('product_subcategories')->where('category_id', $id)->get();

            foreach ($subcategories as $subcategory) {
                DB::table('product_examples')->where('subcategory_id', $subcategory->id)->delete();
            }

            DB::table('product_subcategories')->where('category_id', $id)->delete();

            if ($category->icon_filename) {
                Storage::disk('public_assets')->delete('taxonomy-icons/' . $category->icon_filename);
            }

            DB::table('product_categories')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSubcategory($id)
    {
        try {
            DB::beginTransaction();

            $subcategory = DB::table('product_subcategories')->find($id);

            if (!$subcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub-category not found'
                ], 404);
            }

            $productCount = DB::table('products')->where('subcategory_id', $id)->count();

            if ($productCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete sub-category with associated products'
                ], 400);
            }

            $exampleCount = DB::table('product_examples')->where('subcategory_id', $id)->count();

            if ($exampleCount > 0) {
                DB::table('product_examples')->where('subcategory_id', $id)->delete();
            }

            DB::table('product_subcategories')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sub-category deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sub-category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($id)
    {
        $product = DB::table('product_examples')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $productUsage = DB::table('products')->where('product_name', 'LIKE', '%' . $product->product_name . '%')->count();

        if ($productUsage > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product example that is being used in active listings'
            ], 400);
        }

        DB::table('product_examples')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    // Standards Methods
    public function standardsIndex()
    {
        return view('admin.standards_management');
    }

    public function saveStandard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'standard_type' => 'required|in:unit_of_measure,quality_grade',
            'standard_value' => 'required|string|max:100|unique:system_standards,standard_value',
            'description' => 'nullable|string',
            'display_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('system_standards')->insert([
            'standard_type' => $request->standard_type,
            'standard_value' => $request->standard_value,
            'description' => $request->description,
            'display_order' => $request->display_order ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Standard added successfully'
        ]);
    }

    public function updateStandard(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'standard_value' => 'required|string|max:100|unique:system_standards,standard_value,' . $id,
            'description' => 'nullable|string',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('system_standards')
            ->where('id', $id)
            ->update([
                'standard_value' => $request->standard_value,
                'description' => $request->description,
                'display_order' => $request->display_order,
                'is_active' => $request->is_active,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Standard updated successfully'
        ]);
    }

    public function deleteStandard($id)
    {
        $standard = DB::table('system_standards')->find($id);

        if (!$standard) {
            return response()->json([
                'success' => false,
                'message' => 'Standard not found'
            ], 404);
        }

        DB::table('system_standards')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Standard deleted successfully'
        ]);
    }

    public function editStandard($id)
    {
        $standard = DB::table('system_standards')->find($id);

        if (!$standard) {
            return response()->json([
                'success' => false,
                'message' => 'Standard not found'
            ], 404);
        }

        return response()->json($standard);
    }
}
