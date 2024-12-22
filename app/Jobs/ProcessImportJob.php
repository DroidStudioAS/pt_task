<?php

namespace App\Jobs;

use App\Models\Import;
use App\Models\ImportedData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function handle()
    {
        try {
            $this->import->update(['status' => 'processing']);
            $logs = [];

            // Get file extension
            $extension = strtolower(pathinfo(Storage::path($this->import->file_path), PATHINFO_EXTENSION));
            
            if ($extension === 'csv') {
                // Handle CSV files
                $csv = Reader::createFromPath(Storage::path($this->import->file_path), 'r');
                $csv->setHeaderOffset(0);
                $records = $csv->getRecords();
                $this->processRecords($records);
            } else {
                // Handle Excel files
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open(Storage::path($this->import->file_path));
                
                foreach ($reader->getSheetIterator() as $sheet) {
                    // Only process the first sheet
                    $rows = $sheet->getRowIterator();
                    $headers = null;
                    $processedCount = 0;
                    $errorCount = 0;

                    foreach ($rows as $row) {
                        if (!$headers) {
                            // First row is headers
                            $headers = $row->toArray();
                            continue;
                        }

                        try {
                            $record = array_combine($headers, $row->toArray());
                            
                            ImportedData::create([
                                'import_id' => $this->import->id,
                                'import_type' => $this->import->import_type,
                                'order_date' => $record['Order Date'] ?? null,
                                'channel' => $record['Channel'] ?? null,
                                'sku' => $record['SKU'] ?? null,
                                'item_description' => $record['Item Description'] ?? null,
                                'origin' => $record['Origin'] ?? null,
                                'so_number' => $record['SO#'] ?? null,
                                'total_price' => $record['Total Price'] ?? 0,
                                'cost' => $record['Cost'] ?? 0,
                                'shipping_cost' => $record['Shipping Cost'] ?? 0,
                                'profit' => $record['Profit'] ?? 0
                            ]);
                            $processedCount++;
                        } catch (\Exception $e) {
                            $errorCount++;
                            $logs[] = "Error processing row {$processedCount}: " . $e->getMessage();
                        }
                    }
                    
                    // Only process first sheet
                    break;
                }

                $reader->close();

                $logs[] = "Processed {$processedCount} records successfully.";
                if ($errorCount > 0) {
                    $logs[] = "Encountered {$errorCount} errors during import.";
                }
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

    protected function processRecords($records)
    {
        $processedCount = 0;
        $errorCount = 0;
        $logs = [];

        foreach ($records as $record) {
            try {
                ImportedData::create([
                    'import_id' => $this->import->id,
                    'import_type' => $this->import->import_type,
                    'order_date' => $record['Order Date'] ?? null,
                    'channel' => $record['Channel'] ?? null,
                    'sku' => $record['SKU'] ?? null,
                    'item_description' => $record['Item Description'] ?? null,
                    'origin' => $record['Origin'] ?? null,
                    'so_number' => $record['SO#'] ?? null,
                    'total_price' => $record['Total Price'] ?? 0,
                    'cost' => $record['Cost'] ?? 0,
                    'shipping_cost' => $record['Shipping Cost'] ?? 0,
                    'profit' => $record['Profit'] ?? 0
                ]);
                $processedCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $logs[] = "Error processing row {$processedCount}: " . $e->getMessage();
            }
        }

        return [$processedCount, $errorCount, $logs];
    }
} 