$(document).ready(function () {
  // Pastikan navbar tertutup saat halaman dimuat
  $('#sidebar').addClass('closed');
  $('#sidebarCollapse').removeClass('open');

  $('#sidebarCollapse').on('click', function () {
      if ($('#sidebar').hasClass('active')) {
          $('#sidebar').removeClass('active').addClass('closed');
          $(this).removeClass('open');
      } else {
          $('#sidebar').removeClass('closed').addClass('active');
          $(this).addClass('open');
      }
      $('#content').toggleClass('shift');
  });
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

function focusInput(element) {
    element.parentElement.classList.add("focused");
}

function blurInput(element) {
    element.parentElement.classList.remove("focused");
}

// js buat tanggal
function focusInput(inputElement) {
    if (inputElement.type === "date" && inputElement.value === "") {
        inputElement.classList.add("placeholder-shown");
    }
}

function blurInput(inputElement) {
    if (inputElement.type === "date") {
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
});

function focusDateInput() {
    document.getElementById('tanggal_pengajuan').focus();
}
