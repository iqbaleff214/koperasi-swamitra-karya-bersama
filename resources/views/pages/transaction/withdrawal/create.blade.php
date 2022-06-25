@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('transaction.withdrawal.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Kode Transaksi</label>
                                        <input type="hidden" name="type" value="penarikan">
                                        <input type="text" class="form-control"
                                            value="(Generated)" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="date" name="created_at" class="form-control @error('created_at') is-invalid @enderror" value="{{ old('created_at',date('Y-m-d')) }}" placeholder="Tanggal">
                                        <span class="error invalid-feedback">{{ $errors->first('created_at') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
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
                                        <label>Saldo (Rp)</label>
                                        <input type="text" class="form-control" name="balance" value="Rp0,00" placeholder="Saldo" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Nominal Penarikan (Rp)</label>
                                        <input type="number" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', 0) }}" placeholder="Nominal Pinjaman">
                                        <span class="error invalid-feedback">{{ $errors->first('amount') }}</span>
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
        let customerId = $('select[name=customer_id]').val();
        getCurrentBalance(customerId);

        $('select[name=customer_id]').on('change', function() {
            customerId = $(this).val();
            getCurrentBalance(customerId);
        });

    });

    function getCurrentBalance(id) {
        const _balance = $('input:text[name=balance]');
        const _withdrawal = $('input[name=amount]');
        fetch(`/api/nasabah/${id}/saldo`)
            .then(response => response.json())
            .then(data => {
                _withdrawal.attr('max', data.data.current_balance)
                _balance.val(data.data.current_balance_formatted);
            });
    }

</script>
@endpush
