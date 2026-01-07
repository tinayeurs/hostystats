<?php

namespace App\Addons\HostyStats\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'hostystats_categories';

    protected $fillable = [
        'name', 'description', 'position', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class, 'category_id');
    }
}
