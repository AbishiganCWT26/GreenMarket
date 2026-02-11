<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\LeadFarmerController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\LeadfarmerControlleradmin;
use App\Http\Controllers\Admin\BuyerRequestProductsController;
use App\Http\Controllers\Admin\NotificationAdminController;
use App\Http\Controllers\NotificationController;

Route::get('/', [PublicController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOTP'])->name('password.verify.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('password.verify.otp.submit');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.submit');

Route::view('/about-us', 'aboutus')->name('about');

Route::get('/contact-us', [PublicController::class, 'contactForm'])->name('contact.form');
Route::post('/contact-us/send', [PublicController::class, 'sendContact'])->name('contact.send');

Route::get('/register/buyer', [BuyerController::class, 'showRegistrationForm'])->name('buyer.register');
Route::post('/register/buyer', [BuyerController::class, 'register'])->name('buyer.register.submit');

Route::get('/how-it-works', [PublicController::class, 'howItWorks'])->name('how.it.works');

Route::get('/test-email', function() {
    try {
        // 1. Define the logo path
        $logoPath = public_path('assets/images/Logo-4.png');

        // 2. Fallback check for SVG if PNG doesn't exist
        if (!File::exists($logoPath)) {
            $logoPath = public_path('assets/images/Logo-4.svg');
        }

        // 3. Send HTML email with embedded image
        Mail::send([], [], function($message) use ($logoPath) {
            $message->to('trincoabishigan@gmail.com')
                    ->subject('Test Email with Logo');

            // Embed the logo
            $logoCid = $message->embed($logoPath);

            // Set the body as HTML
            $message->html("
                <div style='font-family: sans-serif; text-align: center;'>
                    <img src='{$logoCid}' alt='GreenMarket' style='width: 150px;'>
                    <hr>
                    <h1>Test Email Successful!</h1>
                    <p>This email confirms that your embedded logo system is working correctly.</p>
                </div>
            ");
        });

        return 'Email sent successfully with logo!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/mail-preview', function () {
    $data = [
        'name' => 'Dulaji',
        'username' => 'Dulaji123',
        'email' => 'Dulaj@CreativeSoftware.com',
        'password' => 'Dulaji@123',
        'login_url' => 'https://smartmarket.com/login'
    ];

    return view('emails.buyerRegistrationMail', $data);
});


/*
|--------------------------------------------------------------------------
| farmer ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('farmer')
    ->middleware(['auth', \App\Http\Middleware\FarmerMiddleware::class])
    ->group(function () {

    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('farmer.dashboard');

    Route::get('/products/my-products', [FarmerController::class, 'myProducts'])->name('farmer.products.my-products');
    Route::get('/products/add', [FarmerController::class, 'addProduct'])->name('farmer.products.add');
    Route::post('/products/store', [FarmerController::class, 'storeProduct'])->name('farmer.products.store');
    Route::get('/products/view/{id}', [FarmerController::class, 'viewProduct'])->name('farmer.products.view');
    Route::get('/products/removed', [FarmerController::class, 'removedProducts'])->name('farmer.products.removed');

    Route::get('/orders/active', [FarmerController::class, 'activeOrders'])->name('farmer.orders.active');
    Route::get('/orders/history', [FarmerController::class, 'orderHistory'])->name('farmer.orders.history');
    Route::get('/orders/view/{id}', [FarmerController::class, 'getOrderDetails'])->name('farmer.orders.view');
    Route::put('/orders/mark-ready/{id}', [FarmerController::class, 'markOrderReady'])->name('farmer.orders.mark-ready');

    Route::get('/complaints/create', [FarmerController::class, 'createComplaint'])->name('farmer.complaints.create');
    Route::post('/complaints/store', [FarmerController::class, 'storeComplaint'])->name('farmer.complaints.store');
    Route::get('/complaints/list', [FarmerController::class, 'listComplaints'])->name('farmer.complaints.list');
    Route::get('/complaints/view/{id}', [FarmerController::class, 'viewComplaint'])->name('farmer.complaints.view');
    Route::delete('/complaints/delete/{id}', [FarmerController::class, 'deleteComplaint'])->name('farmer.complaints.delete');

    Route::get('/profile', [FarmerController::class, 'profile'])->name('farmer.profile.profile');
    Route::post('/profile/update', [FarmerController::class, 'updateProfile'])->name('farmer.profile.update');

    Route::get('/profile/photo', [FarmerController::class, 'profilePhoto'])->name('farmer.profile.photo');
    Route::post('/profile/photo/update', [FarmerController::class, 'updateProfilePhoto'])->name('farmer.profile.photo.update');
    Route::delete('/profile/photo/delete', [FarmerController::class, 'deleteProfilePhoto'])->name('farmer.profile.photo.delete');

    Route::get('/profile/settings', [FarmerController::class, 'profileSettings'])->name('farmer.profile.settings');
    Route::post('/profile/settings/update-password', [FarmerController::class, 'updatePassword'])->name('farmer.profile.settings.update-password');

    Route::get('/profile/payment', [FarmerController::class, 'paymentSettings'])->name('farmer.profile.payment');
    Route::post('/profile/settings/update-payment', [FarmerController::class, 'updatePaymentSettings'])->name('farmer.profile.settings.update-payment');

    Route::get('/notifications', [FarmerController::class, 'notifications'])->name('farmer.notifications');
    Route::post('/notifications/mark-read', [FarmerController::class, 'markNotificationRead'])->name('farmer.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [FarmerController::class, 'markAllNotificationsRead'])->name('farmer.notifications.mark-all-read');

    Route::get('/product-requests', [FarmerController::class, 'viewProductRequests'])->name('farmer.productRequests');
    Route::get('/product-requests/{id}/details', [FarmerController::class, 'getRequestDetails'])->name('farmer.productRequest.details');
});

/*
|--------------------------------------------------------------------------
| LEAD FARMER ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('lead-farmer')
    ->middleware(['auth', \App\Http\Middleware\LeadFarmerMiddleware::class])
    ->group(function () {

    Route::get('/dashboard', [LeadFarmerController::class, 'dashboard'])->name('lf.dashboard');
    Route::get('/register-farmer', [LeadFarmerController::class, 'registerFarmer'])->name('lf.registerFarmer');
    Route::post('/register-farmer', [LeadFarmerController::class, 'storeFarmer'])->name('lf.storeFarmer');
    Route::get('/manage-farmers', [LeadFarmerController::class, 'manageFarmers'])->name('lf.manageFarmers');

    Route::get('/farmer-details/{id}', [LeadFarmerController::class, 'getFarmerDetails'])->name('lf.farmer.details');
    Route::get('/edit-farmer/{id}', [LeadFarmerController::class, 'editFarmer'])->name('lf.editFarmer');
    Route::post('/update-farmer/{id}', [LeadFarmerController::class, 'updateFarmer'])->name('lf.updateFarmer');
    Route::delete('/delete-farmer/{id}', [LeadFarmerController::class, 'deleteFarmer'])->name('lf.farmer.delete');
    Route::delete('/farmers/{id}', [LeadFarmerController::class, 'deleteFarmer'])->name('lf.deleteFarmer');
    Route::post('/farmer/{id}/send-otp', [LeadFarmerController::class, 'sendUpdateOtp'])->name('lf.farmer.sendUpdateOtp');
    
    Route::get('/add-product', [LeadFarmerController::class, 'addProduct'])->name('lf.addProduct');
    Route::post('/add-product', [LeadFarmerController::class, 'storeProduct'])->name('lf.storeProduct');
    Route::get('/manage-products', [LeadFarmerController::class, 'manageProducts'])->name('lf.manageProducts');
    Route::get('/edit-product/{id}', [LeadFarmerController::class, 'editProduct'])->name('lf.editProduct');
    Route::post('/update-product/{id}', [LeadFarmerController::class, 'updateProduct'])->name('lf.updateProduct');
    Route::delete('/delete-product/{id}', [LeadFarmerController::class, 'deleteProduct'])->name('lf.deleteProduct');
    Route::get('/product-details/{id}', [LeadFarmerController::class, 'getProductDetails'])->name('lf.product.details');
    
    Route::post('/product/{id}/send-otp', [LeadFarmerController::class, 'sendProductUpdateOtp'])->name('lf.sendProductUpdateOtp');
    Route::post('/product/{id}/verify-otp', [LeadFarmerController::class, 'verifyProductUpdateOtp'])->name('lf.verifyProductUpdateOtp');
    

    Route::get('/orders', [LeadFarmerController::class, 'viewOrders'])->name('lf.orders');
    Route::post('/orders/mark-payment', [LeadFarmerController::class, 'markPaymentReceived'])->name('lf.orders.markPayment');
    Route::post('/orders/update-status', [LeadFarmerController::class, 'updateOrderStatus'])->name('lf.orders.updateStatus');
    Route::get('/orders/{id}', [LeadFarmerController::class, 'viewOrder'])->name('lf.orders.view');

    Route::get('/profile', [LeadFarmerController::class, 'editProfile'])->name('lf.profile');
    Route::put('/profile/update', [LeadFarmerController::class, 'updateProfile'])->name('lf.profile.update');
    Route::post('/profile/update-password', [LeadFarmerController::class, 'updatePassword'])->name('lf.profile.update.password');
    Route::get('/profile/photo', [LeadFarmerController::class, 'showPhotoForm'])->name('lf.profile.photo');
    Route::post('/profile/photo', [LeadFarmerController::class, 'updatePhoto'])->name('lf.profile.photo.update');

    Route::get('/reports/sales', [LeadFarmerController::class, 'salesReports'])->name('lf.reports.sales');
    Route::get('/reports/inventory', [LeadFarmerController::class, 'inventoryReports'])->name('lf.reports.inventory');
    Route::get('/reports/farmer-performance', [LeadFarmerController::class, 'farmerPerformanceReports'])->name('lf.reports.farmer-performance');

    Route::get('/notifications', [LeadFarmerController::class, 'notifications'])->name('lf.notifications');
    Route::post('/notifications/mark-all-read', [LeadFarmerController::class, 'markAllNotificationsRead'])->name('lf.notifications.mark-all-read');
    Route::post('/notifications/{id}/read', [LeadFarmerController::class, 'markNotificationRead'])->name('lf.notifications.read');

    Route::get('/get-subcategories/{categoryId}', [LeadFarmerController::class, 'getSubcategories'])->name('lf.getSubcategories');
    Route::get('/get-product-examples/{subcategoryId}', [LeadFarmerController::class, 'getProductExamples'])->name('lf.getProductExamples');
});

/*
|--------------------------------------------------------------------------
| BUYER ROUTES - FIXED ROUTE NAMES
|--------------------------------------------------------------------------
*/
Route::prefix('buyer')
    ->middleware(['auth', \App\Http\Middleware\BuyerMiddleware::class])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('buyer.dashboard');

    // Browse Products
    Route::get('/browse-products', [BuyerController::class, 'browseProducts'])->name('buyer.browseProducts');

    // Product Details
    Route::get('/product/{id}', [BuyerController::class, 'productDetail'])->name('buyer.productDetail');

    // Cart Routes
    Route::get('/cart', [BuyerController::class, 'cart'])->name('buyer.cart');
    Route::post('/cart/add/{productId}', [BuyerController::class, 'addToCart'])->name('buyer.addToCart');
    Route::delete('/cart/remove/{cartItemId}', [BuyerController::class, 'removeFromCart'])->name('buyer.removeFromCart');
    Route::post('/cart/update/{cartItemId}', [BuyerController::class, 'updateCartQuantity'])->name('buyer.updateCartQuantity');

    // Order History
    Route::get('/history', [BuyerController::class, 'history'])->name('buyer.history');

    // Wishlist
    Route::get('/wishlist', [BuyerController::class, 'wishlist'])->name('buyer.wishlist');
    Route::post('/wishlist/add', [BuyerController::class, 'addToWishlist'])->name('buyer.addToWishlist');
    Route::post('/wishlist/remove', [BuyerController::class, 'removeFromWishlist'])->name('buyer.removeFromWishlist');
    Route::delete('/wishlist/remove/{wishlistId}', [BuyerController::class, 'removeFromWishlistById'])->name('buyer.removeFromWishlistById');

    // Notifications
    Route::get('/notifications', [BuyerController::class, 'notifications'])->name('buyer.notifications');

    // Address Book
    Route::get('/addresses', [BuyerController::class, 'addressBook'])->name('buyer.addresses');

    // Profile Management
    Route::get('/profile', [BuyerController::class, 'profile'])->name('buyer.profile.profile');
    Route::put('/profile/update', [BuyerController::class, 'updateProfile'])->name('buyer.profile.update');

    // Profile Photo Routes
    Route::get('/profile/photo', [BuyerController::class, 'showPhotoForm'])->name('buyer.profile.photo');
    Route::put('/profile/photo', [BuyerController::class, 'updatePhoto'])->name('buyer.profile.photo.update');
    Route::delete('/profile/photo', [BuyerController::class, 'deletePhoto'])->name('buyer.profile.photo.delete');

    // Business Details
    Route::put('/business', [BuyerController::class, 'updateBusiness'])->name('buyer.business.update');

    // Password Change
    Route::put('/password', [BuyerController::class, 'changePassword'])->name('buyer.password.update');

    // Invoice - PDF Generation
    Route::get('/invoice/{orderId}', [BuyerController::class, 'generateInvoice'])->name('buyer.invoice');

    // Invoice Data API (for AJAX) - ADD THIS ROUTE
    Route::get('/invoice/data/{orderId}', [BuyerController::class, 'getInvoiceData'])->name('buyer.invoice.data');

    // Get Subcategories
    Route::get('/get-subcategories', [BuyerController::class, 'getSubcategories'])->name('buyer.getSubcategories');

    // Checkout Routes
    Route::get('/checkout', [BuyerController::class, 'checkout'])->name('buyer.checkout');
    Route::post('/checkout/place-order', [BuyerController::class, 'placeOrder'])->name('buyer.placeOrder');
    Route::post('/checkout/payment', [BuyerController::class, 'processPayment'])->name('buyer.processPayment');
    Route::get('/checkout/success/{orderId}', [BuyerController::class, 'checkoutSuccess'])->name('buyer.checkoutSuccess');
    Route::get('/checkout/failed', [BuyerController::class, 'checkoutFailed'])->name('buyer.checkoutFailed');

    // Order Actions
    Route::post('/order/{orderId}/feedback', [BuyerController::class, 'submitFeedback'])->name('buyer.order.feedback');

    // Cancel Order
    Route::post('/order/{orderId}/cancel', [BuyerController::class, 'cancelOrder'])->name('buyer.order.cancel');

    // Alternative routes for compatibility
    Route::get('/shopping-cart', [BuyerController::class, 'cart'])->name('buyer.shoppingCart');
    Route::get('/order-history', [BuyerController::class, 'history'])->name('buyer.orderHistory');
    Route::get('/edit-profile', [BuyerController::class, 'profile'])->name('buyer.editProfile');
    Route::get('/product/view/{id}', [BuyerController::class, 'viewProduct'])->name('buyer.viewProduct');

    // Product Requests Routes
    Route::get('/product-request/create', [BuyerController::class, 'createProductRequestForm'])->name('buyer.productRequest.create');
    Route::post('/product-request/store', [BuyerController::class, 'storeProductRequest'])->name('buyer.productRequest.store');
    Route::get('/product-requests/my', [BuyerController::class, 'myProductRequests'])->name('buyer.productRequests.my');
    Route::put('/product-request/{id}/status', [BuyerController::class, 'updateRequestStatus'])->name('buyer.productRequest.updateStatus');
    Route::post('/product-requests/check-expired', [BuyerController::class, 'checkExpiredRequests'])->name('buyer.productRequests.checkExpired');
    Route::delete('/product-request/{id}/delete', [BuyerController::class, 'deleteRequest'])->name('buyer.productRequest.delete');

    // Buyer Complaints Routes
    Route::prefix('complaints')->group(function () {
        Route::get('/create', [BuyerController::class, 'createComplaint'])->name('buyer.complaints.create');
        Route::post('/store', [BuyerController::class, 'storeComplaint'])->name('buyer.complaints.store');
        Route::get('/list', [BuyerController::class, 'listComplaints'])->name('buyer.complaints.list');
        Route::get('/view/{id}', [BuyerController::class, 'viewComplaint'])->name('buyer.complaints.view');
        Route::delete('/delete/{id}', [BuyerController::class, 'deleteComplaint'])->name('buyer.complaints.delete');
        Route::put('/update/{id}', [BuyerController::class, 'updateComplaint'])->name('buyer.complaints.update');
    });

});

/*
|--------------------------------------------------------------------------
| FACILITATOR ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('facilitator')
    ->middleware(['auth', \App\Http\Middleware\FacilitatorMiddleware::class])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [FacilitatorController::class, 'dashboard'])->name('facilitator.dashboard');

    Route::get('/taxonomy', [FacilitatorController::class, 'taxonomyManagement'])->name('facilitator.taxonomy');
    Route::post('/taxonomy/category/store', [FacilitatorController::class, 'storeCategory'])->name('facilitator.taxonomy.category.store');
    Route::post('/taxonomy/category/update', [FacilitatorController::class, 'updateCategory'])->name('facilitator.taxonomy.category.update');
    Route::post('/taxonomy/subcategory/store', [FacilitatorController::class, 'storeSubcategory'])->name('facilitator.taxonomy.subcategory.store');
    Route::post('/taxonomy/subcategory/update', [FacilitatorController::class, 'updateSubcategory'])->name('facilitator.taxonomy.subcategory.update');
    Route::post('/taxonomy/product/store', [FacilitatorController::class, 'storeProductExample'])->name('facilitator.taxonomy.product.store');
    Route::post('/taxonomy/product/update', [FacilitatorController::class, 'updateProductExample'])->name('facilitator.taxonomy.product.update');

    // Standards Management - UPDATED ROUTES
    Route::get('/unit-of-measures', [FacilitatorController::class, 'unitOfMeasures'])->name('facilitator.unit-of-measures');

    // Store new unit
    Route::post('/unit-of-measures/store', [FacilitatorController::class, 'storeUnitOfMeasure'])->name('facilitator.unit-of-measures.store');

    // Update unit - using POST with method override for PUT
    Route::post('/unit-of-measures/{id}/update', [FacilitatorController::class, 'updateUnitOfMeasure'])->name('facilitator.unit-of-measures.update');

    // Deactivate/Activate routes (kept for reference but not used in UI)
    Route::post('/unit-of-measures/{id}/deactivate', [FacilitatorController::class, 'deactivateUnitOfMeasure'])->name('facilitator.unit-of-measures.deactivate');
    Route::post('/unit-of-measures/{id}/activate', [FacilitatorController::class, 'activateUnitOfMeasure'])->name('facilitator.unit-of-measures.activate');

    // Quality Grades Routes
    Route::get('/quality-grades', [FacilitatorController::class, 'qualityGrades'])->name('facilitator.quality-grades');
    Route::post('/quality-grades/store', [FacilitatorController::class, 'storeQualityGrade'])->name('facilitator.quality-grades.store');
    Route::post('/quality-grades/{id}/update', [FacilitatorController::class, 'updateQualityGrade'])->name('facilitator.quality-grades.update');
    Route::post('/quality-grades/{id}/activate', [FacilitatorController::class, 'activateQualityGrade'])->name('facilitator.quality-grades.activate');

    // User Management
    Route::get('/users', [FacilitatorController::class, 'userManagement'])->name('facilitator.users');
    Route::post('/users/{user}/{action}', function ($userId, $action) {
        $validActions = ['deactivate', 'activate'];

        if (!in_array($action, $validActions)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid action'
            ], 400);
        }

        try {
            $user = \App\Models\User::findOrFail($userId);

            // Prevent self-modification
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot modify your own status'
                ], 400);
            }

            $user->is_active = ($action === 'activate');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User ' . $action . 'd successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    })->name('facilitator.users.status');

    Route::get('/users/{id}/profile', [FacilitatorController::class, 'userProfile'])->name('facilitator.users.profile');
    Route::post('/users/{id}/send-otp', [FacilitatorController::class, 'sendEditOTP'])->name('facilitator.users.send-otp');
    Route::post('/users/verify-otp', [FacilitatorController::class, 'verifyEditOTP'])->name('facilitator.users.verify-otp');
    Route::get('/users/{id}/edit-data', [FacilitatorController::class, 'getUserForEdit'])->name('facilitator.users.edit-data');
    Route::post('/users/{id}/update', [FacilitatorController::class, 'updateUser'])->name('facilitator.users.update');
    Route::post('/users/export', [FacilitatorController::class, 'exportUsers'])->name('facilitator.users.export');


    // Complaints
    Route::get('/complaints', [FacilitatorController::class, 'complaints'])->name('facilitator.complaints');

    // Profile Management
    Route::get('/profile', [FacilitatorController::class, 'editProfile'])->name('facilitator.profile');
    Route::post('/profile/update', [FacilitatorController::class, 'updateProfile'])->name('facilitator.profile.update');
    Route::get('/profile/photo', [FacilitatorController::class, 'profilePhoto'])->name('facilitator.profile.photo');
    Route::post('/profile/photo', [FacilitatorController::class, 'updatePhoto'])->name('facilitator.profile.photo.update');

    // Account Settings
    Route::get('/account/settings', [FacilitatorController::class, 'accountSettings'])->name('facilitator.account.settings');
    Route::post('/account/settings', [FacilitatorController::class, 'updateAccountSettings'])->name('facilitator.account.settings.update');

    // Notifications
    Route::get('/notifications', [FacilitatorController::class, 'notifications'])->name('facilitator.notifications');
    Route::post('/notifications/mark-all-read', [FacilitatorController::class, 'markAllNotificationsAsRead'])->name('facilitator.notifications.mark-all-read');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
	->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
	->name('admin.')
	->group(function () {

	Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

	Route::get('/notifications', [NotificationAdminController::class, 'index'])->name('notifications.index');
	Route::get('/notifications/search', [NotificationAdminController::class, 'search'])->name('notifications.search');
	Route::post('/notifications/send', [NotificationAdminController::class, 'sendNotification'])->name('notifications.send');
	Route::post('/notifications/mark-all-read', [NotificationAdminController::class, 'markAllAsRead'])->name('notifications.markAllRead');
	Route::post('/notifications/mark-read/{id}', [NotificationAdminController::class, 'markAsRead'])->name('notifications.markRead');

	Route::get('/users', [UserController::class, 'index'])->name('users.index');
	Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
	Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
	Route::post('/users', [UserController::class, 'store'])->name('users.store');
	Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
	Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
	Route::post('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
	Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
	Route::post('/users/{id}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
	Route::post('/users/{id}/promote', [UserController::class, 'promote'])->name('users.promote');
	Route::post('/users/{id}/make-subadmin', [UserController::class, 'makeSubadmin'])->name('users.makeSubadmin');
	Route::post('/users/send-otp', [UserController::class, 'sendOtp'])->name('users.sendOtp');
	Route::post('/users/verify-otp', [UserController::class, 'verifyOtp'])->name('users.verifyOtp');
	Route::post('/users/resend-otp', [UserController::class, 'resendOtp'])->name('users.resendOtp');
	Route::post('/users/send-notification', [UserController::class, 'sendNotification'])->name('users.sendNotification');
	Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
	Route::post('/users/{id}/process-deletion', [UserController::class, 'processLeadFarmerDeletion'])->name('users.processDeletion');
	Route::get('/get-lead-farmers-for-transfer', [UserController::class, 'getLeadFarmersForTransfer'])->name('users.getLeadFarmersForTransfer');

	Route::get('/products', [ProductController::class, 'index'])->name('products.index');
	Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
	Route::post('/products', [ProductController::class, 'store'])->name('products.store');
	Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
	Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
	Route::get('/products/paginated', [ProductController::class, 'paginatedProducts'])->name('products.paginated');
	Route::get('/products/{product}/details', [ProductController::class, 'getProductDetails'])->name('products.details');
	Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
	Route::get('/products/get-farmers-by-lead-farmer/{leadFarmerId}', [ProductController::class, 'getFarmersByLeadFarmer'])->name('products.get-farmers');

	Route::get('/buyer-requests', [BuyerRequestProductsController::class, 'index'])->name('buyer-requests.index');
	Route::delete('/buyer-requests/{id}', [BuyerRequestProductsController::class, 'destroy'])->name('buyer-requests.destroy');

	Route::get('/taxonomy', [TaxonomyController::class, 'index'])->name('taxonomy.index');
	Route::get('/taxonomy/categories/data', [TaxonomyController::class, 'getCategories'])->name('taxonomy.categories.data');
	Route::get('/taxonomy/subcategories/data', [TaxonomyController::class, 'getSubcategories'])->name('taxonomy.subcategories.data');
	Route::get('/taxonomy/products/data', [TaxonomyController::class, 'getProducts'])->name('taxonomy.products.data');
	Route::get('/taxonomy/subcategories/{categoryId}', [TaxonomyController::class, 'getSubcategoriesByCategory']);
	Route::post('/taxonomy/save/main', [TaxonomyController::class, 'saveMainCategory'])->name('taxonomy.save.main');
	Route::post('/taxonomy/save/subcategory', [TaxonomyController::class, 'saveSubCategory'])->name('taxonomy.save.subcategory');
	Route::post('/taxonomy/save/product', [TaxonomyController::class, 'saveProduct'])->name('taxonomy.save.product');
	Route::get('/taxonomy/edit/category/{id}', [TaxonomyController::class, 'editCategory'])->name('taxonomy.edit.category');
	Route::get('/taxonomy/edit/subcategory/{id}', [TaxonomyController::class, 'editSubcategory'])->name('taxonomy.edit.subcategory');
	Route::get('/taxonomy/edit/product/{id}', [TaxonomyController::class, 'editProduct'])->name('taxonomy.edit.product');
	Route::post('/taxonomy/category/update', [TaxonomyController::class, 'updateCategory'])->name('taxonomy.update.category');
	Route::post('/taxonomy/subcategory/update', [TaxonomyController::class, 'updateSubcategory'])->name('taxonomy.update.subcategory');
	Route::post('/taxonomy/product/example/update', [TaxonomyController::class, 'updateProductExample'])->name('taxonomy.update.product.example');
	Route::delete('/taxonomy/delete/category/{id}', [TaxonomyController::class, 'deleteCategory'])->name('taxonomy.delete.category');
	Route::delete('/taxonomy/delete/subcategory/{id}', [TaxonomyController::class, 'deleteSubcategory'])->name('taxonomy.delete.subcategory');
	Route::delete('/taxonomy/delete/product/{id}', [TaxonomyController::class, 'deleteProduct'])->name('taxonomy.delete.product');

	Route::get('/standards', [TaxonomyController::class, 'standardsIndex'])->name('taxonomy.standards');
	Route::get('/standards/data', [TaxonomyController::class, 'getStandards'])->name('taxonomy.standards.data');
	Route::post('/standards/save', [TaxonomyController::class, 'saveStandard'])->name('taxonomy.standards.save');
	Route::get('/standards/edit/{id}', [TaxonomyController::class, 'editStandard'])->name('taxonomy.standards.edit');
	Route::put('/standards/update/{id}', [TaxonomyController::class, 'updateStandard'])->name('taxonomy.standards.update');
	Route::delete('/standards/delete/{id}', [TaxonomyController::class, 'deleteStandard'])->name('taxonomy.standards.delete');

	Route::get('/sales', [ProductController::class, 'viewSales'])->name('sales.view');
	Route::get('/sales/{id}', [ProductController::class, 'salesDetails'])->name('sales.details');
	Route::get('/sales/export/pdf', [ProductController::class, 'exportPDF'])->name('sales.export.pdf');

	Route::get('/lead-farmer-groups', [LeadfarmerControlleradmin::class, 'index'])->name('lead-farmer-groups');

	Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
	Route::get('/profile/details', [AdminProfileController::class, 'editDetails'])->name('profile.details');
	Route::post('/profile/update-details', [AdminProfileController::class, 'updateDetails'])->name('profile.updateDetails');
	Route::get('/profile/photo', [AdminProfileController::class, 'photoPage'])->name('profile.photo');
	Route::post('/profile/update-photo', [AdminProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
	Route::delete('/profile/delete-photo', [AdminProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');
	Route::post('/profile/update-password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');

	Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
	Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
	Route::post('/reports/custom', [ReportController::class, 'customReport'])->name('reports.custom');
	Route::get('/reports/view/{type}', [ReportController::class, 'viewReport'])->name('reports.view');
	Route::get('/reports/pdf/{type}', [ReportController::class, 'generatePDF'])->name('reports.pdf');

	Route::get('/config', [ConfigController::class, 'index'])->name('config.index');
	Route::get('/config/manage/{group}', [ConfigController::class, 'manage'])->name('config.manage');
	Route::post('/config/update/{group}', [ConfigController::class, 'update'])->name('config.update');
	Route::get('/config/backup', [ConfigController::class, 'backup'])->name('config.backup');

	Route::get('/content', [ConfigController::class, 'content'])->name('content.manage');

	Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
	Route::get('/complaints/{id}', [ComplaintController::class, 'show'])->name('complaints.show');
	Route::post('/complaints/{id}/update-status', [ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
	Route::post('/complaints/bulk-update-status', [ComplaintController::class, 'bulkUpdateStatus'])->name('complaints.bulkUpdateStatus');
	Route::get('/complaints/{id}/details', [ComplaintController::class, 'getComplaintDetails'])->name('complaints.details');
});

/*
|--------------------------------------------------------------------------
| DEBUG ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/test-middleware', function () {
    $kernel = app()->make(\App\Http\Kernel::class);
    $middleware = $kernel->getRouteMiddleware();

    dd([
        'ROLE_MIDDLEWARE' => [
            'admin' => $middleware['admin'] ?? 'NOT FOUND',
            'farmer' => $middleware['farmer'] ?? 'NOT FOUND',
            'lead_farmer' => $middleware['lead_farmer'] ?? 'NOT FOUND',
            'buyer' => $middleware['buyer'] ?? 'NOT FOUND',
            'facilitator' => $middleware['facilitator'] ?? 'NOT FOUND',
        ],
        'all' => array_keys($middleware)
    ]);
})->middleware('auth');
