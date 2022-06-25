@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('transaction.withdrawal.update', $deposit) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Kode Transaksi</label>
                                        <input type="hidden" name="type" value="penarikan">
                                        <input type="text" class="form-control" value="{{ $code }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="date" class="form-control" value="{{ old('created_at', date('Y-m-d', strtotime($deposit->created_at))) }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Nasabah</label>
                                        <input type="text" class="form-control" value="{{ $deposit->customer->number . ' - ' . $deposit->customer->name }}" disabled>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Nominal Penarikan (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $deposit->amount) }}" placeholder="Nominal Pinjaman">
                                        <span class="error invalid-feedback">{{ $errors->first('amount') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Saldo Sebelumnya (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('previous_balance') is-invalid @enderror" name="previous_balance" value="{{ old('previous_balance', $deposit->previous_balance) }}" placeholder="Saldo">
                                        <span class="error invalid-feedback">{{ $errors->first('previous_balance') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Saldo Akhir (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('current_balance') is-invalid @enderror" name="current_balance" value="{{ old('current_balance', $deposit->current_balance) }}" placeholder="Saldo">
                                        <span class="error invalid-feedback">{{ $errors->first('current_balance') }}</span>
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

