<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function job_details()
    {
        return $this->hasmany(JobTranzaksion::class,'job_id');
    }
    public function events()
    {
        return $this->hasmany(JobEvent::class,'job_id');
    }

    public function routes()
    {
        return $this->hasmany(JobRoutePoints::class,'job_id');
    }

    public function tracker()
    {
        return $this->hasmany(JobTracker::class,'job_id');
    }
    public function user()
    {
        return $this->belongsto(User::class,'user_id');
    }

    public function car()
    {
        return $this->belongsto(UserCar::class,'car_id');
    }

    public function category()
    {
        return $this->belongsto(CarCategory::class,'car_category_id');
    }
}
