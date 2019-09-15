<?php
    /*
        THIS FILE IS A FALLBACK. PLEASE REFER TO 
        THE stock_table_db_update.php FILE.
         
        THIS SCRIPT WILL READ FROM A TEXT FILE AND
        QUERY AN API WHICH WILL FETCH INFORMATION
        BASED ON THE TEXT FILE. THE DATA IS THEN STORED
        IN A MYSQL DATABASE.
    */
    
    // connect to database
    $conn = mysqli_connect('localhost', 'steve', 'INSERT PASSWORD HERE', 'stock_data');
    // check connection
    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();
    }
    //read file data
    $textfile = fopen("marketData/symbols_ranked_by_mc.txt", "r");
    $file_data = fread($textfile, filesize("marketData/symbols_ranked_by_mc.txt"));
    fclose($textfile);
    $data_arr = explode(", ", $file_data);
    /*
      textfile size contains 3500 stock tickers,
      API limits us to 50 stocks per call
      therfore we will loop seventry times
      and within those we will loop 50 times and add 
      50 tickers to a string to use for an api call.
    */
    for ($i = 0; $i < 70; $i++) {
        // Creates groups of 50 tickers for api calls
        $concatenated_tickers = "";
        for ($ticker_index =50*$i; $ticker_index < 50*$i+51; $ticker_index++) {
            $concatenated_tickers = $concatenated_tickers.$data_arr[$ticker_index].",";
        }
        if ($i === 0) {
            // remove extra comma from textfile
            $concatenated_tickers = substr($concatenated_tickers,1);
        }
        else {
            $start_index = strpos($concatenated_tickers, ",") + 1;
            $concatenated_tickers = substr($concatenated_tickers, $start_index);
        }
        // remove extra comma on end of string, then remove all string quotes
        $concatenated_tickers = substr($concatenated_tickers, 0, -1);
        $concatenated_tickers = str_replace("'", "", $concatenated_tickers);
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
            $sql = "INSERT INTO stocks (stock_name, ticker, marketcap, price, volume, shares, change24h) VALUES ('$stock_name', '$ticker', $market_cap, $price, $volume, $shares, $change24h)";
            if (mysqli_query($conn, $sql)) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
    mysqli_close($conn);
?>