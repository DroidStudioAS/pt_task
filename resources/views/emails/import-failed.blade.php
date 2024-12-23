<!DOCTYPE html>
<html>
<head>
    <title>Import Failed</title>
</head>
<body>
    <h1>Import Job Failed</h1>
    <p>An import job has failed with the following details:</p>
    
    <ul>
        <li>Import ID: {{ $import->id }}</li>
        <li>Import Type: {{ $import->import_type }}</li>
        <li>File: {{ $import->file_path }}</li>
        <li>Error Message: {{ $errorMessage }}</li>
    </ul>
</body>
</html> 