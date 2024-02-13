<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function salesReport(){
        return $this->hasMany(SalesReports::class, 'customer_id');
    }
    
}
