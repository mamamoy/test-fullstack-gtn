<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReports extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
    public function id_images()
    {
        return $this->hasMany(SalesReportIDPictureImages::class, 'sales_report_id');
    }
    public function home_images()
    {
        return $this->hasMany(SalesReportHomePictureImages::class, 'sales_report_id');
    }
    public function sellingPackets(){
        return $this->belongsTo(SellingPackets::class, 'selling_packet_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'appliedBy_id');
    }
}
