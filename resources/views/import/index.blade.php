@extends('adminlte::page')

@section('title', 'Data Import')

@section('content_header')
    <h1>Data Import</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Import Data</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="importType">Import Type</label>
                    <select class="form-control" id="importType" name="import_type">
                        <option value="orders">Import Orders</option>
                        <!-- Additional import types will be added dynamically -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="file">Select File</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.xls,.csv">
                            <label class="custom-file-label" for="file">Choose file</label>
                        </div>
                    </div>
                </div>

                <div class="required-headers mt-4">
                    <h5>Required Headers:</h5>
                    <p class="text-muted">{{ implode(', ', $requiredHeaders) }}</p>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Import</button>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Update the file input label when a file is selected
            $('input[type="file"]').change(function(e){
                var fileName = e.target.files[0].name;
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>
@stop 