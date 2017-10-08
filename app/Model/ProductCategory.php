<?php namespace App\Model;

class Product extends BaseModel
{
    protected $fillable = ['sku', 'name', 'price', 'qty', 'desc', 'status'];

    public static function updateQty($id, $qty) {
        $model = self::find($id);
        $product = $model->get();
        if (count($product) > 0) {
            $product = $product[0];
            $qty = $product->qty + $qty;
            if ($qty > 0) {
                return $model->update(["qty" => $qty]);
            } else {
                throw new \Exception("Quantity should not less than 0");
            }
        } else {
            throw new \Exception("Product not found");
        }
    }
}
?>