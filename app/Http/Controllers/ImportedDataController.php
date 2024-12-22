<?php

namespace App\Http\Controllers;

use App\Models\ImportedData;
use Illuminate\Http\Request;

class ImportedDataController extends Controller
{
    public function orders(Request $request)
    {
        $query = ImportedData::query();

        // Get all fillable fields for filtering
        $fillableFields = (new ImportedData())->getFillable();
        
        // Handle search across all fillable fields
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search, $fillableFields) {
                foreach ($fillableFields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        // Handle individual field filters
        foreach ($fillableFields as $field) {
            if ($request->has("filter_$field") && $request->{"filter_$field"}) {
                $query->where($field, 'like', "%{$request->{"filter_$field"}}%");
            }
        }

        // Handle date range filters if they exist
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        // Handle sorting
        $sortField = $request->get('sort_by', 'order_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(10)->withQueryString();

        // Get unique values for dropdown filters
        $filterOptions = [];
        foreach ($fillableFields as $field) {
            $filterOptions[$field] = ImportedData::select($field)
                ->distinct()
                ->whereNotNull($field)
                ->pluck($field);
        }

        return view('imported-data.orders', compact('orders', 'fillableFields', 'filterOptions'));
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