<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use Sluggable;

    protected $fillable = [
        'code',
        'name',
        'description',
        'slug',
        'thumb',
        'video',
        'is_processed'
    ];

    protected $slugColumnFrom = 'name';

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
