<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cj_variant_id',
        'sku',
        'name',
        'option1_name',
        'option1_value',
        'option2_name',
        'option2_value',
        'selling_price',
        'cost_price',
        'stock_quantity',
        'status',
        'image_url',
    ];

    /**
     * Prevent internal supplier variant IDs from leaking in public JSON responses
     */
    protected $hidden = [
        'cj_variant_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cjVariant()
    {
        return $this->belongsTo(CjVariant::class, 'cj_variant_id', 'cj_variant_id');
    }

    /**
     * Defense-in-depth customer-facing display name waterfall
     */
    public function getDisplayNameAttribute(): string
    {
        // 1. Direct name
        if (!empty(trim((string)$this->name))) {
            return trim($this->name);
        }

        // 2. Local option value combinations
        $opts = array_filter([$this->option1_value, $this->option2_value]);
        if (!empty($opts)) {
            return implode(' · ', array_map('trim', $opts));
        }

        // 3. Fallback to associated CjVariant raw_data or name
        if ($this->cjVariant) {
            $cjName = $this->cjVariant->variant_name;
            if (!empty(trim((string)$cjName))) {
                return trim($cjName);
            }
            $cjRaw = is_array($this->cjVariant->raw_data) ? $this->cjVariant->raw_data : json_decode((string)$this->cjVariant->raw_data, true);
            if (is_array($cjRaw)) {
                $rawCandidates = [
                    $cjRaw['variantNameEn'] ?? null,
                    $cjRaw['variantKey'] ?? null,
                    $cjRaw['variantStandard'] ?? null,
                ];
                foreach ($rawCandidates as $cand) {
                    if (!empty(trim((string)$cand))) {
                        return trim($cand);
                    }
                }
                $vals = array_filter([$cjRaw['variantValue1'] ?? null, $cjRaw['variantValue2'] ?? null, $cjRaw['variantValue3'] ?? null]);
                if (!empty($vals)) {
                    return implode(' · ', array_map('trim', $vals));
                }
            }
        }

        // 4. SKU suffix fallback
        if (!empty(trim((string)$this->sku))) {
            return 'Option ' . substr(trim($this->sku), -4);
        }

        // 5. Ultimate fallback
        return 'Option #' . $this->id;
    }
}
