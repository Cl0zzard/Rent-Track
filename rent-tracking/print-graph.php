<?php
session_start();

if (!isset($_SESSION['admin']['admin_id'])) {
    header('Location: login');
    exit;
}

include "connect.php"; 

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
        .page-break-avoid {
            break-inside: avoid;
            page-break-inside: avoid;
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
  <div class="row col-12 mx-0 row-gap-4 justify-content-center border border-dark py-3">
          <h3 class="text-center">STALL RECORDS</h3>
          <!-- Bar Chart Container -->
          <div class="col-12">
            <div class="shadow d-flex flex-column align-items-center justify-content-between border border-1 p-0 rounded-2">
                <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold">
                    Yearly Rate
                </div>
                <div class="w-100 p-3">
                    <canvas id="myBarChart" style="max-width: 100% !important; max-height: 100%;" ></canvas>
                </div>
            </div>
          </div>

          <!-- Pie Chart Container -->
          <div class="col-auto">
            <div class="shadow d-flex flex-column align-items-center justify-content-center border border-1 p-0 rounded-2">
                <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold mb-auto">
                    Rent Location Rate
                </div>
                <div class="w-100 d-flex align-items-center justify-content-center p-3" >
                    <canvas id="statusPieChart" style="max-width: 100% !important; max-height: 86%;"></canvas>
                </div>
            </div>
          </div>
          <div class="col-12 page-break-avoid">
            <div class="shadow d-flex flex-column align-items-center justify-content-between border border-1 p-0 rounded-2">
                <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold">
                    Monthly Rate
                </div>
                <div class="w-100 p-3">
                    <canvas id="myBarChart3" style="max-width: 100% !important; max-height: 100%;"></canvas>
                </div>
            </div>
          </div>

  </div>

  <div class="footer">
    <strong>Prepared by: Admin</strong>
    <div><?= $_SESSION['admin']['name']?></div>
</div>
</div>

<?php
$ss_status = 1;
//yearly rate
$sql = "SELECT 
            YEAR(th.completed_date) AS year,
            SUM(th.amount_paid) AS total_amount
        FROM 
            transaction_history th
        INNER JOIN stall_slots ss 
            ON th.stall_slots_id = ss.stall_slots_id
        WHERE 
            ss.status = :ss_status
        GROUP BY 
            YEAR(th.completed_date)
        ORDER BY 
            year ASC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":ss_status", $ss_status);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays
$year = [];
$year_percentage = [];
$total_amount_all_years = 0;

// Calculate the total of all years
foreach ($results as $row) {
    $total_amount_all_years += $row['total_amount'];
}

// Calculate percentages for each year
foreach ($results as $row) {
    $year[] = $row['year'];
    $percentage = $row['total_amount'] ?? 0;
    $year_percentage[] = round($percentage, 2); // Round to 2 decimal places
}









//Get each total rate of location
$sql = "SELECT 
            SUM(CASE WHEN ss.location = '1' THEN th.amount_paid ELSE 0 END) AS location1,
            SUM(CASE WHEN ss.location = '2' THEN th.amount_paid ELSE 0 END) AS location2,
            SUM(CASE WHEN ss.location = '3' THEN th.amount_paid ELSE 0 END) AS location3,
            SUM(th.amount_paid) AS total_amount
        FROM 
            transaction_history th
        INNER JOIN 
            stall_slots ss ON th.stall_slots_id = ss.stall_slots_id
        WHERE 
            ss.status = :ss_status";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":ss_status", $ss_status);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Get the total each location
$location1 = (int) $result['location1'];
$location2 = (int) $result['location2'];
$location3 = (int) $result['location3'];
$total_amount = (int) $result['total_amount'];

// Avoid division by zero
if ($total_amount > 0) {
    $location1_percentage = $location1 ?? 0;
    $location2_percentage = $location2 ?? 0;
    $location3_percentage = $location3 ?? 0;
} else {
    $location1_percentage = $location2_percentage = $location3_percentage = 0;
}



// Monthly rate query for the third graph
$sql = "SELECT 
            MONTH(th.completed_date) AS month,
            SUM(th.amount_paid) AS total_amount
        FROM 
            transaction_history th
        INNER JOIN 
            stall_slots ss ON th.stall_slots_id = ss.stall_slots_id
        WHERE 
            ss.status = :ss_status AND th.completed_date IS NOT NULL
        GROUP BY 
           MONTH(th.completed_date)
        ORDER BY 
            month ASC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":ss_status", $ss_status);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays
$months3 = [];
$monthly_percentage3 = [];
$total_amount_all_months3 = 0;

