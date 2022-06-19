@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('transaction.loan.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Kode Transaksi</label>
                                        <input type="text" class="form-control"
                                            value="(Generated)" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" class="form-control @error('created_at') is-invalid @enderror" name="created_at" value="{{ old('created_at',date('Y-m-d')) }}" placeholder="Tanggal Daftar">
                                        <span class="error invalid-feedback">{{ $errors->first('created_at') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nasabah</label>
                                        <select class="form-control @error('customer_id') is-invalid @enderror" name="customer_id">
                                            @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->number . ' - ' . $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('customer_id') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nominal Pinjaman (Rp)</label>
                                        <input type="number" min="0" class="form-control change-installment @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', 0) }}" placeholder="Nominal Pinjaman">
                                        <span class="error invalid-feedback">{{ $errors->first('amount') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Jangka Waktu (Bulan)</label>
                                        <input type="number" min="1" class="form-control change-installment change-amount @error('period') is-invalid @enderror" name="period" value="{{ old('period', 1) }}" placeholder="Jangka Waktu (Bulan)">
                                        <span class="error invalid-feedback">{{ $errors->first('period') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nominal Angsuran (Rp)</label>
                                        <input type="number" min="0" class="form-control change-amount @error('installment') is-invalid @enderror" name="installment" value="{{ old('installment', 0) }}" placeholder="Nominal Angsuran">
                                        <span class="error invalid-feedback">{{ $errors->first('installment') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nominal Pengembalian (Rp)</label>
                                        <input type="number" readonly min="0" class="form-control @error('return_amount') is-invalid @enderror" name="return_amount" value="{{ old('return_amount', 0) }}" placeholder="Nominal Pengembalian">
                                        <span class="error invalid-feedback">{{ $errors->first('return_amount') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Barang Jaminan</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Barang Jaminan">
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nilai Jaminan (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value', 0) }}" placeholder="Nilai Jaminan (Rp)">
                                        <span class="error invalid-feedback">{{ $errors->first('value') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" placeholder="Keterangan">
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

@push('script')
<script>
    $(function() {
        $('.change-installment').on('change', function() {
            const amount = $('input[name=amount]').val();
            const period = $('input[name=period]').val();

            const installment = setInstallment(amount, period);
            setReturnAmount(period, installment);
        });
        $('.change-amount').on('change', function() {
            const period = $('input[name=period]').val();
            const installment = $('input[name=installment]').val();
            setReturnAmount(period, installment);
        });
    });

    function setInstallment(amount, period) {
        const expectedInstallment = parseInt(amount / period);
        $('input[name=installment]').val(expectedInstallment);
        return expectedInstallment;
    }

    function setReturnAmount(period, installment) {
        const returnAmount = period * installment;
        $('input[name=return_amount]').val(returnAmount);
        return returnAmount;
    }
</script>
@endpush
