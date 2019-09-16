<?php
    // connect to database
    $conn = mysqli_connect('localhost', 'steve', 'INSERT PASSWORD HERE', 'market_data');

    // check connection
    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();
    }

    // query for getting the top 100 coins in db
    $sql = 'SELECT * FROM crypto ORDER BY marketcap';
    $result = mysqli_query($conn, $sql);

    // feth the resulting rows as an array, reverse the array to descending order
    $coins = array_reverse(mysqli_fetch_all($result, MYSQLI_ASSOC));
    
    // free result from memory
    mysqli_free_result($result);
    //close connection to database
    mysqli_close($conn);
    
    $rank =0;
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
   <h1 style="text-align: center">MktCap</h1>
    <table id="table" align="center">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Market Cap</th>
            <th>Price</th>
            <th>Volume</th>
            <th>Circulating Supply</th>
            <th>Change (24)</th>
        </tr>
        <!------Loops through top 100 coins in database and inserts them into html table------->
        <?php foreach($coins as $coin){ ?>
            <?php 
                if ($rank === 100) {
                    break;
                }
            ?>
            <?php $rank+=1;?>
            <tr>
                <td><?php echo $coin['rank']?></td>
                <td>
                <img height=18 width=18 src=<?php echo $coin['image_url']?>/>
                <?php echo htmlspecialchars($coin['cryptocurrency_name'])?> 
                <?php echo " "."(".htmlspecialchars($coin['ticker']).")"?>
                </td>
                <td><?php echo '$'.number_format($coin['marketcap'], 2, '.', ',')?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($coin['price'], 2, '.', ',')?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($coin['volume'], 2, '.', ',')?></td>
                <td><?php echo number_format($coin['supply'],0, '.', ',')?></td>
                <td style='color: <?php echo percent_color($coin['change24h'])?>'><?php echo $coin['change24h'].'%'?></td>
            </tr>
        <?php } ?>
    </table>
   
   </body>

</html>