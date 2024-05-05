<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionKey extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function region()
    {
        return $this->belongsTo(Region::class,'region_id');
    }
    public function work_rule()
    {
        return $this->Hasmany(YandexWorkRule::class,'key_id');
    }
}
