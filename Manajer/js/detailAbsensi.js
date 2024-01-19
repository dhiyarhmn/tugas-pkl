// Fungsi untuk menampilkan detail absensi
function showDetail(absensiId) {
  $.ajax({
    url: "getAbsensiDetail.php", // Pastikan ini adalah path yang benar ke file PHP Anda
    type: "POST",
    dataType: "json",
    data: { id: absensiId },
    success: function (response) {
      if (response.error) {
        alert("Error: " + response.error);
        return;
      }

      // Mengisi modal dengan data dari response
      $("#detailModalLabel").text("Detail Absensi");
      let modalBody = `
        <p><strong>Jenis Absensi:</strong> ${response.NamaJenisAbsensi}</p>
        <p><strong>Tanggal Pengajuan:</strong> ${response.TanggalPengajuan}</p>
        <p><strong>Keterangan:</strong> ${response.Keterangan}</p>
        <p><strong>Status Persetujuan:</strong> ${response.StatusPersetujuan}</p>
        <p><strong>periode cuti :</strong> ${response.WaktuPeriodeAbsensiMulai} sampai ${response.WaktuPeriodeAbsensiSelesai}</p>
        <p><strong>Tahapan Saat Ini:</strong> ${response.TahapanSaatIni}, ${response.RolePersetujuan}</p>
        <p><strong>Nama Alur:</strong> ${response.NamaAlur}</p>
      `;

      // Menampilkan data di modal body
      $(".modal-body").html(modalBody);

      // Menampilkan modal
      $("#detailModal").modal("show");
    },
    error: function (xhr, status, error) {
      alert("Terjadi kesalahan: " + error);
    },
  });
}