<?php
$url = 'https://ecommerce-production-3bc1.up.railway.app/api/products';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
if (isset($data['data'])) {
    foreach ($data['data'] as $product) {
        if ($product['name'] === 'MMMM' || $product['name'] === 'kmk') {
            echo "Product: {$product['name']}\n";
            echo "Image URL: {$product['image_url']}\n";
            echo "Full Image URL: {$product['full_image_url']}\n";
        }
    }
} else {
    echo "Failed to fetch products\n";
}
