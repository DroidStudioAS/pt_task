<?php

namespace App\Http\Controllers;

use App\Models\ImportedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ImportedDataController extends Controller
{
    public function show($type)
    {
        $data = ImportedData::where('import_type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('imported-data.' . $type, compact('data'));
    }

    public function search(Request $request, $type)
    {
        $query = $request->get('query');
        
        $data = ImportedData::where('import_type', $type)
            ->where(function($q) use ($query) {
                $q->where('order_date', 'like', "%{$query}%")
                  ->orWhere('channel', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('item_description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($data);
    }

    public function export($type)
    {
        $data = ImportedData::where('import_type', $type)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '_export.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Order Date', 'Channel', 'SKU', 'Item Description', 'Origin', 'SO#', 'Total Price', 'Cost', 'Shipping Cost', 'Profit']);
            
            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->order_date,
                    $row->channel,
                    $row->sku,
                    $row->item_description,
                    $row->origin,
                    $row->so_number,
                    $row->total_price,
                    $row->cost,
                    $row->shipping_cost,
                    $row->profit
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
} 