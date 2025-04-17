<?php
session_start();

if (!isset($_SESSION['admin']['admin_id'])) {
  header('Location: login');
  exit;
}

include "connect.php";

$get_stall_slots_id = $_GET['stall_slots_id'] ?? null;
$sql = "SELECT * FROM stall_slots WHERE stall_slots_id = :stall_slots_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":stall_slots_id", $get_stall_slots_id);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);

if ($rows) {
  $tenantname = $rows['tenantname'];
}

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
            <div class="d-flex align-items-center justify-content-between">
              <h5>
                Transaction History
                <br class="d-block d-lg-none">
                of <?= $tenantname ?? ""; ?>
              </h5>
              <div class="d-none d-lg-flex align-items-center gap-2">
                <a target="_blank" href="transaction-print.php?stall_slots_id=<?= $get_stall_slots_id ?>"
                  class=" btn btn-sm btn-secondary rounded-1 py-2">
                  <i class="fa-solid fa-print me-2"></i><span>Prints</span>
                </a>
                <a type="button" class="create_transaction btn btn-sm btn-primary rounded-1 py-2">
                  <i class="fa-solid fa-circle-plus me-2"></i><span>Create Transaction</span>
                </a>
              </div>
              <div class="d-flex d-lg-none align-items-center gap-2">
                <a type="button" data-bs-toggle="modal" href="#transaction-modal"
                  class="btn btn-sm btn-primary rounded-1 py-2">
                  <i class="fal fa-plus-circle fw-bold fs-5"></i>
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
                    <th>Location</th>
                    <th>Balance</th>
                    <th>Amount Paid</th>
                    <th>Penalty</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                  <?php

                  $index = 1;
                  $sql = "SELECT *, th.status as th_status
                                  FROM transaction_history th
                                  INNER JOIN stall_slots ss 
                                  ON ss.stall_slots_id = th.stall_slots_id
                                  WHERE th.stall_slots_id = :stall_slots_id";
                  $stmt = $conn->prepare($sql);
                  $stmt->bindParam(":stall_slots_id", $get_stall_slots_id);
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
                          $location_txt = 'USA Main Campus Permanent';
                          break;
                        case '3':
                          $location_txt = 'USA Main Kiosks';
                          break;
                      }

                      if ($th_status === 3) {
                        $status_txt = 'Overdue';
                        $badge_bg = 'text-bg-danger';
                      } else if ($th_status === 1 && $balance <= 0) {
                        $status_txt = 'Complete';
                        $badge_bg = 'text-bg-success';
                      } else if ($th_status === 2 || $balance >= 1) {
                        $status_txt = 'Incomplete';
                        $badge_bg = 'text-bg-warning';
                      }

                      $formatdate = date("F j, Y", strtotime($duedate));
                      ?>

                      <tr data-id="2">
                        <td data-label="#" width="50"><?= $index++; ?></td>
                        <td data-label="Location">
                          <?= $location_txt ?>
                        </td>
                        <td data-label="Balance">
                          ₱
                          <?= $balance != null ? number_format($balance, 2) : '0.00'; ?>
                        </td>
                        <td data-label="Amount Paid">
                          ₱
                          <?= $amount_paid != null ? number_format($amount_paid, 2) : '0.00'; ?>
                        </td>
                        <td data-label="Penalty">
                          ₱
                          <?= $penalty != null ? number_format($penalty, 2) : '0.00'; ?>
                        </td>
                        <td data-label="Due Date">
                          <?= $duedate != null ? $formatdate : 'Incomplete'; ?>
                        </td>
                        <td data-label="Status">

                          <span class="badge <?= $status != null ? $badge_bg : 'text-bg-danger'; ?>">
                            <?= $status != null ? $status_txt : 'no due date'; ?>
                          </span>
                        </td>
                        <td data-label="Action" width="160">
                          <div class="d-flex align-items-center column-gap-3">
                            <small>


                              <a type="button" target="_blank" class="pay_btn py-1 px-2 rounded-1 text-bg-success text-decoration-none d-flex align-items-center 
             <?php echo ($th_status == 1 || $th_status == 3) ? 'disabled opacity-50' : ''; ?>"
                                href="javascript:void(0);"
                                data-data1="<?= $transaction_history_id ? $transaction_history_id : null; ?>"
                                data-data2="<?= $duedate; ?>" <?php echo ($th_status == 1 || $th_status == 3) ? 'style="pointer-events: none;"' : ''; ?>>
                                <i class="fa-solid fa-wallet me-2"></i><span class="">Pay</span>
                              </a>
                            </small>

                            <small>
                              <a type="button" target="_blank"
                                class="edit_transaction py-1 px-2 rounded-1 text-bg-primary text-decoration-none d-flex align-items-center"
                                data-data1="<?= $transaction_history_id ? $transaction_history_id : null; ?>"
                                data-data2="<?= $balance != null ? $balance : '0'; ?>"
                                data-data3="<?= $penalty != null ? $penalty : '0'; ?>" data-data4="<?= $status; ?>"
                                data-data5="<?= $duedate; ?>">
                                <i class="far fa-edit me-2 "></i><span class="">Edit</span>
                              </a>
                            </small>
                            <small class="delete_data py-1 px-2 rounded-1 text-bg-danger d-flex align-items-center"
                              role="button" data-id="<?= $transaction_history_id; ?>" data-table="transaction_history"
                              data-type="transaction_history_id">
                              <i class="far fa-trash-alt me-2 "></i><span class="">Delete</span>
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

  <?php include 'modal/pay.php' ?>
  <?php include 'modal/create-transaction.php' ?>
  <?php include "plugins-footer.php"; ?>
  <script type="text/javascript">
    $(document).ready(function () {
      $('#payment-form').submit(function (e) {
        e.preventDefault();

        $('#loader-div').removeClass('d-none');
        $('#pay-modal').modal('hide');

        const formData = $(this).serialize();

        $.ajax({
          url: "controller/create-transaction.php",
          method: "POST",
          data: formData,
          dataType: "json",
          success: function (response) {
            $('#loader-div').addClass('d-none');

            if (response && response.length > 0) {
              var statusSent = response[0].status_sent;
              var tenant = response[0].tenant;

              if (statusSent === "no_overdue") {
                Swal.fire({
                  title: "Error To Update",
                  icon: "error"
                });
              } else if (statusSent === "success") {
                Swal.fire({
                  title: "Payment Receipt Sent",
                  text: "Successfully sent to " + tenant,
                  icon: "success"
                }).then(() => {
                  location.reload(); // Reloads the page after clicking OK
                });
              } else if (statusSent === "failed") {
                Swal.fire({
                  title: "Email cannot send!",
                  icon: "info"
                });
              }

            } else {
              console.error('Invalid response format:', response);
            }

            console.log(response);
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error: ", error);
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