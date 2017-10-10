<?php namespace App\Model;

use App\Exceptions\AppException;

class Cart extends BaseModel
{
    protected $fillable = ['user_id', 'product_id', 'price', 'qty', 'sub_total'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public static function getList($params = [], $page = 1, $perPage = 10, $searchColumn = []) {
        $model = self::query();

        foreach ($searchColumn as $col) {
            if (isset($params[$col])) {
                $model->where($col, $params[$col]);
            }
        }

        $model->with("product");

        return $model->paginate($perPage, ['*'], "page", $page);
    }

    public static function createData($data) {
        $product = Product::find($data["product_id"]);
        if (!$product) {
            throw new AppException("Product not found");
        } else if ($product->qty - intval($data["qty"]) < 0) {
            throw new AppException("Insufficient quantity");
        } else if ($product->status == 0) {
            throw new AppException("Product is not active");
        }

        $model = self::query();

        if($model->where("user_id", $data["user_id"])->where("product_id", $data["product_id"])->count() > 0) {
            $data["price"] = $product->price;
            $data["sub_total"] = $product->price * intval($data["qty"]);
            return $model->update($data);
        }

        $data["price"] = $product->price;
        $data["sub_total"] = $product->price * intval($data["qty"]);
        return $model->create($data);
    }

    public static function removeCart($user, $id) {
        $model = self::query()->where("user_id", $user->id)->where("id", $id)->first();
        if (!$model) {
            throw new AppException("Cart not found");
        }
        return $model->delete();
    }
}
?>