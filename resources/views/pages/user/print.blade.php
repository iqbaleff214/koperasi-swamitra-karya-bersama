@extends('layouts.pdf')

@section('header')
<table style="width: 100%">
    <tr>
        <td style="width: 15%" class="font-weight-bold">Dicetak:</td>
        <td style="width: 55%">{{ $user->name . ' (' . $user->username . ')' }}</td>
        <td style="width: 15%" class="font-weight-bold">Tanggal Cetak:</td>
        <td style="width: 15%; text-align: right">{{ $date }}</td>
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
            <th scope="col">No.</th>
            <th scope="col">Nama</th>
            <th scope="col">JK</th>
            <th scope="col">Tgl Lahir</th>
            <th scope="col">Pendidikan</th>
            <th scope="col">Alamat</th>
            <th scope="col">Nomor Telepon</th>
            <th scope="col">Mulai Bekerja</th>
            <th scope="col">Divisi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $item->name }}</td>
                <td>{{ $item->gender }}</td>
                <td>{{ \Carbon\Carbon::parse($item->birth)->isoFormat('DD-MM-Y') }}</td>
                <td>{{ $item->last_education }}</td>
                <td>{{ $item->address }}</td>
                <td>{{ $item->phone }}</td>
                <td>{{ \Carbon\Carbon::parse($item->joined_at)->isoFormat('DD-MM-Y') }}</td>
                <td>{{ ucfirst($item->role) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
