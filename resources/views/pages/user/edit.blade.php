@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('user.update', $user) }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama">
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->username) }}" placeholder="Username" readonly>
                                        <span class="error invalid-feedback">{{ $errors->first('username') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Divisi</label>
                                        <input type="text" class="form-control" value="{{ strtoupper($user->role) }}" disabled readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Bergabung Sejak</label>
                                        <input type="text" class="form-control" value="{{ Carbon\Carbon::parse($user->joined_at)->isoFormat('D MMMM Y') }}" disabled readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-control @error('gender') is-invalid @enderror" name="gender">
                                            <option value="L" {{ old('gender', $user->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('gender', $user->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('gender') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" class="form-control @error('birth') is-invalid @enderror" name="birth" value="{{ old('birth', $user->birth) }}" placeholder="Tanggal Lahir">
                                        <span class="error invalid-feedback">{{ $errors->first('birth') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nomor Telepon">
                                        <span class="error invalid-feedback">{{ $errors->first('phone') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', $user->address) }}" placeholder="Alamat">
                                        <span class="error invalid-feedback">{{ $errors->first('address') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Pendidikan Terakhir</label>
                                        <input type="text" class="form-control @error('last_education') is-invalid @enderror" name="last_education" value="{{ old('last_education', $user->last_education) }}" placeholder="Pendidikan Terakhir">
                                        <span class="error invalid-feedback">{{ $errors->first('last_education') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ipsum, at?</p>
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
