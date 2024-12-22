<?php

namespace App\Jobs;

use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $import;
    protected $importConfig;

    public function __construct(Import $import)
    {
        $this->import = $import;
        $this->importConfig = Config::get("imports.{$import->import_type}");
    }

    public function handle()
    {
        try {
            if (!$this->importConfig) {
                throw new \Exception("Invalid import type configuration");
            }

            $this->import->update(['status' => 'processing']);
            $logs = [];

            $extension = strtolower(pathinfo(Storage::path($this->import->file_path), PATHINFO_EXTENSION));
            
            if ($extension === 'csv') {
                $this->processCsvFile($logs);
            } else {
                $this->processExcelFile($logs);
            }

            $this->import->update([
                'status' => 'completed',
                'logs' => implode("\n", $logs)
            ]);

        } catch (\Exception $e) {
            $this->import->update([
                'status' => 'failed',
                'logs' => "Import failed: " . $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function processRecord($record, &$logs)
    {
        $fileConfig = collect($this->importConfig['files'])->first();
        
        // Map headers to database columns
        $mappedData = collect($fileConfig['headers_to_db'])->map(function($dbColumn, $fileHeader) use ($record) {
            return [$dbColumn => $record[$fileHeader] ?? null];
        })->collapse()->toArray();

        // Add import_id and import_type
        $mappedData['import_id'] = $this->import->id;
        $mappedData['import_type'] = $this->import->import_type;

        // Validate data
        $validator = Validator::make($mappedData, $fileConfig['validation'] ?? []);
        
        if ($validator->fails()) {
            $logs[] = "Validation failed: " . implode(", ", $validator->errors()->all());
            return false;
        }

        // Convert types
        foreach ($fileConfig['types'] ?? [] as $field => $type) {
            if (isset($mappedData[$field])) {
                $mappedData[$field] = $this->convertType($mappedData[$field], $type);
            }
        }

        $model = $this->importConfig['model'];

        if (!empty($fileConfig['update_or_create'])) {
            $keys = collect($fileConfig['update_or_create']['keys'])
                ->mapWithKeys(function($key) use ($mappedData) {
                    return [$key => $mappedData[$key]];
                })->toArray();

            // If audit is enabled, record the changes
            if ($fileConfig['update_or_create']['audit']) {
                $existing = $model::where($keys)->first();
                if ($existing) {
                    $this->auditChanges($existing, $mappedData);
                }
            }

            $model::updateOrCreate($keys, $mappedData);
        } else {
            $model::create($mappedData);
        }

        return true;
    }

    protected function convertType($value, $type)
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

    protected function auditChanges($existing, $newData)
    {
        $changes = [];
        foreach ($newData as $key => $value) {
            if ($existing->$key != $value) {
                $changes[] = [
                    'field' => $key,
                    'old_value' => $existing->$key,
                    'new_value' => $value
                ];
            }
        }

        if (!empty($changes)) {
            // Implement your audit logging here
            // You might want to create an Audit model and table
        }
    }

    // ... rest of the methods for processing CSV and Excel files remain similar
    // but should use processRecord() method instead of direct creation
} 