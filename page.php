<?php
    $page = intval($_GET['page']);
    // range of the market cap ranks that will be displayed on this page
    // ex) page 2 would have coins ranked [100, 200]
    $range = [($page-1)*100, ($page)*100];
    
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
    $coins = array_slice($coins, $range[0], $range[1]+1);
    // free result from memory
    mysqli_free_result($result);
    //close connection to database
    mysqli_close($conn);
    
    $rank = $range[0];
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

    <!------nav bar------>
   <div style="margin-left: 15%;">
   <ul>
    <li><a class="active" href="index.php">Crypto</a></li>
    <li><a href="stocks.php">Stocks</a></li>
    <li><a href="forex.php">Forex</a></li>
   </ul>
   </div>

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
                if ($rank === $range[1]) {
                    break;
                }
            ?>
            <?php $rank+=1;?>
            <tr>
                <td><?php echo $rank?></td>
                <td>
                <img height=18 width=18 src=<?php echo $coin['image_url']?>/>
                <a style="text-decoration:none;color:black;"href="search_crypto.php?coin=<?php echo $coin['id']?>">
                <?php echo htmlspecialchars($coin['cryptocurrency_name'])?></a> 
                <?php echo " "."(".htmlspecialchars($coin['ticker']).")"?>
                </span>
                </td>
                <td><?php echo '$'.number_format($coin['marketcap'], 2, '.', ',')?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($coin['price'], 2, '.', ',')?></td>
                <td style='color: #1070e0;'><?php echo '$'.number_format($coin['volume'], 2, '.', ',')?></td>
                <td><?php echo number_format($coin['supply'],0, '.', ',')?></td>
                <td style='color: <?php echo percent_color($coin['change24h'])?>'><?php echo $coin['change24h'].'%'?></td>
            </tr>
        <?php } ?>
    </table>
    
    <br />
    <?php
    // establishes url for previous 100 link 
    $prev_url = "";
        if($page === 2) {
            $prev_url = "index.php";
        }
        else {
            $prev_url = "page.php?page=".strval(($page-1));
        }
    ?>
    <!---------left button / previous 100 coin------------>
    <div align= "right">
    <span align = "left" style="margin-left: 15%;">
    <a href = <?php echo $prev_url?> class = "button">Previous 100</a>
    </span>

    <!----------right button / next 100 coins------------->
    <span align = "right" style="margin-right: 15%;">
    <a href = <?php echo "page.php?page=".strval($page+1)?> class = "button">Next 100</a>
    </span>
    </div>
   </body>

</html>