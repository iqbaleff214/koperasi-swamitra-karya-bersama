@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <a href="{{ route('collection.visit.create') }}" class="btn btn-success">Baru</a>
                                <button class="btn btn-outline-success" data-toggle="modal"
                                    data-target="#print">Cetak</button>
                            </div>
                            <div class="col row">
                                <div class="col-12 col-md-6">
                                    <input type="date" class="form-control date-filter" name="time_from" placeholder="Sejak">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="date" class="form-control date-filter" name="time_to" placeholder="Hingga">
                                </div>
                            </div>
                        </div>
                        <table id="datatable-bs" class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 25px">No</th>
                                    <th style="width: 70px;">Tanggal</th>
                                    <th>Nasabah</th>
                                    <th>Pinjaman</th>
                                    <th>Sisa</th>
                                    <th>Kolektor</th>
                                    <th>Keterangan</th>
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
                <form action="{{ route('collection.visit.print') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="printLabel">Cetak Laporan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
            let timeFrom = null;
            let timeTo = null;

            //Initialize Datatables Elements
            const dtTable = $('#datatable-bs').DataTable({
                ajax: {
                    url: "{!! url()->current() !!}",
                    data: function (d) {
                        d.from = timeFrom;
                        d.to = timeTo;
                    }
                },
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'loan',
                        name: 'loan',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'remaining_amount',
                        name: 'remaining_amount'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('.date-filter').on('change', function() {
                const filterName = $(this).attr('name');
                const filterValue = $(this).val();

                if (filterName == 'time_from') {
                    timeFrom = filterValue;
                } else {
                    timeTo = filterValue;
                }

                dtTable.draw();
            });
        });
    </script>
@endpush
