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

$admin_id = $_SESSION['admin']['admin_id'];
$admin_role = $_SESSION['admin']['role'];

// Determine which stalls to process
if (in_array($admin_role, [1, 2])) {
  $query = "
        SELECT t.transaction_history_id, t.stall_slots_id, t.balance, t.amount_paid, t.penalty, t.duedate, t.status, t.downpayment
        FROM transaction_history t
        INNER JOIN (
            SELECT stall_slots_id, MAX(transaction_history_id) AS max_id
            FROM transaction_history
            GROUP BY stall_slots_id
        ) latest ON t.transaction_history_id = latest.max_id
        INNER JOIN stall_slots s ON t.stall_slots_id = s.stall_slots_id
        WHERE s.status = 1
    ";
  $stmt = $conn->prepare($query);
  $stmt->execute();
} else {
  // For admin_role = 3, restrict to that user's stalls
  $query = "
        SELECT t.transaction_history_id, t.stall_slots_id, t.balance, t.amount_paid, t.penalty, t.duedate, t.status, t.downpayment
        FROM transaction_history t
        INNER JOIN (
            SELECT stall_slots_id, MAX(transaction_history_id) AS max_id
            FROM transaction_history
            GROUP BY stall_slots_id
        ) latest ON t.transaction_history_id = latest.max_id
        INNER JOIN stall_slots s ON t.stall_slots_id = s.stall_slots_id
        WHERE s.status = 1 AND s.tenant_account_id = :admin_id
    ";
  $stmt = $conn->prepare($query);
  $stmt->execute(['admin_id' => $admin_id]);
}

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($transactions as $t) {
  $cur_stall_id = $t['stall_slots_id'];
  $cur_balance = $t['balance'];
  $cur_paid = $t['amount_paid'];
  $cur_penalty = $t['penalty'];
  $cur_duedate = $t['duedate'];
  $cur_status = $t['status'];
  $cur_downpayment = $t['downpayment'];

  // Get monthly rent
  $stmt_rent = $conn->prepare("SELECT monthly FROM stall_slots WHERE stall_slots_id = ?");
  $stmt_rent->execute([$cur_stall_id]);
  $monthly_rent = $stmt_rent->fetchColumn();

  // Compute penalty
  $computed_penalty = ($cur_status == 3) ? (0.02 * $monthly_rent) + $cur_balance : 0.00;

  // New values
  $new_balance = $monthly_rent + $computed_penalty - $cur_downpayment;
  $new_duedate = date('Y-m-d H:i:s', strtotime($cur_duedate . ' +30 days'));
  $now = date('Y-m-d H:i:s');
  $new_status = ($new_duedate < $now) ? 3 : 2;

  // Insert only if status is paid or overdue
  if (in_array($cur_status, [1, 3])) {
    $stmt_insert = $conn->prepare("
            INSERT INTO transaction_history (
                stall_slots_id, balance, amount_paid, penalty, duedate, status, completed_date, downpayment
            ) VALUES (
                :stall_slots_id, :balance, 0.00, :penalty, :duedate, :status, NULL, 0.00
            )
        ");

    $stmt_insert->execute([
      'stall_slots_id' => $cur_stall_id,
      'balance' => $new_balance,
      'penalty' => $computed_penalty,
      'duedate' => $new_duedate,
      'status' => $new_status
    ]);
  }
}

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
                  <i class="fa-solid fa-print me-2"></i><span>Print</span>
                </a>
                <?php if (in_array($_SESSION['admin']['role'], [1, 2])): ?>
                  <a type="button" class="create_transaction btn btn-sm btn-primary rounded-1 py-2">
                    <i class="fa-solid fa-circle-plus me-2"></i><span>Create Transaction</span>
                  </a>
                <?php endif; ?>
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
                <form method="get" id="show-form">
                  <input type="hidden" name="stall_slots_id"
                    value="<?= htmlspecialchars($_GET['stall_slots_id'] ?? '') ?>">
                  <div class="d-flex align-items-center gap-2">
                    <label for="table_show">Show: </label>
                    <select name="limit" id="table_show" class="form-select form-select-sm rounded-1 py-2"
                      onchange="document.getElementById('show-form').submit()">
                      <option value="10" <?= (!isset($_GET['limit']) || $_GET['limit'] == '10') ? 'selected' : '' ?>>10
                      </option>
                      <option value="25" <?= (isset($_GET['limit']) && $_GET['limit'] == '25') ? 'selected' : '' ?>>25
                      </option>
                      <option value="50" <?= (isset($_GET['limit']) && $_GET['limit'] == '50') ? 'selected' : '' ?>>50
                      </option>
                      <option value="100" <?= (isset($_GET['limit']) && $_GET['limit'] == '100') ? 'selected' : '' ?>>100
                      </option>
                    </select>
                  </div>
                </form>
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
                    <th>Advance Payment</th>
                    <th>Outstanding + Penalty(2%)</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <!-- <th>Last Edited</th> -->
                    <th>Edited By</th>
                    <th>File</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  // Get selected pagination value
                  $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                  $page = isset($_GET['page']) ? $_GET['page'] : 1;

                  if (!is_numeric($limit)) {
                    if ($limit === 'all') {
                      $useLimit = false;
                    } else {
                      $limit = 10;
                      $useLimit = true;
                    }
                  } else {
                    $limit = (int) $limit;
                    $useLimit = true;
                  }

                  $page = is_numeric($page) ? (int) $page : 1;
                  $offset = ($page - 1) * $limit;



                  // SQL query to get data with limit and offset
                  $sql = "SELECT *, th.status as th_status,  ss.*, aa.name AS edited_by_name
              FROM transaction_history th
              INNER JOIN stall_slots ss 
              ON ss.stall_slots_id = th.stall_slots_id
              LEFT JOIN admin_account aa ON th.transaction_edited_by = aa.admin_id
              WHERE th.stall_slots_id = :stall_slots_id
              LIMIT :limit OFFSET :offset";
                  $stmt = $conn->prepare($sql);
                  $stmt->bindParam(":stall_slots_id", $get_stall_slots_id);
                  $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
                  $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
                  $stmt->execute();
                  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  if ($rows) {
                    $index = 1 + $offset; // Adjust the index based on the offset
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
                      $formatdateedited = !empty($transaction_date_edited) ? date("F j, Y", strtotime($transaction_date_edited)) : 'No edits yet';
                      ?>

                      <tr data-id="2">
                        <td data-label="#" width="50"><?= $index++; ?></td>
                        <td data-label="Location"><?= $location_txt ?></td>
                        <td data-label="Balance">₱ <?= $balance != null ? number_format($balance, 2) : '0.00'; ?></td>
                        <td data-label="Amount Paid">₱
                          <?= $amount_paid != null ? number_format($amount_paid, 2) : '0.00'; ?>
                        </td>
                        <td data-label="Advance Payment">₱
                          <?= $downpayment != null ? number_format($downpayment, 2) : '0.00'; ?>
                        </td>
                        <td data-label="Penalty">₱ <?= $penalty != null ? number_format($penalty, 2) : '0.00'; ?></td>
                        <td data-label="Due Date"><?= $duedate != null ? $formatdate : 'Incomplete'; ?></td>
                        <td data-label="Status">
                          <span class="badge <?= $status != null ? $badge_bg : 'text-bg-danger'; ?>">
                            <?= $status != null ? $status_txt : 'no due date'; ?>
                          </span>
                        </td>
                        <!-- <td data-label="Last Edited"><?= $formatdateedited; ?></td> -->
                        <td><?= $row['edited_by_name'] . ' ' . $formatdateedited ?? 'No edits yet'; ?></td>
                        <td data-label="File" style="min-width: 120px; max-width: 250px; word-break: break-word;">
                          <?php
                          $sql2 = "SELECT *
                                        FROM transaction_file
                                        WHERE transaction_history_id = :transaction_history_id";
                          $stmt2 = $conn->prepare($sql2);
                          $stmt2->bindParam(":transaction_history_id", $transaction_history_id);
                          $stmt2->execute();
                          $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                          if ($rows2) {
                            foreach ($rows2 as $row2):
                              ?>
                              <div text-wrap class="mb-2 d-flex align-items-start column-gap-2">
                                <a class="text-wrap"
                                  href="upload/<?= $row2['transactions_file']; ?>"><?= $row2['transactions_file']; ?></a>
                                <!-- target="_blank" -->
                                <small class="delete_data" role="button" data-id="<?= $row2['transaction_file_id']; ?>"
                                  data-table="transaction_file" data-type="transaction_file_id">
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
                          <div
                            class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 flex-wrap">

                            <small data-id="<?= $transaction_history_id; ?>" data-id2="<?= $stall_slots_id; ?>"
                              role="button"
                              class="upload_file py-1 px-2 rounded-1 text-bg-secondary text-decoration-none d-flex align-items-center">
                              <i class="fa-solid fa-upload me-2"></i><span>Upload</span>
                            </small>

                            <?php if (in_array($_SESSION['admin']['role'], [1, 2])): ?>
                              <small>
                                <a type="button" class="pay_btn py-1 px-2 rounded-1 text-bg-success text-decoration-none d-flex align-items-center 
                              <?php echo ($th_status == 1 || $th_status == 3) ? 'disabled opacity-50' : ''; ?>"
                                  href="javascript:void(0);"
                                  data-data1="<?= $transaction_history_id ? $transaction_history_id : null; ?>"
                                  data-data2="<?= $duedate; ?>" data-data3="<?= $balance != null ? $balance : '0'; ?>" <?php echo ($th_status == 1 || $th_status == 3) ? 'style="pointer-events: none;"' : ''; ?>>
                                  <i class="fa-solid fa-wallet me-2"></i><span class="">Pay</span>
                                </a>
                                <!-- target="_blank" -->
                              </small>

                              <small>
                                <a type="button"
                                  class="edit_transaction py-1 px-2 rounded-1 text-bg-primary text-decoration-none d-flex align-items-center"
                                  data-data1="<?= $transaction_history_id ? $transaction_history_id : null; ?>"
                                  data-data2="<?= $balance != null ? $balance : '0'; ?>"
                                  data-data3="<?= $penalty != null ? $penalty : '0'; ?>" data-data4="<?= $status; ?>"
                                  data-data5="<?= $duedate; ?>">
                                  <i class="far fa-edit me-2 "></i><span class="">Edit</span>
                                </a>
                                <!-- target="_blank" -->
                              </small>

                            <?php endif; ?>
                            <?php
                            if ($_SESSION['admin']['role'] === 1) {
                              ?>
                              <small class="delete_data py-1 px-2 rounded-1 text-bg-danger d-flex align-items-center"
                                role="button" data-id="<?= $transaction_history_id; ?>" data-table="transaction_history"
                                data-type="transaction_history_id">
                                <i class="far fa-trash-alt me-2 "></i><span class="">Delete</span>
                              </small>
                              <?php
                            }
                            ?>

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
            <!-- Pagination Controls -->
            <?php
            // Count total rows
            $count_sql = "SELECT COUNT(*) FROM transaction_history WHERE stall_slots_id = :stall_slots_id";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bindParam(":stall_slots_id", $get_stall_slots_id);
            $count_stmt->execute();
            $total_rows = $count_stmt->fetchColumn();

            // Only show pagination if not "all"
            if ($useLimit && $limit > 0):
              $total_pages = ceil($total_rows / $limit);
              if ($total_pages > 1): ?>
                <nav class="mt-3">
                  <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                      <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link"
                          href="?stall_slots_id=<?= $get_stall_slots_id ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                          <?= $i ?>
                        </a>
                      </li>
                    <?php endfor; ?>
                  </ul>
                </nav>
              <?php endif;
            endif;
            ?>
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
      // Handle changes to the number of entries shown
      $(document).ready(function () {
        $('#table_show').change(function () {
          const limit = $(this).val();
          const currentPage = 1; // Reset to page 1 when changing the limit
          const stallSlotsId = $('input[name="stall_slots_id"]').val(); // Get the stall_slots_id value

          // Redirect to the correct URL including the stall_slots_id
          window.location.href = `?stall_slots_id=${stallSlotsId}&limit=${limit}&page=${currentPage}`;
        });
      });


      // Other JavaScript functionalities
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
                  location.reload();
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
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error: ", error);
          }
        });
      });

      $('.upload_file').click(function (e) {
        $('#upload-modal').modal('show');

        $('#transaction_history_id2').val($(this).data('id'));
        $('#stall_slots_id2').val($(this).data('id2'));

      });
    });





  </script>
</body>

</html>