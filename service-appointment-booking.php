<!doctype html>
<?php
include 'class/include.php';

$COMPANY_PROFILE = new CompanyProfile(1);

// Get all brands and service types
$BRAND = new VehicleBrand();
$brands = $BRAND->all();

$SERVICE_TYPE = new ServiceType();
$serviceTypes = $SERVICE_TYPE->all();
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book Service Appointment | <?php echo $COMPANY_PROFILE->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Book your vehicle service appointment online">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            padding: 24px 16px;
            color: #0f172a;
        }

        .booking-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .booking-header {
            text-align: center;
            color: white;
            margin-bottom: 32px;
        }

        .booking-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.03em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .booking-header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .booking-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .booking-card-header {
            background: #f8fafc;
            padding: 24px 32px;
            border-bottom: 1px solid #e2e8f0;
        }

        .steps-indicator {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 40px;
            right: 40px;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: #94a3b8;
            transition: all 0.3s ease;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .step.active .step-number {
            background: #667eea;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .step.completed .step-number {
            background: #10b981;
            color: white;
        }

        .step-label {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
        }

        .step.active .step-label,
        .step.completed .step-label {
            color: #374151;
        }

        .booking-card-body {
            padding: 32px;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .section-subtitle {
            color: #64748b;
            margin-bottom: 24px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-label .required {
            color: #ef4444;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        /* Service Selection */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }

        .service-checkbox {
            display: none;
        }

        .service-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .service-card:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .service-checkbox:checked + .service-card {
            border-color: #667eea;
            background: #f0f4ff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .service-checkbox:checked + .service-card::after {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .service-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
            font-size: 15px;
        }

        .service-price {
            color: #10b981;
            font-weight: 700;
            font-size: 16px;
        }

        /* Time Slots */
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }

        .time-slot {
            display: none;
        }

        .time-slot-label {
            display: block;
            padding: 12px 8px;
            text-align: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            transition: all 0.2s ease;
        }

        .time-slot-label:hover {
            border-color: #667eea;
        }

        .time-slot:checked + .time-slot-label {
            background: #667eea;
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline {
            background: white;
            border: 2px solid #e2e8f0;
            color: #374151;
        }

        .btn-outline:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .btn-success {
            background: #10b981;
            color: white;
            flex: 1;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        /* Total Estimate */
        .total-estimate {
            background: #f0fdf4;
            border-radius: 14px;
            padding: 20px;
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-weight: 600;
            color: #374151;
            font-size: 16px;
        }

        .total-amount {
            font-size: 28px;
            font-weight: 800;
            color: #059669;
        }

        /* Success State */
        .success-state {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 50px;
            color: white;
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
        }

        .success-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .success-subtitle {
            color: #64748b;
            margin-bottom: 24px;
        }

        .booking-code-display {
            background: #f0f4ff;
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
        }

        .booking-code-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .booking-code {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
            letter-spacing: 4px;
        }

        .share-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .share-btn {
            padding: 12px 24px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background: white;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .share-btn:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .share-btn.whatsapp:hover {
            border-color: #25d366;
            color: #25d366;
        }

        /* Summary */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
        }

        .summary-label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }

        .services-summary {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
        }

        .services-summary-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .service-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .service-summary-item:last-child {
            border-bottom: none;
        }

        /* Footer */
        .booking-footer {
            text-align: center;
            color: white;
            margin-top: 32px;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .booking-header h1 {
                font-size: 1.75rem;
            }
            
            .booking-card-body {
                padding: 24px 20px;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }
            
            .booking-code {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>

    <div class="booking-container">
        <div class="booking-header">
            <h1><i class="uil uil-calendar-alt me-2"></i><?php echo $COMPANY_PROFILE->name ?></h1>
            <p>Book your vehicle service appointment online</p>
        </div>

        <div class="booking-card">
            <div class="booking-card-header">
                <div class="steps-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Your Details</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Vehicle</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Services</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-label">Schedule</div>
                    </div>
                </div>
            </div>

            <div class="booking-card-body">
                <form id="booking-form" autocomplete="off">

                    <!-- Step 1: Customer Details -->
                    <div class="form-section active" data-section="1">
                        <h2 class="section-title">Your Details</h2>
                        <p class="section-subtitle">Please provide your contact information</p>
                        
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" 
                                placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="text" id="customer_phone" name="customer_phone" class="form-control" 
                                        placeholder="+94 7X XXX XXXX" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" id="customer_email" name="customer_email" class="form-control" 
                                        placeholder="your@email.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                Continue <i class="uil uil-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Vehicle Details -->
                    <div class="form-section" data-section="2">
                        <h2 class="section-title">Vehicle Information</h2>
                        <p class="section-subtitle">Tell us about your vehicle</p>
                        
                        <div class="form-group">
                            <label class="form-label">Vehicle Number <span class="required">*</span></label>
                            <input type="text" id="vehicle_no" name="vehicle_no" class="form-control" 
                                placeholder="e.g., ABC-1234" required style="text-transform: uppercase;">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Vehicle Brand <span class="required">*</span></label>
                                    <select id="vehicle_brand_id" name="vehicle_brand_id" class="form-control" required>
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $brand): ?>
                                        <option value="<?php echo $brand['id']; ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Vehicle Model <span class="required">*</span></label>
                                    <select id="vehicle_model_id" name="vehicle_model_id" class="form-control" required>
                                        <option value="">Select Model</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline" onclick="prevStep(1)">
                                <i class="uil uil-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                Continue <i class="uil uil-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Service Selection -->
                    <div class="form-section" data-section="3">
                        <h2 class="section-title">Select Services</h2>
                        <p class="section-subtitle">Choose the services you need (select one or more)</p>
                        
                        <div class="services-grid">
                            <?php foreach ($serviceTypes as $service): ?>
                            <div>
                                <input type="checkbox" class="service-checkbox" name="service_type_ids[]" 
                                    value="<?php echo $service['id']; ?>" id="service_<?php echo $service['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($service['name']); ?>"
                                    data-price="<?php echo $service['price']; ?>">
                                <label class="service-card" for="service_<?php echo $service['id']; ?>">
                                    <div class="service-name"><?php echo htmlspecialchars($service['name']); ?></div>
                                    <div class="service-price">LKR <?php echo number_format($service['price'], 2); ?></div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="total-estimate">
                            <span class="total-label">Estimated Total</span>
                            <span class="total-amount">LKR <span id="total-estimate">0.00</span></span>
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline" onclick="prevStep(2)">
                                <i class="uil uil-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                                Continue <i class="uil uil-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Schedule -->
                    <div class="form-section" data-section="4">
                        <h2 class="section-title">Choose Date & Time</h2>
                        <p class="section-subtitle">Select your preferred appointment slot</p>
                        
                        <div class="form-group">
                            <label class="form-label">Preferred Date <span class="required">*</span></label>
                            <input type="date" id="preferred_date" name="preferred_date" class="form-control" 
                                required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Preferred Time <span class="required">*</span></label>
                            <div class="time-slots" id="time-slots">
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="08:00" id="time_0800">
                                    <label class="time-slot-label" for="time_0800">8:00 AM</label>
                                </div>
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="09:00" id="time_0900">
                                    <label class="time-slot-label" for="time_0900">9:00 AM</label>
                                </div>
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="10:00" id="time_1000">
                                    <label class="time-slot-label" for="time_1000">10:00 AM</label>
                                </div>
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="11:00" id="time_1100">
                                    <label class="time-slot-label" for="time_1100">11:00 AM</label>
                                </div>
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="14:00" id="time_1400">
                                    <label class="time-slot-label" for="time_1400">2:00 PM</label>
                                </div>
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="15:00" id="time_1500">
                                    <label class="time-slot-label" for="time_1500">3:00 PM</label>
                                </div>
                                <div>
                                    <input type="radio" class="time-slot" name="preferred_time" value="16:00" id="time_1600">
                                    <label class="time-slot-label" for="time_1600">4:00 PM</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3" 
                                placeholder="Any special requests or requirements..."></textarea>
                        </div>
                        
                        <!-- Summary -->
                        <div class="services-summary mt-4">
                            <div class="services-summary-title"><i class="uil uil-file-check-alt me-2"></i>Booking Summary</div>
                            <div id="booking-summary"></div>
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline" onclick="prevStep(3)">
                                <i class="uil uil-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-success" onclick="submitBooking()">
                                <i class="uil uil-check"></i> Confirm Booking
                            </button>
                        </div>
                    </div>

                    <!-- Success State -->
                    <div class="form-section" data-section="success">
                        <div class="success-state">
                            <div class="success-icon">
                                <i class="uil uil-check"></i>
                            </div>
                            <h2 class="success-title">Booking Confirmed!</h2>
                            <p class="success-subtitle">Your appointment has been successfully scheduled</p>
                            
                            <div class="booking-code-display">
                                <div class="booking-code-label">Your Booking Code</div>
                                <div class="booking-code" id="booking-code-display">APT******</div>
                            </div>
                            
                            <p class="text-muted">Please save this code to check your appointment status</p>
                            
                            <div class="share-buttons">
                                <button type="button" class="share-btn" onclick="copyBookingCode()">
                                    <i class="uil uil-copy"></i> Copy Code
                                </button>
                                <button type="button" class="share-btn whatsapp" onclick="shareWhatsApp()">
                                    <i class="uil uil-whatsapp"></i> Share on WhatsApp
                                </button>
                            </div>
                            
                            <div class="mt-4">
                                <a href="service-appointment-booking.php" class="btn btn-outline">
                                    <i class="uil uil-plus"></i> Book Another Appointment
                                </a>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    
    <script>
        let currentStep = 1;
        let bookingCode = '';

        $(document).ready(function() {
            // Initialize phone with prefix
            $('#customer_phone').val('+94');
            
            // Phone number formatting
            $('#customer_phone').on('input', function() {
                let v = $(this).val();
                if (!v.startsWith('+94')) {
                    v = '+94' + v.replace(/[^0-9]/g, '');
                } else {
                    v = '+94' + v.substring(3).replace(/[^0-9]/g, '');
                }
                v = v.slice(0, 12); // +94 + 9 digits
                $(this).val(v);
            });

            // Vehicle number uppercase
            $('#vehicle_no').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            // Set date input constraints
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const maxDate = new Date(today);
            maxDate.setDate(maxDate.getDate() + 30);
            
            const formatDate = (d) => d.toISOString().split('T')[0];
            $('#preferred_date').attr('min', formatDate(tomorrow));
            $('#preferred_date').attr('max', formatDate(maxDate));

            // Brand change - load models
            $('#vehicle_brand_id').change(function() {
                const brandId = $(this).val();
                loadModelsByBrand(brandId);
            });

            // Service selection change
            $('.service-checkbox').change(function() {
                updateTotalEstimate();
            });
        });

        function loadModelsByBrand(brandId) {
            if (!brandId) {
                $('#vehicle_model_id').html('<option value="">Select Model</option>');
                return;
            }

            $.get('ajax/php/service-appointment.php', { get_models_by_brand: true, brand_id: brandId }, function(response) {
                let options = '<option value="">Select Model</option>';
                if (response.data) {
                    response.data.forEach(model => {
                        options += `<option value="${model.id}">${model.name}</option>`;
                    });
                }
                $('#vehicle_model_id').html(options);
            }, 'json');
        }

        function updateTotalEstimate() {
            let total = 0;
            $('.service-checkbox:checked').each(function() {
                total += parseFloat($(this).data('price')) || 0;
            });
            $('#total-estimate').text(total.toFixed(2));
        }

        function validateStep(step) {
            if (step === 1) {
                if (!$('#customer_name').val().trim()) {
                    showError('Please enter your name');
                    return false;
                }
                const phone = $('#customer_phone').val();
                if (!phone || phone.length < 12) {
                    showError('Please enter a valid phone number');
                    return false;
                }
            } else if (step === 2) {
                if (!$('#vehicle_no').val().trim()) {
                    showError('Please enter vehicle number');
                    return false;
                }
                if (!$('#vehicle_brand_id').val()) {
                    showError('Please select vehicle brand');
                    return false;
                }
                if (!$('#vehicle_model_id').val()) {
                    showError('Please select vehicle model');
                    return false;
                }
            } else if (step === 3) {
                if ($('.service-checkbox:checked').length === 0) {
                    showError('Please select at least one service');
                    return false;
                }
            } else if (step === 4) {
                if (!$('#preferred_date').val()) {
                    showError('Please select a date');
                    return false;
                }
                if (!$('input[name="preferred_time"]:checked').val()) {
                    showError('Please select a time slot');
                    return false;
                }
            }
            return true;
        }

        function nextStep(step) {
            if (!validateStep(currentStep)) return;

            // Update step indicators
            $(`.step[data-step="${currentStep}"]`).removeClass('active').addClass('completed');
            $(`.step[data-step="${step}"]`).addClass('active');

            // Show new section
            $(`.form-section[data-section="${currentStep}"]`).removeClass('active');
            $(`.form-section[data-section="${step}"]`).addClass('active');

            currentStep = step;

            // Update summary on step 4
            if (step === 4) {
                updateSummary();
            }

            // Scroll to top
            $('html, body').animate({ scrollTop: 0 }, 300);
        }

        function prevStep(step) {
            $(`.step[data-step="${currentStep}"]`).removeClass('active');
            $(`.step[data-step="${step}"]`).removeClass('completed').addClass('active');

            $(`.form-section[data-section="${currentStep}"]`).removeClass('active');
            $(`.form-section[data-section="${step}"]`).addClass('active');

            currentStep = step;
        }

        function updateSummary() {
            let html = '';
            
            // Customer info
            html += `<div class="service-summary-item">
                <span>Customer</span>
                <span><strong>${$('#customer_name').val()}</strong></span>
            </div>`;
            
            html += `<div class="service-summary-item">
                <span>Phone</span>
                <span>${$('#customer_phone').val()}</span>
            </div>`;
            
            html += `<div class="service-summary-item">
                <span>Vehicle</span>
                <span><strong>${$('#vehicle_no').val()}</strong> - ${$('#vehicle_brand_id option:selected').text()} ${$('#vehicle_model_id option:selected').text()}</span>
            </div>`;
            
            // Selected services
            let total = 0;
            $('.service-checkbox:checked').each(function() {
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price'));
                total += price;
                html += `<div class="service-summary-item">
                    <span>${name}</span>
                    <span>LKR ${price.toFixed(2)}</span>
                </div>`;
            });
            
            html += `<div class="service-summary-item" style="background: #f0fdf4; margin: 8px -16px -16px; padding: 12px 16px; border-radius: 0 0 8px 8px;">
                <span><strong>Total Estimate</strong></span>
                <span style="color: #059669; font-weight: 700;">LKR ${total.toFixed(2)}</span>
            </div>`;
            
            $('#booking-summary').html(html);
        }

        function submitBooking() {
            if (!validateStep(4)) return;

            // Show loading
            const submitBtn = $('.btn-success');
            submitBtn.prop('disabled', true).html('<i class="uil uil-spinner-alt uil-spin"></i> Processing...');

            const formData = new FormData($('#booking-form')[0]);
            formData.append('create', true);

            $.ajax({
                url: 'ajax/php/service-appointment.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        bookingCode = response.booking_code;
                        $('#booking-code-display').text(bookingCode);
                        
                        // Hide steps
                        $('.steps-indicator').hide();
                        
                        // Show success
                        $(`.form-section[data-section="4"]`).removeClass('active');
                        $(`.form-section[data-section="success"]`).addClass('active');
                    } else {
                        showError(response.message || 'Failed to create booking');
                        submitBtn.prop('disabled', false).html('<i class="uil uil-check"></i> Confirm Booking');
                    }
                },
                error: function() {
                    showError('An error occurred. Please try again.');
                    submitBtn.prop('disabled', false).html('<i class="uil uil-check"></i> Confirm Booking');
                }
            });
        }

        function copyBookingCode() {
            navigator.clipboard.writeText(bookingCode).then(() => {
                alert('Booking code copied!');
            });
        }

        function shareWhatsApp() {
            const phone = $('#customer_phone').val().replace(/\D/g, '');
            const message = `🚗 Vehicle Service Appointment Booked!\n\nBooking Code: ${bookingCode}\nVehicle: ${$('#vehicle_no').val()}\nDate: ${$('#preferred_date').val()}\nTime: ${$('input[name="preferred_time"]:checked + label').text()}\n\nPlease arrive 10 minutes before your scheduled time.`;
            
            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
        }

        function showError(message) {
            alert(message); // For simplicity, using alert. You can replace with a nicer modal
        }
    </script>

</body>

</html>
