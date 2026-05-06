<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionView extends Model
{
    use HasFactory;

    protected $table = 'attraction_views';

    protected $fillable = [
        'user_id',
        'attraction_id',
    ];

    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
