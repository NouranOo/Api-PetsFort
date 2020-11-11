<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchRequest extends Model
{
    protected $table = "MatchRequests";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ReqFrom', 'ReqTo', 'ReqFrom_Pet_id','ReqTo_Pet_id', 'Status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public function Sender()
    {
        return $this->belongsTo('App\Models\User', 'ReqFrom');
    }
    public function PetSender()
    {
        return $this->belongsTo('App\Models\PetProfile', 'ReqFrom_Pet_id');
    }
    public function Reciver()
    {
        return $this->belongsTo('App\Models\User', 'ReqTo');
    }
    public function PetReciver()
    {
        return $this->belongsTo('App\Models\PetProfile', 'ReqTo_Pet_id');
    }
}
