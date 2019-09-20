<?php
    // get id of coin from URL query string
    $coin_id = $_GET["coin"];
    // use coin id to fetch a lot of specific data about the coin from coingecko api
    $url = "https://api.coingecko.com/api/v3/coins/".$coin_id;
    $coin_data = file_get_contents($url);
    $coin_data = json_decode($coin_data, true);
    
    // connect to database
    $conn = mysqli_connect('localhost', 'steve', 'INSERT PASSWORD HERE', 'market_data');

    // check connection
    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();
    }

    // query for getting the top 100 coins in db
    $sql = 'SELECT * FROM crypto WHERE id = '.'"'.$coin_id.'"';    
    $result = mysqli_query($conn, $sql);

    // feth the resulting rows as an array, reverse the array to descending order
    $coin = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // free result from memory
    mysqli_free_result($result);
    //close connection to database
    mysqli_close($conn);

    $coin = $coin[0];

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

   <style>
    .column { padding:10px;}
    .left {width:30%}
    .right{width:70%}
    .cent {margin-right: 10%; margin-left:10%;}
   </style>
   
   <body >
   <h1 style="text-align: center">MktCap</h1>
   <br />

    
    <section align="center" class = "cent">
    <div class="row">
        <div class="column left">
            <h1><?php echo htmlspecialchars($coin['cryptocurrency_name'])?></h1>
        </div>
        
        <div class="column right">
        <h1><?php echo $coin['price']?></h1>
        <table id="table" align="right">
        <tr>
            <th>Market Cap</th>
            <th>Volume</th>
            <th>Circulating Supply</th>
        </tr>
 
        <tr>
            <td><?php echo '$'.number_format($coin['marketcap'], 2, '.', ',')?></td>
            <td><?php echo '$'.number_format($coin['volume'], 2, '.', ',')?></td>
            <td><?php echo number_format($coin['supply'],0, '.', ',')?></td>
        </tr>
        </table>
        </div>
    </div>
    </section>
   </body>

</html>