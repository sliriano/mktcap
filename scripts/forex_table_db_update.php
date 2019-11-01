<?php 
// connect to database
$conn = mysqli_connect('ip_address', 'user', 'password', 'market_data');

// check connection
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}

$forex_data = file_get_contents("https://financialmodelingprep.com/api/v3/forex");
$forex_data = json_decode($forex_data, true);
$forex_data = $forex_data['forexList'];
for ($i = 0; $i < count($forex_data); $i++) {
    $name = $forex_data[$i]['ticker'];
    $bid = floatval($forex_data[$i]['bid']);
    $ask = $forex_data[$i]["ask"];
    $open = $forex_data[$i]['open'];
    $low = $forex_data[$i]['low'];
    $high = $forex_data[$i]['high'];
    $changes = $forex_data[$i]['changes'];
    // attempt to insert data to database
    $sql = "INSERT INTO forex (pair, bid, ask, open, low, high, changes) VALUES ('$name', $bid, $ask, $open, $low, $high, $changes)";
    if (mysqli_query($conn, $sql)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }       
}
mysqli_close($conn);


?>