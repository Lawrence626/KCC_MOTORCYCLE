<?php

namespace App\Console\Commands;

use App\Models\ProductCatalog;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Console\Command;

class GenerateProductQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for products that do not have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = ProductCatalog::whereNull('qr_code_path')->get();
        
        $this->info("Found {$products->count()} products without QR codes.");
        
        foreach ($products as $product) {
            try {
                $filename = "qrcodes/{$product->sku}.png";
                $path = storage_path('app/public/' . $filename);
                
                // Ensure directory exists
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }

                $qrCode = new QrCode($product->sku);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $result->saveToFile($path);

                $product->update(['qr_code_path' => $filename]);
                
                $this->info("Generated QR code for: {$product->product_name} ({$product->sku})");
            } catch (\Exception $e) {
                $this->error("Failed to generate QR code for {$product->product_name}: {$e->getMessage()}");
            }
        }
        
        $this->info('QR code generation completed.');
        
        return 0;
    }
}
