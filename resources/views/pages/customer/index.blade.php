@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <a href="{{ route('customer.create') }}" class="btn btn-success">Baru</a>
                                <button class="btn btn-outline-success" data-toggle="modal"
                                    data-target="#print">Cetak</button>
                            </div>
                        </div>
                        <table id="datatable-bs" class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 25px">No</th>
                                    <th style="width: 50px;">Bergabung</th>
                                    <th>No. Rekening</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th style="width: 50px;">Status</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <!-- /.content -->

    <!-- Modal -->
    <div class="modal fade" id="print" tabindex="-1" aria-labelledby="printLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('customer.print') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="printLabel">Cetak Laporan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Sejak</label>
                                    <input required type="date" value="{{ date('Y-m-d') }}" class="form-control" name="time_from" value="{{ date('Y-m-d') }}" placeholder="Sejak">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Hingga</label>
                                    <input required type="date" value="{{ date('Y-m-d') }}" class="form-control" name="time_to" value="{{ date('Y-m-d') }}" placeholder="Sejak">
                                </div>
                            </div>
                        </div>
                        Tekan tombol Cetak untuk mengunduh laporan dalam bentuk PDF.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <!-- Datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css">
@endpush

@push('script')
    <!--Datatable-->
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(function() {

            //Initialize Datatables Elements
            $('#datatable-bs').DataTable({
                ajax: "{!! url()->current() !!}",
                autoWidth: false,
                responsive: true,
                processing: true,
                serverSide: true,
                lengthChange: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'joined_at',
                        name: 'joined_at'
                    },
                    {
                        data: 'number',
                        name: 'number'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
