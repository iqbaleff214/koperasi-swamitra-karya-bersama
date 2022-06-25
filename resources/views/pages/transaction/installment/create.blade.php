@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('transaction.installment.store') }}" method="post">
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
                                        <input type="date" max="{{ date('Y-m-d') }}" class="form-control @error('created_at') is-invalid @enderror" value="{{ old('created_at',date('Y-m-d')) }}" placeholder="Tanggal">
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
                                        <label>Nominal Pembayaran (Rp)</label>
                                        <input type="number" min="0" class="form-control change-installment @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', 0) }}" placeholder="Nominal Pinjaman">
                                        <span class="error invalid-feedback">{{ $errors->first('amount') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Jenis Simpanan</label>
                                        <input type="text" class="form-control"
                                            value="Wajib" disabled>
                                        <input type="hidden" name="type" value="wajib">
                                    </div>
                                    <div class="form-group" id="customer">
                                        <label>Kode Transaksi Pinjaman</label>
                                        <select class="form-control" name="loan_id" required>
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('loan_id') }}</span>
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

        populateLoanOption(customerId);

        $('select[name=customer_id]').on('change', function() {
            customerId = $(this).val();
            populateLoanOption(customerId);
        });

        $('select[name=loan_id]').on('change', function() {
            setInstallment();
        })

    });

    function populateLoanOption(customerId) {
        const _select = $('select[name=loan_id]');
        const currencyFormat = Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        })
        fetch(`/api/nasabah/${customerId}/pinjaman`)
            .then(response => response.json())
            .then(data => {
                let element = ``;
                data.data.forEach(el => {
                    const price = currencyFormat.format(el.amount);
                    const noTrans = `PI-${el.id.toString().padStart(5, '0')}`
                    element += `<option data-installment="${el.installment}" value="${el.id}">${noTrans} (${price})</option>`;
                });
                _select.html(element);
                setInstallment();
            });
    }

    function setInstallment() {
        $('input[name=amount]').val($('select[name=loan_id] > option:selected').data('installment'));
    }
</script>
@endpush

