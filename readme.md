# Order Transaction API
This API is written on PHP using Lumen Framework.

In order to work this API need following technology:
* **[PHP]** - open source general-purpose scripting language that is especially suited for web development and can be embedded into HTML [PHP 7.1].
* **[Lumen]** - a PHP micro-framework for building web applications with expressive, elegant syntax [lumen 5.5].
* **[PHPunit]** - unit test for PHP [PHPUnit 6.4.1].
* **[Swagger UI]** - Swagger UI is a collection of HTML, Javascript, and CSS assets that dynamically generate beautiful documentation from a Swagger-compliant API.
* **[Heroku]** - A platform as a service (PaaS) that enables developers to build, run, and operate applications entirely in the cloud.

## Features
This API cover order transaction for basic operation such as:
* Manage User and Admin
* Manage Product and Categories
* Manage Coupon
* Manage Shipping Method
* Manage Order and Payment

Rules User:
* User can view product listing without login in
* In order to select product to cart, user must login to application
* In one transaction user can add as many product that user want
* Product which does not have any quantity and in-active cannot be added to cart
* If user add quantity more than available, the order cannot be submitted
* When submitting order user can add coupon to get discount, and provide the following data:
    * Shipping method
    * Name (If user not provided name application will automatically use from registered user name)
    * Email (If user not provided email application will automatically use from registered user email)
    * Phone Number (If user not provided phone number application will automatically use from registered user phone number)
    * Address (Must be provided)
* After submitting order, user can view the submitted order and must confirm payment and provide the following data: 
    * Sender User Bank Account
    * Sender Bank Name
* Then after admin confirm the payment and ship the order, user can view order status and view tracking number of shipment
* Upon delivery user can confirm the delivered shipment on application and the process is completed

Rules Admin:
* Admin can add new product or update existing product
* Admin can map product and categories, one product can be mapped to several categories
* Admin can add new coupon or update existing coupon
   
    
    Coupon can be used as discount exact value or as percentage value
    - when want to use exact value input the discount on field coupon_calue
    - when want to use percentage value input the discount on field coupon_percentage
    - when both field coupon_value and coupon_percentage is filled,
      total value will be discounted as the coupon_percentage but max at coupon value
      ex: coupon_value: 100.000, coupon_percentage: 20%
          total order: 725.000
          total discount: (725.000 * 20%) = 145.000 -> max 100.000 = 100.000
          grand total: 725.000 - 100.000 = 625.000
* Admin can add or update Shipping Vendor, Shipping Type and Shipping Cost


    Shipping cost is flat not depending on weight since there are 
    no weight metadata on product
* Admin can view payment that already made by user, and confirm or rejected the payment
* After Admin confirm the payment, Admin can change order status to Shipped and provide air way bill (tracking number) so user can track the shipment        

## User
Prefix url: `{host}/oAuth/v1/`

For more information about login visit: [Lumen Rest oAuth 2.0](http://laravel-lumen-rest.dockerboxes.us/v1)

## Transaction
Prefix url: `{host}/api/v1/`
For demonstration visit: [Order Transaction API](http://l5-order-transaction.herokuapp.com)

#### User Login
Admin User Role:

    username: admin
    passowrd: admin123

Buyer User Role :

    username: user
    password: user123


### List Api Endpoint
Api Documentation: [Order Transaction API Docs](http://l5-order-transaction.herokuapp.com/api/documentation)

# Unit Test Result
### Test Result
![N|Solid](http://preview.ibb.co/igoxFb/image.png)

### Overall
![N|Solid](http://image.ibb.co/c3vG8w/image.png)

### Controller
![N|Solid](http://image.ibb.co/nqi8MG/image.png)

### Model
![N|Solid](http://image.ibb.co/irmdMG/image.png)
