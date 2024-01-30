// Fungsi untuk menampilkan detail absensi
function getFullAbsensiTypeName(shortCode) {
  const mapping = {
    A: "Absent (Absen)",
    AL: "Annual Leave (Cuti Tahunan)",
    BT: "Business Trip (Dinas)",
    DL: "Doctor Letter (Sakit Dengan Surat Dokter)",
    L: "Late (Terlambat)",
    LP: "Legal Permit (Izin Resmi)",
    P: "Permit (Izin)",
    S: "Suspend (Skors)",
    SBA: "Sick By Accident (Sakit Akibat Kecelakaan Kerja)",
  };

  return mapping[shortCode] || shortCode; // Kembalikan deskripsi atau kode asli
}

// Fungsi untuk menampilkan detail absensi
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

      // Mengubah kode jenis absensi menjadi deskripsi lengkap
      const fullAbsensiTypeName = getFullAbsensiTypeName(response.NamaJenisAbsensi);

      // Mengisi modal dengan data dari response
      $("#detailModalLabel").text("Detail Absensi");
      let modalBody = `
        <p><strong>Departemen:</strong> ${response.Departemen}</p>
        <p><strong>Jabatan:</strong> ${response.Jabatan}</p>
        <p><strong>Tanggal Pengajuan:</strong> ${response.TanggalPengajuan}</p>
        <p><strong>Jenis Absensi:</strong> ${fullAbsensiTypeName}</p>
        <p><strong>Status Persetujuan:</strong> ${response.StatusPersetujuan}</p>
        <p><strong>Periode Absensi:</strong> ${response.WaktuPeriodeAbsensiMulai} sampai ${response.WaktuPeriodeAbsensiSelesai}</p>
        <p><strong>Keterangan:</strong> ${response.Keterangan}</p>
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
