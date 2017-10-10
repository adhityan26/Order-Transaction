<?php namespace App\Model;

use App\Exceptions\AppException;
use Illuminate\Support\Facades\DB;

class OrderItem extends BaseModel
{
    protected $fillable = ["order_id", "product_id", "qty", "price", "total"];
}
?>