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

            <div class="row row-gap-2 mx-0 mt-2 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="m-0 fw-bold">Dashboard</h5>
                </div>
                <div class="col-12 row row-gap-4">

                    <?php
                    $queries = [
                        [
                            "background" => "#CB680BB5",
                            "title" => "USA BED Campus",
                            "sql" => "SELECT COUNT(*) as total 
		                                FROM stall_slots 
                                        WHERE location = '1' AND status = 1"
                        ],
                        [
                            "background" => "#2AA06BB5",
                            "title" => "USA Main Campus",
                            "sql" => "SELECT COUNT(*) as total 
		                                FROM stall_slots WHERE location = '2' AND status = 1"
                        ],
                        [
                            "background" => "#27135DB5",
                            "title" => "USA Main Kiosks",
                            "sql" => "SELECT COUNT(*) as total 
		                                FROM stall_slots WHERE location = '3' AND status = 1"
                        ]

                    ];


                    foreach ($queries as $query) {
                        $stmt = $conn->prepare($query['sql']);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $total = $row['total'];
                        ?>
                        <div class="col-12 col-xl-4">
                            <div class="border border-top-0 border-bottom-0 border-end-0 p-4 rounded-2 shadow-sm"
                                style="border-left: 4px solid var(--primary) !important; background-color: <?= $query['background'] ?>;">
                                <div>
                                    <h6 class="fw-bold text-white">
                                        <?= $query['title'] ?>
                                    </h6>
                                </div>
                                <div style="font-size: 2.5rem;" class="d-flex align-items-center justify-content-between">
                                    <div class="m-0 text-white">
                                        <?= $total ?>
                                    </div>
                                    <i class="fas fa-store"></i>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-12">
                        <a target="_blank" href="print-graph.php" class=" btn btn-sm btn-secondary rounded-1 py-2">
                            <i class="fa-solid fa-print me-2"></i><span>Print All Graph</span>
                        </a>
                    </div>

                    <div class="col-12 row mx-0 gap-5">
                        <!-- Bar Chart Container -->
                        <div
                            class="col-11 col-md-10 col-lg-8 shadow d-flex flex-column align-items-center justify-content-between border border-1 p-0 rounded-2">
                            <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold">
                                Yearly Rate
                            </div>
                            <div class="w-100 p-3">
                                <canvas id="myBarChart" style="width: 100%; height: 400px;"></canvas>
                            </div>
                        </div>

                        <!-- Pie Chart Container -->
                        <div
                            class="col-11 col-md-5 col-lg-3 shadow d-flex flex-column align-items-center justify-content-center border border-1 p-0 rounded-2">
                            <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold mb-auto">
                                Rent Location Rate
                            </div>
                            <div class="w-100 d-flex align-items-center justify-content-center p-3">
                                <canvas id="statusPieChart" style="width: 100%; height: 400px;"></canvas>
                            </div>
                        </div>
                        <div
                            class="col-12 shadow d-flex flex-column align-items-center justify-content-between border border-1 p-0 rounded-2">
                            <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold">
                                Monthly Rate
                            </div>
                            <div class="w-100 p-3">
                                <canvas id="myBarChart3" style="width: 100%; height: 400px;"></canvas>
                            </div>
                        </div>
                        <div
                            class="col-12 shadow d-flex flex-column align-items-center justify-content-between border border-1 p-0 rounded-2">
                            <?php
                            include 'Calendar.php';

                            // Initialize Calendar with today's date
                            $calendar = new Calendar(date('Y-m-d'));
                            // $calendar = new Calendar('2025-05-12');
                            
                            try {
                                // Fetch transaction history with status = 2
                                $stmt = $conn->prepare(
                                    "SELECT ss.tenantname, th.duedate, th.stall_slots_id 
                                FROM transaction_history th 
                                JOIN stall_slots ss 
                                ON th.stall_slots_id = ss.stall_slots_id 
                                WHERE th.status = 2"
                                );

                                $stmt->execute();
                                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($rows) {
                                    foreach ($rows as $row) {
                                        $stallSlotId = $row['stall_slots_id'];
                                        $tenantName = $row['tenantname']; // From stall_slots table
                                        $dueDate = $row['duedate'];       // From transaction_history table
                            
                                        $payNowLink = "<a href='transaction?stall_slots_id=" . urlencode($stallSlotId) . "' class='text-decoration-underline text-primary'>pay now</a>";
                                        $calendar->add_event("$tenantName - $payNowLink", $dueDate, 1, 'orange');
                                    }
                                } else {
                                    echo "<div class='p-2 text-danger'>No ongoing transactions found.</div>";
                                }
                            } catch (PDOException $e) {
                                echo "<div class='p-2 text-danger'>Database error: " . $e->getMessage() . "</div>";
                            }
                            ?>
                            <div class="text-center px-3 py-4 bg-secondary-subtle w-100 fw-bold">
                                Calendar
                            </div>
                            <div class="w-100 p-3">
                                <?= $calendar ?>
                            </div>
                        </div>




                    </div>

                </div>
            </div>


        </div>
        <!-- content container end-->
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
        $percentage = $row['total_amount'];
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
        $location1_percentage = $location1;
        $location2_percentage = $location2;
        $location3_percentage = $location3;
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
        $monthly_percentage3[] = $row['total_amount'];
    }


    ?>
    <?php include "plugins-footer.php"; ?>

    <script type="text/javascript">

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
                            label: function (tooltipItem) {
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
                            label: function (tooltipItem) {
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
                            label: function (tooltipItem) {
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
</body>

</html>