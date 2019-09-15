<?php
// connect to database
$conn = mysqli_connect('localhost', 'steve', 'INSERT PASSWORD HERE', 'market_data');

// check connection
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}


?>