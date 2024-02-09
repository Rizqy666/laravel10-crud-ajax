@extends('layouts.app')
@section('style')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Halaman Kategori') }}</div>
                    <div class="card-body">
                        <div>
                            <a class="btn btn-success" href="javascript:void(0)" id="createNewKategori"> Create New
                                kategori</a>
                        </div>
                        <table class="table table-bordered data-table">
                            <thead>
                                <tr>
                                    <th width="10px">No</th>
                                    <th>Nama Kategori</th>
                                    <th width="150px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- modal ajax --}}
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form id="kategoriForm" name="kategoriForm" class="form-horizontal">
                        <input type="hidden" name="kategori_id" id="kategori_id">
                        <div class="form-group">
                            <label for="nama_kategori" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                                    placeholder="Enter Name" value="" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10 py-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <SCript src="https://code.jquery.com/jquery-3.7.0.js"></SCript>
    <SCript src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></SCript>
    <script type="text/javascript">
        $(function() {
            //Pass Header Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //Render DataTable
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kategori.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nama_kategori',
                        name: 'nama_kategori'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            //Click to Create Button
            $('#createNewKategori').click(function() {
                $('#saveBtn').val("create-kategori");
                $('#kategori_id').val('');
                $('#kategoriForm').trigger("reset");
                $('#modelHeading').html("Create New Kategori");
                $('#ajaxModel').modal('show');
            });
            //Click to Edit Button
            $('body').on('click', '.editProduct', function() {
                var kategori_id = $(this).data('id');
                $.get("{{ route('kategori.edit', ':id') }}".replace(':id', kategori_id), function(data) {
                    $('#modelHeading').html("Edit Kategori");
                    $('#saveBtn').val("edit-kategori");
                    $('#ajaxModel').modal('show');
                    $('#kategori_id').val(data.id);
                    $('#nama_kategori').val(data.nama_kategori);
                })
            });
            //Save Button Click
            $('body').on('click', '#saveBtn', function(e) {
                e.preventDefault();
                var url = "{{ route('kategori.store') }}";
                var method = "POST";

                var kategori_id = $('#kategori_id').val();
                if (kategori_id) {
                    url = "{{ route('kategori.update', ':id') }}".replace(':id', kategori_id);
                    method = "PUT";
                }

                $.ajax({
                    data: $('#kategoriForm').serialize(),
                    url: url,
                    type: method,
                    dataType: 'json',
                    success: function(data) {
                        $('#kategoriForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        showAlert('Kategori berhasil disimpan!', 'success');
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save Changes');
                    }
                });
            });
            //Delete Button Click
            $('body').on('click', '.deleteProduct', function() {
                var kategori_id = $(this).data("id");
                if (confirm("Are You sure want to delete!")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('kategori.destroy', ':id') }}".replace(':id', kategori_id),
                        success: function(data) {
                            table.draw();
                            showAlert('Kategori berhasil dihapus!', 'success');
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            showAlert('Gagal menghapus kategori.', 'danger');
                        }
                    });
                }
            })
            //Function to Show Alert
            function showAlert(message, type) {
                $('.alert').remove();
                var alertBox = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '</div>');
                $('.card-body').prepend(alertBox);
            }

        });
    </script>
@endpush
