<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    public $incremention = false;
    public $timestamps = false;


    public function toUserId()
    {
        return $this->belongTo('App\User', 'to_user_id', 'id');
    }

    public function fromUserId()
    {
        return $this->belongTo('App\User', 'from_user_id', 'id'); 
    }
}
