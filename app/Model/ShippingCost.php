<?php namespace App\Model;

class ShippingCost extends BaseModel
{
    protected $fillable = ['shipping_package_id', 'shipping_origin_province', 'shipping_origin_city', 'shipping_origin_district', 'shipping_destination_province', 'shipping_destination_city', 'shipping_destination_district', 'shipping_etd', 'cost', 'status'];

    public function shippingPackage() {
        return $this->belongsTo(ShippingPackage::class, "shipping_package_id", "id");
    }
}
?>