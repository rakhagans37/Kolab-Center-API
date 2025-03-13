<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seiton extends Model
{
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'score'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
