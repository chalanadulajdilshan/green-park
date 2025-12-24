<!doctype html>
<?php
include 'class/include.php';

$trackingCode = $_GET['code'] ?? '';
$serviceData = null;

if ($trackingCode) {
    $serviceData = VehicleService::getByTrackingCode($trackingCode);
}

$COMPANY_PROFILE = new CompanyProfile(1);
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Track Your Service | <?php echo $COMPANY_PROFILE->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: radial-gradient(circle at 20% 20%, #e9f7ff, #f7fbff 40%, #f2f4f7);
            min-height: 100vh;
            padding: 32px 16px 24px;
            color: #0f172a;
        }

        .track-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .track-header {
            text-align: center;
            color: #0f172a;
            margin-bottom: 28px;
        }

        .track-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .track-header p {
            opacity: 0.75;
            font-size: 15px;
        }

        .track-search {
            background: #ffffff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }

        .search-input-group {
            display: flex;
            gap: 12px;
        }

        .search-input {
            flex: 1;
            padding: 16px 20px;
            border: 1px solid #d4d9e1;
            border-radius: 12px;
            font-size: 17px;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            text-align: center;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #0ea5e9;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
        }

        .search-btn {
            padding: 16px 28px;
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
            min-width: 120px;
        }

        .search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.25);
            filter: brightness(1.05);
        }

        .track-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
            border: 1px solid #e2e8f0;
        }

        .track-card-header {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 50%, #e7f5ff 100%);
            color: #0f172a;
            padding: 26px;
            text-align: center;
        }

        .track-code {
            font-size: 13px;
            opacity: 0.7;
            margin-bottom: 6px;
            letter-spacing: 1px;
        }

        .vehicle-info {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
        }

        .vehicle-details {
            font-size: 15px;
            opacity: 0.8;
            margin-top: 6px;
        }

        .track-card-body {
            padding: 26px;
        }

        /* Timeline Styles */
        .track-timeline {
            position: relative;
            padding: 20px 0;
        }

        .track-timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #e5e7eb;
            border-radius: 2px;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            position: relative;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #9ca3af;
            position: relative;
            z-index: 2;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .timeline-item.completed .timeline-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .timeline-item.current .timeline-icon {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: white;
            animation: pulse 2s infinite;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.35);
        }

        .timeline-content {
            margin-left: 20px;
            flex: 1;
            padding-top: 15px;
        }

        .timeline-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .timeline-item.completed .timeline-title {
            color: #10b981;
        }

        .timeline-item.current .timeline-title {
            color: #0ea5e9;
        }

        .timeline-time {
            font-size: 14px;
            color: #9ca3af;
        }

        .timeline-notes {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 0 0 20px rgba(102, 126, 234, 0); }
        }

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .info-card-icon {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 8px;
        }

        .info-card-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .info-card-value {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 16px;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-badge.in_progress {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-badge.completed {
            background: #d1fae5;
            color: #059669;
        }

        .status-badge.delivered {
            background: #d1fae5;
            color: #059669;
        }

        /* Services List */
        .services-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f3f4f6;
        }

        .services-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 16px;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: #f9fafb;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .service-name {
            font-weight: 600;
            color: #374151;
        }

        .service-price {
            font-weight: 700;
            color: #10b981;
        }

        /* Error State */
        .error-state {
            text-align: center;
            padding: 60px 30px;
        }

        .error-icon {
            font-size: 80px;
            color: #ef4444;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 10px;
        }

        .error-text {
            color: #6b7280;
        }

        /* Expected Completion */
        .expected-completion {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .expected-label {
            font-size: 14px;
            color: #92400e;
            margin-bottom: 4px;
        }

        .expected-time {
            font-size: 24px;
            font-weight: 700;
            color: #78350f;
        }

        /* Footer */
        .track-footer {
            text-align: center;
            color: white;
            margin-top: 30px;
            opacity: 0.8;
        }

        @media (max-width: 640px) {
            .search-input-group {
                flex-direction: column;
            }

            .vehicle-info {
                font-size: 22px;
            }

            .timeline-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .track-timeline::before {
                left: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="track-container">
        <div class="track-header">
            <h1><i class="uil uil-car me-2"></i><?php echo $COMPANY_PROFILE->name ?></h1>
            <p>Track your vehicle service status in real-time</p>
        </div>

        <div class="track-search">
            <form method="GET" action="">
                <div class="search-input-group">
                    <input type="text" name="code" class="search-input" 
                        placeholder="Enter Tracking Code" 
                        value="<?php echo htmlspecialchars($trackingCode); ?>"
                        maxlength="20">
                    <button type="submit" class="search-btn">
                        <i class="uil uil-search me-2"></i>Track
                    </button>
                </div>
            </form>
        </div>

        <?php if ($trackingCode && !$serviceData): ?>
        <div class="track-card">
            <div class="error-state">
                <div class="error-icon">
                    <i class="uil uil-exclamation-triangle"></i>
                </div>
                <h2 class="error-title">Tracking Code Not Found</h2>
                <p class="error-text">Please check your tracking code and try again.</p>
            </div>
        </div>
        <?php elseif ($serviceData): ?>
        <?php 
            $SERVICE = new VehicleService($serviceData['id']);
            $jobs = $SERVICE->getServiceJobs();
            $logs = $SERVICE->getServiceLogs();
            $currentStage = $serviceData['current_stage'];
            $stages = VehicleService::STAGES;
        ?>
        <div class="track-card">
            <div class="track-card-header">
                <div class="track-code">Tracking Code: <?php echo htmlspecialchars($serviceData['tracking_code']); ?></div>
                <div class="vehicle-info"><?php echo htmlspecialchars($serviceData['vehicle_no']); ?></div>
                <div class="vehicle-details">
                    <?php echo htmlspecialchars($serviceData['brand_name'] . ' ' . $serviceData['model_name']); ?>
                </div>
                <div class="status-badge <?php echo $serviceData['status']; ?>">
                    <i class="uil uil-<?php 
                        echo $serviceData['status'] == 'delivered' ? 'check-circle' : 
                            ($serviceData['status'] == 'completed' ? 'check' : 
                            ($serviceData['status'] == 'in_progress' ? 'cog' : 'clock')); 
                    ?>"></i>
                    <?php echo ucwords(str_replace('_', ' ', $serviceData['status'])); ?>
                </div>
            </div>

            <div class="track-card-body">
                <?php if ($serviceData['expected_completion']): ?>
                <div class="expected-completion">
                    <div class="expected-label">Expected Completion</div>
                    <div class="expected-time">
                        <?php echo date('M d, Y - h:i A', strtotime($serviceData['expected_completion'])); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-card-icon"><i class="uil uil-user"></i></div>
                        <div class="info-card-label">Customer</div>
                        <div class="info-card-value"><?php echo htmlspecialchars($serviceData['customer_name']); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-icon"><i class="uil uil-calendar-alt"></i></div>
                        <div class="info-card-label">Service Started</div>
                        <div class="info-card-value"><?php echo date('M d, Y', strtotime($serviceData['created_at'])); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-icon"><i class="uil uil-layer-group"></i></div>
                        <div class="info-card-label">Current Stage</div>
                        <div class="info-card-value"><?php echo $stages[$currentStage]['name']; ?></div>
                    </div>
                </div>

                <h3 class="services-title"><i class="uil uil-list-ul me-2"></i>Service Progress</h3>

                <div class="track-timeline">
                    <?php foreach ($stages as $num => $stage): 
                        $stageClass = '';
                        $logEntry = null;
                        
                        foreach ($logs as $log) {
                            if ($log['stage'] == $num) {
                                $logEntry = $log;
                                break;
                            }
                        }
                        
                        if ($num < $currentStage) $stageClass = 'completed';
                        elseif ($num == $currentStage) $stageClass = 'current';
                    ?>
                    <div class="timeline-item <?php echo $stageClass; ?>">
                        <div class="timeline-icon">
                            <i class="uil <?php echo $stage['icon']; ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title"><?php echo $stage['name']; ?></div>
                            <?php if ($logEntry): ?>
                            <div class="timeline-time">
                                <?php echo date('M d, Y - h:i A', strtotime($logEntry['created_at'])); ?>
                            </div>
                            <?php if ($logEntry['notes']): ?>
                            <div class="timeline-notes"><?php echo htmlspecialchars($logEntry['notes']); ?></div>
                            <?php endif; ?>
                            <?php elseif ($num > $currentStage): ?>
                            <div class="timeline-time">Pending</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($jobs)): ?>
                <div class="services-section">
                    <h3 class="services-title"><i class="uil uil-wrench me-2"></i>Services</h3>
                    <?php 
                    $total = 0;
                    foreach ($jobs as $job): 
                        $total += $job['price'];
                    ?>
                    <div class="service-item">
                        <span class="service-name"><?php echo htmlspecialchars($job['service_name']); ?></span>
                        <span class="service-price">LKR <?php echo number_format($job['price'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="service-item" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                        <span class="service-name" style="font-size: 18px;">Total Amount</span>
                        <span class="service-price" style="font-size: 20px;">LKR <?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php else: ?>
        <div class="track-card">
            <div class="track-card-body" style="text-align: center; padding: 60px 30px;">
                <div style="font-size: 80px; color: #667eea; margin-bottom: 20px;">
                    <i class="uil uil-search"></i>
                </div>
                <h2 style="font-size: 24px; font-weight: 700; color: #374151; margin-bottom: 10px;">Enter Your Tracking Code</h2>
                <p style="color: #6b7280;">Enter the tracking code provided by the service center to view your vehicle service status.</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="track-footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $COMPANY_PROFILE->name ?>. All rights reserved.</p>
        </div>
    </div>

</body>

</html>
