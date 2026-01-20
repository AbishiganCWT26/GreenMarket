<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use PDF;

class ProductController extends Controller
{
	public function index(Request $request)
	{
		$leadFarmers = DB::table('lead_farmers')->orderBy('group_name')->get();
		$categories = DB::table('product_categories')->orderBy('category_name')->get();

		if ($request->ajax() || $request->has('ajax')) {
			$products = $this->getFilteredProducts($request);
			return response()->json(['products' => $products]);
		}

		return view('admin.products.index', compact('leadFarmers', 'categories'));
	}

	public function create()
	{
		$leadFarmers = DB::table('lead_farmers')->orderBy('group_name')->get();
		$categories = DB::table('product_categories')->orderBy('category_name')->get();
		$subcategories = DB::table('product_subcategories')->orderBy('subcategory_name')->get();

		$units = DB::table('system_standards')
			->where('standard_type', 'unit_of_measure')
			->where('is_active', true)
			->orderBy('display_order')
			->get();

		$grades = DB::table('system_standards')
			->where('standard_type', 'quality_grade')
			->where('is_active', true)
			->orderBy('display_order')
			->get();

		return view('admin.products.create', compact(
			'leadFarmers',
			'categories',
			'subcategories',
			'units',
			'grades'
		));
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'lead_farmer_id' => 'required|exists:lead_farmers,id',
			'farmer_id' => 'required|exists:farmers,id',
			'product_name' => 'required|string|max:255',
			'product_description' => 'nullable|string',
			'category_id' => 'required|exists:product_categories,id',
			'subcategory_id' => 'required|exists:product_subcategories,id',
			'type_variant' => 'nullable|string|max:50',
			'quantity' => 'required|numeric|min:0',
			'unit_of_measure' => 'required|string|max:20',
			'quality_grade' => 'required|string|max:50',
			'selling_price' => 'required|numeric|min:0',
			'pickup_address' => 'required|string',
			'pickup_map_link' => 'nullable|url',
			'expected_availability_date' => 'nullable|date',
			'product_photo' => 'nullable|image|max:4096',
			'is_available' => 'boolean'
		]);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		$data = $request->only([
			'lead_farmer_id',
			'farmer_id',
			'product_name',
			'product_description',
			'category_id',
			'subcategory_id',
			'type_variant',
			'quantity',
			'unit_of_measure',
			'quality_grade',
			'selling_price',
			'pickup_address',
			'pickup_map_link',
			'expected_availability_date',
			'is_available'
		]);

		$data['product_status'] = 'have it';
		$data['views_count'] = 0;
		$data['created_at'] = now();
		$data['updated_at'] = now();

		if ($request->hasFile('product_photo')) {
			$file = $request->file('product_photo');
			$filename = 'product_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

			$destinationPath = public_path('uploads/product_images');
			$file->move($destinationPath, $filename);

			$data['product_photo'] = $filename;
		}

		$productId = DB::table('products')->insertGetId($data);

		if ($productId) {
			return redirect()->route('admin.products.index')
				->with('success', 'Product created successfully!');
		}

		return redirect()->back()
			->with('error', 'Failed to create product. Please try again.')
			->withInput();
	}

	public function viewSales(Request $request)
	{
		$query = DB::table('orders')
			->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
			->leftJoin('lead_farmers', 'orders.lead_farmer_id', '=', 'lead_farmers.id')
			->select(
				'orders.*',
				'buyers.name as buyer_name',
				'lead_farmers.name as lead_farmer_name'
			)
			->whereIn('orders.order_status', ['paid', 'completed']);

		if ($request->has('start_date') && $request->start_date) {
			$query->whereDate('orders.created_at', '>=', $request->start_date);
		}

		if ($request->has('end_date') && $request->end_date) {
			$query->whereDate('orders.created_at', '<=', $request->end_date);
		}

		if ($request->has('lead_farmer_id') && $request->lead_farmer_id) {
			$query->where('orders.lead_farmer_id', $request->lead_farmer_id);
		}

		if ($request->has('farmer_id') && $request->farmer_id) {
			$query->where('orders.farmer_id', $request->farmer_id);
		}

		if ($request->has('status') && $request->status) {
			$query->where('orders.order_status', $request->status);
		}

		if ($request->has('search') && $request->search) {
			$search = $request->search;
			$query->where(function($q) use ($search) {
				$q->where('orders.order_number', 'like', "%{$search}%")
					->orWhere('buyers.name', 'like', "%{$search}%");
			});
		}

		$totalSales = $query->count();
		$totalAmount = $query->sum('total_amount');
		$uniqueBuyers = DB::table('orders')->distinct('buyer_id')->count('buyer_id');

		$sales = $query->orderBy('orders.created_at', 'desc')
			->paginate(15)
			->appends($request->query());

		$leadFarmers = DB::table('lead_farmers')->orderBy('name')->get();

		return view('admin.sales.view', compact(
			'sales',
			'leadFarmers',
			'totalSales',
			'totalAmount',
			'uniqueBuyers'
		));
	}

	public function exportPDF(Request $request)
	{
		try {
			\Log::info('Export PDF called with params:', $request->all());

			$query = DB::table('orders')
				->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
				->leftJoin('lead_farmers', 'orders.lead_farmer_id', '=', 'lead_farmers.id')
				->select(
					'orders.id',
					'orders.order_number',
					'orders.created_at',
					'buyers.name as buyer_name',
					'lead_farmers.name as lead_farmer_name',
					'orders.total_amount',
					'orders.order_status'
				)
				->whereIn('orders.order_status', ['paid', 'completed']);

			if ($request->has('start_date') && $request->start_date) {
				$query->whereDate('orders.created_at', '>=', $request->start_date);
			}

			if ($request->has('end_date') && $request->end_date) {
				$query->whereDate('orders.created_at', '<=', $request->end_date);
			}

			if ($request->has('lead_farmer_id') && $request->lead_farmer_id) {
				$query->where('orders.lead_farmer_id', $request->lead_farmer_id);
			}

			if ($request->has('farmer_id') && $request->farmer_id) {
				$query->where('orders.farmer_id', $request->farmer_id);
			}

			if ($request->has('status') && $request->status) {
				$query->where('orders.order_status', $request->status);
			}

			if ($request->has('search') && $request->search) {
				$search = $request->search;
				$query->where(function($q) use ($search) {
					$q->where('orders.order_number', 'like', "%{$search}%")
						->orWhere('buyers.name', 'like', "%{$search}%");
				});
			}

			$sales = $query->orderBy('orders.created_at', 'desc')->get();

			$stats = [
				'total_sales' => $sales->count(),
				'total_amount' => $sales->sum('total_amount'),
				'export_date' => now()->format('Y-m-d H:i:s'),
				'start_date' => $request->start_date ?? null,
				'end_date' => $request->end_date ?? null,
				'unique_buyers' => $sales->unique('buyer_name')->count(),
				'average_order_value' => $sales->count() > 0 ? $sales->avg('total_amount') : 0
			];

			$pdf = PDF::loadView('admin.sales.pdf_report', compact('sales', 'stats'))
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'arial',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false,
                    'dpi' => 72,
                    'enable_font_subsetting' => false,
                    'enable_unicode' => true,
                    'defaultMediaType' => 'screen',
                    'isFontSubsettingEnabled' => false,
                ]);

            return $pdf->download('sales-report-' . now()->format('Y-m-d-His') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Export PDF failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Export failed. Please try again.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

	public function salesDetails($id)
	{
		$order = DB::table('orders')
			->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
			->leftJoin('lead_farmers', 'orders.lead_farmer_id', '=', 'lead_farmers.id')
			->leftJoin('farmers', 'orders.farmer_id', '=', 'farmers.id')
			->select(
				'orders.*',
				'buyers.name as buyer_name',
				'buyers.primary_mobile as buyer_mobile',
				'lead_farmers.name as lead_farmer_name',
				'lead_farmers.primary_mobile as lead_farmer_mobile',
				'farmers.name as farmer_name'
			)
			->where('orders.id', $id)
			->first();

		if (!$order) {
			abort(404, 'Order not found');
		}

		$orderItems = DB::table('order_items')
			->leftJoin('products', 'order_items.product_id', '=', 'products.id')
			->where('order_items.order_id', $id)
			->select(
				'order_items.*',
				'products.product_name'
			)
			->get();

		return view('admin.sales.details', compact('order', 'orderItems'));
	}

	private function getFilteredProducts($request)
	{
		$query = DB::table('products')
			->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
			->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
			->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
			->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
			->select(
				'products.*',
				'farmers.name as farmer_name',
				'farmers.primary_mobile as farmer_mobile',
				'lead_farmers.group_name as lead_group_name',
				'lead_farmers.primary_mobile as lead_farmer_mobile',
				'product_categories.category_name as category_name',
				'product_subcategories.subcategory_name as subcategory_name'
			);

		if ($request->has('lead_farmer_id') && $request->lead_farmer_id) {
			$query->where('products.lead_farmer_id', $request->lead_farmer_id);
		}

		if ($request->has('farmer_id') && $request->farmer_id) {
			$query->where('products.farmer_id', $request->farmer_id);
		}

		if ($request->has('category_id') && $request->category_id) {
			$query->where('products.category_id', $request->category_id);
		}

		if ($request->has('search') && $request->search) {
			$search = $request->search;
			$query->where(function($q) use ($search) {
				$q->whereRaw('LOWER(products.product_name) LIKE ?', ['%' . strtolower($search) . '%'])
					->orWhereRaw('LOWER(product_categories.category_name) LIKE ?', ['%' . strtolower($search) . '%']);
			});
		}

		if ($request->has('price_range') && $request->price_range) {
			switch ($request->price_range) {
				case '0-100':
					$query->whereBetween('products.selling_price', [0, 100]);
					break;
				case '101-250':
					$query->whereBetween('products.selling_price', [101, 250]);
					break;
				case '251-500':
					$query->whereBetween('products.selling_price', [251, 500]);
					break;
				case '501-1000':
					$query->whereBetween('products.selling_price', [501, 1000]);
					break;
				case '1001+':
					$query->where('products.selling_price', '>=', 1001);
					break;
			}
		}

		if ($request->has('product_status') && $request->product_status) {
			$query->where('products.product_status', $request->product_status);
		}

		if ($request->has('is_available') && $request->is_available !== '') {
			if ($request->is_available === 'true') {
				$query->where('products.is_available', true);
			} elseif ($request->is_available === 'false') {
				$query->where('products.is_available', false);
			}
		}

		if ($request->has('coming_soon') && $request->coming_soon === 'true') {
			$currentDate = date('Y-m-d');
			$query->where('products.expected_availability_date', '>', $currentDate)
				->where('products.product_status', 'have it')
				->where('products.is_available', false);
		}

		return $query->orderBy('products.created_at', 'desc')->get();
	}

	private function sendSMS($to, $message)
	{
		try {
			$user = env('SMS_USER');
			$password = env('SMS_PASSWORD');
			$baseurl = env('SMS_API_URL');

			$text = urlencode($message);
			$url = "$baseurl/?id=$user&pw=$password&to=$to&text=$text";

			$response = Http::get($url);

			return $response->body();
		} catch (\Exception $e) {
			\Log::error('SMS sending failed: ' . $e->getMessage());
			return false;
		}
	}

	public function destroy($productId)
	{
		$product = DB::table('products')->where('id', $productId)->first();
		if (!$product) {
			return response()->json(['error' => 'Product not found'], 404);
		}

		$farmer = DB::table('farmers')->where('id', $product->farmer_id)->first();
		$leadFarmer = DB::table('lead_farmers')->where('id', $product->lead_farmer_id)->first();

		DB::table('products')->where('id', $productId)->update([
			'product_status' => 'removed by the admin',
			'is_available' => false,
			'updated_at' => now()
		]);

		if ($farmer && $farmer->primary_mobile) {
			$message = "The product '{$product->product_name}' has been removed by the Admin.";
			$this->sendSMS($farmer->primary_mobile, $message);
		}

		if ($leadFarmer && $leadFarmer->primary_mobile) {
			$message = "The product '{$product->product_name}' has been removed by the Admin.";
			$this->sendSMS($leadFarmer->primary_mobile, $message);
		}

		return response()->json([
			'success' => true,
			'message' => 'Product status changed to removed. SMS notifications sent to farmer and lead farmer.'
		]);
	}

	public function update(Request $request, $productId)
	{
		$product = DB::table('products')->where('id', $productId)->first();
		if (!$product) {
			return response()->json(['error' => 'Product not found'], 404);
		}

		$rules = [
			'product_name' => 'sometimes|required|string|max:255',
			'selling_price' => 'sometimes|required|numeric|min:0',
			'quantity' => 'sometimes|required|numeric|min:0',
			'unit_of_measure' => 'sometimes|required|string',
			'quality_grade' => 'sometimes|required|string',
			'product_photo' => 'nullable|image|max:4096',
			'is_available' => 'sometimes|boolean',
			'product_status' => 'sometimes|in:have it,removed by the admin,removed by lead farmer,removed by facilitator',
			'expected_availability_date' => 'nullable|date'
		];

		$v = Validator::make($request->all(), $rules);
		if ($v->fails()) {
			return response()->json(['errors' => $v->errors()], 422);
		}

		$update = [];
		foreach (['product_name','product_description','selling_price','quantity','unit_of_measure','quality_grade','category_id','subcategory_id','pickup_address','pickup_map_link','type_variant','is_available','product_status','expected_availability_date'] as $f) {
			if ($request->has($f)) {
				$update[$f] = $request->input($f);
			}
		}

		if ($request->has('product_status') && $request->product_status !== 'have it') {
			$removedStatus = $request->product_status;
			$update['product_status'] = $removedStatus;
			$update['is_available'] = false;

			$farmer = DB::table('farmers')->where('id', $product->farmer_id)->first();
			$leadFarmer = DB::table('lead_farmers')->where('id', $product->lead_farmer_id)->first();

			if ($farmer && $farmer->primary_mobile) {
				$message = "The product '{$product->product_name}' has been {$removedStatus}.";
				$this->sendSMS($farmer->primary_mobile, $message);
			}

			if ($leadFarmer && $leadFarmer->primary_mobile) {
				$message = "The product '{$product->product_name}' has been {$removedStatus}.";
				$this->sendSMS($leadFarmer->primary_mobile, $message);
			}
		}

		if ($request->hasFile('product_photo')) {
			$file = $request->file('product_photo');
			$filename = 'product_' . $productId . '_' . time() . '.' . $file->getClientOriginalExtension();

			$destinationPath = public_path('uploads/product_images');
			$file->move($destinationPath, $filename);

			if ($product->product_photo && file_exists($destinationPath . '/' . $product->product_photo)) {
				@unlink($destinationPath . '/' . $product->product_photo);
			}

			$update['product_photo'] = $filename;
		}

		if (!empty($update)) {
			$update['updated_at'] = now();
			DB::table('products')->where('id', $productId)->update($update);

			$updatedProduct = DB::table('products')
				->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
				->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
				->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
				->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
				->where('products.id', $productId)
				->select(
					'products.*',
					'farmers.name as farmer_name',
					'farmers.primary_mobile as farmer_mobile',
					'lead_farmers.group_name as lead_group_name',
					'lead_farmers.primary_mobile as lead_farmer_mobile',
					'product_categories.category_name as category_name',
					'product_subcategories.subcategory_name as subcategory_name'
				)
				->first();

			return response()->json([
				'success' => true,
				'message' => 'Product updated successfully.',
				'product' => $updatedProduct
			]);
		}

		return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
	}

	public function paginatedProducts(Request $request)
	{
		$perPage = 20;
		$page = $request->get('page', 1);
		$offset = ($page - 1) * $perPage;

		$baseQuery = DB::table('products')
			->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
			->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
			->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
			->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
			->select(
				'products.*',
				'farmers.name as farmer_name',
				'lead_farmers.group_name as lead_group_name',
				'product_categories.category_name as category_name',
				'product_subcategories.subcategory_name as subcategory_name'
			);

		$filteredQuery = clone $baseQuery;
		$filters = $request->except(['page', 'per_page']);
		$this->applyFilters($filteredQuery, $filters);

		$total = $filteredQuery->count();
		$products = $filteredQuery->offset($offset)->limit($perPage)->orderBy('products.created_at', 'desc')->get();

		$totalProductsQuery = DB::table('products');
		$haveItQuery = DB::table('products')->where('product_status', 'have it');
		$categoriesQuery = DB::table('products')
			->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
			->select('product_categories.category_name')
			->distinct();

		$totalProducts = $totalProductsQuery->count();
		$haveItCount = $haveItQuery->count();
		$totalCategories = $categoriesQuery->count();

		return response()->json([
			'products' => $products,
			'total' => $total,
			'per_page' => $perPage,
			'current_page' => (int)$page,
			'last_page' => ceil($total / $perPage),
			'total_stats' => [
				'total_products' => $totalProducts,
				'have_it_count' => $haveItCount,
				'total_categories' => $totalCategories
			]
		]);
	}

	private function applyFilters($query, $filters)
	{
		foreach ($filters as $key => $value) {
			if (!empty($value)) {
				switch ($key) {
					case 'lead_farmer_id':
						$query->where('products.lead_farmer_id', $value);
						break;
					case 'farmer_id':
						$query->where('products.farmer_id', $value);
						break;
					case 'category_id':
						$query->where('products.category_id', $value);
						break;
					case 'search':
						$query->where(function($q) use ($value) {
							$q->whereRaw('LOWER(products.product_name) LIKE ?', ['%' . strtolower($value) . '%'])
								->orWhereRaw('LOWER(product_categories.category_name) LIKE ?', ['%' . strtolower($value) . '%']);
						});
						break;
					case 'price_range':
						$this->applyPriceRangeFilter($query, $value);
						break;
					case 'product_status':
						$query->where('products.product_status', $value);
						break;
					case 'is_available':
						$query->where('products.is_available', $value === 'true');
						break;
					case 'coming_soon':
						if ($value === 'true') {
							$currentDate = date('Y-m-d');
							$query->where('products.expected_availability_date', '>', $currentDate)
								->where('products.product_status', 'have it')
								->where('products.is_available', false);
						}
						break;
				}
			}
		}
	}

	private function applyPriceRangeFilter($query, $range)
	{
		switch ($range) {
			case '0-100':
				$query->whereBetween('products.selling_price', [0, 100]);
				break;
			case '101-250':
				$query->whereBetween('products.selling_price', [101, 250]);
				break;
			case '251-500':
				$query->whereBetween('products.selling_price', [251, 500]);
				break;
			case '501-1000':
				$query->whereBetween('products.selling_price', [501, 1000]);
				break;
			case '1001+':
				$query->where('products.selling_price', '>=', 1001);
				break;
		}
	}

	public function getFarmersByLeadFarmer($leadFarmerId)
	{
		$farmers = DB::table('farmers')
			->where('lead_farmer_id', $leadFarmerId)
			->orderBy('name')
			->get();

		return response()->json(['farmers' => $farmers]);
	}

	public function getProductDetails($productId)
	{
		$product = DB::table('products')
			->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
			->leftJoin('lead_farmers', 'products.lead_farmer_id', '=', 'lead_farmers.id')
			->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
			->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
			->where('products.id', $productId)
			->select(
				'products.*',
				'farmers.name as farmer_name',
				'farmers.primary_mobile as farmer_mobile',
				'lead_farmers.group_name as lead_group_name',
				'lead_farmers.primary_mobile as lead_farmer_mobile',
				'product_categories.category_name as category_name',
				'product_subcategories.subcategory_name as subcategory_name'
			)
			->first();

		if (!$product) {
			return response()->json(['error' => 'Product not found'], 404);
		}

		$product->stock_status = $product->is_available ? 'In Stock' : 'Out of Stock';

		$currentDate = date('Y-m-d');
		$product->is_coming_soon = !empty($product->expected_availability_date) &&
								$product->expected_availability_date > $currentDate;

		return response()->json(['product' => $product]);
	}

	public function filter(Request $request)
	{
		$products = $this->getFilteredProducts($request);
		return response()->json(['products' => $products]);
	}
}
