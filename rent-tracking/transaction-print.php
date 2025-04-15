<?php
session_start();

if (!isset($_SESSION['admin']['admin_id'])) {
    header('Location: login');
    exit;
}

include "connect.php"; 

$get_stall_slots_id = $_GET['stall_slots_id'] ?? null;
$sql = "SELECT
         ss.*, 
         SUM(th.amount_paid) AS total_payments, 
         SUM(th.penalty) AS total_penaltys, 
         SUM(th.balance) AS total_balances
        FROM stall_slots ss
        LEFT JOIN transaction_history th ON th.stall_slots_id = ss.stall_slots_id
        WHERE ss.stall_slots_id = :stall_slots_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":stall_slots_id", $get_stall_slots_id);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);

if ($rows) {
  $tenantname = $rows['tenantname'];
  $location = $rows['location'];
  $email = $rows['email'];
  $phonenumber = $rows['phonenumber'];
  $manager_name = $rows['manager_name'];
  $total_payments = $rows['total_payments'];
  $total_penaltys = $rows['total_penaltys'];
  $total_balances = $rows['total_balances'];

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
}
?>

<style>
      .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-weight: bold;
        background-color: #fff;
        padding: 10px 0;
    }
    .ff-roman {
        font-family: "Times New Roman", Times, serif;
    }
    @media print {
        body {
            -webkit-print-color-adjust: exact !important;
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<div class="row mx-0">
  <div class="col-12 d-flex align-items-center justify-content-center column-gap-4 mb-4 mx-4">
    <img src="images/logo.png" height="80px" width="80px">
    <div class="text-center">
      <h1 class="m-0 mb-1">University of San Agustin</h1>
      <p class="m-0">General Luna St, Iloilo City Proper, Iloilo City, <br> 5000 Iloilo, Phillippines</p>
    </div>
    
  </div>
  <div class="row col-12 mx-0 border border-dark py-3">
    <h3 class="text-center">STALL RECORDS</h3>
    <div class="col-12 row mx-0 row-gap-2">
      <div class="col-6">
        Stall Name: <?= $tenantname?>
      </div>
      <div class="col-6">
        Manager's Name: <?= $manager_name?> 
      </div>
      <div class="col-6">
        Location:  <?= $location_txt?>
      </div>
      <div class="col-6">
        Email: <?= $email?> 
      </div>
      <div class="col-6">
        Contact Number: <?= $phonenumber?>
      </div>
      <div class="col-6">
        Date: <?= date("F j, Y")?>
      </div>
    </div>
    <hr class="mt-3 mb-0 text-danger border-2 border-black">
    <div class="row mx-0 mt-3 mb-2">
      <h4 class="col-12 text-center mb-3">Budget Summary</h4>
      <div class="col-4">Total Payment: ₱ <?= ($total_payments != null) ? $total_payments . '.00' : 0; ?></div>
      <div class="col-4">Total Penalty: ₱ <?= ($total_penaltys != null) ? $total_penaltys . '.00' : 0; ?></div>
      <div class="col-4">Total Balance Due: ₱ <?= ($total_balances != null) ? $total_balances . '.00' : 0; ?></div>
    </div>
    <table class="table mt-3">
      <thead class="position-sticky top-0">
          <tr class="py-5">
              <th>#</th>
              <th>Operator</th>
              <th>Due Date</th>
              <th>Balance</th>
              <th>Amount Paid</th>
              <th>Penalty</th>
              <th>Status</th>
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
        $stmt->bindParam(":stall_slots_id", $_GET['stall_slots_id']);
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
              switch ($th_status) {
                  case '1':
                    $status_txt = 'Complete';
                    $badge_bg = 'text-bg-success';
                    break;
                  case '2':
                    $status_txt = 'Incomplete';
                    $badge_bg = 'text-bg-danger';
                    break;
                }
              $formatdate = date("F j, Y", strtotime($duedate));
        ?>

          <tr data-id="2">
            <td data-label="#" width="50"><?= $index++; ?></td>
            <td data-label="Operator">Admin</td>
            <td data-label="Due Date">
              <?= $duedate != null ? $formatdate : 'Incomplete'; ?>
            </td>
            <td data-label="Balance">
              ₱ <?= $balance != null ? number_format($balance, 2) : '0.00'; ?>
            </td>
            <td data-label="Amount Paid">
              ₱ <?= $amount_paid != null ? number_format($amount_paid, 2) : '0.00'; ?>
            </td>
            <td data-label="Penalty">
              ₱ <?= $penalty != null ? number_format($penalty, 2) : '0.00'; ?>
            </td>
            <td data-label="Status">
              <span class="badge <?= $status != null ? $badge_bg : 'text-bg-danger'; ?>">
                <?= $status != null ? $status_txt : 'No due date'; ?>
              </span>
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

  <div class="footer">
    <strong>Prepared by: Admin</strong>
    <div><?= $_SESSION['admin']['name']?></div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
  // JavaScript for printing and closing window after print
  window.print();  
  window.onafterprint = window.close; 
</script>
