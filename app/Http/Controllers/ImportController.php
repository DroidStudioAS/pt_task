<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportedData;
use App\Http\Requests\Import\StoreImportRequest;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessImportJob;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            return auth()->user()->can($config['permission_required'] ?? '');
        });

        $importNames = collect(Config::get('imports'))->map(function($config, $key) {
            return [
                'key' => $key,
                'name' => $config['name'],
                'permission_required' => $config['permission_required']
            ];
        });

        $requiredHeaders = collect(Config::get('imports'))->mapWithKeys(function($config, $key) {
            $headers = collect($config['files'])->first()['headers_to_db'] ?? [];
            return [$config['name'] => $headers];
        })->toArray();

        return view('import.index', compact('importTypes', 'requiredHeaders', 'importNames'));
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

        // Process each uploaded file
        foreach ($request->file('files') as $file) {
            $path = $file->store('imports');
            
            $import = Import::create([
                'user_id' => auth()->id(),
                'import_type' => $validated['import_type'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'status' => 'pending'
            ]);

            ProcessImportJob::dispatch($import);
        }

        return redirect()->route('imports.history')
            ->with('success', count($request->file('files')) . ' files uploaded successfully. Imports are being processed.');
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
        $fillableFields = (new ImportedData())->getFillable();
        $filterOptions = [];
        foreach ($fillableFields as $field) {
            $filterOptions[$field] = ImportedData::select($field)
                ->distinct()
                ->whereNotNull($field)
                ->pluck($field);
        }


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
            'import' => $import,
            'fillableFields' => $fillableFields,
            'filterOptions' => $filterOptions
        ]);
    }

    public function destroy(Import $import)
    {
        $import->delete();
        return redirect()->back()->with('success', 'Import deleted successfully');
    }
} 