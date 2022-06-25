@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('collection.foreclosure.update', $foreclosure) }}" method="post">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Penarikan</label>
                                        <input type="date" class="form-control" value="{{ old('created_at', date('Y-m-d', strtotime($foreclosure->created_at))) }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Nasabah</label>
                                        <input type="text" class="form-control" value="{{ $foreclosure->customer->number . ' - ' . $foreclosure->customer->name }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Kode Transaksi Pinjaman</label>
                                        <input type="text" class="form-control" value="PI-{{ sprintf("%05d", $foreclosure->loan->id) }} (Rp{{ number_format($foreclosure->loan->amount, 2, ',', '.') }})" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Jaminan</label>
                                        <input type="text" class="form-control" value="{{ $foreclosure->collateral->name }} (Rp{{ number_format($foreclosure->collateral->value, 2, ',', '.') }})" disabled>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Sudah Dibayar (Rp)</label>
                                        <input type="text" class="form-control" value="Rp{{ number_format($foreclosure->loan->paid, 2, ',', '.') }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Sisa Pembayaran (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('remaining_amount') is-invalid @enderror" name="remaining_amount" value="{{ old('remaining_amount', $foreclosure->remaining_amount) }}" placeholder="Sisa Pembayaran">
                                        <span class="error invalid-feedback">{{ $errors->first('remaining_amount') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Pengembalian (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('return_amount') is-invalid @enderror" name="return_amount" value="{{ old('return_amount', $foreclosure->return_amount) }}" placeholder="Sisa Pembayaran">
                                        <span class="error invalid-feedback">{{ $errors->first('return_amount') }}</span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <!-- /.content -->
@endsection
