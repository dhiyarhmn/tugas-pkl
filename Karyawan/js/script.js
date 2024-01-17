$(document).ready(function () {
    // Pastikan navbar tertutup saat halaman dimuat
    var isSidebarOpen = sessionStorage.getItem('isSidebarOpen');
  
    if (isSidebarOpen === 'true') {
      $('#sidebar').addClass('active');
      $('#sidebarCollapse').addClass('open');
      $('#content').addClass('shift');
    } else {
      $('#sidebar').addClass('closed');
      $('#sidebarCollapse').removeClass('open');
    }
  
    $('#sidebarCollapse').on('click', function () {
      if ($('#sidebar').hasClass('active')) {
        $('#sidebar').removeClass('active').addClass('closed');
        $(this).removeClass('open');
        sessionStorage.setItem('isSidebarOpen', 'false');
      } else {
        $('#sidebar').removeClass('closed').addClass('active');
        $(this).addClass('open');
        sessionStorage.setItem('isSidebarOpen', 'true');
      }
      $('#content').toggleClass('shift');
    });
  });  
  
  $('.logout-button').on('click', function() {
    sessionStorage.removeItem('isSidebarOpen');
  });
  
function focusInput(element) {
    element.parentElement.classList.add("focused");
}

function blurInput(element) {
    if (element.value === "") {
        element.parentElement.classList.remove("focused");
    }
}
function updateInput(value) {
    document.getElementById('jenisAbsensiInput').value = value;
    // Tutup dropdown setelah pemilihan
    document.querySelector('.popup input[type="checkbox"]').checked = false;
}

// function focusInput(element) {
//     element.parentElement.classList.add("focused");
// }

function blurInput(element) {
    element.parentElement.classList.remove("focused");
}

/* -------------------------------------------------------------------------------------------------------------------------- */ 
// JS BUAT DATETIME PADA FORM PENGAJUAN
/* -------------------------------------------------------------------------------------------------------------------------- */ 
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
document.addEventListener("DOMContentLoaded", function() {
    var dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(function(input) {
        if (input.value === "") {
            input.classList.add("placeholder-shown");
        } else {
            input.classList.remove("placeholder-shown");
        }
    });

    var dateTimeInputs = document.querySelectorAll('input[type="datetime-local"]');
    dateTimeInputs.forEach(function(input) {
        if (input.value === "") {
            input.classList.add("placeholder-shown");
        } else {
            input.classList.remove("placeholder-shown");
        }
    });
});

function focusDateInput() {
    document.getElementById('tanggal_pengajuan').focus();
}

function focusDateTimeInput() {
    document.getElementById('periode_awal').focus();
}

function focusDateTimeInput() {
    document.getElementById('periode_akhir').focus();
}
/* -------------------------------------------------------------------------------------------------------------------------- */
$(document).ready(function() {
    // Fungsi untuk membuka popup ketika tombol detail diklik
    $('.custom-detail-btn-blue').click(function() {
        $('#popupCard').slideDown(); // Menggunakan slideDown untuk animasi dari atas ke bawah
    });

    // Fungsi untuk menutup popup ketika tombol close (x) diklik
    $('.close-btn').click(function() {
        $('#popupCard').slideUp(); // Menggunakan slideUp untuk animasi tutup ke atas
    });
});

  // Initialize DataTable
  $(document).ready(function() {
    $('#dataTable').DataTable();
  });

// Fungsi untuk menampilkan data detail absensi
function showDetailModal(status, jenis, tanggal) {
    // Mengisi konten modal dengan data absensi
    var modalTitle = document.getElementById("detailModalLabel");
    var modalBody = document.querySelector(".modal-body");

    modalTitle.textContent = "Detail Absensi - " + status;
    modalBody.innerHTML = "<p>Jenis Absensi: " + jenis + "</p>" +
                          "<p>Tanggal Pengajuan: " + tanggal + "</p>";

    // Menampilkan modal
    $('#detailModal').modal('show');
  }