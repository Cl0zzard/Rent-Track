
  <!-- JQUERY -->
  <script src="plugins/jquery/jquery-3.6.0.min.js"></script>
  <!-- BOOTSTRAP 5 JS -->
  <script type="text/javascript" src="plugins/bootstrap5/bootstrap.min.js"></script>
  <!-- FONT AWESOME OFFLINE -->
  <script src="plugins/fontawesome/all.min.js" crossorigin="anonymous"></script>
  <!-- sweetalert2 -->
  <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- custom js -->
  <script src="assets/js/script.js"></script>
  <!-- <script src="../assets/js/script.js"></script> -->
  <script type="text/javascript">
        $(document).ready(function() {
            $('#showPassword').on('change', function () {
                const passwordField = $('#password');
                if ($(this).is(':checked')) {
                    passwordField.attr('type', 'text');
                } else {
                    passwordField.attr('type', 'password');
                }
            });


            var currentPage = window.location.pathname.split('/').pop(); // Get the current page from the URL
            $('.sidebar a').each(function() {
                if ($(this).attr('href') === currentPage) {
                    $(this).addClass('active-sidebar');
                }
            });


            $('#menu-icon').click(function() {
                $('.sidebar').toggleClass('sidebar-toggle');
                $('.content-div').toggleClass('content-div-toggle');
            });
        });
  </script>