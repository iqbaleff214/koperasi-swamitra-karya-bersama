@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('collection.visit.update', $visit) }}" method="post">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Kunjungan</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" value="{{ old('created_at', date('Y-m-d', strtotime($visit->created_at))) }}" class="form-control @error('created_at') is-invalid @enderror" name="created_at" value="{{ old('created_at') }}" readonly placeholder="Tanggal Daftar">
                                        <span class="error invalid-feedback">{{ $errors->first('created_at') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nasabah</label>
                                        <input type="hidden" name="customer_id" value="{{ $visit->customer_id }}">
                                        <input type="text" class="form-control" value="{{ $visit->customer->number . ' - ' . $visit->customer->name }}" placeholder="Nasabah" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Kode Transaksi Pinjaman</label>
                                        <input type="hidden" name="loan_id" value="{{ $visit->loan_id }}">
                                        <input type="text" class="form-control" value="PI-{{ sprintf("%05d", $visit->loan->id) }} (Rp{{ number_format($visit->loan->amount) }})" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Sisa Pembayaran (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('remaining_amount') is-invalid @enderror" name="remaining_amount" value="{{ old('remaining_amount', $visit->remaining_amount) }}" placeholder="Sisa Pembayaran">
                                        <span class="error invalid-feedback">{{ $errors->first('remaining_amount') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Kolektor</label>
                                        <select class="form-control @error('user_id') is-invalid @enderror" name="user_id">
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $visit->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('user_id') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Keterangan" name="description" cols="30" rows="5">{{ old('description', $visit->description) }}</textarea>
                                        <span class="error invalid-feedback">{{ $errors->first('description') }}</span>
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
