@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4">
                                <a href="{{ route('transaction.withdrawal.create') }}" class="btn btn-success">Baru</a>
                                <button class="btn btn-outline-success" data-toggle="modal"
                                    data-target="#print">Cetak</button>
                            </div>
                            <div class="col-8 row">
                                <div class="col-12 col-md-4">
                                    <select class="form-control" name="customer">
                                        <option value="">Semua Nasabah</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->number . ' - ' . $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <input type="date" class="form-control date-filter" name="time_from" placeholder="Sejak">
                                </div>
                                <div class="col-12 col-md-4">
                                    <input type="date" class="form-control date-filter" name="time_to" placeholder="Hingga">
                                </div>
                            </div>
                        </div>
                        <table id="datatable-bs" class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 60px">#</th>
                                    <th style="width: 70px;">Tanggal</th>
                                    <th>Nasabah</th>
                                    <th>Nominal</th>
                                    <th>Saldo</th>
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
                <form action="{{ route('transaction.withdrawal.print') }}" method="post">
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
                                    <input required type="date" class="form-control" name="time_from" value="{{ date('Y-m-d') }}" placeholder="Sejak">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Hingga</label>
                                    <input required type="date" class="form-control" name="time_to" value="{{ date('Y-m-d') }}" placeholder="Sejak">
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
            let customerId = null;
            let timeFrom = null;
            let timeTo = null;

            //Initialize Datatables Elements
            const dtTable = $('#datatable-bs').DataTable({
                ajax: {
                    url: "{!! url()->current() !!}",
                    data: function (d) {
                        d.customer = customerId;
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
                        data: 'id',
                        name: 'id',
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
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'current_balance',
                        name: 'current_balance'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('select[name=customer]').on('change', function() {
                customerId = $(this).val();
                dtTable.draw();
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
