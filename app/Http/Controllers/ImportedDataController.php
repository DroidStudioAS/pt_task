<?php

namespace App\Http\Controllers;

use App\Models\ImportedData;
use Illuminate\Http\Request;

class ImportedDataController extends Controller
{
    public function orders(Request $request)
    {
        $query = ImportedData::query();

        // Handle search if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('item_description', 'like', "%{$search}%")
                  ->orWhere('so_number', 'like', "%{$search}%")
                  ->orWhere('channel', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('order_date', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        return view('imported-data.orders', compact('orders'));
    }

    public function export()
    {
        $orders = ImportedData::all();
        
        // Generate CSV file
        $filename = "orders-export-" . date('Y-m-d') . ".csv";
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = ['Order Date', 'Channel', 'SKU', 'Item Description', 'Origin', 'SO#', 
                   'Total Price', 'Cost', 'Shipping Cost', 'Profit'];

        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_date->format('Y-m-d'),
                    $order->channel,
                    $order->sku,
                    $order->item_description,
                    $order->origin,
                    $order->so_number,
                    $order->total_price,
                    $order->cost,
                    $order->shipping_cost,
                    $order->profit
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 