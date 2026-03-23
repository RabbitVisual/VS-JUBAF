<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorshipCustomSlide extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'is_active'];
}
