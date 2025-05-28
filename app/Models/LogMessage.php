<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogMessage extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'log_messages';

    public const ROLE_RADIO = [
        'user' => 'User',
        'chat' => 'Chat',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'log_id',
        'role',
        'message',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function log()
    {
        return $this->belongsTo(Log::class, 'log_id');
    }
}
