// -------------------------------------------------------------------
// JS BUAT SIDEBAR
// -------------------------------------------------------------------
$(document).ready(function () {
  // Pastikan navbar tertutup saat halaman dimuat
  var isSidebarOpen = sessionStorage.getItem("isSidebarOpen");

  if (isSidebarOpen === "true") {
    $("#sidebar").addClass("active");
    $("#sidebarCollapse").addClass("open");
    $("#content").addClass("shift");
  } else {
    $("#sidebar").addClass("closed");
    $("#sidebarCollapse").removeClass("open");
  }

  $("#sidebarCollapse").on("click", function () {
    if ($("#sidebar").hasClass("active")) {
      $("#sidebar").removeClass("active").addClass("closed");
      $(this).removeClass("open");
      sessionStorage.setItem("isSidebarOpen", "false");
    } else {
      $("#sidebar").removeClass("closed").addClass("active");
      $(this).addClass("open");
      sessionStorage.setItem("isSidebarOpen", "true");
    }
    $("#content").toggleClass("shift");
  });
});

$(".logout-button").on("click", function () {
  sessionStorage.removeItem("isSidebarOpen");
});

// -------------------------------------------------------------------
// JS BUAT ...
// -------------------------------------------------------------------
function focusInput(element) {
  element.parentElement.classList.add("focused");
  element.style.backgroundColor = "#fff"; // Example style
}

function blurInput(element) {
  if (element.value === "") {
    element.parentElement.classList.remove("focused");
    element.style.backgroundColor = ""; // Reset to default
  }
}
function updateInput(value) {
  document.getElementById("jenisAbsensiInput").value = value;
  // Tutup dropdown setelah pemilihan
  document.querySelector('.popup input[type="checkbox"]').checked = false;
}

// function focusInput(element) {
//     element.parentElement.classList.add("focused");
// }

function blurInput(element) {
  element.parentElement.classList.remove("focused");
}

// -------------------------------------------------------------------
// JS BUAT DATETIME PADA FORM PENGAJUAN
// -------------------------------------------------------------------
function focusInput(inputElement) {
  if ((inputElement.type === "date" || inputElement.type === "datetime-local") && inputElement.value === "") {
    inputElement.classList.add("placeholder-shown");
  }
}

function blurInput(inputElement) {
  if (inputElement.type === "date" || inputElement.type === "datetime-local") {
    inputElement.classList.remove("placeholder-shown");
  }
}

// Inisialisasi saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
  var dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach(function (input) {
    if (input.value === "") {
      input.classList.add("placeholder-shown");
    } else {
      input.classList.remove("placeholder-shown");
    }
  });

  var dateTimeInputs = document.querySelectorAll('input[type="datetime-local"]');
  dateTimeInputs.forEach(function (input) {
    if (input.value === "") {
      input.classList.add("placeholder-shown");
    } else {
      input.classList.remove("placeholder-shown");
    }
  });
});

function focusDateInput() {
  document.getElementById("tanggal_pengajuan").focus();
}

function focusDateTimeInput() {
  document.getElementById("periode_awal").focus();
}

function focusDateTimeInput() {
  document.getElementById("periode_akhir").focus();
}

// -------------------------------------------------------------------
// JS BUAT BUTTON DETAIL PADA STATUS PENGAJUAN
// -------------------------------------------------------------------
$(document).ready(function () {
  // Fungsi untuk membuka popup ketika tombol detail diklik
  $(".custom-detail-btn-blue").click(function () {
    $("#popupCard").slideDown(); // Menggunakan slideDown untuk animasi dari atas ke bawah
  });

  // Fungsi untuk menutup popup ketika tombol close (x) diklik
  $(".close-btn").click(function () {
    $("#popupCard").slideUp(); // Menggunakan slideUp untuk animasi tutup ke atas
  });
});

// Initialize DataTable
$(document).ready(function () {
  $("#dataTable").DataTable();
});

// Fungsi untuk menampilkan data detail absensi
function showDetailModal(status, jenis, tanggal) {
  // Mengisi konten modal dengan data absensi
  var modalTitle = document.getElementById("detailModalLabel");
  var modalBody = document.querySelector(".modal-body");

  modalTitle.textContent = "Detail Absensi - " + status;
  modalBody.innerHTML = "<p>Jenis Absensi: " + jenis + "</p>" + "<p>Tanggal Pengajuan: " + tanggal + "</p>";

  // Menampilkan modal
  $("#detailModal").modal("show");
}

// -------------------------------------------------------------------
// JS BUAT KOTAK INPUT
// -------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".input-container .input").forEach(function (input) {
    if (input.value !== "") {
      input.nextElementSibling.classList.add("active-label");
    }
  });
});
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".input-container .input[readonly]").forEach(function (input) {
    input.nextElementSibling.classList.add("static-label");
  });
});

// -------------------------------------------------------------------
// JS BUAT CHOOSE FILE
// -------------------------------------------------------------------
document.getElementById('jenis_absensi').addEventListener('change', function() {
  var selectedValue = this.value;
  var fileContainer = document.getElementById('fileContainer');
  var fileInput = document.getElementById('picture');

  if (['BT', 'DL', 'SBA', 'LP'].includes(selectedValue)) {
      fileContainer.style.display = 'block';
      fileInput.required = true;
  } else {
      fileContainer.style.display = 'none';
      fileInput.required = false;
  }
});

// JS BUAT DATETIME TANGGAL PENGAJUAN
document.addEventListener('DOMContentLoaded', function() {
  var now = new Date();
  var month = ('0' + (now.getMonth() + 1)).slice(-2); // Bulan dimulai dari 0
  var day = ('0' + now.getDate()).slice(-2);
  var hours = ('0' + now.getHours()).slice(-2);
  var minutes = ('0' + now.getMinutes()).slice(-2);
  var formattedNow = now.getFullYear() + '-' + month + '-' + day + 'T' + hours + ':' + minutes;

  document.getElementById('tanggal_pengajuan').value = formattedNow;
});

// -------------------------------------------------------------------
// JS FOTO PROFILE DI EDIT PROFILE
// -------------------------------------------------------------------
// Menambahkan event listener ke tombol "Upload new image"
// Menambahkan event listener untuk mengganti gambar profil saat gambar diunggah
// Menambahkan event listener untuk mengganti gambar profil saat gambar diunggah
document.getElementById("imageUpload").addEventListener("change", function() {
  // Mendapatkan file yang diunggah
  var file = this.files[0];
  if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
          // Mengubah sumber gambar profil dengan data URL dari file yang diunggah
          document.getElementById("profileImage").src = e.target.result;
      };
      reader.readAsDataURL(file);
  }
});

// Menambahkan event listener untuk form submit
document.querySelector("form").addEventListener("submit", function() {
  // Menjalankan fungsi ini saat form disubmit
  // Cek apakah ada file yang diunggah
  var fileInput = document.getElementById("imageUpload");
  if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
      // Tidak ada file yang diunggah, jangan lanjutkan submit
      alert("Anda harus mengunggah foto profil terlebih dahulu.");
      event.preventDefault(); // Menghentikan submit form
  }
});

// JS FOTO PROFILE DI EDIT PROFILE
// Fungsi untuk menampilkan gambar yang diunggah oleh pengguna
    function displayImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#uploadedImage').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Event listener untuk input file
    $('#imageUpload').on('change', function() {
        displayImage(this);
    });