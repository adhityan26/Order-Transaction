<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return "Order Transaction Rest API v.1";
});

$router->group(['prefix' => 'product'], function($router) {
    $router->get('/', 'ProductController@index');
    $router->get('/{id}', 'ProductController@show');
    $router->post('/', 'ProductController@store');
    $router->put('/{id}', 'ProductController@update');
    $router->get('/{id}/status', 'ProductController@changeStatus');
});

$router->group(['prefix' => 'category'], function($router) {
    $router->get('/', 'CategoryController@index');
    $router->get('/{id}', 'CategoryController@show');
    $router->post('/', 'CategoryController@store');
    $router->put('/mapCategoryProduct', 'CategoryController@mapCategoryProduct');
    $router->put('/{id}', 'CategoryController@update');
    $router->get('/{id}/status', 'CategoryController@changeStatus');
    $router->delete('/{id}', 'CategoryController@remove');
});

$router->group(['prefix' => 'cart'], function($router) {
    $router->get('/', 'CartController@index');
    $router->get('/{id}', 'CartController@show');
    $router->post('/', 'CartController@store');
    $router->put('/{id}', 'CartController@update');
    $router->get('/{id}/status', 'CartController@changeStatus');
    $router->delete('/{id}', 'CartController@remove');
});

$router->group(['prefix' => 'coupon'], function($router) {
    $router->get('/', 'CouponController@index');
    $router->get('/{id}', 'CouponController@show');
    $router->post('/', 'CouponController@store');
    $router->put('/{id}', 'CouponController@update');
    $router->get('/{id}/status', 'CouponController@changeStatus');
    $router->delete('/{id}', 'CouponController@remove');
    $router->get('/preview/{coupon_code}', 'CouponController@viewCouponDiscount');
});

$router->group(['prefix' => 'order'], function($router) {
    $router->get('/', 'OrderController@index');
    $router->get('/{id}', 'OrderController@show');
    $router->post('/', 'OrderController@store');
    $router->put('/{id}', 'OrderController@update');
    $router->get('/{id}/confirm', 'OrderController@confirmOrder');
    $router->get('/{id}/reject', 'OrderController@rejectOrder');
    $router->get('/{id}/cancel', 'OrderController@cancelOrder');
    $router->get('/{id}/ship', 'OrderController@shipOrder');
    $router->get('/{id}/delivered', 'OrderController@confirmDeliveryOrder');
});

$router->group(['prefix' => 'shippingVendor'], function($router) {
    $router->get('/', 'ShippingVendorController@index');
    $router->get('/{id}', 'ShippingVendorController@show');
    $router->post('/', 'ShippingVendorController@store');
    $router->put('/{id}', 'ShippingVendorController@update');
    $router->get('/{id}/status', 'ShippingVendorController@changeStatus');
    $router->delete('/{id}', 'ShippingVendorController@remove');
});

$router->group(['prefix' => 'shippingPackage'], function($router) {
    $router->get('/', 'ShippingPackageController@index');
    $router->get('/{id}', 'ShippingPackageController@show');
    $router->post('/', 'ShippingPackageController@store');
    $router->put('/{id}', 'ShippingPackageController@update');
    $router->get('/{id}/status', 'ShippingPackageController@changeStatus');
    $router->delete('/{id}', 'ShippingPackageController@remove');
});

$router->group(['prefix' => 'shippingCost'], function($router) {
    $router->get('/', 'ShippingCostController@index');
    $router->get('/{id}', 'ShippingCostController@show');
    $router->post('/', 'ShippingCostController@store');
    $router->put('/{id}', 'ShippingCostController@update');
    $router->get('/{id}/status', 'ShippingCostController@changeStatus');
    $router->delete('/{id}', 'ShippingCostController@remove');
});

$router->group(['prefix' => 'payment'], function($router) {
    $router->get('/', 'PaymentController@index');
    $router->get('/{id}', 'PaymentController@show');
    $router->post('/', 'PaymentController@store');
    $router->get('/{id}/confirm', 'PaymentController@confirmPayment');
    $router->get('/{id}/reject', 'PaymentController@rejectPayment');
    $router->get('/{id}/cancel', 'PaymentController@cancelPayment');
});