<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api\V1', 'middleware' => 'localization'], function () {

    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('registration', 'CustomerAuthController@registration');
        Route::post('login', 'CustomerAuthController@login');
        Route::post('social-customer-login', 'CustomerAuthController@social_customer_login');

        Route::post('check-phone', 'CustomerAuthController@check_phone');
        Route::post('verify-phone', 'CustomerAuthController@verify_phone');

        Route::post('check-email', 'CustomerAuthController@check_email');
        Route::post('verify-email', 'CustomerAuthController@verify_email');

        Route::post('forgot-password', 'PasswordResetController@reset_password_request');
        Route::post('verify-token', 'PasswordResetController@verify_token');
        Route::put('reset-password', 'PasswordResetController@reset_password_submit');

        Route::group(['prefix' => 'delivery-man'], function () {
            Route::post('login', 'DeliveryManLoginController@login');
            Route::post('register', 'DeliveryManLoginController@registration');
        });

        Route::group(['prefix' => 'kitchen', 'middleware' => 'app_activate:' . APPS['kitchen_app']['software_id']], function () {
            Route::post('login', 'KitchenLoginController@login');
            Route::post('logout', 'KitchenLoginController@logout')->middleware('auth:kitchen_api');
        });
    });

    Route::group(['prefix' => 'delivery-man', 'middleware' => 'deliveryman_is_active'], function () {
        Route::get('profile', 'DeliverymanController@get_profile');
        Route::get('current-orders', 'DeliverymanController@get_current_orders');
        Route::get('all-orders', 'DeliverymanController@get_all_orders');
        Route::post('record-location-data', 'DeliverymanController@record_location_data');
        Route::get('order-delivery-history', 'DeliverymanController@get_order_history'); // not used
        Route::put('update-order-status', 'DeliverymanController@update_order_status');
        Route::put('update-payment-status', 'DeliverymanController@order_payment_status_update');
        Route::get('order-details', 'DeliverymanController@get_order_details');
//        Route::get('last-location', 'DeliverymanController@get_last_location');
        Route::put('update-fcm-token', 'DeliverymanController@update_fcm_token');
        Route::get('order-model', 'DeliverymanController@order_model');

        //delivery-man message
        Route::group(['prefix' => 'message'], function () {
            Route::post('get-message', 'ConversationController@get_order_message_for_dm');
            Route::post('send/{sender_type}', 'ConversationController@store_message_by_order');
        });

        Route::group(['prefix' => 'reviews', 'middleware' => ['auth:api']], function () {
            Route::get('/{delivery_man_id}', 'DeliveryManReviewController@get_reviews'); //not used
            Route::get('rating/{delivery_man_id}', 'DeliveryManReviewController@get_rating'); //not used
//            Route::post('/submit', 'DeliveryManReviewController@submit_review');
        });

    });

    Route::group(['prefix' => 'delivery-man'], function () {

        // Route::group(['middleware' => ['auth:api']], function () {
            # reservation routes
            Route::post('user_reservation', 'ReservationController@storeReservation');
            Route::get('fetch_reservation', 'ReservationController@getReservations');

            # membership routes
            Route::get('memberships', 'MembershipController@getMemebrships');

            # packages routes
            Route::get('packages', 'PackageController@getPackages');
        // });

        # branches routes
        Route::post('subscribe_membership', 'MembershipController@SubscribeMembership');
        Route::post('packages/subscription', 'PackageController@userSubscription');
        Route::get('branches', 'BranchController@getBranches');
        Route::get('branch_slots','BranchController@getBranchSlots');
        Route::get('nearest_branches','BranchController@getNearestBranches');

    });

    Route::middleware('auth:api')->post('delivery-man/reviews/submit', 'DeliveryManReviewController@submit_review');
    Route::middleware('auth:api')->get('delivery-man/last-location', 'DeliverymanController@get_last_location');



    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
        Route::get('table', 'TableConfigController@configuration');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('latest', 'ProductController@get_latest_products');
        Route::get('popular', 'ProductController@get_popular_products');
        Route::get('set-menu', 'ProductController@get_set_menus');
        Route::get('search', 'ProductController@get_searched_products');
        Route::get('details/{id}', 'ProductController@get_product');
        Route::get('related-products/{product_id}', 'ProductController@get_related_products');
        Route::get('reviews/{product_id}', 'ProductController@get_product_reviews');
        Route::get('rating/{product_id}', 'ProductController@get_product_rating');
        Route::post('reviews/submit', 'ProductController@submit_product_review')->middleware('auth:api');
        Route::get('featured_products', 'ProductController@featuredProduct')->middleware('auth:api');
    });

    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BannerController@get_banners');
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'NotificationController@get_notifications');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@get_categories');
        Route::get('childes/{category_id}', 'CategoryController@get_childes');
        Route::get('products/{category_id}', 'CategoryController@get_products');
        Route::get('products/{category_id}/all', 'CategoryController@get_all_products');
    });

    Route::group(['prefix' => 'customer', 'middleware' => ['auth:api', 'is_active']], function () {
        Route::get('info', 'CustomerController@info');
        Route::put('update-profile', 'CustomerController@update_profile');
        Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');
        Route::get('transaction-history', 'CustomerController@get_transaction_history');

        Route::namespace('Auth')->group(function () {
            Route::delete('remove-account', 'CustomerAuthController@remove_account');
        });

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', 'CustomerController@address_list');
            Route::post('add', 'CustomerController@add_new_address');
            Route::put('update/{id}', 'CustomerController@update_address');
            Route::delete('delete', 'CustomerController@delete_address');
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('list', 'OrderController@get_order_list');
            Route::get('details', 'OrderController@get_order_details');
            Route::post('place', 'OrderController@place_order');
            Route::put('cancel', 'OrderController@cancel_order');
            Route::get('track', 'OrderController@track_order');
            Route::put('payment-method', 'OrderController@update_payment_method');
        });
        // Chatting
        Route::group(['prefix' => 'message'], function () {
            //customer-admin
            Route::get('get-admin-message', 'ConversationController@get_admin_message');
            Route::post('send-admin-message', 'ConversationController@store_admin_message');
            //customer-deliveryman
            Route::get('get-order-message', 'ConversationController@get_message_by_order');
            Route::post('send/{sender_type}', 'ConversationController@store_message_by_order');
        });

        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', 'WishlistController@wish_list');
            Route::post('add', 'WishlistController@add_to_wishlist');
            Route::delete('remove', 'WishlistController@remove_from_wishlist');
        });
        Route::group(['prefix' => 'vote'], function () {
            Route::post('add-product-vote', 'VoteController@addProductVote');
            Route::post('add-restaurant-vote', 'VoteController@addRestaurantVote');
            Route::get('product-votes', 'VoteController@productVote');
            Route::get('restaurant-votes', 'VoteController@restaurantVote');
        });
    });

    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BannerController@get_banners');
    });

    Route::group(['prefix' => 'coupon', 'middleware' => 'auth:api'], function () {
        Route::get('list', 'CouponController@list');
        Route::get('apply', 'CouponController@apply');
    });

    //map api
    Route::group(['prefix' => 'mapapi'], function () {
        Route::get('place-api-autocomplete', 'MapApiController@place_api_autocomplete');
        Route::get('distance-api', 'MapApiController@distance_api');
        Route::get('place-api-details', 'MapApiController@place_api_details');
        Route::get('geocode-api', 'MapApiController@geocode_api');
    });

    Route::post('subscribe-newsletter', 'CustomerController@subscribe_newsletter');

    Route::get('pages', 'PageController@index');

    Route::group(['prefix' => 'table'], function () {
        Route::get('list', 'TableController@list');
        Route::get('product/type', 'TableController@filter_by_product_type');
        Route::get('promotional/page', 'TableController@get_promotional_page');
        Route::post('order/place', 'TableController@place_order');
        Route::get('order/details', 'TableController@get_order_details');
        Route::get('order/list', 'TableController@table_order_list');
    });

    Route::group(['prefix' => 'kitchen', 'middleware' => 'auth:kitchen_api', 'app_activate:' . APPS['kitchen_app']['software_id']], function () {
        Route::get('profile', 'KitchenController@get_profile');
        Route::get('order/list', 'KitchenController@get_order_list');
        Route::get('order/search', 'KitchenController@search');
        Route::get('order/filter', 'KitchenController@filter_by_status');
        Route::get('order/details', 'KitchenController@get_order_details');
        Route::put('order/status', 'KitchenController@change_status');
        Route::put('update-fcm-token', 'KitchenController@update_fcm_token');
    });
});
