@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('user.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Nama">
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Username">
                                        <span class="error invalid-feedback">{{ $errors->first('username') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" placeholder="Password">
                                        <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Divisi</label>
                                        <select class="form-control @error('role') is-invalid @enderror" name="role">
                                            <option value="teller" {{ old('role') == 'teller' ? 'selected' : '' }}>Teller</option>
                                            <option value="collector" {{ old('role') == 'collector' ? 'selected' : '' }}>Collector</option>
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('role') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Bergabung Sejak</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="form-control @error('joined_at') is-invalid @enderror" name="joined_at" value="{{ old('joined_at') }}" placeholder="Bergabung Sejak">
                                        <span class="error invalid-feedback">{{ $errors->first('joined_at') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-control @error('gender') is-invalid @enderror" name="gender">
                                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('gender') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" class="form-control @error('birth') is-invalid @enderror" name="birth" value="{{ old('birth') }}" placeholder="Tanggal Lahir">
                                        <span class="error invalid-feedback">{{ $errors->first('birth') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Nomor Telepon">
                                        <span class="error invalid-feedback">{{ $errors->first('phone') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" placeholder="Alamat">
                                        <span class="error invalid-feedback">{{ $errors->first('address') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Pendidikan Terakhir</label>
                                        <input type="text" class="form-control @error('last_education') is-invalid @enderror" name="last_education" value="{{ old('last_education') }}" placeholder="Pendidikan Terakhir">
                                        <span class="error invalid-feedback">{{ $errors->first('last_education') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>Foto</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" accept="image/*" name="photo" id="image">
                                                <label class="custom-file-label" for="image">Pilih Gambar</label>
                                            </div>
                                        </div>
                                        @error('photo')
                                        <span class="text-danger text-sm">{{ $errors->first('photo') }}</span>
                                        @enderror
                                        <div class="form-text font-weight-lighter text-sm">
                                            Maksimal: 2048KB
                                        </div>
                                    </div>
                                    <img src="<?= asset('swamitra.jpeg') ?>" class="img-thumbnail img-preview"
                                         style="width: 100%;" alt="Foto">
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
        $(function () {
            $('#image').on('change', function () {
                previewImage();
            });
        });

        function previewImage() {
            const cover = document.querySelector('.custom-file-input');
            const coverLabel = document.querySelector('.custom-file-label');
            const imgPreview = document.querySelector('.img-preview');
            coverLabel.textContent = cover.files[0].name;
            const coverFile = new FileReader();
            coverFile.readAsDataURL(cover.files[0]);
            coverFile.onload = function (e) {
                imgPreview.src = e.target.result;
            }
        }
    </script>
@endpush
