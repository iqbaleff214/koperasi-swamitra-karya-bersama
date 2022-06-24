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
                                    <label>Kode Transaksi</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $code }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $loan->created_at->isoFormat('D MMMM Y') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nasabah</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $loan->customer->number . ' - ' . $loan->customer->name }}"
                                        placeholder="Nasabah" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nominal Pinjaman (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($loan->amount, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Jangka Waktu (Bulan)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $loan->period }} (bulan) kali cicilan"
                                        placeholder="Jangka Waktu (Bulan)" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nominal Angsuran (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($loan->installment, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nominal Pengembalian (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($loan->return_amount, 2, ',', '.') }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Terbayar (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($loan->paid, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Sisa Hutang/Pinjaman (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($loan->amount - $loan->paid, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Barang Jaminan</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ old('name', $loan->collateral->name) }}" placeholder="Barang Jaminan"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nilai Jaminan (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($loan->collateral->value, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ old('description', $loan->collateral->description) }}"
                                        placeholder="Keterangan" disabled>
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
