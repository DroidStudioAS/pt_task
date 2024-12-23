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
                    <label for="file">Select File</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.xls,.csv">
                            <label class="custom-file-label" for="file">Choose file</label>
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
            // Update the file input label when a file is selected
            $('input[type="file"]').change(function(e){
                var fileName = e.target.files[0].name;
                $(this).next('.custom-file-label').html(fileName);
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