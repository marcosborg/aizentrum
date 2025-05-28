<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoloniItem extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'moloni_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'moloni_invoice_id',
        'name',
        'qty',
        'handled',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function moloni_invoice()
    {
        return $this->belongsTo(MoloniInvoice::class, 'moloni_invoice_id');
    }
}
