<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class OrderingTest extends TestCase
{
    protected $sku = "SKU0TEST00001";
    protected $vendorName = "VendorTest";
    protected $shippingPackageCode = "ONS";
    protected $couponCode = "COUPONTEST";
    /**
     * Test product user
     *
     * @return void
     */
    public function testListProductUser()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('GET', 'api/v1/product', ["sku" => $this->sku], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        // error authorized
        $this->json('POST', 'api/v1/product', [
            "sku" => $this->sku,
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('GET', 'api/v1/product', ["sku" => $this->sku], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);
    }
    
    public function testListProductAdmin()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('GET', 'api/v1/product', ["sku" => $this->sku], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        // error mandatory
        $this->json('POST', 'api/v1/product', [
            "sku" => $this->sku
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('GET', 'api/v1/product', ["sku" => $this->sku], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        $this->json('POST', 'api/v1/product', [
            "sku" => $this->sku,
            "name" => "T-Shirt Test",
            "price" => 53500,
            "qty" => 125,
        ], $accessToken)
            ->seeStatusCode(200)
            ->seeJson([
                "status" => \App\Constant\Status::NEW
            ]);

        $this->json('GET', 'api/v1/product', ["sku" => $this->sku], $accessToken)
            ->seeJson([
                'total' => 1,
            ]);

        $this->json('POST', 'api/v1/product', [
            "sku" => $this->sku,
            "name" => "T-Shirt Test",
            "price" => 53500,
            "qty" => 125,
        ], $accessToken)
            ->seeStatusCode(422)
            ->seeJson([
                "sku" => ["The sku has already been taken."]
            ]);
    }

    public function testProductDetailUser() {
        $accessToken = ["x-access-token" => env("test_user_access_code", "76b1e261bf0426bc1d250d564e07b52d")];

        $product = \App\Model\Product::query()->where("sku", $this->sku)->first();

        $this->json('get', 'api/v1/product/' . $product->id, [], $accessToken)
            ->seeJSON([
                "sku" => $this->sku
            ]);

        $this->json('get', 'api/v1/product/' . $product->id . '/status', [], $accessToken)
            ->seeStatusCode(401);

        $this->json('get', 'api/v1/product/' . $product->id, [], $accessToken)
            ->seeJSON([
                "status" => 1
            ]);
    }

    public function testProductDetailAdmin() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $product = \App\Model\Product::query()->where("sku", $this->sku)->first();

        $this->json('get', 'api/v1/product/' . $product->id, [], $accessToken)
            ->seeJSON([
                "sku" => $this->sku
            ]);

        $this->json('get', 'api/v1/product/' . $product->id . '/status', [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/product/' . $product->id, [], $accessToken)
            ->seeJSON([
                "status" => 0
            ]);

        $this->json('put', 'api/v1/product/' . $product->id, [
            "qty" => 150,
            "price" => 57500,
        ], $accessToken)
            ->seeJSON([
                "qty" => 150,
                "price" => 57500
            ]);
    }

    /**
     * Test product user
     *
     * @return void
     */
    public function testListShippingVendorUser()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('GET', 'api/v1/shippingVendor', ["name" => $this->vendorName], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        // error authorized
        $this->json('POST', 'api/v1/shippingVendor', [
            "name" => $this->vendorName,
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('GET', 'api/v1/shippingVendor', ["name" => $this->vendorName], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);
    }

    /**
     * Test product user
     *
     * @return void
     */
    public function testListShippingVendorAdmin()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('GET', 'api/v1/shippingVendor', ["name" => $this->vendorName], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        // error mandatory
        $this->json('POST', 'api/v1/shippingVendor', [
            "name" => "vendorName",
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('GET', 'api/v1/shippingVendor', ["name" => $this->vendorName], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        $this->json('POST', 'api/v1/shippingVendor', [
            "name" => $this->vendorName,
            "track_url" => "test.com/tracking",
            "address" => "Jakarta",
            "phone_number" => "081082831023",
            "notes" => "Note Test",
        ], $accessToken)
            ->seeStatusCode(200)
            ->seeJson([
                "name" => $this->vendorName,
                "track_url" => "test.com/tracking",
                "address" => "Jakarta",
                "phone_number" => "081082831023",
                "notes" => "Note Test",
                "status" => \App\Constant\Status::NEW
            ]);

        $this->json('GET', 'api/v1/shippingVendor', ["name" => $this->vendorName], $accessToken)
            ->seeJson([
                'total' => 1,
            ]);
    }

    public function testShippingVendorDetail() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $vendor = \App\Model\ShippingVendor::query()->where("name", $this->vendorName)->first();

        $this->json('get', 'api/v1/shippingVendor/' . $vendor->id, [], $accessToken)
            ->seeJSON([
                "name" => $this->vendorName
            ]);

        $this->json('get', 'api/v1/shippingVendor/' . $vendor->id . '/status', [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/shippingVendor/' . $vendor->id, [], $accessToken)
            ->seeJSON([
                "status" => 0
            ]);

        $this->json('put', 'api/v1/shippingVendor/' . $vendor->id, [
            "address" => "Bandung",
            "phone_number" => "0226600001",
        ], $accessToken)
            ->seeJSON([
                "address" => "Bandung",
                "phone_number" => "0226600001",
            ]);
    }

    public function testListShippingPackageAdmin()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('GET', 'api/v1/shippingPackage', ["code" => $this->shippingPackageCode], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        // error mandatory
        $this->json('POST', 'api/v1/shippingPackage', [
            "code" => $this->shippingPackageCode,
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('GET', 'api/v1/shippingPackage', ["code" => $this->shippingPackageCode], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        $vendor = \App\Model\ShippingVendor::query()->where("name", $this->vendorName)->first();

        $this->json('POST', 'api/v1/shippingPackage', [
            "code" => $this->shippingPackageCode,
            "shipping_vendor_id" => $vendor->id,
            "description" => "One Night Service Test",
        ], $accessToken)
            ->seeStatusCode(200)
            ->seeJson([
                "code" => $this->shippingPackageCode,
                "shipping_vendor_id" => $vendor->id,
                "description" => "One Night Service Test",
                "status" => \App\Constant\Status::NEW
            ]);

        $this->json('GET', 'api/v1/shippingPackage', ["code" => $this->shippingPackageCode], $accessToken)
            ->seeJson([
                'total' => 1,
            ]);
    }

    public function testListShippingCostAdmin()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();

        $this->json('GET', 'api/v1/shippingCost', ["shipping_package_id" => $package->id], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        // error mandatory
        $this->json('POST', 'api/v1/shippingCost', [
            "shipping_package_id" => $package->id,
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('GET', 'api/v1/shippingCost', ["shipping_package_id" => $package->id], $accessToken)
            ->seeJson([
                'total' => 0,
            ]);

        $this->json('POST', 'api/v1/shippingCost', [
            "shipping_package_id" => $package->id,
            "shipping_origin_province" => "DKI Jakarta",
            "shipping_origin_city" => "Jakarta Selatan",
            "shipping_origin_district" => "Setiabudi",
            "shipping_destination_province" => "Jawa Barat",
            "shipping_destination_city" => "Cimahi",
            "shipping_destination_district" => "Cimahi Utara",
            "shipping_etd" => "3",
            "cost" => 9500
        ], $accessToken)
            ->seeStatusCode(200)
            ->seeJson([
                "shipping_package_id" => $package->id,
                "shipping_origin_province" => "DKI Jakarta",
                "shipping_origin_city" => "Jakarta Selatan",
                "shipping_origin_district" => "Setiabudi",
                "shipping_destination_province" => "Jawa Barat",
                "shipping_destination_city" => "Cimahi",
                "shipping_destination_district" => "Cimahi Utara",
                "shipping_etd" => "3",
                "cost" => 9500,
                "status" => \App\Constant\Status::NEW
            ]);

        $this->json('GET', 'api/v1/shippingPackage', ["code" => $this->shippingPackageCode], $accessToken)
            ->seeJson([
                'total' => 1,
            ]);
    }

    public function testCartInActiveUser()
    {
        $accessToken = ["x-access-token" => env("test_user_access_code", "76b1e261bf0426bc1d250d564e07b52d")];
        $product = \App\Model\Product::query()->where("sku", $this->sku)->first();

        $this->json('post', 'api/v1/cart', ["product_id" => $product->id, "qty" => 50], $accessToken)
            ->seeJson([
                "error" => ["code" => 500, "message" => "Product is not active"]
            ]);
    }

    public function testUpdateProduct()
    {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];
        $product = \App\Model\Product::query()->where("sku", $this->sku)->first();

        $this->json('get', 'api/v1/product/' . $product->id . '/status', [], $accessToken)
            ->seeStatusCode(200);
    }

    public function testCoupon() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('get', 'api/v1/coupon', ["code" => $this->couponCode], $accessToken)
            ->seeJson([
                "data" => []
            ]);

        $this->json('post', 'api/v1/coupon', [
            "code" => $this->couponCode,
        ], $accessToken)
            ->seeStatusCode(422);

        $this->json('get', 'api/v1/coupon/preview/' . $this->couponCode, [], $accessToken)
            ->seeJson([
                "message" => "Coupon not found"
            ]);

        $this->json('post', 'api/v1/coupon', [
            "code" => $this->couponCode,
            "name" => "Test Coupon",
            "valid_from" => \Carbon\Carbon::now()->addDay(-1)->format("Y/m/d"),
            "valid_to" => \Carbon\Carbon::now()->addDay(1)->format("Y/m/d"),
            "coupon_value" => 200000,
            "coupon_percentage" => 35,
            "limit" => 5,
        ], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/coupon', ["code" => $this->couponCode], $accessToken)
            ->seeJson([
                "total" => 1
            ]);

        $this->json('get', 'api/v1/coupon/preview/' . $this->couponCode, [], $accessToken)
            ->seeJson([
                "message" => "No item found on cart"
            ]);
    }
    
    public function testCartUser()
    {
        $accessToken = ["x-access-token" => env("test_user_access_code", "76b1e261bf0426bc1d250d564e07b52d")];
        $product = \App\Model\Product::query()->where("sku", $this->sku)->first();

        $this->json('post', 'api/v1/cart', ["product_id" => $product->id, "qty" => 500], $accessToken)
            ->seeJson([
                "error" => ["code" => 500,"message" => "Insufficient quantity"]
            ]);

        $this->json('post', 'api/v1/cart', ["product_id" => $product->id, "qty" => 50], $accessToken)
            ->seeStatusCode(200);

        $cart = \App\Model\Cart::query()->where("product_id", $product->id)->first();

        $this->assertTrue($cart->qty == 50);

        $this->json('get', 'api/v1/coupon/preview/' . $this->couponCode, [], $accessToken)
            ->seeJson([
                "value" => 200000
            ]);

        $this->json('post', 'api/v1/cart', ["product_id" => $product->id, "qty" => 2], $accessToken)
            ->seeStatusCode(200);

        $cart = \App\Model\Cart::query()->find($cart->id);

        $this->assertTrue($cart->qty == 2);

        $this->json('delete', 'api/v1/cart/' . $cart->id, [], $accessToken)
            ->seeStatusCode(200);

        $this->json('post', 'api/v1/cart', ["product_id" => $product->id, "qty" => 2], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/cart', [], $accessToken)
            ->seeJson([
                "total" => 1
            ]);

        $cart = \App\Model\Cart::query()->where("product_id", $product->id)->first();

        $this->json('get', 'api/v1/cart', [], $accessToken)
            ->seeJson([
                "total" => 1
            ]);

        $this->json('get', 'api/v1/cart/' . $cart->id, [], $accessToken)
            ->seeJson([
                "product_id" => $cart->product_id
            ]);

        $this->json('get', 'api/v1/coupon/preview/' . $this->couponCode, [], $accessToken)
            ->seeJson([
                "value" => 40250
            ]);

        $coupon = \App\Model\Coupon::query()->where("code", $this->couponCode)->first();
        $coupon->valid_to = \Carbon\Carbon::now()->addDay(-1)->format("Y/m/d");
        $coupon->save();

        $this->json('get', 'api/v1/coupon/preview/' . $this->couponCode, [], $accessToken)
            ->seeJson([
                "message" => "Coupon is expired"
            ]);

        $coupon->valid_to = \Carbon\Carbon::now()->addDay(1)->format("Y/m/d");
        $coupon->save();
    }

    public function testExceptionCart() {
        $this->expectExceptionMessage("Product not found");
        \App\Model\Cart::createData(["product_id" => 0, "qty" => 500]);
    }

    public function testOrderSubmission() {
        $accessToken = ["x-access-token" => env("test_user_access_code", "76b1e261bf0426bc1d250d564e07b52d")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();
        $cost = \App\Model\ShippingCost::query()->where("shipping_package_id", $package->id)->first();

        $this->json('post', 'api/v1/order', [
            "shipping_method" => $cost->id,
            "address" => "Jalan cisitu",
            "coupon_code" => $this->couponCode
        ], $accessToken)
            ->seeJson([
                "grand_total" => 84250
            ]);
    }

    public function testInvalidOrderConfirmed() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();

        $order = \App\Model\Order::query()->where("shipping_package_id", $package->id)->where("status", \App\Constant\Status::NEW)->first();

        $this->json('get', 'api/v1/order/' . $order->id . '/confirm', [], $accessToken)
            ->seeJson([
                "message" => "Payment not found or not confirmed"
            ]);
    }

    public function testPaymentUser() {
        $accessToken = ["x-access-token" => env("test_user_access_code", "76b1e261bf0426bc1d250d564e07b52d")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();

        $order = \App\Model\Order::query()->where("shipping_package_id", $package->id)->where("status", \App\Constant\Status::NEW)->first();

        $this->json('post', 'api/v1/payment', [
            "order_id" => $order->id,
            "user_account" => "Nama Test",
            "user_bank_account" => "BTS",
        ], $accessToken)
            ->seeJson([
                "order_id" => $order->id,
                "user_account" => "Nama Test",
                "user_bank_account" => "BTS",
                "status" => \App\Constant\Status::NEW,
            ]);

        $payment = \App\Model\Payment::query()->where("order_id", $order->id)->where("status", \App\Constant\Status::NEW)->first();

        $this->json('get', 'api/v1/payment/' . $payment->id, [], $accessToken)
            ->seeJson([
                "order_id" => $order->id,
                "user_account" => "Nama Test",
                "user_bank_account" => "BTS",
                "status" => \App\Constant\Status::NEW,
            ]);
    }

    public function testOrderConfirmed() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();

        $order = \App\Model\Order::query()->where("shipping_package_id", $package->id)->where("status", \App\Constant\Status::NEW)->first();

        $this->json('get', 'api/v1/payment', [
            "order_id" => $order->id
        ], $accessToken)
            ->seeJson([
                "total" => 1
            ]);

        $payment = \App\Model\Payment::query()->where("order_id", $order->id)->where("status", \App\Constant\Status::NEW)->first();

        $this->json('get', 'api/v1/payment/' . $payment->id . '/confirm', [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/order/' . $order->id . '/confirm', [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/order/' . $order->id, [], $accessToken)
            ->seeJson([
                "status" => \App\Constant\Status::CONFIRMED
            ]);

        $this->json('get', 'api/v1/order/' . $order->id . '/ship', [
            "awb" => "AWB-040173-Test-01"
        ], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/order/' . $order->id, [], $accessToken)
            ->seeJson([
                "status" => \App\Constant\Status::SHIPPED
            ]);
    }

    public function testOrderDelivered() {
        $accessToken = ["x-access-token" => env("test_user_access_code", "76b1e261bf0426bc1d250d564e07b52d")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();
        $order = \App\Model\Order::query()->where("shipping_package_id", $package->id)->where("status", \App\Constant\Status::SHIPPED)->first();

        $this->json('get', 'api/v1/order/' . $order->id . '/delivered', [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/order/' . $order->id, [], $accessToken)
            ->seeJson([
                "status" => \App\Constant\Status::DELIVERED
            ]);
    }

    /**
     * clean test data
     * 
     */

    public function testRemoveCart() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "76b1e261bf0426bc1d250d564e07b52d")];

        $product = \App\Model\Product::query()->where("sku", $this->sku)->first();

        \App\Model\Cart::query()->where("product_id", $product->id)->delete();

        $this->json('get', 'api/v1/cart', ["product_id" => $product->id], $accessToken)
            ->seeJson([
                "data" => []
            ]);
    }

    public function testRemoveCoupon() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "76b1e261bf0426bc1d250d564e07b52d")];

        $coupon = \App\Model\Coupon::query()->where("CODE", $this->couponCode)->first();

        $this->json('delete', 'api/v1/coupon/' . $coupon->id, [], $accessToken)
            ->seeStatusCode(200);
    }
    
    public function testRemoveProduct() {
        $this->json('get', 'api/v1/product', ["sku" => $this->sku])
            ->seeJSON([
                'total' => 1,
            ]);

        \App\Model\Product::query()->where("sku", $this->sku)->delete();

        $this->json('get', 'api/v1/product', ["sku" => $this->sku])
            ->seeJSON([
                "data" => []
            ]);
    }

    public function testRemoveShippingCost() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();

        $this->json('get', 'api/v1/shippingCost', ["shipping_package_id" => $package->id])
            ->seeJSON([
                'total' => 1,
            ]);

        $cost = \App\Model\ShippingCost::query()->where("shipping_package_id", $package->id)->first();

        $this->json('delete', 'api/v1/shippingCost/' . $cost->id, [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/shippingCost', ["shipping_package_id" => $package->id])
            ->seeJSON([
                "data" => []
            ]);
    }

    public function testRemoveShippingPackage() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('get', 'api/v1/shippingPackage', ["code" => $this->shippingPackageCode])
            ->seeJSON([
                'total' => 1,
            ]);

        $package = \App\Model\ShippingPackage::query()->where("code", $this->shippingPackageCode)->first();

        $this->json('delete', 'api/v1/shippingPackage/' . $package->id, [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/shippingPackage', ["code" => $this->shippingPackageCode])
            ->seeJSON([
                "data" => []
            ]);
    }

    public function testRemoveShippingVendor() {
        $accessToken = ["x-access-token" => env("test_admin_access_code", "af5241e52c23cffed9f9e999b510fb34")];

        $this->json('get', 'api/v1/shippingVendor', ["name" => $this->vendorName])
            ->seeJSON([
                'total' => 1,
            ]);

        $vendor = \App\Model\ShippingVendor::query()->where("name", $this->vendorName)->first();

        $this->json('delete', 'api/v1/shippingVendor/' . $vendor->id, [], $accessToken)
            ->seeStatusCode(200);

        $this->json('get', 'api/v1/shippingVendor', ["name" => $this->vendorName])
            ->seeJSON([
                "data" => []
            ]);
    }
}
