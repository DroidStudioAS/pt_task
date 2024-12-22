@extends('adminlte::page')

@section('title', 'Permissions Management')

@section('content_header')
    <h1>Permissions Management</h1>
@stop

@section('content')
@include('partials.notification')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Permissions List</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPermissionModal">
                    Add New Permission
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->description }}</td>
                            <td>
                                <button class="btn btn-primary edit-btn" data-id="{{ $permission->id }}" data-name="{{ $permission->name }}" data-description="{{ $permission->description }}" data-toggle="modal" data-target="#editPermissionModal">Edit</button>
                                <button class="btn btn-danger delete-btn" data-id="{{ $permission->id }}" data-toggle="modal" data-target="#deletePermissionModal">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add this modal definition somewhere in your Blade template -->
    <div class="modal fade" id="addPermissionModal" tabindex="-1" role="dialog" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">Add New Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form for adding a new permission -->
                    <form id="addPermissionForm" action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                            <label for="permissionName">Permission Name</label>
                            <input type="text" class="form-control" id="permissionName" placeholder="Enter permission name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="permissionDescription">Description</label>
                            <textarea class="form-control" id="permissionDescription" placeholder="Enter description" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Permission Modal -->
    <div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPermissionForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="editPermissionName">Permission Name</label>
                            <input type="text" class="form-control" id="editPermissionName" name="name">
                        </div>
                        <div class="form-group">
                            <label for="editPermissionDescription">Description</label>
                            <textarea class="form-control" id="editPermissionDescription" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Permission Modal -->
    <div class="modal fade" id="deletePermissionModal" tabindex="-1" role="dialog" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePermissionModalLabel">Delete Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this permission?</p>
                    <form id="deletePermissionForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var description = $(this).data('description');
                $('#editPermissionForm').attr('action', '/permissions/' + id);
                $('#editPermissionName').val(name);
                $('#editPermissionDescription').val(description);
            });

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#deletePermissionForm').attr('action', '/permissions/' + id);
            });
        });
    </script>
@stop 