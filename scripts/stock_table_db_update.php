<?php
// connect to database
$conn = mysqli_connect('ip_address', 'user', 'password', 'market_data');

// check connection
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}
// get each ticker from the database
$sql = 'SELECT ticker FROM stocks';
$result = mysqli_query($conn, $sql);

// feth the resulting rows as an array, reverse the array to descending order
$stock_tickers = array_reverse(mysqli_fetch_all($result, MYSQLI_ASSOC));

// free result from memory
mysqli_free_result($result);

for ($i = 0; $i < floor(count($stock_tickers) / 50); $i++) {
    // if stocks cant be evenly grouped in groups of 50
    // add the remainder
    $concatenated_tickers = "";
    if ($i === count($stock_tickers)-1 && count($stock_tickers) % 50 !== 0 ) {
        for ($ticker_index = $i*50; $ticker_index < $i*50 + count($stock_tickers) % 50; $ticker_index++) {
            $concatenated_tickers .= $stock_tickers[$ticker_index]['ticker'].",";
        }
    }
    else {
        // Creates groups of 50 tickers 
        for ($ticker_index =50*$i; $ticker_index < 50*$i+51; $ticker_index++) {
            $concatenated_tickers .= $stock_tickers[$ticker_index]['ticker'].",";
        }
    }
    // removes duplicats
    if ($i !== 0) {
        $start_index = strpos($concatenated_tickers, ",") + 1;
        $concatenated_tickers = substr($concatenated_tickers, $start_index);
    }
    // remove extra comma on end of string, then remove all string quotes
    $concatenated_tickers = substr($concatenated_tickers, 0, -1);

    // fetch and format api data
    $api_data = file_get_contents("https://api.worldtradingdata.com/api/v1/stock?symbol=".$concatenated_tickers.".L&api_token=SFPEhW2FhtpmOkmGBlM6n1Sv0fesimu4ZP9dMo6dLhkgNFWEbJ1TIihQgBJo");
    $api_data = json_decode($api_data, true);
    $api_data = $api_data['data'];

    // Loop through the API data and add to the database table
    for ($stock_index = 0; $stock_index < count($api_data); $stock_index++) {
        // database values
        $stock_name = str_replace("'", "", $api_data[$stock_index]['name']);
        $ticker = $api_data[$stock_index]['symbol'];
        $market_cap = intval($api_data[$stock_index]['market_cap']);
        $price = floatval($api_data[$stock_index]['price']);
        $volume = intval($api_data[$stock_index]['volume']);
        $shares = intval($api_data[$stock_index]['shares']);
        $change24h = floatval($api_data[$stock_index]['change_pct']);
        // attempt to insert data to database
        $sql = "REPLACE INTO stocks (stock_name, ticker, marketcap, price, volume, shares, change24h) VALUES ('$stock_name', '$ticker', $market_cap, $price, $volume, $shares, $change24h)";
        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }   
    }
}
mysqli_close($conn);

?>