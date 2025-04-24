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
            <div class="d-flex align-items-center justify-content-between">
              <h5>Archived History</h5>
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
                    <th>Stall Name</th>
                    <th>Manager</th>
                    <th>Email</th>
                    <th>Phone No.</th>
                    <th>Monthly</th>
                    <th>Location</th>
                    <th>Date Archived</th>
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

                  $sql = "SELECT *
                          FROM stall_slots
                          WHERE status = 2
                          LIMIT :limit OFFSET :offset";
                  $stmt = $conn->prepare($sql);
                  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                  $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                  $stmt->execute();
                  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  if ($rows) {
                    $index = 1 + $offset;
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
                      $formatdate = date("F j, Y", strtotime($date_archived));
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
                        <td data-label="Action" width="160">
                          <div class="d-flex align-items-center column-gap-3">
                            <a href="transaction-print.php?stall_slots_id=<?= $stall_slots_id ?>" target="_blank"
                              class="text-decoration-none">
                              <small class="py-1 px-2 rounded-1 text-bg-secondary d-flex align-items-center" role="button">
                                <i class="fa-solid fa-print me-2"></i><span>Print</span>
                              </small>
                            </a>
                            <small class="archive_button py-1 px-2 rounded-1 text-bg-primary d-flex align-items-center"
                              role="button" data-id="<?= $stall_slots_id; ?>" data-type="1">
                              <i class="fa-solid fa-archive me-2 "></i><span class="">Unarchive</span>
                            </small>
                            <small class="delete_data py-1 px-2 rounded-1 text-bg-danger d-flex align-items-center"
                              role="button" data-id="<?= $stall_slots_id; ?>" data-table="stall_slots"
                              data-type="stall_slots_id">
                              <i class="far fa-trash-alt me-2 "></i><span class="text-nowrap">Delete</span>
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
            <!-- Pagination Controls -->
            <?php
            // Total rows in the 'stall_slots' table with status 1
            $count_sql = "SELECT COUNT(*) FROM stall_slots WHERE status = 2";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->execute();
            $total_rows = $count_stmt->fetchColumn();

            // Pagination logic
            $total_pages = ceil($total_rows / $limit);
            if ($total_pages > 1): ?>
              <nav class="mt-3">
                <ul class="pagination justify-content-center">
                  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                      <a class="page-link"
                        href="?stall_slots_id=<?= htmlspecialchars($stall_slots_id) ?>&limit=<?= $limit ?>&page=<?= $i ?>">
                        <?= $i ?>
                      </a>
                    </li>
                  <?php endfor; ?>
                </ul>
              </nav>
            <?php endif; ?>
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
      // Handle changes to the number of entries shown
      $('#table_show').change(function () {
        const limit = $(this).val();
        const currentPage = new URLSearchParams(window.location.search).get('page') || 1; // Get the current page from URL or default to 1
        const stallSlotsId = $('input[name="stall_slots_id"]').val(); // Get the stall_slots_id value

        // Redirect to the correct URL including the stall_slots_id and the current page
        window.location.href = `?stall_slots_id=${stallSlotsId}&limit=${limit}&page=${currentPage}`;
      });
    });

    $(document).ready(function () {
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