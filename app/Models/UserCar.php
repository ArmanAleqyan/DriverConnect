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
    public function categories()
    {
        return $this->belongsToMany(CarCategory::class, 'car_categor_relations', 'car_id', 'category_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(CarAmenities::class, 'car_amenities_relations', 'car_id', 'amenities_id')->wherein('amenities_id', [1,2]);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
