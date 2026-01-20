<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class PublicController extends Controller
{
	public function index()
	{
		$products = DB::table('products')
			->select(
				'products.*',
				'product_categories.category_name',
				'product_subcategories.subcategory_name',
				DB::raw('farmers.name as farmer_name')
			)
			->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
			->leftJoin('product_subcategories', 'products.subcategory_id', '=', 'product_subcategories.id')
			->leftJoin('farmers', 'products.farmer_id', '=', 'farmers.id')
			->where('products.is_available', true)
			->where('products.quantity', '>', 0)
			->orderBy('products.created_at', 'desc')
			->paginate(8);

		$stats = [
			'total_products' => DB::table('products')->where('is_available', true)->count(),
			'registered_farmers' => DB::table('farmers')->where('is_active', true)->count(),
			'successful_orders' => DB::table('orders')->where('order_status', 'completed')->count(),
			'happy_buyers' => DB::table('orders')->distinct('buyer_id')->count('buyer_id')
		];

		$districts = DB::table('farmers')
			->select('district', DB::raw('COUNT(*) as farmer_count'))
			->where('is_active', true)
			->groupBy('district')
			->orderBy('farmer_count', 'desc')
			->get();

		$quality_grades = DB::table('system_standards')
			->where('standard_type', 'quality_grade')
			->where('is_active', true)
			->orderBy('display_order')
			->get();

		$categories = DB::table('product_categories')
			->select(
				'product_categories.*',
				DB::raw('(SELECT COUNT(*) FROM products WHERE products.category_id = product_categories.id AND products.is_available = true AND products.quantity > 0) as product_count')
			)
			->where('is_active', true)
			->orderBy('display_order')
			->get();

		$taxonomyData = DB::table('product_categories as pc')
			->select(
				'pc.id',
				'pc.category_name',
				'pc.description',
				'ps.subcategory_name',
				'pe.product_name as example_product'
			)
			->leftJoin('product_subcategories as ps', 'pc.id', '=', 'ps.category_id')
			->leftJoin('product_examples as pe', 'ps.id', '=', 'pe.subcategory_id')
			->where('pc.is_active', true)
			->where('ps.is_active', true)
			->where('pe.is_active', true)
			->orderBy('pc.display_order')
			->orderBy('ps.display_order')
			->orderBy('pe.display_order')
			->limit(20)
			->get();

		$groupedTaxonomy = [];
		foreach ($taxonomyData as $item) {
			if (!isset($groupedTaxonomy[$item->category_name])) {
				$groupedTaxonomy[$item->category_name] = [
					'description' => $item->description,
					'subcategories' => []
				];
			}

			if (!isset($groupedTaxonomy[$item->category_name]['subcategories'][$item->subcategory_name])) {
				$groupedTaxonomy[$item->category_name]['subcategories'][$item->subcategory_name] = [
					'examples' => []
				];
			}

			if ($item->example_product) {
				$groupedTaxonomy[$item->category_name]['subcategories'][$item->subcategory_name]['examples'][] = $item->example_product;
			}
		}

		return view('index', compact(
			'products',
			'stats',
			'districts',
			'quality_grades',
			'categories',
			'groupedTaxonomy'
		));
	}

	public function about()
	{
		return view('aboutus');
	}

	public function contactForm()
	{
		return view('contactus');
	}

	public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10'
        ]);

        try {
            $adminEmail = config('mail.admin_email') ?? env('MAIL_ADMIN_EMAIL') ?? 'koneswaramkovil@gmail.com';

            if (!$adminEmail) {
                throw new \Exception('Admin email is not configured.');
            }

            \Log::info('Attempting to send email to: ' . $adminEmail);
            \Log::info('Using mailer: ' . config('mail.default'));
            \Log::info('From address: ' . config('mail.from.address'));

            Mail::to($adminEmail)
                ->send(new ContactFormMail($request->all()));

            \Log::info('Email sent successfully to: ' . $adminEmail);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We will respond within 24 hours.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again later or contact us directly.'
            ], 500);
        }
    }

	public function howItWorks()
	{
		$categories = DB::table('product_categories')
			->select('id', 'category_name', 'description', 'icon_filename', 'display_order')
			->where('is_active', true)
			->orderBy('display_order')
			->orderBy('category_name')
			->get();

		$stats = [
			'total_categories' => DB::table('product_categories')->where('is_active', true)->count(),
			'total_products' => DB::table('products')->where('is_available', true)->count(),
			'active_farmers' => DB::table('farmers')->where('is_active', true)->count(),
			'total_buyers' => DB::table('buyers')->count()
		];

		return view('how-it-works', compact('categories', 'stats'));
	}
}
