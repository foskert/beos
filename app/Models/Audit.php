<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Audit extends Model
{
    protected $fillable = [
        'event',
        'auditable_id',
        'auditable_type',
        'old_values',
        'new_values',
        'user_id',
        'url',
        'ip_address'
    ];
    protected $guarded = [];
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
   public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
