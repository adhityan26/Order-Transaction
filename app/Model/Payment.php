<?php namespace App\Model;

class Coupon extends BaseModel
{
    protected $fillable = ['code', 'name', 'desc', 'valid_from', 'valid_to', 'coupon_value', 'coupon_percentage', 'limit', 'limit_terms'];


}
?>