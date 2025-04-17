<?php 
session_start();

if (!isset($_SESSION['admin']['admin_id'])) {
    header('Location: login');
    exit;
}else if($_SESSION['admin']['role'] == 2){
    header('Location: dashboard');
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
  background-position: 0px 11px , 8px 35px, 0px 60px;
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
    background-position: 100px 11px , 115px 35px, 105px 60px;
    opacity: 1;
  }

  50% {
    background-position: 0px 11px , 20px 35px, 5px 60px;
  }

  60% {
    background-position: -30px 11px , 0px 35px, -10px 60px;
  }

  75%, 100% {
    background-position: -30px 11px , -30px 35px, -30px 60px;
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
		                  <h5>
                      <?php 
                      switch ($_GET['account_status']) {
                        case '1':
                          echo "Registered Account";
                          break;
                        
                        case '2':
                          echo "Archived Account";
                          break;
                      }

                      ?>  
                      </h5>
		                  <div class="d-flex align-items-center gap-2">
		                    <a type="button" class="add_staff btn btn-sm btn-primary rounded-1 py-2">
		                      <i class="fa-solid fa-circle-plus me-2"></i><span>Add Account</span>
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
		                      <input type="text" id="search-bar" class="form-control form-control-sm rounded-1 py-2" placeholder="Search here">
		                    </div>
		                      
		                  </div>
		                </div>
                
                <div style="height: auto;">
                    <table id="product_table" class="table table-bordered table-striped">
                        <thead class="position-sticky top-0">
                            <tr class="py-5">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Phone No.</th>
                                <th>Address</th>
                                <th>Date Added</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php 
                          $index = 1;
                          $sql = "SELECT *
                                  FROM admin_account
                                  WHERE role = 2 AND status_archived = :account_status";
                          $stmt = $conn->prepare($sql);
                          $stmt->bindParam(":account_status", $_GET['account_status']);
                          $stmt->execute();
                          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                              if ($rows) {
                                  foreach ($rows as $row):

                                    foreach ($row as $key => $value):
                                      $$key = $value;
                                    endforeach;
                                    $formatdate = date("F j, Y", strtotime($date_added));
                          ?>
                          <tr data-id="2">
                              <td data-label="#" width="50"><?= $index++; ?></td>
                              <td data-label="Name"><?= $name; ?></td>
                              <td data-label="Email"><?= $email; ?></td>
                              <td data-label="Username"><?= $username; ?></td>
                              <td data-label="Phone No.">
                                <?= $phonenumber; ?>
                              </td>
                              <td data-label="Address">
                                <?= $address; ?>
                              </td>
                              <td data-label="Date Added"><?= $formatdate; ?></td>
                              <td data-label="Action" width="160">
                                    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2">
                                      <?php if($_GET['account_status'] == 1){?>
                                      <small>
                                        <a type="button" class="edit_staff py-1 px-2 rounded-1 text-bg-primary text-decoration-none d-flex align-items-center"
                                        data-data1="<?= $admin_id; ?>"
                                        data-data2="<?= $name; ?>"
                                        data-data3="<?= $email; ?>"
                                        data-data4="<?= $username; ?>"
                                        data-data5="<?= $phonenumber; ?>"
                                        data-data6="<?= $address; ?>"
                                        >
                                          <i class="fa-solid fa-pen-to-square me-2 "></i><span class="">Edit</span>
                                        </a>
                                      </small>
                                      <?php } ?>

                                      <small class="archive_staff py-1 px-2 rounded-1 <?= ($_GET['account_status'] == 1) ? 'text-bg-danger' : 'text-bg-primary'?> d-flex align-items-center"
                                                role="button"
                                                data-id="<?= $admin_id; ?>" 
                                                data-type="<?= ($_GET['account_status'] == 1) ? '2' : '1'?>" 
                                            >
                                            <i class="fa-solid fa-box-archive me-2 "></i><span class=""><?= ($_GET['account_status'] == 1) ? 'Archive' : 'Unarchive'?></span>
                                      </small>

                                      <?php if($_GET['account_status'] == 2){?>
                                      <small class="delete_data py-1 px-2 rounded-1 text-bg-danger d-flex align-items-center"
                                                role="button"
                                                data-id="<?= $admin_id; ?>"
                                                data-table="admin_account"
                                                data-type="admin_id"
                                            >
                                            <i class="far fa-trash-alt me-2 "></i><span class="text-nowrap">Delete Permanently</span>
                                      </small>
                                      <?php } ?>
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
    <?php include 'modal/add-staff.php' ?>
    <?php include "plugins-footer.php"; ?>
    <script type="text/javascript">
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


