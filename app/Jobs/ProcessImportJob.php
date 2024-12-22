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

            // Read CSV file
            $csv = Reader::createFromPath(Storage::path($this->import->file_path), 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();
            $processedCount = 0;
            $errorCount = 0;

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

            $logs[] = "Processed {$processedCount} records successfully.";
            if ($errorCount > 0) {
                $logs[] = "Encountered {$errorCount} errors during import.";
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
} 