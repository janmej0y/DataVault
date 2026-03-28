<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'business_name',
        'area',
        'city',
        'mobile_no',
        'category',
        'sub_category',
        'address',
        'normalized_business_name',
        'normalized_area',
        'normalized_city',
        'normalized_address',
        'duplicate_group',
        'is_duplicate',
        'merged_into',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_duplicate' => 'boolean',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $builder, string $search) {
                $builder->where(function (Builder $inner) use ($search) {
                    $inner
                        ->where('business_name', 'like', '%' . $search . '%')
                        ->orWhere('mobile_no', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['city'] ?? null, fn (Builder $builder, string $city) => $builder->where('city', $city))
            ->when($filters['category'] ?? null, fn (Builder $builder, string $category) => $builder->where('category', $category))
            ->when($filters['area'] ?? null, fn (Builder $builder, string $area) => $builder->where('area', $area));
    }

    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where(function (Builder $builder) {
            $builder
                ->whereNull('business_name')
                ->orWhere('business_name', '')
                ->orWhereNull('mobile_no')
                ->orWhere('mobile_no', '')
                ->orWhereNull('category')
                ->orWhere('category', '');
        });
    }

    public function mergedMaster(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into');
    }

    public function mergedChildren(): HasMany
    {
        return $this->hasMany(self::class, 'merged_into');
    }

    public function getIsIncompleteAttribute(): bool
    {
        return blank($this->business_name) || blank($this->mobile_no) || blank($this->category);
    }
}
