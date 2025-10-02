<table class="table table-bordered" id="tabel-bahan-baku">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Kadaluarsa</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data akan diisi via AJAX atau server-side rendering -->
    </tbody>
</table>

<!-- Form tambah bahan baku -->
<form id="form-tambah-bahan-baku" method="POST" action="{{ route('admin.bahan_baku.store') }}">
    @csrf
    <div class="row">
        <div class="col-md-4 mb-2">
            <input type="text" name="nama" class="form-control" placeholder="Nama Bahan" required maxlength="120">
        </div>
        <div class="col-md-3 mb-2">
            <input type="text" name="kategori" class="form-control" placeholder="Kategori" required maxlength="60">
        </div>
        <div class="col-md-2 mb-2">
            <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required min="0">
        </div>
        <div class="col-md-2 mb-2">
            <input type="text" name="satuan" class="form-control" placeholder="Satuan" required maxlength="20">
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" name="tanggal_masuk" class="form-control" required>
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" name="tanggal_kadaluarsa" class="form-control" required>
        </div>
        <div class="col-md-3 mb-2">
            <select name="status" class="form-control" required>
                <option value="tersedia">Tersedia</option>
                <option value="segera_kadaluarsa">Segera Kadaluarsa</option>
                <option value="kadaluarsa">Kadaluarsa</option>
                <option value="habis">Habis</option>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
    </div>
</form>

<script>
// Submit form via AJAX agar hanya aksi tambah yang berjalan
$('#form-tambah-bahan-baku').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(res) {
            if(res.success) {
                // Tambahkan baris baru ke tabel
                var row = '<tr>' +
                    '<td>' + (res.data.id || '-') + '</td>' +
                    '<td>' + res.data.nama + '</td>' +
                    '<td>' + res.data.kategori + '</td>' +
                    '<td>' + res.data.jumlah + '</td>' +
                    '<td>' + res.data.satuan + '</td>' +
                    '<td>' + res.data.tanggal_masuk + '</td>' +
                    '<td>' + res.data.tanggal_kadaluarsa + '</td>' +
                    '<td>' + res.data.status + '</td>' +
                    '<td>' + (res.data.created_at || '-') + '</td>' +
                    '<td>-</td>' +
                '</tr>';
                $('#tabel-bahan-baku tbody').prepend(row);
                form[0].reset();
            } else {
                alert('Gagal menambah data!');
            }
        },
        error: function(xhr) {
            alert('Terjadi error: ' + xhr.responseText);
        }
    });
});
</script>
