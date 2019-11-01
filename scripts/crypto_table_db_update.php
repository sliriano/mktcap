<?php
// connect to database
$conn = mysqli_connect('ip_address', 'user', 'password', 'market_data');

// check connection
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}

for ($i = 0; $i < 10; $i++) {
    $api_url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=250&page='.strval($i+1).'&sparkline=true&price_change_percentage=24h';
    $crypto_data = file_get_contents($api_url);
    $crypto_data = json_decode($crypto_data, true);
    for ($coin_index = 0; $coin_index < count($crypto_data); $coin_index++) {
        $name = $crypto_data[$coin_index]['name'];
        $coin_id = $crypto_data[$coin_index]['id'];
        $ticker = strtoupper($crypto_data[$coin_index]['symbol']);
        $marketcap = $crypto_data[$coin_index]['market_cap'];
        $price = $crypto_data[$coin_index]['current_price'];
        $volume = $crypto_data[$coin_index]['total_volume'];
        $supply = $crypto_data[$coin_index]['circulating_supply'];
        $change24h = $crypto_data[$coin_index]['price_change_percentage_24h'];
        $url = $crypto_data[$coin_index]['image'];
        // attempt to insert data to database
        $sql = "REPLACE INTO crypto (rank, id, cryptocurrency_name, ticker, marketcap, price, volume, supply, change24h, image_url) VALUES (null, '$coin_id', '$name', '$ticker', $marketcap, $price, $volume, $supply, $change24h, '$url')";
        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }   
    }
}
mysqli_close($conn);

?>