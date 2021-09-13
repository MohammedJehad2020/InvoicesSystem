<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'Product_name',
        'section_name',
        'description',
        'section_id'
    ];

    protected $guarded = [];

    public function section()
    {
    return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
