<?php namespace App\Model;

class CouponCategory extends BaseModel
{
    protected $fillable = ['coupon_id', 'category_id'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Product::class);
    }
}
?>