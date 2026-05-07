<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequestApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_request_id',
        'user_id',
        'level',
        'status',
        'comment',
    ];

    public function assetRequest()
    {
        return $this->belongsTo(AssetRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
