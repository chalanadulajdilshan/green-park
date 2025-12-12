<!doctype html>
<?php
include 'class/include.php';
include 'auth.php';

?>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Homes | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name; ?>" name="author" />
    <?php include 'main-css.php'; ?>


    <style>
        .chart-container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .chart-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .chart-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #5b73e8, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .chart-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            font-weight: 500;
        }

        .chart-wrapper {
            position: relative;
            height: 500px;
            margin: 30px 100px;
            background: linear-gradient(145deg, #f8f9ff, #e8ecff);
            border-radius: 15px;
            padding: 40px;
            box-shadow: inset 0 2px 10px rgba(91, 115, 232, 0.1);
        }

        .bar-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            height: 100%;
            position: relative;
        }

        .bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            margin: 0 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .bar-wrapper:hover {
            transform: translateY(-5px);
        }

        .bar {
            width: 100%;
            max-width: 50px;
            background: linear-gradient(180deg, #5b73e8, #667eea);
            border-radius: 8px 8px 4px 4px;
            position: relative;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(91, 115, 232, 0.3);
            transform-origin: bottom;
            animation: barGrow 1.5s ease-out forwards;
            animation-delay: calc(var(--index) * 0.1s);
            height: 0;
        }

        @keyframes barGrow {
            from {
                height: 0;
                transform: scaleY(0);
            }

            to {
                height: var(--height);
                transform: scaleY(1);
            }
        }

        .bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #fff, #f0f2ff);
            border-radius: 8px 8px 0 0;
            opacity: 0.8;
        }

        .bar:hover {
            background: linear-gradient(180deg, #6c82f0, #7589f2);
            box-shadow: 0 6px 25px rgba(91, 115, 232, 0.4);
            transform: scale(1.05);
        }

        .bar-value {
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(91, 115, 232, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .bar-wrapper:hover .bar-value {
            opacity: 1;
        }

        .bar-label {
            margin-top: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 60px;
            pointer-events: none;
        }

        .grid-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(108, 117, 125, 0.15);
        }

        .grid-label {
            position: absolute;
            left: -50px;
            transform: translateY(-50%);
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .chart-wrapper {
                padding: 20px;
                height: 400px;
            }

            .chart-title {
                font-size: 2rem;
            }

            .bar {
                max-width: 35px;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(91, 115, 232, 0.3);
            }

            50% {
                box-shadow: 0 6px 25px rgba(91, 115, 232, 0.5);
            }

            100% {
                box-shadow: 0 4px 15px rgba(91, 115, 232, 0.3);
            }
        }
    </style>

</head>

<body data-layout="horizontal" data-topbar="colored">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>



        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <!-- Modern Welcome Card -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="welcome-card" style="
                                background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);
                                border-radius: 16px;
                                overflow: hidden;
                                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                                color: #ffffff;
                                position: relative;
                                transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
                                border: none;
                                border-top: 1px solid rgba(255, 255, 255, 0.1);
                                border-left: 1px solid rgba(255, 255, 255, 0.05);
                            ">
                                <div class="card-body p-4 p-md-5 position-relative">
                                    <!-- Welcome Image (positioned absolutely) -->
                                    <div class="position-absolute d-none d-lg-block" style="right: 0; top: 50%; transform: translateY(-50%); z-index: 1;">
                                        <div style="position: relative; animation: float 6s ease-in-out infinite;">
                                            <div style="
                                                width: 140px;
                                                height: 140px;
                                                background: rgba(255,255,255,0.1);
                                                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                                                position: absolute;
                                                top: -20px;
                                                right: -20px;
                                                animation: morph 8s ease-in-out infinite;
                                            "></div>
                                            <img src="assets/images/welcome.png" alt="Welcome Illustration" class="img-fluid position-relative" style="filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row align-items-center">
                                        <div class="flex-shrink-0 me-4 mb-3 mb-md-0 position-relative">
                                            <div class="avatar-xxl position-relative">
                                                <?php 
                                                // Get the base URL - works for both local and live servers
                                                $isLocal = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1');
                                                $base_path = $isLocal ? '/360-ERP/' : '/';
                                                $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $base_path;
                                                
                                                // Check if user has a profile image and it exists, otherwise use default
                                                $defaultImage = $base_url . 'upload/users/8.jpg';
                                                $profileImage = $defaultImage;
                                                if (!empty($US->image_name)) {
                                                    $image_path = ($isLocal ? 'upload/users/' : 'upload/users/') . $US->image_name;
                                                    if (file_exists($image_path)) {
                                                        $profileImage = $base_url . 'upload/users/' . $US->image_name;
                                                    }
                                                }
                                                // Add a cache-busting parameter
                                                $profileImage .= '?v=' . time();
                                                ?>
                                                <div class="position-absolute" style="
                                                    width: 100px;
                                                    height: 100px;
                                                    background: rgba(255,255,255,0.15);
                                                    border-radius: 50%;
                                                    top: 50%;
                                                    left: 50%;
                                                    transform: translate(-50%, -50%);
                                                    z-index: 0;
                                                    animation: pulse 2s infinite;
                                                "></div>
                                                <img src="<?php echo $profileImage; ?>" alt="Profile Picture" class="img-fluid rounded-circle position-relative" style="
                                                    width: 90px;
                                                    height: 90px;
                                                    object-fit: cover;
                                                    border: 3px solid rgba(255,255,255,0.9);
                                                    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
                                                    transition: all 0.3s ease;
                                                    z-index: 1;
                                                " onerror="this.onerror=null; this.src='<?php echo $defaultImage; ?>'" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-center text-md-start">
                                            <h2 class="mb-2" style="
                                                font-weight: 700; 
                                                font-size: 1.8rem; 
                                                background: linear-gradient(90deg, #ffffff, #e6f9ff);
                                                -webkit-background-clip: text;
                                                -webkit-text-fill-color: transparent;
                                                text-shadow: 0 2px 8px rgba(26, 41, 128, 0.2);
                                                position: relative;
                                                display: inline-block;
                                            ">
                                                Welcome back, <span style="
                                                    background: linear-gradient(90deg, #ffd700, #ffb700);
                                                    -webkit-background-clip: text;
                                                    -webkit-text-fill-color: transparent;
                                                    text-shadow: 0 2px 8px rgba(255, 183, 0, 0.2);
                                                    font-weight: 800;
                                                "><?php echo htmlspecialchars($US->name); ?></span>! 
                                                <span class="welcome-emoji" style="
                                                    display: inline-block;
                                                    transform: rotate(0deg);
                                                    transition: transform 0.3s ease;
                                                ">👋</span>
                                            </h2>
                                            <p class="mb-3" style="font-size: 1.1rem; opacity: 0.9; max-width: 600px;">
                                                <?php 
                                                // Set the default timezone to match your location (Asia/Colombo for Sri Lanka)
                                                date_default_timezone_set('Asia/Colombo');
                                                
                                                $current_hour = (int)date('H');
                                                $current_time = date('h:i A');
                                                $greeting = '';
                                                $icon = '';
                                                
                                                // Debug information (you can remove this after testing)
                                                // echo "<!-- Debug: Current hour is $current_hour, Time: $current_time -->";
                                                
                                                if ($current_hour < 12) {
                                                    $greeting = 'Good Morning';
                                                    $icon = '☀️';
                                                } elseif ($current_hour < 17) {
                                                    $greeting = 'Good Afternoon';
                                                    $icon = '🌤️';
                                                } else {
                                                    $greeting = 'Good Evening';
                                                    $icon = '🌙';
                                                }
                                                
                                                // Add animation to the greeting
                                                echo "<span class='d-inline-flex align-items-center'>";
                                                echo "<span class='greeting-icon me-2' style='display: inline-block; animation: bounce 2s infinite;'>$icon</span>";
                                                echo "<span class='greeting-text'><span class='fw-medium'>$greeting!</span> Here's what's happening with your store today.</span>";
                                                echo "</span>";
                                                
                                                // Add some CSS for the animation
                                                echo "<style>";
                                                echo "@keyframes bounce {";
                                                echo "  0%, 100% { transform: translateY(0); }";
                                                echo "  50% { transform: translateY(-3px); }";
                                                echo "}";
                                                echo "</style>";
                                                ?>
                                            </p>
                                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3">
                                                <span class="badge" style="
                                                    background: rgba(255,255,255,0.15);
                                                    backdrop-filter: blur(5px);
                                                    border: 1px solid rgba(255,255,255,0.2);
                                                    font-size: 0.9rem;
                                                    font-weight: 500;
                                                    padding: 0.5rem 1rem;
                                                    border-radius: 50px;
                                                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                                                ">
                                                    <i class="bx bx-calendar me-2"></i> <?php echo date('l, F j, Y'); ?>
                                                </span>
                                                <span class="badge" id="realtime-clock" style="
                                                    background: rgba(255,255,255,0.15);
                                                    backdrop-filter: blur(5px);
                                                    border: 1px solid rgba(255,255,255,0.2);
                                                    font-size: 0.9rem;
                                                    font-weight: 500;
                                                    padding: 0.5rem 1rem;
                                                    border-radius: 50px;
                                                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                                                ">
                                                    <i class="bx bx-time me-2"></i> <span id="time-display"><?php echo date('h:i A'); ?></span>
                                                </span>
                                                <script>
                                                    function updateClock() {
                                                        const now = new Date();
                                                        const timeString = now.toLocaleTimeString('en-US', {
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                            hour12: true
                                                        });
                                                        document.getElementById('time-display').textContent = timeString;
                                                    }
                                                    updateClock();
                                                    setInterval(updateClock, 1000);
                                                </script>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="position-absolute" style="
                                        bottom: -50px;
                                        right: -50px;
                                        width: 250px;
                                        height: 250px;
                                        background: rgba(255,255,255,0.05);
                                        border-radius: 50%;
                                    "></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                    <?php
                    $ITEM_MASTER = new ItemMaster(NULL);
                    $MESSAGE = new Message(null);

                    $reorderItems = $ITEM_MASTER->checkReorderLevel();

                    if (!empty($reorderItems)) {
                        $customMessages = [];

                        foreach ($reorderItems as $item) {
                            $customMessages[] = "Reorder Alert: <strong>{$item['code']}</strong> - {$item['name']} is below reorder level.";
                        }

                        $MESSAGE->showCustomMessages($customMessages, 'danger');
                    }

                    // Due Date Notifications
                     $db = Database::getInstance();
                    $query = "SELECT COUNT(*) as total FROM sales_invoice 
                              WHERE payment_type = 2 AND due_date IS NOT NULL 
                              AND due_date >= CURDATE() AND due_date <= DATE_ADD(CURDATE(), INTERVAL 2 DAY) 
                              AND is_cancel = 0";
                    $result = $db->readQuery($query);
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        $totalDueNotifications = $row['total'];
                        if ($totalDueNotifications > 0) {
                            $dueNotifications = ["<a href='customer-outstanding-report.php' class='alert-link'>View {$totalDueNotifications} upcoming due date(s) within 2 days</a>"];
                            echo '<div id="due_date_notification">';
                            $MESSAGE->showCustomMessages($dueNotifications, 'warning');
                            echo '</div>';
                        }
                    }

                    ?>

                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="float-end mt-2">
                                        <div id="tire-sales-chart"></div>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 mt-1">LKR <span data-plugin="counterup">128,450</span></h4>
                                        <p class="text-muted mb-0">Total Tire Sales</p>
                                    </div>
                                    <p class="text-muted mt-3 mb-0">
                                        <span class="text-success me-1">
                                            <i class="mdi mdi-arrow-up-bold me-1"></i>5.2%
                                        </span> this month
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Availability -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="float-end mt-2">
                                        <div id="stock-chart"></div>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 mt-1"><span data-plugin="counterup">2,450</span></h4>
                                        <p class="text-muted mb-0">Tires in Stock</p>
                                    </div>
                                    <p class="text-muted mt-3 mb-0">
                                        <span class="text-danger me-1">
                                            <i class="mdi mdi-arrow-down-bold me-1"></i>3.1%
                                        </span> from last week
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Suppliers -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="float-end mt-2">
                                        <div id="suppliers-chart"></div>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 mt-1"><span data-plugin="counterup">18</span></h4>
                                        <p class="text-muted mb-0">Active Suppliers</p>
                                    </div>
                                    <p class="text-muted mt-3 mb-0">
                                        <span class="text-success me-1">
                                            <i class="mdi mdi-arrow-up-bold me-1"></i>2 New
                                        </span> this month
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Growth -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="float-end mt-2">
                                        <div id="revenue-growth-chart"></div>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 mt-1">+ <span data-plugin="counterup">15.75</span>%</h4>
                                        <p class="text-muted mb-0">Revenue Growth</p>
                                    </div>
                                    <p class="text-muted mt-3 mb-0">
                                        <span class="text-success me-1">
                                            <i class="mdi mdi-arrow-up-bold me-1"></i>12.4%
                                        </span> vs last quarter
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Bar Chart -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Sales Overview</h4>
                                    <p class="card-title-desc">Monthly sales performance</p>
                                </div>
                                <div class="card-body">
                                    <div class="chart-wrapper">
                                        <div class="chart-grid" id="chart-grid"></div>
                                        <div class="bar-container" id="bar-container">
                                            <!-- Bars will be generated by JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Bar Chart -->
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <?php include 'footer.php' ?>

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="ajax/js/common.js"></script>

    <!-- ApexCharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- include main js  -->
    <?php include 'main-js.php' ?>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard.init.js"></script>

</body>

</html>