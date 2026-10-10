<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private const PRODUCT_IMAGES = [
        'Controllers' => [
            'images/products/controller.jpg',
            'images/products/catalog/controller-02.jpg',
            'images/products/catalog/controller-03.jpg',
            'images/products/catalog/controller-04.jpg',
            'images/products/catalog/controller-05.jpg',
            'images/products/catalog/controller-06.jpg',
            'images/products/catalog/controller-07.jpg',
            'images/products/catalog/controller-08.jpg',
        ],
        'Headsets' => [
            'images/products/headset.jpg',
            'images/products/catalog/headset-02.jpg',
            'images/products/catalog/headset-03.jpg',
            'images/products/catalog/headset-04.jpg',
            'images/products/catalog/headset-05.jpg',
            'images/products/catalog/headset-06.jpg',
            'images/products/catalog/headset-07.jpg',
            'images/products/catalog/headset-08.jpg',
        ],
        'Keyboards' => [
            'images/products/keyboard.jpg',
            'images/products/catalog/keyboard-02.jpg',
            'images/products/catalog/keyboard-03.jpg',
            'images/products/catalog/keyboard-04.jpg',
            'images/products/catalog/keyboard-05.jpg',
            'images/products/catalog/keyboard-06.jpg',
            'images/products/catalog/keyboard-07.jpg',
            'images/products/catalog/keyboard-08.jpg',
        ],
        'Mouse' => [
            'images/products/mouse.jpg',
            'images/products/catalog/mouse-02.jpg',
            'images/products/catalog/mouse-03.jpg',
            'images/products/catalog/mouse-04.jpg',
            'images/products/catalog/mouse-05.jpg',
            'images/products/catalog/mouse-06.jpg',
            'images/products/catalog/mouse-07.jpg',
            'images/products/catalog/mouse-08.jpg',
        ],
        'Processors' => [
            'images/products/cpu.jpg',
            'images/products/catalog/cpu-02.jpg',
            'images/products/catalog/cpu-03.jpg',
            'images/products/catalog/cpu-04.jpg',
        ],
        'Graphics Cards' => [
            'images/products/gpu.jpg',
            'images/products/catalog/gpu-02.jpg',
            'images/products/catalog/gpu-03.jpg',
            'images/products/catalog/gpu-04.jpg',
        ],
        'Memory' => [
            'images/products/ram.jpg',
            'images/products/catalog/ram-02.jpg',
            'images/products/catalog/ram-03.jpg',
            'images/products/catalog/ram-04.jpg',
        ],
        'Storage' => [
            'images/products/ssd.jpg',
            'images/products/catalog/ssd-02.jpg',
            'images/products/catalog/ssd-03.jpg',
            'images/products/catalog/ssd-04.jpg',
        ],
        'Monitors' => [
            'images/products/monitor.jpg',
            'images/products/catalog/monitor-02.jpg',
            'images/products/catalog/monitor-03.jpg',
            'images/products/catalog/monitor-04.jpg',
        ],
    ];

    public function run(): void
    {
        $products = [
            ['Pro Wireless Controller', 'Nexa', 'Controllers', 2499, 14, 820],
            ['Arcade Pad Mini', 'Voltix', 'Controllers', 1299, 4, 310],
            ['7.1 Surround Headset', 'Sonar', 'Headsets', 3299, 9, 540],
            ['Lite Gaming Headset', 'Voltix', 'Headsets', 1199, 22, 1200],
            ['Mechanical Keyboard TKL', 'Keyra', 'Keyboards', 2899, 3, 430],
            ['RGB Membrane Keyboard', 'Nexa', 'Keyboards', 899, 0, 960],
            ['Ultralight Mouse', 'Keyra', 'Mouse', 1799, 12, 670],
            ['Ergo Gaming Mouse', 'Sonar', 'Mouse', 1499, 18, 390],
            ['Core Wired Controller', 'Nexa', 'Controllers', 999, 28, 540],
            ['Pulse Pro Wireless Controller', 'Voltix', 'Controllers', 3499, 9, 264],
            ['Nova Bluetooth Controller', 'Sonar', 'Controllers', 2199, 16, 381],
            ['Retro USB Gamepad', 'Voltix', 'Controllers', 749, 31, 692],
            ['Dual Grip Pro Pad', 'Keyra', 'Controllers', 2799, 7, 183],
            ['Trigger Elite Controller', 'Nexa', 'Controllers', 4299, 5, 112],
            ['StudioX Wireless Headset', 'Sonar', 'Headsets', 4499, 8, 194],
            ['Bassline Stereo Headset', 'Nexa', 'Headsets', 1599, 13, 405],
            ['Cloud Comfort Headset', 'Keyra', 'Headsets', 2299, 10, 276],
            ['Pulse 7.1 USB Headset', 'Voltix', 'Headsets', 1899, 17, 362],
            ['AirLite Bluetooth Headset', 'Sonar', 'Headsets', 2699, 8, 155],
            ['StreamMic Gaming Headset', 'Nexa', 'Headsets', 3199, 6, 97],
            ['Storm Mechanical Full Size', 'Keyra', 'Keyboards', 3699, 7, 218],
            ['Compact 60 Mechanical Keyboard', 'Nexa', 'Keyboards', 2599, 12, 344],
            ['Prism RGB TKL Keyboard', 'Voltix', 'Keyboards', 2199, 18, 485],
            ['Silent Switch Keyboard', 'Sonar', 'Keyboards', 3299, 6, 128],
            ['RapidFire Gaming Keyboard', 'Keyra', 'Keyboards', 4899, 4, 83],
            ['Office-to-Game Keyboard', 'Nexa', 'Keyboards', 1299, 20, 510],
            ['Precision Wireless Mouse', 'Nexa', 'Mouse', 1399, 15, 432],
            ['TriMode Gaming Mouse', 'Voltix', 'Mouse', 2499, 9, 207],
            ['Silent Click Mouse', 'Sonar', 'Mouse', 899, 22, 516],
            ['Phantom RGB Mouse', 'Keyra', 'Mouse', 1899, 11, 301],
            ['MacroMaster MMO Mouse', 'Nexa', 'Mouse', 2799, 8, 147],
            ['Swift Mini Mouse', 'Voltix', 'Mouse', 1099, 19, 378],
            ['Core i5 Gaming Processor', 'Nexa', 'Processors', 8999, 8, 88],
            ['Ryzen 5 Performance Processor', 'Voltix', 'Processors', 11499, 6, 102],
            ['8-Core Creator Processor', 'Keyra', 'Processors', 18999, 4, 39],
            ['Core i7 Enthusiast Processor', 'Sonar', 'Processors', 21999, 5, 57],
            ['RTX 4060 Dual-Fan Graphics Card', 'Voltix', 'Graphics Cards', 23999, 5, 73],
            ['RX 7600 Gaming Graphics Card', 'Nexa', 'Graphics Cards', 21999, 7, 62],
            ['RTX 4070 Super Graphics Card', 'Keyra', 'Graphics Cards', 35999, 3, 34],
            ['RTX 3060 12GB Graphics Card', 'Sonar', 'Graphics Cards', 18999, 6, 91],
            ['DDR4 16GB Memory Kit', 'Nexa', 'Memory', 2199, 20, 240],
            ['RGB DDR5 32GB Memory Kit', 'Keyra', 'Memory', 5699, 12, 134],
            ['DDR5 6000MHz 32GB Memory Kit', 'Voltix', 'Memory', 4799, 10, 106],
            ['RGB DDR5 64GB Memory Kit', 'Sonar', 'Memory', 8999, 6, 47],
            ['NVMe SSD 1TB', 'Nexa', 'Storage', 3899, 16, 207],
            ['NVMe SSD 2TB', 'Sonar', 'Storage', 6999, 9, 96],
            ['SATA SSD 1TB', 'Keyra', 'Storage', 3599, 13, 118],
            ['NVMe SSD 500GB', 'Voltix', 'Storage', 1999, 18, 281],
            ['27-inch QHD 165Hz Monitor', 'Voltix', 'Monitors', 12999, 6, 74],
            ['24-inch Full HD 144Hz Monitor', 'Nexa', 'Monitors', 8999, 9, 151],
            ['32-inch Curved 165Hz Monitor', 'Sonar', 'Monitors', 17999, 4, 48],
            ['27-inch 4K IPS Monitor', 'Keyra', 'Monitors', 21999, 3, 31],
        ];

        $imageIndexes = [];
        foreach ($products as [$name, $brand, $category, $price, $stock, $sold]) {
            $imageIndex = $imageIndexes[$category] ?? 0;
            $imagePath = self::PRODUCT_IMAGES[$category][$imageIndex] ?? throw new \LogicException('Missing product image mapping for '.$name);
            $imageIndexes[$category] = $imageIndex + 1;
            $product = Product::firstOrCreate(['name' => $name], compact('brand', 'category', 'price', 'stock', 'sold') + ['image_path' => $imagePath]);
            if (! str_starts_with((string) $product->image_path, 'storage/')) {
                $product->update(['image_path' => $imagePath]);
            }
            $product->images()->delete();
            $gallery = [$imagePath];
            if ($name === 'Pro Wireless Controller') {
                $gallery = [
                    $imagePath,
                    'images/products/controller-angle.jpg',
                    'images/products/controller-collection.jpg',
                    'images/products/controller-closeup.jpg',
                ];
            }

            foreach ($gallery as $index => $galleryPath) {
                $product->images()->updateOrCreate(
                    ['image_path' => $galleryPath],
                    ['alt_text' => $product->name.' product view '.($index + 1), 'sort_order' => $index],
                );
            }

            if ($name === 'Pro Wireless Controller') {
                $examples = [
                    ['Mika R.', 5, 'Comfortable for long sessions', 'Paired quickly and the grip feels comfortable. The buttons have a nice, firm response.'],
                    ['Jules T.', 4, 'Good everyday controller', 'Works well on my PC and the battery lasts through a few evenings of play.'],
                    ['Sam C.', 5, 'Solid build', 'Feels sturdy and the sticks are smooth. Good value for the price.'],
                ];
                foreach ($examples as [$reviewer, $rating, $title, $body]) {
                    $product->reviews()->updateOrCreate(
                        ['user_id' => null, 'reviewer_name' => $reviewer],
                        ['rating' => $rating, 'title' => $title, 'body' => $body, 'is_sample' => true],
                    );
                }
            }
        }
    }
}