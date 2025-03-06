<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use Sluggable;

    protected $fillable = [
        'title',
        'description',
        'body',
        'cover',
        'type'
    ];

    protected $casts = [
        'created_at' => 'date:d/m/Y H:i'
    ];

    protected $slugColumnFrom = 'title';

    /**
     * Scopes
     */
    public function scopeActiveVideos($query)
    {
        return $this->videos()
            ->whereNotNull('code')
            ->whereisProcessed(true);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }
}
