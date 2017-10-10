<?php

/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *       title="Order Transaction API",
 *       version="1.0"
 *   ),
 *   @SWG\Tag(
 *       name="/product",
 *       description="Product management"
 *   ),
 *   @SWG\Tag(
 *       name="/category",
 *       description="Categories management"
 *   ),
 *   @SWG\Tag(
 *       name="/coupon",
 *       description="Coupon management"
 *   ),
 *   @SWG\Tag(
 *       name="/cart",
 *       description="Cart management"
 *   ),
 *   @SWG\Tag(
 *       name="/shippingVendor",
 *       description="Shipping vendor management"
 *   ),
 *   @SWG\Tag(
 *       name="/shippingPackage",
 *       description="Shipping package management"
 *   ),
 *   @SWG\Tag(
 *       name="/shippingCost",
 *       description="Shipping cost management"
 *   ),
 *   @SWG\Tag(
 *       name="/order",
 *       description="Order management"
 *   ),
 *   @SWG\Tag(
 *       name="/payment",
 *       description="Payment management"
 *   ),
 *   host="",
 *   basePath="/api/v1",
 *   @SWG\SecurityScheme(
 *       securityDefinition="AccessToken",
 *       type="apiKey",
 *       name="x-access-token",
 *       in="header"
 *   )
 * ),
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
}
