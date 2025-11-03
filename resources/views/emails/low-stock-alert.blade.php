<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Low Stock Alert</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333333;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Alert badge */
        .alert-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .out-of-stock-badge {
            background-color: #721c24;
        }

        /* Main content */
        .content {
            padding: 30px 20px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #495057;
        }

        /* Product card */
        .product-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            margin: 25px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .product-info {
            padding: 20px;
        }

        .product-name {
            font-size: 22px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 12px;
        }

        .product-description {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .price-tag {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 18px;
        }

        .stock-info {
            text-align: right;
        }

        .stock-number {
            font-size: 24px;
            font-weight: 700;
            color: #dc3545;
        }

        .stock-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Alert message */
        .alert-message {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #ffc107;
        }

        .alert-message.critical {
            background-color: #f8d7da;
            border-color: #f1aeb5;
            border-left-color: #dc3545;
        }

        .alert-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .alert-text {
            color: #495057;
        }

        /* Action buttons */
        .action-section {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 0 10px 10px 0;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        /* Footer */
        .footer {
            background-color: #f8f9fa;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .footer-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .company-logo {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                width: 100% !important;
            }

            .header {
                padding: 20px 15px;
            }

            .header h1 {
                font-size: 24px;
            }

            .content {
                padding: 20px 15px;
            }

            .product-details {
                flex-direction: column;
                align-items: flex-start;
            }

            .stock-info {
                text-align: left;
                width: 100%;
            }

            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="alert-badge {{ $isOutOfStock ? 'out-of-stock-badge' : '' }}">
                {{ $isOutOfStock ? 'OUT OF STOCK' : 'LOW STOCK ALERT' }}
            </div>
            <h1>Inventory Alert</h1>
            <div class="subtitle">Immediate attention required for your product inventory</div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="greeting">
                Hello Administrator,
            </div>

            <p>We're writing to inform you that one of your products has {{ $isOutOfStock ? 'run out of stock' : 'reached a critically low stock level' }}. Please review the details below and take appropriate action.</p>

            <!-- Product Card -->
            <div class="product-card">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                @else
                    <img src="{{ asset('storage/SampleProductImage.png') }}" alt="{{ $product->name }}" class="product-image">
                @endif
                
                <div class="product-info">
                    <h2 class="product-name">{{ $product->name }}</h2>
                    
                    @if($product->description)
                        <p class="product-description">{{ Str::limit($product->description, 120) }}</p>
                    @endif

                    <div class="product-details">
                        <div class="price-tag">
                            ${{ number_format($product->price, 2) }}
                        </div>
                        
                        <div class="stock-info">
                            <div class="stock-number">{{ $stockLevel }}</div>
                            <div class="stock-label">Units Remaining</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Message -->
            <div class="alert-message {{ $isOutOfStock ? 'critical' : '' }}">
                <div class="alert-title">
                    {{ $isOutOfStock ? 'Critical: Product Out of Stock' : 'Warning: Low Stock Level' }}
                </div>
                <div class="alert-text">
                    @if($isOutOfStock)
                        This product is completely out of stock. New orders cannot be fulfilled until you restock this item. Consider updating your product availability status or removing it from active listings.
                    @else
                        This product has only {{ $stockLevel }} {{ Str::plural('unit', $stockLevel) }} left in stock. We recommend restocking soon to avoid running out and disappointing customers.
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-section">
                <a href="{{ $productUrl }}" class="btn btn-primary">View Product Details</a>
                <a href="{{ $adminDashboardUrl }}" class="btn btn-secondary">Manage Inventory</a>
            </div>

            <p><strong>Product ID:</strong> #{{ $product->id }}</p>
            <p><strong>Alert Generated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">
            
            <p style="color: #6c757d; font-size: 14px;">
                This is an automated alert from your inventory management system. Please take appropriate action to maintain optimal stock levels and ensure customer satisfaction.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="company-logo">E-Commerce Store</div>
            <div class="footer-text">
                Simple Ecommerce System<br>
                © {{ date('Y') }} All rights reserved.
            </div>
            <div class="footer-text" style="font-size: 12px; margin-top: 15px;">
                You're receiving this email because you're an administrator of our e-commerce platform.
                <br>If you have questions, please contact your system administrator.
            </div>
        </div>
    </div>
</body>
</html>