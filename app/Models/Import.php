<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'user_id',
        'import_type',
        'file_name',
        'file_path',
        'status',
        'records_processed',
        'failed_records',
        'logs'
    ];

    protected $casts = [
        'logs' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 