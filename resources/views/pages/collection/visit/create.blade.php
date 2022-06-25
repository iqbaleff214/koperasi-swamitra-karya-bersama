@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('collection.visit.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Kunjungan</label>
                                        <input type="date" class="form-control @error('created_at') is-invalid @enderror" name="created_at" value="{{ old('created_at',date('Y-m-d')) }}" placeholder="Tanggal Daftar">
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
                                    <div class="form-group" id="customer">
                                        <label>Kode Transaksi Pinjaman</label>
                                        <select class="form-control" name="loan_id" required>
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('loan_id') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Sisa Pembayaran (Rp)</label>
                                        <input type="number" min="0" class="form-control change-installment @error('remaining_amount') is-invalid @enderror" name="remaining_amount" value="{{ old('remaining_amount', 0) }}" placeholder="Sisa Pembayaran">
                                        <span class="error invalid-feedback">{{ $errors->first('remaining_amount') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Kolektor</label>
                                        <select class="form-control @error('user_id') is-invalid @enderror" name="user_id">
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('user_id') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Keterangan" name="description" cols="30" rows="5">{{ old('description') }}</textarea>
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
        let customerId = $('select[name=customer_id]').val();

        populateLoanOption(customerId);

        $('select[name=customer_id]').on('change', function() {
            customerId = $(this).val();
            populateLoanOption(customerId);
        });

        $('select[name=loan_id]').on('change', function() {
            setRemaining();
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
                    element += `<option data-remaining="${el.amount - el.paid}" value="${el.id}">${noTrans} (${price})</option>`;
                });
                _select.html(element);
                setRemaining();
            });
    }

    function setRemaining() {
        const amount = $('select[name=loan_id] > option:selected').data('remaining');
        console.log(amount);
        $('input[name=remaining_amount]').val(amount);
    }
</script>
@endpush
