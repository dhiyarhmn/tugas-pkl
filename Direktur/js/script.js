// -------------------------------------------------------------------
// SIDEBAR
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
// KOTAK KOTAK 
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

function blurInput(element) {
  element.parentElement.classList.remove("focused");
}

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
// DATETIME PADA FORM PENGAJUAN
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
// BUTTON DETAIL PADA STATUS PENGAJUAN DAN APPROVAL
// -------------------------------------------------------------------
$(document).ready(function () {
  // membuka popup ketika tombol detail diklik
  $(".custom-detail-btn-blue").click(function () {
    $("#popupCard").slideDown(); 
  });

  // untuk menutup popup ketika tombol close diklik
  $(".close-btn").click(function () {
    $("#popupCard").slideUp();
  });
});

// Initialize DataTable
$(document).ready(function () {
  $("#dataTable").DataTable();
});

// menampilkan data detail pengajuan absensi
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
// BUTTON APPROVE
// -------------------------------------------------------------------
$(document).ready(function () {
  $(".custom-approval-btn-green").click(function () {
    var approveModalBody = document.querySelector("#approveModal .modal-body");
    approveModalBody.textContent = "Pengajuan Absensi Berhasil Di Approve";
    $("#approveModal").modal("show");

    // Menyimpan referensi ke tombol
    var button = $(this);

    // Menghilangkan efek hover dan focus 
    button.removeClass("custom-approval-btn-green");

    // Menunggu efek modal selesai
    setTimeout(function () {
      // Mengembalikan class dan warna asli
      button.addClass("custom-approval-btn-green");
    }, 100); 
  });

  $("#dataTable").DataTable();
});

// -------------------------------------------------------------------
// BUTTON DECLINE
// -------------------------------------------------------------------
$(document).ready(function () {
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
// CHOOSE FILE
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

// -------------------------------------------------------------------
// DATETIME TANGGAL PENGAJUAN
// -------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
  var now = new Date();
  var month = ('0' + (now.getMonth() + 1)).slice(-2); 
  var day = ('0' + now.getDate()).slice(-2);
  var hours = ('0' + now.getHours()).slice(-2);
  var minutes = ('0' + now.getMinutes()).slice(-2);
  var formattedNow = now.getFullYear() + '-' + month + '-' + day + 'T' + hours + ':' + minutes;

  document.getElementById('tanggal_pengajuan').value = formattedNow;
});

// ----------------------------------------------------------------
// Cuti Tahunan
// ----------------------------------------------------------------
document.getElementById('jenis_absensi').addEventListener('change', function() {
  var jenisAbsensi = this.value;
  var sisaCutiContainer = document.getElementById('sisaCutiContainer');
  if (jenisAbsensi === 'AL') { 
      sisaCutiContainer.style.display = 'block';
  } else {
      sisaCutiContainer.style.display = 'none';
  }
});