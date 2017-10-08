<?php namespace App\Model;

class Coupon extends BaseModel
{
    protected $fillable = ['code', 'name', 'desc', 'valid_from', 'valid_to', 'coupon_value', 'coupon_percentage', 'limit', 'limit_terms'];

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public static function getList($params = [], $page = 1, $perPage = 10, $searchColumn = []) {
        $model = self::query();

        foreach ($searchColumn as $col) {
            if (isset($params[$col])) {
                $model->where($col, $params[$col]);
            }
        }

        $model->with("productCategories.category");

        return $model->paginate($perPage, ['*'], "page", $page);
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