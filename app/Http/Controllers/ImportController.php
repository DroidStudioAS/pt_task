<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportedData;
use App\Http\Requests\Import\StoreImportRequest;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessImportJob;
use Illuminate\Support\Facades\Config;

class ImportController extends Controller
{
    public function index()
    {
        $importTypes = collect(Config::get('imports'))->map(function($config, $key) {
            return [
                'key' => $key,
                'name' => $config['name'],
                'headers' => collect($config['files'])->map(function($file) {
                    return array_keys($file['headers_to_db']);
                })->first()
            ];
        })->filter(function($type) {
            return auth()->user()->hasPermission($type['permission_required'] ?? '');
        });

        $requiredHeaders = array_keys(config('imports.orders.files.orders.headers_to_db'));

        return view('import.index', compact('importTypes', 'requiredHeaders'));
    }

    public function store(StoreImportRequest $request)
    {
        $validated = $request->validated();
        
        $importConfig = Config::get("imports.{$validated['import_type']}");
        
        if (!$importConfig) {
            return back()->with('error', 'Invalid import type.');
        }

        if (!auth()->user()->hasPermission("import_data")) {
            return back()->with('error', 'You do not have permission to perform this import.');
        }

        $path = $request->file('file')->store('imports');
        
        $import = Import::create([
            'user_id' => auth()->id(),
            'import_type' => $validated['import_type'],
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending'
        ]);

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
        $query = ImportedData::query()
            ->whereHas('import', function($q) {
                $q->whereNull('deleted_at');
            })
            ->where('import_id', $import->id);

        $orders = $query->orderBy('order_date', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        return view('imported-data.orders', [
            'orders' => $orders,
            'import' => $import
        ]);
    }

    public function destroy(Import $import)
    {
        $import->delete();
        return redirect()->back()->with('success', 'Import deleted successfully');
    }
} 