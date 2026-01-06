<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Sales Report</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #059669;">📊 Daily Sales Report</h2>
        
        <p>Dear Admin,</p>
        
        <p>Here is your daily sales report for <strong>{{ $date }}</strong>:</p>
        
        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Summary</h3>
            <p><strong>Total Orders:</strong> {{ $totalOrders }}</p>
            <p><strong>Total Revenue:</strong> ${{ number_format($totalRevenue, 2) }}</p>
        </div>
        
        @if(count($salesData) > 0)
        <h3 style="margin-top: 30px;">Products Sold</h3>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background-color: #059669; color: white;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Product Name</th>
                    <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Quantity</th>
                    <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesData as $sale)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $sale['product_name'] }}</td>
                    <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ $sale['quantity'] }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">${{ number_format($sale['revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #6b7280; font-style: italic;">No products were sold today.</p>
        @endif
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 12px;">
            This is an automated daily report from your e-commerce system.
        </p>
    </div>
</body>
</html>
