<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormField extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'form_fields';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'label',
        'type',
        'position',
        'form_id',
        'required',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'text'     => 'Text',
        'date'     => 'Date',
        'email'    => 'Email',
        'textarea' => 'Textarea',
        'radio'    => 'Radio',
        'file'     => 'File',
        'checkbox' => 'Checkbox',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
