<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'tell_1',
        'tell_2',
        'tell_3',
        'address',
        'building_name',
        'category_id',
        'body',
    ];
    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
