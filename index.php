<?php

// Simple Gold Price Scraper (No Framework)
// Source: https://msgold.com.my/

date_default_timezone_set('Asia/Kuala_Lumpur');

$url = "https://msgold.com.my/";

// Fetch website content
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0 Safari/537.36',
    CURLOPT_TIMEOUT => 30,
]);

$html = curl_exec($ch);

if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}

curl_close($ch);

// Load HTML
libxml_use_internal_errors(true);

$dom = new DOMDocument();
$dom->loadHTML($html);

libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Find all rows
$rows = $xpath->query("//tr");

$gold999Buy = null;
$gold999Sell = null;

foreach ($rows as $row) {

    $text = trim($row->textContent);

    // Find row containing 999.9 Gold
    if (strpos($text, '999.9 Gold') !== false) {

        $tds = $row->getElementsByTagName('td');

        if ($tds->length >= 3) {

            // Buy Price
            $gold999Buy = trim($tds->item(1)->textContent);

            // Sell Price
            $gold999Sell = trim($tds->item(2)->textContent);
        }

        break;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Gold Price</title>

    <!-- Auto Refresh Every 30 Seconds -->
    <meta http-equiv="refresh" content="30">

    <style>

        body{
            font-family: Arial, sans-serif;
            background:#f5f5f5;
            padding:40px;
        }

        .container{
            max-width:600px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h1{
            text-align:center;
            margin-bottom:30px;
        }

        .price-box{
            display:flex;
            justify-content:space-between;
            margin-bottom:20px;
            padding:20px;
            border-radius:8px;
        }

        .buy{
            background:#e8f5e9;
        }

        .sell{
            background:#ffebee;
        }

        .label{
            font-size:18px;
            font-weight:bold;
        }

        .price{
            font-size:28px;
            font-weight:bold;
        }

        .time{
            text-align:center;
            margin-top:20px;
            color:#777;
        }

    </style>
</head>

<body>

<div class="container">

    <h1>LIVE GOLD PRICE</h1>

    <?php if($gold999Buy && $gold999Sell): ?>

        <div class="price-box buy">
            <div class="label">999.9 Gold Buy</div>
            <div class="price">
                RM <?= number_format((float)$gold999Buy, 2) ?>
            </div>
        </div>

        <div class="price-box sell">
            <div class="label">999.9 Gold Sell</div>
            <div class="price">
                RM <?= number_format((float)$gold999Sell, 2) ?>
            </div>
        </div>

    <?php else: ?>

        <h3>Unable to scrape gold price.</h3>

    <?php endif; ?>

    <div class="time">
        Last Updated:
        <?= date('d M Y h:i:s A') ?>
    </div>

</div>

</body>
</html>
