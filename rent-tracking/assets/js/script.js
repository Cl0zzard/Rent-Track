//for deleting any data
    $(document).on('click', '.delete_data', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      var tablename = $(this).data('table');
      var id_type = $(this).data('type');

      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.isConfirmed) {
              
              $.ajax({
                  url: '../rent-tracking/controller/delete_data.php',
                  type: 'POST',
                  data: { id: id, tablename: tablename, id_type: id_type },
                  dataType: 'json',
                  success: function(output) {

                      if (output.status === 'success') {
                          // Remove the <tr> from the table
                          var deletedRow = $('.delete_data[data-id="' + id + '"]').closest('tr');
                          deletedRow.remove();

                          Swal.fire({
                              icon: "success",
                              title: "Deleted!",
                              text: "Data deleted successfully.",
                              showConfirmButton: false,
                              timer: 1500, // Time in milliseconds (1.5 seconds)
                              timerProgressBar: true
                          }).then(() => {
                              location.reload(true); // Reload the page after the alert
                          });
                      } else {
                          Swal.fire({
                              icon: "error",
                              title: "Error!",
                              text: "Failed to delete data.",
                              showConfirmButton: false,
                              timer: 1500,
                              timerProgressBar: true
                          });
                      }
                  }
              });
          }
      });
    });



//for archive stall
   $(document).on('click', '.archive_button', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var type = $(this).data('type');

    // Determine the action (Archive or Unarchive)
    var title = (type === 1) ? 'Unarchive this stall?' : 'Archive this stall?';
    var text = (type === 1) 
        ? "This stall will be restored and made available again." 
        : "This stall will be moved to the archive. You can restore it anytime if needed.";
    var confirmButtonText = (type === 2) ? 'Yes, archive it!' : 'Yes, unarchive it!';
    var successMessage = (type === 2) 
        ? 'The stall has been successfully unarchived.' 
        : 'The stall has been successfully archived.';

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "controller/archive-stall.php?stall_slots_id=" + id + "&type=" + type;
        }
    });
});




//for archive staff
   $(document).on('click', '.archive_staff', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var type = $(this).data('type');

    // Determine the action (Archive or Unarchive)
    var title = (type === 1) ? 'Unarchive this staff?' : 'Archive this staff?';
    var text = (type === 1) 
        ? "This staff will be restored and made available again." 
        : "This staff will be moved to the archive. You can restore it anytime if needed.";
    var confirmButtonText = (type ===2) ? 'Yes, archive it!' : 'Yes, unarchive it!';
    var successMessage = (type === 2) 
        ? 'The staff has been successfully unarchived.' 
        : 'The staff has been successfully archived.';

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "controller/archive-staff.php?admin_id=" + id + "&type=" + type;
        }
    });
});




//========stall slots start=========//
$('.add_tenant').click(function (e) {
    e.preventDefault();

    $('#tenant-modal').modal('show');
    $('#tenant-modal form')[0].reset();
    $('.modal-title').text('ADD NEW TENANT INFORMATION');

});


$('.edit_tenant').click(function (e) {
    e.preventDefault();

    $('#tenant-modal').modal('show');
    $('.modal-title').text('EDIT EXISTING TENANT INFORMATION');

    $('#stall_slots_id').val($(this).data('data1'));
    $('#tenantname').val($(this).data('data2'));
    $('#monthly').val($(this).data('data3'));
    $('#email').val($(this).data('data4'));
    $('#phonenumber').val($(this).data('data5'));
    $('#location').val($(this).data('data6'));
    $('#manager_name').val($(this).data('data7'));
});


//========stall slots end=========//



//========stall slots start=========//
$('.add_staff').click(function (e) {
    e.preventDefault();

    $('#staff-modal').modal('show');
    $('#staff-modal form')[0].reset();
    $('.modal-title').text('ADD NEW STAFF INFORMATION');

});


$('.edit_staff').click(function (e) {
    e.preventDefault();

    $('#staff-modal').modal('show');
    $('.modal-title').text('EDIT EXISTING STAFF INFORMATION');

    $('#admin_id').val($(this).data('data1'));
    $('#name').val($(this).data('data2'));
    $('#email').val($(this).data('data3'));
    $('#username').val($(this).data('data4'));
    $('#phonenumber').val($(this).data('data5'));
    $('#address').val($(this).data('data6'));
});


//========stall slots end=========//




//========create transaction start=========//
$('.create_transaction').click(function (e) {
    e.preventDefault();

    $('#transaction-modal').modal('show');
    $('#transaction-modal form')[0].reset();
    $('.modal-title').text('CREATE NEW TRANSACTION');

});


$('.edit_transaction').click(function (e) {
    e.preventDefault();

    $('#transaction-modal').modal('show');
    $('.modal-title').text('EDIT EXISTING TRANSACTION');

    $('.transaction_history_id').val($(this).data('data1'));
    $('#balance').val($(this).data('data2'));
    $('#penalty').val($(this).data('data3'));
    $('#duedate').val($(this).data('data5'));

});


$('.pay_btn').click(function (e) {
    e.preventDefault();

    let dateValue = $(this).data('data2'); // Example: "2025-01-25"
    let dateObj = new Date(dateValue);

    let formattedDate = dateObj.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    $('#pay-modal').modal('show');
    $('.modal-title').text('PAID AMOUNT FOR - ' + formattedDate);

    $('.transaction_history_id').val($(this).data('data1'));

});


//========create transaction end=========//





//========OTHER JS HANDLING start=========//
$(document).ready(function() {

        //=======TABLE SEARCHBAR
        $("#search-bar").on("keyup", function() {
              var value = $(this).val().toLowerCase();
              $("table tbody tr").filter(function() {
                  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
              });
        });

        
        //=======TOOL TIPS FOR BOOTRAP IF HAVE
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

});

$(document).ready(function() { 
    var currentPage = window.location.pathname.split('/').pop(); // Get the current page from the URL

    $('.sidebar a').each(function() {
        var linkPage = $(this).attr('href').split('/').pop();
        if (linkPage === currentPage) {
            $(this).closest('li').addClass('active-sidebar'); // Add class to the closest <li> of the active link
        }
    });
});

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))



//========OTHER JS HANDLING end=========//