<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReport;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily sales report to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->endOfDay();

        // Get all orders from today
        $orders = Order::whereBetween('created_at', [$today, $tomorrow])->get();

        if ($orders->isEmpty()) {
            $this->info('No sales today. Report not sent.');
            return 0;
        }

        // Get all order items from today's orders
        $orderItems = OrderItem::whereIn('order_id', $orders->pluck('id'))
            ->with('product')
            ->get();

        // Group by product and calculate totals
        $salesData = [];
        foreach ($orderItems as $item) {
            $productId = $item->product_id;
            $productName = $item->product->name;

            if (!isset($salesData[$productId])) {
                $salesData[$productId] = [
                    'product_name' => $productName,
                    'quantity' => 0,
                    'revenue' => 0,
                ];
            }

            $salesData[$productId]['quantity'] += $item->quantity;
            $salesData[$productId]['revenue'] += $item->price * $item->quantity;
        }

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();
        $date = now()->format('Y-m-d');

        $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL'));

        if (!$adminEmail) {
            $this->error('Admin email not configured. Please set ADMIN_EMAIL in .env');
            return 1;
        }

        Mail::to($adminEmail)->send(
            new DailySalesReport(
                array_values($salesData),
                $totalRevenue,
                $totalOrders,
                $date
            )
        );

        $this->info("Daily sales report sent to {$adminEmail}");
        return 0;
    }
}
