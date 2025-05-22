<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'poem_text',
        'poem_type',
        'user_id',
        'keywords',
        'generated_with_model',
        'is_public',
        'views',
        'likes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords' => 'array',
        'is_public' => 'boolean',
        'views' => 'integer',
        'likes' => 'integer',
    ];

    /**
     * Get the user that owns the poem.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
