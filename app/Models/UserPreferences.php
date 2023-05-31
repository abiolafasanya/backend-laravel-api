<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'preferred_sources',
        'preferred_categories',
        'preferred_authors',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];

    /**
     * Get the user associated with the preferences.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
