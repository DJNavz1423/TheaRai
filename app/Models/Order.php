<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $table = 'laravel.orders';

    protected static function booted(){
        static::addGlobalScope('branch', function (Builder $builder){
            if(auth()->check() && auth()->user()->role !== 'admin'){
                $builder->where('branch_id', auth()->user()->branch_id);
            }
        });
    }
}
