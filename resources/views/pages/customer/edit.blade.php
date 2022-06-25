@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('customer.update', $user) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label>Nomor Rekening</label>
                                        <input type="text" class="form-control @error('number') is-invalid @enderror" name="number" value="{{ old('number', $user->number) }}" placeholder="Nomor Rekening">
                                        <span class="error invalid-feedback">{{ $errors->first('number') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama">
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>NIK</label>
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik', $user->nik) }}" placeholder="NIK">
                                        <span class="error invalid-feedback">{{ $errors->first('nik') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Status Nasabah</label>
                                        <select class="form-control @error('status') is-invalid @enderror" name="status">
                                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="blacklist" {{ old('status', $user->status) == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('status') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Daftar</label>
                                        <input type="date" max="{{ date('Y-m-d') }}" class="form-control @error('joined_at') is-invalid @enderror" name="joined_at" value="{{ old('joined_at', date('Y-m-d', strtotime($user->joined_at))) }}" placeholder="Tanggal Daftar">
                                        <span class="error invalid-feedback">{{ $errors->first('joined_at') }}</span>
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
                                        <label>Pekerjaan</label>
                                        <input type="text" class="form-control @error('profession') is-invalid @enderror" name="profession" value="{{ old('profession', $user->profession) }}" placeholder="Pekerjaan">
                                        <span class="error invalid-feedback">{{ $errors->first('profession') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Pendidikan Terakhir</label>
                                        <input type="text" class="form-control @error('last_education') is-invalid @enderror" name="last_education" value="{{ old('last_education', $user->last_education) }}" placeholder="Pendidikan Terakhir">
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
                                    <img src="<?= asset($user->photo ? 'storage/' . $user->photo : 'swamitra.jpeg') ?>" class="img-thumbnail img-preview"
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