// Calculate the total of all months
foreach ($results as $row) {
    $total_amount_all_months3 += $row['total_amount'];
}

// Calculate percentages for each month
foreach ($results as $row) {
    $month = $row['month'];
    
    // FIXED: Use mktime() to convert month number to month name
    $months3[] = date("F", mktime(0, 0, 0, $month, 1));
    $monthly_percentage3[] = $row['total_amount'] ?? 0;
}


?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
  // JavaScript for printing and closing window after print
  window.print();  
  window.onafterprint = window.close; 

    const ctx1 = document.getElementById('myBarChart').getContext('2d');
    const years = <?php echo json_encode($year); ?>;
    const totals = <?php echo json_encode($year_percentage); ?>;

    const data1 = {
        labels: years, // X-axis labels
        datasets: [{
            label: 'Total Rate Per Year', // Dataset label
            data: totals, // Data for the bar chart
            backgroundColor: ['#FF5733', '#3357FF', '#FFC107', '#8E44AD', '#1ABC9C'], // Different colors for each bar
            borderColor: '#003366', // Bar border color
            borderWidth: 1 // Border width
        }]
    };

    const config1 = {
        type: 'bar',
        data: data1,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false // Remove horizontal grid lines
                    }
                },
                x: {
                    grid: {
                        display: false // Remove vertical grid lines
                    }
                }
            },
            animation: {
                duration: 2500, // Animation duration in milliseconds
                easing: 'easeOutQuart' // Animation easing
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const datasetLabel = tooltipItem.chart.data.datasets[tooltipItem.datasetIndex].label || ''; // Get dataset label
                            const value = tooltipItem.raw || 0; // Get the value
                            return `${datasetLabel}: ₱ ${value.toFixed(2)}`; // Format the tooltip as "Label: Value %"
                        }
                    }
                }
            }
        }
    };

    new Chart(ctx1, config1);

    // ====================================================================
    
    const ctx2 = document.getElementById('statusPieChart').getContext('2d');

    // Fetch PHP variables and parse them into JavaScript
    const location1 = <?php echo json_encode($location1_percentage); ?>;
    const location2 = <?php echo json_encode($location2_percentage); ?>;
    const location3 = <?php echo json_encode($location3_percentage); ?>;

    // Define the dataset
    const data2 = {
        labels: ['USA BED Campus', 'USA Main Campus', 'USA Main Kiosks'],
        datasets: [{
            label: 'Rent Location Rate',
            data: [location1, location2, location3],
            backgroundColor: ['#4CAF50', '#FF5722', '#FFC107'], // Colors
            hoverOffset: 4
        }]
    };

    // Configure the pie chart
    const config2 = {
        type: 'pie',
        data: data2,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const label = tooltipItem.label || '';
                            const value = tooltipItem.raw || 0;
                            return `${label}: ₱ ${value.toFixed(2)}`;
                        }
                    }
                }
            }
        }
    };

    // Render the chart
    new Chart(ctx2, config2);


    // ====================================================================




    const ctx3 = document.getElementById('myBarChart3').getContext('2d'); // Unique ID
const months3 = <?php echo json_encode($months3); ?>;
const totals3 = <?php echo json_encode($monthly_percentage3); ?>;

const data3 = {
    labels: months3, // X-axis labels for each month
    datasets: [{
        label: 'Total Rate Per Month', // Dataset label
        data: totals3, // Data for the bar chart
        backgroundColor: ['#FFC300', '#FF5733', '#DAF7A6'], // Different colors for each bar chart
        borderColor: '#003366', // Bar border color
        borderWidth: 1 // Border width
    }]
};

const config3 = {
    type: 'bar',
    data: data3,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: false // Remove horizontal grid lines
                }
            },
            x: {
                grid: {
                    display: false // Remove vertical grid lines
                },
                ticks: {
                    autoSkip: true, // Automatically skip ticks if needed
                    maxRotation: 45, // Limit label rotation for better visibility
                    minRotation: 30
                }
            }
        },
        animation: {
            duration: 2500, // Animation duration in milliseconds
            easing: 'easeOutQuart' // Animation easing
        },
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 14
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        const datasetLabel = tooltipItem.chart.data.datasets[tooltipItem.datasetIndex].label || ''; // Get dataset label
                        const value = tooltipItem.raw || 0; // Get the value
                        return `${datasetLabel}: ₱ ${value.toFixed(2)}`; // Format the tooltip as "Label: Value %"
                    }
                }
            }
        }
    }
};

new Chart(ctx3, config3); // Initialize the third graph

</script>
