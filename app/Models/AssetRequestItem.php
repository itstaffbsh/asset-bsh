<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_request_id',
        'classification_id',
        'specs',
        'qty',
        'hr_approval',
        'dept_approval',
        'it_approval',
        'md_approval',
    ];

    protected $casts = [
        'hr_approval' => 'boolean',
        'dept_approval' => 'boolean',
        'it_approval' => 'boolean',
        'md_approval' => 'boolean',
    ];

    public function assetRequest()
    {
        return $this->belongsTo(AssetRequest::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }
}
