<?php namespace App\Model;

class Category extends BaseModel
{
    protected $fillable = ['sku', 'parent', 'name', 'status'];

    public function categoryProducts()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent', 'id');
    }

    public function parentCategories()
    {
        return $this->belongsTo(Category::class, 'parent', 'id');
    }

    public function allParentCategories()
    {
        return $this->parentCategories()->with('allParentCategories');
    }

    public function allSubCategories()
    {
        return $this->subCategories()->with('allSubCategories');
    }

    public static function getList($params = [], $page = 1, $perPage = 10, $searchColumn = []) {
        $model = self::query();

        foreach ($searchColumn as $col) {
            if (isset($params[$col])) {
                $model->where($col, $params[$col]);
            }
        }

        $model->with("categoryProducts.product");

        return $model->paginate($perPage, ['*'], "page", $page);
    }

    public static function getDetail($id) {
        $category = self::query();
        $category->with(["allParentCategories", "allSubCategories"]);
        $category = $category->find($id);
        return $category;
    }
}
?>