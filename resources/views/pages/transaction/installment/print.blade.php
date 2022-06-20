@extends('layouts.pdf')

@section('header')
<table style="width: 100%">
    <tr>
        <td style="width: 15%" class="font-weight-bold">Dicetak:</td>
        <td style="width: 50%">{{ $user->name . ' (' . $user->username . ')' }}</td>
        <td style="width: 15%" class="font-weight-bold">Tanggal Cetak:</td>
        <td style="width: 20%; text-align: right">{{ $date }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-weight-bold">NIK:</td>
        <td>{{ $customer->nik }}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-weight-bold">No Rek:</td>
        <td>{{ $customer->number }}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-weight-bold">Nama:</td>
        <td>{{ $customer->name }}</td>
        <td></td>
        <td></td>
    </tr>
</table>
@endsection

@section('content')
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">Tgl Simpan</th>
            <th scope="col">Simpanan Wajib</th>
            <th scope="col">Simpanan Sukarela</th>
            <th scope="col">Simpanan Pokok</th>
            <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            @php($total += $item->saldo)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('DD-MM-Y') }}</td>
                <td class="text-right">Rp{{ number_format($item->wajib, 2, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($item->sukarela, 2, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($item->pokok, 2, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($item->saldo, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="5">Total</th>
            <td class="text-right">Rp{{ number_format($total, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
@endsection
