<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'import_type',
        'file_name',
        'file_path',
        'status',
        'logs'
    ];

    public function importedData()
    {
        return $this->hasMany(ImportedData::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 