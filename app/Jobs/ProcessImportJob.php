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
use Illuminate\Support\Facades\Mail;
use App\Mail\ImportFailedMail;

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
            
            // Send failure notification email
            Mail::send(new ImportFailedMail($this->import, $e->getMessage()));
            
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

    protected function processCsvFile(&$logs)
    {
        $csv = Reader::createFromPath(Storage::path($this->import->file_path), 'r');
        $csv->setHeaderOffset(0);

        // Get the headers from the CSV file
        $fileHeaders = $csv->getHeader();
        
        // Get required headers from config
        $requiredHeaders = array_keys($this->importConfig['files']['orders']['headers_to_db']);

        // Check for missing headers
        $missingHeaders = array_diff($requiredHeaders, $fileHeaders);
        
        if (!empty($missingHeaders)) {
            throw new \Exception("Missing required headers: " . implode(', ', $missingHeaders));
        }

        // Process records
        foreach ($csv->getRecords() as $record) {
            $this->processRecord($record, $logs);
        }
    }

    protected function processExcelFile(&$logs)
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open(Storage::path($this->import->file_path));

        foreach ($reader->getSheetIterator() as $sheet) {
            $isFirstRow = true;
            $headers = [];
            
            foreach ($sheet->getRowIterator() as $row) {
                if ($isFirstRow) {
                    $headers = $row->toArray();
                    
                    // Get required headers from config
                    $requiredHeaders = array_keys($this->importConfig['files']['orders']['headers_to_db']);

                    // Check for missing headers
                    $missingHeaders = array_diff($requiredHeaders, $headers);
                    
                    if (!empty($missingHeaders)) {
                        throw new \Exception("Missing required headers: " . implode(', ', $missingHeaders));
                    }

                    $isFirstRow = false;
                    continue;
                }

                $record = array_combine($headers, $row->toArray());
                $this->processRecord($record, $logs);
            }
            break; // Only process first sheet
        }

        $reader->close();
    }
} 