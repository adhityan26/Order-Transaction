<?php namespace App\Model;

class ProductCategory extends BaseModel
{
    protected $fillable = ['product_id', 'category_id'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
?>