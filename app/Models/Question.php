<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    protected $table = "Questions";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Type', 'Bread', 'Question', 'Like', 'User_id','Photo'
    ];

    public function Comments()
    {
        return $this->hasMany('App\Models\Comment', 'Question_id');
    }
    public function Owner()
    {
        return $this->belongsTo('App\Models\User', 'User_id');
    }
}
