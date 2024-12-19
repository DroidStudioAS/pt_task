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
use League\Csv\Statement;

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

            // Read the CSV file
            $csv = Reader::createFromPath(Storage::path($this->import->file_path), 'r');
            $csv->setHeaderOffset(0);

            // Get headers and validate them
            $headers = $csv->getHeader();
            $requiredHeaders = [
                'Order Date', 'Channel', 'SKU', 'Item Description', 'Origin', 
                'SO#', 'Total Price', 'Cost', 'Shipping Cost', 'Profit'
            ];

            $missingHeaders = array_diff($requiredHeaders, $headers);
            if (!empty($missingHeaders)) {
                throw new \Exception('Missing required headers: ' . implode(', ', $missingHeaders));
            }

            // Process records
            $records = Statement::create()->process($csv);
            $processedCount = 0;
            $failedCount = 0;

            foreach ($records as $record) {
                try {
                    ImportedData::create([
                        'import_type' => $this->import->import_type,
                        'order_date' => $record['Order Date'],
                        'channel' => $record['Channel'],
                        'sku' => $record['SKU'],
                        'item_description' => $record['Item Description'],
                        'origin' => $record['Origin'],
                        'so_number' => $record['SO#'],
                        'total_price' => $record['Total Price'],
                        'cost' => $record['Cost'],
                        'shipping_cost' => $record['Shipping Cost'],
                        'profit' => $record['Profit']
                    ]);
                    $processedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $logs[] = [
                        'row' => $processedCount + $failedCount,
                        'error' => $e->getMessage(),
                        'data' => $record
                    ];
                }
            }

            // Update import status
            $this->import->update([
                'status' => 'completed',
                'records_processed' => $processedCount,
                'failed_records' => $failedCount,
                'logs' => $logs
            ]);

        } catch (\Exception $e) {
            $this->import->update([
                'status' => 'failed',
                'logs' => [['error' => $e->getMessage()]]
            ]);
        }
    }
} 