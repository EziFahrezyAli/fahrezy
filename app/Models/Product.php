<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'expired_at',
        'modified_by'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'category_id' => 'integer'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!is_null($this->image)) {
            return env('APP_URL') . '/storage/product-images/' . $this->image;
        } else {
            return null;
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
