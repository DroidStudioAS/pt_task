<?php

namespace App\Http\Controllers;

use App\Models\Import;
use Illuminate\Http\Request;

class ImportHistoryController extends Controller
{
    public function index()
    {
        $imports = Import::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('imports.history', compact('imports'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $imports = Import::with('user')
            ->where(function($q) use ($query) {
                $q->where('import_type', 'like', "%{$query}%")
                  ->orWhere('file_name', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($imports);
    }

    public function logs(Import $import)
    {
        return response()->json([
            'logs' => $import->logs
        ]);
    }
} 