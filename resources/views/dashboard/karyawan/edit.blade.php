<form action="/karyawan/{{ $karyawan->id_karyawan }}/update" method="POST" id="frmkaryawan" enctype="multipart/form-data">
@csrf
    <div class="row g-2">
        <div class="col mb-0">
            <label for="id_karyawan" class="form-label">Id Karyawan</label>
            <input type="text" id="id_karyawan" name="id_karyawan" readonly value="{{ $karyawan->id_karyawan }}" class="form-control" placeholder="Id Karyawan"/>
        </div>
        <div class="col mb-0">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" id="nama" name="nama" value="{{ $karyawan->nama }}" class="form-control" placeholder="nama"/>
        </div>
        <div class="row g-2 mt-3">
        <div class="col mb-0">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" id="jabatan" name="jabatan" value="{{ $karyawan->jabatan }}" class="form-control" placeholder="Jabatan"/>
        </div>
        <div class="col mb-0">
            <label for="no_hp" class="form-label">No Hp</label>
            <input type="text" id="no_hp" name="no_hp" value="{{ $karyawan->no_hp }}" class="form-control" placeholder="No HP"/>
        </div>
    </div>
    <div class="modal-footer mt-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
