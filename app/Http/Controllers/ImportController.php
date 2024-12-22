<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessImportJob;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'import_type' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        // Store the file
        $path = $request->file('file')->store('imports');
        
        // Create import record
        $import = Import::create([
            'user_id' => auth()->id(),
            'import_type' => $validated['import_type'],
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending'
        ]);

        // Dispatch job to process the import
        ProcessImportJob::dispatch($import);

        return redirect()->route('imports.history')
            ->with('success', 'File uploaded successfully. Import is being processed.');
    }

    public function viewLogs(Import $import)
    {
        return response()->json([
            'success' => true,
            'logs' => $import->logs,
            'filename' => $import->file_name
        ]);
    }

    public function showImportOrders(Import $import)
    {
        $query = ImportedData::query()->where('import_id', $import->id);

        $orders = $query->orderBy('order_date', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        return view('imported-data.orders', [
            'orders' => $orders,
            'import' => $import
        ]);
    }
} 