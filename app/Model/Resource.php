<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    //
    //use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '', '', '',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        '', '',
    ];

    public function getList($where,$limit){
        return $this->where($where)->paginate($limit)->toArray();
    }

}
