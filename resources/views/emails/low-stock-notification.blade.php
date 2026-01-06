<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Stock Alert</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc2626;">⚠️ Low Stock Alert</h2>
        
        <p>Dear Admin,</p>
        
        <p>The following product is running low on stock:</p>
        
        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0;">{{ $product->name }}</h3>
            <p><strong>Current Stock:</strong> {{ $product->stock_quantity }} units</p>
            <p><strong>Low Stock Threshold:</strong> {{ \App\Models\Product::getLowStockThreshold() }} units</p>
            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
        </div>
        
        <p>Please consider restocking this product soon.</p>
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 12px;">
            This is an automated notification from your e-commerce system.
        </p>
    </div>
</body>
</html>
