<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'subcategory_id',
        'company_id',
        'is_active',
        'is_global',
        'price',
        'sku',
        'stock_quantity',
        'specifications'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'price' => 'decimal:2',
        'specifications' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('title') && empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    // Relationships
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
              ->orWhere('is_global', true);
        });
    }

    public function scopeBySubcategory($query, $subcategoryId)
    {
        return $query->where('subcategory_id', $subcategoryId);
    }

    // Accessors
    public function getMainImageUrlAttribute()
    {
        $primaryImage = $this->primaryImage;
        if ($primaryImage) {
            return asset('storage/' . $primaryImage->image_path);
        }
        
        $firstImage = $this->images()->first();
        return $firstImage ? asset('storage/' . $firstImage->image_path) : asset('assets/images/no-image.png');
    }

    public function getImageUrlsAttribute()
    {
        return $this->images->map(function ($image) {
            return asset('storage/' . $image->image_path);
        });
    }

    public function getFormattedPriceAttribute()
    {
        return $this->price ? '$' . number_format($this->price, 2) : 'N/A';
    }

    public function getCategoryNameAttribute()
    {
        return $this->subcategory->category->name ?? 'N/A';
    }

    public function getSubcategoryNameAttribute()
    {
        return $this->subcategory->name ?? 'N/A';
    }

    public function getCompanyNameAttribute()
    {
        if ($this->is_global) {
            return 'Global (All Companies)';
        }
        return $this->company->business_name ?? 'N/A';
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity > 10) {
            return 'In Stock';
        } elseif ($this->stock_quantity > 0) {
            return 'Low Stock';
        } else {
            return 'Out of Stock';
        }
    }

    public function getStockStatusColorAttribute()
    {
        if ($this->stock_quantity > 10) {
            return 'success';
        } elseif ($this->stock_quantity > 0) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
}
