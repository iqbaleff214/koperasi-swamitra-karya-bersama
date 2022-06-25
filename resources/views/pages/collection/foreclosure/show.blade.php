@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Penarikan</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ \Carbon\Carbon::parse($foreclosure->date)->isoFormat('D MMMM Y') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nasabah</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $foreclosure->customer->number . ' - ' . $foreclosure->customer->name }}"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label>Kode Transaksi Pinjaman</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="PI-{{ sprintf('%05d', $foreclosure->loan->id) }} (Rp{{ number_format($foreclosure->loan->amount, 2, ',', '.') }})"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label>Jaminan</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $foreclosure->collateral->name }} (Rp{{ number_format($foreclosure->collateral->value, 2, ',', '.') }})"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Sudah Dibayar (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($foreclosure->loan->paid, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Sisa Pembayaran (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($foreclosure->remaining_amount, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Pengembalian (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($foreclosure->return_amount, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Total (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($foreclosure->collateral->value - $foreclosure->remaining_amount, 2, ',', '.') }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <!-- /.content -->
@endsection
