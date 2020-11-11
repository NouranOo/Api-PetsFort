<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $table = "Messages";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Message_From', 'Message_To', 'Message', 'Seen', 'Seen_at',
    ];

    public function UserSent()
    {
        return $this->belongsTo('App\Models\User', 'Message_From');
    }
    public function UserRecived()
    {
        return $this->belongsTo('App\Models\User', 'Message_To');
    }

}
