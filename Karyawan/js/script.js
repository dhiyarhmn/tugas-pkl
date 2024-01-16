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
