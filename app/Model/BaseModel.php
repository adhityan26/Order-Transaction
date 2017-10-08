<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['sku', 'name', 'price', 'qty', 'desc'];

    public static function getProductList($page = 1, $perPage = 10, $params = []) {
        $model = self::query();

        foreach ($params as $key => $param) {
            $model->where($key, $param);
        }

        return $model->paginate($perPage, ['*'], "page", $page);
    }

    public static function createProduct($product) {
        $model = self::query();
        return $model->create($product);
    }

    public static function updateProduct($id, $product) {
        $model = self::query();
        $model->find($id);
        return $model->update($product);
    }

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