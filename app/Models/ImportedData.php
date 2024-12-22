<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedData extends Model
{
    protected $guarded = ['id'];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $importConfig = config("imports.{$model->import_type}");
            if ($importConfig && isset($importConfig['files'])) {
                $fileConfig = collect($importConfig['files'])->first();
                foreach ($fileConfig['types'] ?? [] as $field => $type) {
                    if (isset($model->$field)) {
                        $model->$field = static::castValue($model->$field, $type);
                    }
                }
            }
        });
    }

    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'date':
                return \Carbon\Carbon::parse($value);
            case 'decimal':
                return floatval($value);
            case 'integer':
                return intval($value);
            default:
                return $value;
        }
    }
} 