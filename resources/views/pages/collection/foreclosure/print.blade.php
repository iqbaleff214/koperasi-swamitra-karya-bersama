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
        <td class="font-weight-bold">Filter:</td>
        <td>{{ $filter }}</td>
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
            <th scope="col">No Rek</th>
            <th scope="col">Nama Nasabah</th>
            <th scope="col">Nama Jaminan</th>
            <th scope="col">Tgl Penarikan</th>
            <th scope="col">Nominal</th>
            <th scope="col">Sisa Belum Dibayar</th>
            <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $item->customer->number }}</td>
                <td>{{ $item->customer->name }}</td>
                <td>{{ $item->collateral->name ?? $item->collateral->description }}</td>
                <td>{{ \Carbon\Carbon::parse($item->date)->isoFormat('DD-MM-Y') }}</td>
                <td class="text-right">Rp{{ number_format($item->collateral->value, 2, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($item->remaining_amount, 2, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($item->collateral->value - $item->remaining_amount, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
