<?php

namespace App\Console\Commands;

use App\Jobs\NotifyLowStock;
use App\Models\Product;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-low';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all products for low stock and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = Product::getLowStockThreshold();
        // Get all products with low stock that haven't been notified in the last 24 hours
        $lowStockProducts = Product::where('stock_quantity', '<=', $threshold)
            ->where(function ($query) {
                $query->whereNull('low_stock_notified_at')
                    ->orWhere('low_stock_notified_at', '<', now()->subDay());
            })
            ->get();
        if ($lowStockProducts->isEmpty()) {
            $this->info('No products with low stock found (or already notified recently).');
            return 0;
        }

        $count = 0;
        foreach ($lowStockProducts as $product) {
            // Dispatch job to send notification (job will mark as notified after sending)
            NotifyLowStock::dispatch($product);
            $count++;
            $this->line("Low stock notification queued for: {$product->name} (Stock: {$product->stock_quantity})");
        }

        $this->info("Queued {$count} low stock notification(s).");
        return 0;
    }
}
