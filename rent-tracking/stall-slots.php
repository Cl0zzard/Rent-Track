<?php
session_start();

if (!isset($_SESSION['admin']['admin_id'])) {
  header('Location: login');
  exit;
}
include 'connect.php';
?>
<!DOCTYPE html>
<html>

<style type="text/css">
  .loader {
    position: relative;
    border-style: solid;
    box-sizing: border-box;
    border-width: 40px 60px 30px 60px;
    border-color: #3760C9 #96DDFC #96DDFC #36BBF7;
    animation: envFloating 1s ease-in infinite alternate;
  }

  .loader:after {
    content: "";
    position: absolute;
    right: 62px;
    top: -40px;
    height: 70px;
    width: 50px;
    background-image: linear-gradient(#fff 45px, transparent 0),
      linear-gradient(#fff 45px, transparent 0),
      linear-gradient(#fff 45px, transparent 0);
    background-repeat: no-repeat;
    background-size: 30px 4px;
    background-position: 0px 11px, 8px 35px, 0px 60px;
    animation: envDropping 0.75s linear infinite;
  }

  @keyframes envFloating {
    0% {
      transform: translate(-2px, -5px)
    }

    100% {
      transform: translate(0, 5px)
    }
  }

  @keyframes envDropping {
    0% {
      background-position: 100px 11px, 115px 35px, 105px 60px;
      opacity: 1;
    }

    50% {
      background-position: 0px 11px, 20px 35px, 5px 60px;
    }

    60% {
      background-position: -30px 11px, 0px 35px, -10px 60px;
    }

    75%,
    100% {
      background-position: -30px 11px, -30px 35px, -30px 60px;
      opacity: 0;
    }
  }
</style>


<!-- header link -->
<?php include "plugins-header.php"; ?>

<body>
  <div class="main-div">
    <!-- sidebar start -->
    <?php include "sidebar.php"; ?>
    <!-- sidebar end -->

    <!-- content container start -->
    <div class="content-div">

      <!-- topbar start -->
      <?php include "topbar.php"; ?>
      <!-- topbar end -->

      <div class="row row-gap-2 mx-0 mt-4 p-3">
        <div class="col-12 mx-auto border border-red d-flex flex-column row-gap-2">
          <div class="d-flex flex-column row-gap-2 p-3">
            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
              <h5>Stall Slots</h5>
              <div class="d-flex align-items-center gap-2">
                <a type="button" class="notification-btn btn btn-sm btn-warning rounded-1 py-2">
                  <i class="fas fa-bell me-2"></i><span>Notify Due Date</span>
                </a>
                <a type="button" class="add_tenant btn btn-sm btn-primary rounded-1 py-2">
                  <i class="fa-solid fa-circle-plus me-2"></i><span>Add Tenant</span>
                </a>
              </div>
            </div>
            <div>
              <div class="mt-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <label for="categories">Show: </label>
                  <select required name="categories" id="table_show" class="form-select form-select-sm rounded-1 py-2">
                    <option value="" selected hidden>10</option>
                    <option value="all">All</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
                <div>
                  <input type="text" id="search-bar" class="form-control form-control-sm rounded-1 py-2"
                    placeholder="Search here">
                </div>

              </div>
            </div>

            <div style="height: auto;">
              <table id="product_table" class="table table-bordered table-striped">
                <thead class="position-sticky top-0">
                  <tr class="py-5">
                    <th>#</th>
                    <th>Stall Name</th>
                    <th>Manager</th>
                    <th>Email</th>
                    <th>Phone No.</th>
                    <th>Monthly</th>
                    <th>Location</th>
                    <th>Date Added</th>
                    <th>File</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $index = 1;
                  $sql = "SELECT *
                                  FROM stall_slots
                                  WHERE status = 1";
                  $stmt = $conn->prepare($sql);
                  $stmt->execute();
                  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  if ($rows) {
                    foreach ($rows as $row):

                      foreach ($row as $key => $value):
                        $$key = $value;
                      endforeach;
                      switch ($location) {
                        case '1':
                          $location_txt = 'USA BED Campus';
                          break;
                        case '2':
                          $location_txt = 'USA Main Campus';
                          break;
                        case '3':
                          $location_txt = 'USA Main Kiosks';
                          break;
                      }
                      $formatdate = date("F j, Y", strtotime($date_added));
                      ?>
                      <tr data-id="2">
                        <td data-label="#" width="50"><?= $index++; ?></td>
                        <td data-label="Stall Name"><?= $tenantname; ?></td>
                        <td data-label="Manager"><?= $manager_name; ?></td>
                        <td data-label="Email"><?= $email; ?></td>
                        <td data-label="Phone No.">
                          <?= $phonenumber; ?>
                        </td>
                        <td data-label="Monthly">
                          <?= number_format($monthly, 2); ?>
                        </td>
                        <td data-label="Location"><?= $location_txt; ?></td>
                        <td data-label="Date Added"><?= $formatdate; ?></td>
                        <td data-label="File">
                          <?php
                          $sql2 = "SELECT *
                                        FROM stall_slots_file
                                        WHERE stall_slots_id = :stall_slots_id";
                          $stmt2 = $conn->prepare($sql2);
                          $stmt2->bindParam(":stall_slots_id", $stall_slots_id);
                          $stmt2->execute();
                          $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                          if ($rows2) {
                            foreach ($rows2 as $row2):
                              ?>
                              <div text-wrap class="mb-2 d-flex align-items-start column-gap-2">
                                <a class="text-wrap" target="_blank"
                                  href="upload/<?= $row2['stall_file']; ?>"><?= $row2['stall_file']; ?></a>
                                <small class="delete_data" role="button" data-id="<?= $row2['stall_slots_file_id']; ?>"
                                  data-table="stall_slots_file" data-type="stall_slots_file_id">
                                  <i class="fa-solid fa-xmark text-danger "></i></span>
                                </small>
                              </div>
                              <?php
                            endforeach;
                          } else {
                            echo "No File";
                          }
                          ?>
                        </td>
                        <td data-label="Action" width="160">
                          <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2">

                            <small data-id="<?= $stall_slots_id; ?>" role="button"
                              class="upload_file py-1 px-2 rounded-1 text-bg-secondary text-decoration-none d-flex align-items-center">
                              <i class="fa-solid fa-upload me-2"></i><span>Upload</span>
                            </small>
                            <small>
                              <a type="button" target="_blank" class="edit_category py-1 px-2 rounded-1 text-bg-warning text-decoration-none d-flex align-items-center 
    <?php echo ($confirmed == 0) ? 'disabled opacity-50' : ''; ?>"
                                href="transaction?stall_slots_id=<?= $stall_slots_id; ?>" <?php echo ($confirmed == 0) ? 'style="pointer-events: none;"' : ''; ?>>
                                <i class="fa-solid fa-wallet me-2"></i><span>Transactions</span>
                              </a>

                            </small>
                            <small>
                              <a type="button"
                                class="edit_tenant py-1 px-2 rounded-1 text-bg-primary text-decoration-none d-flex align-items-center"
                                data-data1="<?= $stall_slots_id; ?>" data-data2="<?= $tenantname; ?>"
                                data-data3="<?= $monthly; ?>" data-data4="<?= $email; ?>" data-data5="<?= $phonenumber; ?>"
                                data-data6="<?= $location; ?>" data-data7="<?= $manager_name; ?>">
                                <i class="fa-solid fa-pen-to-square me-2 "></i><span class="">Edit</span>
                              </a>
                            </small>
                            <small class="archive_button py-1 px-2 rounded-1 text-bg-danger d-flex align-items-center"
                              role="button" data-id="<?= $stall_slots_id; ?>" data-type="2">
                              <i class="fa-solid fa-box-archive me-2 "></i><span class="">Archive</span>
                            </small>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach;
                  } else { ?>
                    <tr class="remove-row">
                      <td colspan="100" class="text-center">No Data Added</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>


    </div>
    <!-- content container end-->
  </div>
  <?php include 'modal/add-tenant.php' ?>
  <?php include "plugins-footer.php"; ?>
  <script type="text/javascript">
    $(document).ready(function () {

      $('.upload_file').click(function (e) {
        $('#upload-modal').modal('show');

        $('#stall_slots_id2').val($(this).data('id'));

      });
      $('.notification-btn').click(function (e) {
        e.preventDefault();

        $('#loader-div').removeClass('d-none');

        $.ajax({
          url: "controller/notify_due_date.php",
          method: "POST",
          dataType: "json",
          success: function (response) {
            $('#loader-div').addClass('d-none');

            if (response.status === 'no_overdue') {
              Swal.fire({
                title: "No Overdue Stalls",
                text: "There are no overdue payments at this time.",
                icon: "info"
              });
              return;
            }

            let successCount = 0;
            let failCount = 0;

            response.emails_sent.forEach(item => {
              if (item.status === 'success') {
                successCount++;
              } else {
                failCount++;
              }
            });

            let message = `Successfully sent ${successCount} email(s).`;
            if (failCount > 0) message += ` Failed to send ${failCount} email(s).`;

            Swal.fire({
              title: "Notify Tenant!",
              text: message,
              icon: failCount > 0 ? "warning" : "success"
            });
          },
          error: function () {
            $('#loader-div').addClass('d-none');
            Swal.fire({
              title: "Error",
              text: "Something went wrong. Please try again.",
              icon: "error"
            });
          }
        });
      });

    });


  </script>
</body>

</html>