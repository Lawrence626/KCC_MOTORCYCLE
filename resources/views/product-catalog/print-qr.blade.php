<!DOCTYPE html>
<html>
<head>
    <title>QR Code - {{ $productCatalog->product_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        .qr-container {
            text-align: center;
            border: 2px solid #000;
            padding: 20px;
            margin: 20px 0;
        }
        .qr-image {
            width: 200px;
            height: 200px;
            margin: 0 auto 15px;
        }
        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .sku {
            font-size: 14px;
            font-family: monospace;
            color: #666;
        }
        .brand {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        @if($productCatalog->qr_code_path)
            <img src="{{ asset('storage/' . $productCatalog->qr_code_path) }}" alt="QR Code" class="qr-image">
        @endif
        <div class="product-name">{{ $productCatalog->product_name }}</div>
        <div class="sku">{{ $productCatalog->sku }}</div>
        <div class="brand">{{ $productCatalog->brand }}</div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
