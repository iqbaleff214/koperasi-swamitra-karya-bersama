@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-8">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control-plaintext @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $user->name) }}" disabled placeholder="Nama">
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control-plaintext @error('username') is-invalid @enderror"
                                        name="username" value="{{ old('username', $user->username) }}"
                                        placeholder="Username" disabled readonly>
                                </div>
                                <div class="form-group">
                                    <label>Divisi</label>
                                    <input type="text" class="form-control-plaintext" value="{{ strtoupper($user->role) }}"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label>Bergabung Sejak</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ Carbon\Carbon::parse($user->joined_at)->isoFormat('D MMMM Y') }}"
                                        disabled readonly>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <input type="text" class="form-control-plaintext" value="{{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="text" class="form-control-plaintext"
                                        value="{{ Carbon\Carbon::parse($user->birth)->isoFormat('D MMMM Y') }}"
                                        disabled readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nomor Telepon</label>
                                    <input type="text" class="form-control-plaintext @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone', $user->phone) }}"
                                        placeholder="Nomor Telepon" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" class="form-control-plaintext @error('address') is-invalid @enderror"
                                        name="address" disabled value="{{ old('address', $user->address) }}" placeholder="Alamat">
                                </div>
                                <div class="form-group">
                                    <label>Pendidikan Terakhir</label>
                                    <input type="text" class="form-control-plaintext @error('last_education') is-invalid @enderror"
                                        name="last_education" disabled value="{{ old('last_education', $user->last_education) }}"
                                        placeholder="Pendidikan Terakhir">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ipsum, at?</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <!-- /.content -->
@endsection
