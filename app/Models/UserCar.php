<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCar extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function mark()
    {
        return $this->belongsTo(Mark::class, 'mark_id');
    }
    public function model()
    {
        return $this->belongsTo(Models::class, 'model_id');
    }
//    public function color()
//    {
//        return $this->belongsTo(CarColor::class, 'color_id');
//    }
}
