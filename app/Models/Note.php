<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['content'];

    protected $casts = [
        'user_id' => 'int',
    ];

    /*
    |------------------------------------------------------------------------------------
    | Relations
    |------------------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /*
    |------------------------------------------------------------------------------------
    | Scopes
    |------------------------------------------------------------------------------------
    */
    public function scopeMine($q)
    {
        $q->where('user_id', auth()->id());
    }

    /*
    |------------------------------------------------------------------------------------
    | Attributes
    |------------------------------------------------------------------------------------
    */
    public function getIsMineAttribute()
    {
        return $this->user_id === auth()->id();
    }
}
