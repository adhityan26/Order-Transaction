<?php namespace App\Model;

use App\Exceptions\AppException;

class Product extends BaseModel
{
    protected $fillable = ['sku', 'name', 'price', 'qty', 'desc', 'status'];

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
}
?>