<?php namespace App\Model;

class ShippingVendor extends BaseModel
{
    protected $fillable = ['name', 'track_url', 'address', 'phone_number', 'notes', 'status'];
}
?>