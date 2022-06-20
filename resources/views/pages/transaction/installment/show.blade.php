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
                                        value="{{ $deposit->created_at->isoFormat('D MMMM Y') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nasabah</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ $deposit->customer->number . ' - ' . $deposit->customer->name }}"
                                        placeholder="Nasabah" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nominal Pembayaran (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($deposit->amount, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Saldo Sebelumnya (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($deposit->previous_balance, 2, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Saldo Akhir (Rp)</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="Rp{{ number_format($deposit->current_balance, 2, ',', '.') }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Jenis Simpanan</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ ucfirst($deposit->type) }}"
                                        placeholder="Jenis Simpanan" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Kode Transaksi Pinjaman</label>
                                    <input type="text" class="form-control-plaintext" value="PI-{{ sprintf("%05d", $deposit->loan->id) }} (Rp{{ number_format($deposit->loan->amount) }})" disabled>
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
