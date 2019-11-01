<?php
    // connect to database
    $conn = mysqli_connect('ip_address', 'user', 'password', 'market_data');

    // check connection
    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();
    }

    // query for getting the top currencies in db
    $sql = 'SELECT * FROM forex ORDER BY id';
    $result = mysqli_query($conn, $sql);

    // feth the resulting rows as an array, reverse the array to descending order
    $currencies = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // free result from memory
    mysqli_free_result($result);
    //close connection to database
    mysqli_close($conn);
    
    function percent_color($num) {
        if ($num < 0) {
            return "#e15241";
        }
        else if ($num > 0) {
            return "#43aa05";
        }
    }
?>

<!DOCTYPE html>
<html>
   <head>
      <title>Mkt Cap - Market Data</title>
      <link href="/mktcap/style.css" rel="stylesheet" type="text/css" />
   </head>
   
   <body >
   <h1 style="margin-left:15%;margin-right:15%;text-align: left">MktCap</h1>
   <br />
   <!----nav bar----->
   <div style="margin-left: 15%;">
   <ul>
    <li><a href="index.php">Crypto</a></li>
    <li><a href="stocks.php">Stocks</a></li>
    <li><a class = "active" href="forex.php">Forex</a></li>
   </ul>
   </div>

    <!-----table of currency pairs---->
    <table id="table" align="center">
        <tr>
            <th>#</th>
            <th>Pair</th>
            <th>Bid</th>
            <th>Ask</th>
            <th>Open</th>
            <th>Low</th>
            <th>High</th>
            <th>Changes</th>
        </tr>
        <!------Loops through top 100 currencies in database and inserts them into html table------->
        <?php foreach($currencies as $currency){ ?>
            <tr>
                <td><?php echo $currency['id']?></td>
                <td><?php echo htmlspecialchars($currency['pair'])?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($currency['bid'], 4, '.', ',')?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($currency['ask'], 4, '.', ',')?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($currency['open'], 4, '.', ',')?></td>
                <td style='color: #e15241'><?php echo '$'.number_format($currency['low'], 4, '.', ',')?></td>
                <td style='color: #43aa05;'><?php echo '$'.number_format($currency['high'], 4, '.', ',')?></td>
                <td style='color: <?php echo percent_color($currency['changes'])?>'><?php echo $currency['changes']?></td>
            </tr>
        <?php } ?>
    </table>
   
   </body>

</html>