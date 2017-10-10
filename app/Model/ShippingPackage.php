<?php namespace App\Model;

class ShippingPackage extends BaseModel
{
    protected $fillable = ['shipping_vendor_id', 'code', 'description', 'status'];

    public function shippingVendor() {
        $this->belongsTo(ShippingVendor::class);
    }
}
?>