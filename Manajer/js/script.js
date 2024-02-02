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
}

function blurInput(element) {
  if (element.value === "") {
    element.parentElement.classList.remove("focused");
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
// JS BUTTON DETAIL PADA STATUS PENGAJUAN DAN APPROVAL
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

// Fungsi untuk menampilkan data detail pengajuan absensi
function showDetailModal(status, jenis, tanggal) {
  // Mengisi konten modal dengan data absensi
  var modalTitle = document.getElementById("detailModalLabel");
  var modalBody = document.querySelector(".modal-body");

  modalTitle.textContent = "Detail Pengajuan Absensi - " + status;
  modalBody.innerHTML = "<p>Jenis Absensi: " + jenis + "</p>" + "<p>Tanggal Pengajuan: " + tanggal + "</p>";

  // Menampilkan modal
  $("#detailModal").modal("show");
}
// -------------------------------------------------------------------
// JS BUTTON APPROVE
// -------------------------------------------------------------------
$(document).ready(function () {
  $(".custom-approval-btn-green").click(function () {
    var approveModalBody = document.querySelector("#approveModal .modal-body");
    approveModalBody.textContent = "Pengajuan Absensi Berhasil Di Approve";
    $("#approveModal").modal("show");

    // Menyimpan referensi ke tombol
    var button = $(this);

    // Menghilangkan efek hover dan focus sejenak
    button.removeClass("custom-approval-btn-green");

    // Menunggu efek modal selesai
    setTimeout(function () {
      // Mengembalikan class dan warna asli
      button.addClass("custom-approval-btn-green");
    }, 100); // Anda bisa menyesuaikan waktu tunggu sesuai kebutuhan
  });

  $("#dataTable").DataTable();
});

// -------------------------------------------------------------------
// JS BUTTON DECLINE
// -------------------------------------------------------------------
$(document).ready(function () {
  // Corrected the class for the Approve button
  $(".custom-decline-btn-red").click(function () {
    // Menampilkan pesan sukses pada modal
    var declineModalBody = document.querySelector("#declineModal .modal-body");
    declineModalBody.textContent = "Pengajuan Absensi Berhasil Di Decline";

    // Menampilkan modal
    $("#declineModal").modal("show");
  });

  // Initialize DataTable
  $("#dataTable").DataTable();
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

// ----------------------------------------------------------------
// JS BUAT Cuti Tahunan
// ----------------------------------------------------------------
document.getElementById('jenis_absensi').addEventListener('change', function() {
  var jenisAbsensi = this.value;
  var sisaCutiContainer = document.getElementById('sisaCutiContainer');
  if (jenisAbsensi === 'AL') { // Sesuaikan dengan kode jenis cuti tahunan Anda
      sisaCutiContainer.style.display = 'block';
  } else {
      sisaCutiContainer.style.display = 'none';
  }
});

// ----------------------------------------------------------------
// JS BUAT mata liat password
// ----------------------------------------------------------------
function change() {
    
  // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
  var x = document.getElementById('pass').type;

  //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
  if (x == 'password') {

      //ubah form input password menjadi text
      document.getElementById('pass').type = 'text';
      
      //ubah icon mata terbuka menjadi tertutup
      document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                      <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                      <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                      <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                      </svg>`;
  }
  else {

      //ubah form input password menjadi text
      document.getElementById('pass').type = 'password';

      //ubah icon mata terbuka menjadi tertutup
      document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                      <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                      <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                      </svg>`;
  }
}