<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuyerProductRequest;
use App\Models\Buyer;
use App\Models\User;
use App\Mail\BuyerRequestDeletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyerRequestProductsController extends Controller
{
	public function index(Request $request)
	{
		$search = $request->input('search');

		$query = BuyerProductRequest::with(['buyer.user'])
			->orderBy('created_at', 'desc');

		if ($search) {
			$query->where(function($q) use ($search) {
				$q->where('product_name', 'ilike', "%{$search}%")
					->orWhere('description', 'ilike', "%{$search}%")
					->orWhereHas('buyer', function($buyerQuery) use ($search) {
						$buyerQuery->where('name', 'ilike', "%{$search}%")
							->orWhere('business_name', 'ilike', "%{$search}%");
					});
			});
		}

		if ($request->ajax()) {
			$view = $request->input('view', 'table');
			$perPage = $view === 'card' ? 12 : 10;

			$buyerRequests = $query->paginate($perPage);

			foreach ($buyerRequests as $requestItem) {
				$requestItem->formatted_date = \Carbon\Carbon::parse($requestItem->needed_date)->format('M d, Y');
				$requestItem->formatted_quantity = number_format($requestItem->needed_quantity, 2) . ' ' . ($requestItem->unit_of_measure ?? '');
				$requestItem->formatted_price = $requestItem->unit_price ? 'Rs. ' . number_format($requestItem->unit_price, 2) : 'Not specified';

				$imagePath = public_path('uploads/buyer_product_requests/' . $requestItem->product_image);
				$placeholderPath = public_path('assets/images/product-placeholder.png');

				if ($requestItem->product_image && file_exists($imagePath)) {
					$requestItem->image_url = asset('uploads/buyer_product_requests/' . $requestItem->product_image);
				} else {
					$requestItem->image_url = asset('assets/images/product-placeholder.png');
				}
			}

			if ($view === 'card') {
				return view('admin.buyer-requests.card-view', compact('buyerRequests'));
			} else {
				return view('admin.buyer-requests.table-view', compact('buyerRequests'));
			}
		}

		$buyerRequests = $query->paginate(10);

		foreach ($buyerRequests as $requestItem) {
			$requestItem->formatted_date = \Carbon\Carbon::parse($requestItem->needed_date)->format('M d, Y');
			$requestItem->formatted_quantity = number_format($requestItem->needed_quantity, 2) . ' ' . ($requestItem->unit_of_measure ?? '');
			$requestItem->formatted_price = $requestItem->unit_price ? 'Rs. ' . number_format($requestItem->unit_price, 2) : 'Not specified';

			$imagePath = public_path('uploads/buyer_product_requests/' . $requestItem->product_image);
			$placeholderPath = public_path('assets/images/product-placeholder.png');

			if ($requestItem->product_image && file_exists($imagePath)) {
				$requestItem->image_url = asset('uploads/buyer_product_requests/' . $requestItem->product_image);
			} else {
				$requestItem->image_url = asset('assets/images/product-placeholder.png');
			}
		}

		return view('admin.buyer-requests.index', compact('buyerRequests'));
	}

	private function sendSMS($to, $message)
	{
		try {
			$user = env('SMS_USER');
			$password = env('SMS_PASSWORD');
			$baseurl = env('SMS_API_URL');

			if (!$user || !$password || !$baseurl) {
				Log::error('SMS credentials not configured');
				return false;
			}

			$text = urlencode($message);
			$url = "{$baseurl}/?id={$user}&pw={$password}&to={$to}&text={$text}";

			$ch = curl_init($url);
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => false,
				CURLOPT_TIMEOUT => 30,
			]);

			$content = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			Log::info("SMS Response: {$content}");

			if (strpos($content, 'OK:') === 0) {
				return true;
			}

			return false;
		} catch (\Exception $e) {
			Log::error('SMS sending failed: ' . $e->getMessage());
			return false;
		}
	}

	public function destroy($id)
	{
		try {
			DB::beginTransaction();

			$buyerRequest = BuyerProductRequest::with(['buyer.user'])->findOrFail($id);
			$buyer = $buyerRequest->buyer;
			$user = $buyer->user;

			$buyerRequest->delete();

			if ($user && $user->email) {
				try {
					$logoController = new \App\Http\Controllers\LogoController();
					$logoBase64 = $logoController->getLogoBase64();

					$mailData = [
						'product_name' => $buyerRequest->product_name,
						'buyer_name' => $buyer->name,
						'needed_date' => $buyerRequest->needed_date,
						'quantity' => $buyerRequest->needed_quantity . ($buyerRequest->unit_of_measure ? ' ' . $buyerRequest->unit_of_measure : ''),
						'logo_base64' => $logoBase64,
					];

					Mail::to($user->email)->send(new BuyerRequestDeletedMail($mailData));
				} catch (\Exception $e) {
					Log::error('Email sending failed: ' . $e->getMessage());
				}
			}

			if ($buyer && $buyer->primary_mobile) {
				$message = "Dear {$buyer->name}, your product request for '{$buyerRequest->product_name}' has been removed by admin as it doesn't meet system requirements. ";
				$this->sendSMS($buyer->primary_mobile, $message);
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => 'Buyer request deleted successfully. Buyer has been notified via email and SMS.'
			]);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('Delete buyer request failed: ' . $e->getMessage());

			return response()->json([
				'success' => false,
				'message' => 'Failed to delete buyer request. Please try again.'
			], 500);
		}
	}
}
