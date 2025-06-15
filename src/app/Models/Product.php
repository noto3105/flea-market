<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function categories()
    {
        return $this->belongsToMany(Category::class,'product_category','product_id','category_id',);
    }

    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'price',
        'description',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'category_products_table', 'product_id', 'category_id');
    }
    
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
