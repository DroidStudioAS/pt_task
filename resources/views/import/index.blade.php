@extends('adminlte::page')

@section('title', 'Data Import')

@section('content_header')
    <h1>Data Import</h1>
@stop

@section('content')
@include('partials.notification')
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
                        @foreach($importNames as $type)
                            <option value="{{ $type['key'] }}">{{ $type['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="file">Select Files</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="files[]" multiple accept=".xlsx,.xls,.csv">
                            <label class="custom-file-label" for="file">Choose files</label>
                        </div>
                    </div>
                </div>

                <div class="required-headers mt-4 mb-4">
                    <h5>Required Headers:</h5>
                    <ul class="list-unstyled"></ul>
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
        // Add the headers data from PHP to JavaScript
        const importHeaders = @json($requiredHeaders);

        $(document).ready(function() {
            // Update the file input label when files are selected
            $('input[type="file"]').change(function(e){
                var fileNames = Array.from(e.target.files).map(file => file.name);
                var label = fileNames.length > 1 
                    ? fileNames.length + ' files selected' 
                    : fileNames[0];
                $(this).next('.custom-file-label').html(label);
            });

            // Function to update headers display
            function updateHeaders() {
                const selectedImportType = $('#importType option:selected').text();
                const headers = importHeaders[selectedImportType] || {};
                
                let headersList = '<h5>Required Headers:</h5>';
                headersList += '<ul class="list-unstyled">';
                
                for (const [excelHeader, dbField] of Object.entries(headers)) {
                    headersList += `<li><code>${excelHeader}</code> → ${dbField}</li>`;
                }
                
                headersList += '</ul>';
                
                $('.required-headers').html(headersList);
            }

            // Update headers when page loads
            updateHeaders();

            // Update headers when import type changes
            $('#importType').change(updateHeaders);
        });
    </script>
@stop 