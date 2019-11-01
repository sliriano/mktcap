<?php
    $stock_id = $_GET["stock"];
    
    $conn = mysqli_connect('ip_address', 'user', 'password', 'market_data');

    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();
    }

    $sql = 'SELECT * FROM stocks WHERE ticker = '.'"'.$stock_id.'"';    
    $result = mysqli_query($conn, $sql);

    $stock = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    mysqli_free_result($result);

    mysqli_close($conn);

    $stock = $stock[0];

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
      <script src="/mktcap/crypto_search.js"></script>
   </head>

   <style>
    .row {display: flex}
    .column { padding:10px;}
    .left {width:30%}
    .right{width:70%}
    hr.border{border-top: 1px solid rgba(39,52,64,0.2)}
    a {color: #1070e0;}
    h1, h2, h3 {font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;}
    .buttons {background-color: white;  border: none;color: black;padding: 10px 26px;text-align: center;text-decoration: none;
    display: inline-block;font-size: 16px;}
   </style>
   
   <body >
   <a style = "color: black; text-decoration: none;"href = "/mktcap/stocks.php"><h1 style="margin-left:5%;margin-right:15%;text-align: left">MktCap</h1></a>
   <hr class="border">
   <br />

<?php
    // Get additional info from worldtradingdata api
    $api_data = file_get_contents("https://api.worldtradingdata.com/api/v1/stock?symbol=".($stock_id)."&api_token=SFPEhW2FhtpmOkmGBlM6n1Sv0fesimu4ZP9dMo6dLhkgNFWEbJ1TIihQgBJo");
    $api_data = json_decode($api_data, true);
    $api_data = $api_data['data'][0];

    $previous_close = $api_data['close_yesterday'];
    $high = $api_data['day_high'];
    $low = $api_data['day_low'];
    $percent_change_24h = $api_data['change_pct'];
    $year_high = $api_data['52_week_high'];
    $year_low = $api_data['52_week_low'];
    $open = $api_data['price_open'];
    $exchange = $api_data["stock_exchange_short"];
    $eps = $api_data['eps'];
?>

    <section >
    <div class="row">

        <div class="column left">
            <!------Coin Name and Ticker-------->
            <div style="text-align: center;">
                <h1 style="font-size: 32px;"> <img height=32 width=32 src='https://storage.googleapis.com/iex/api/logos/<?php echo $stock['ticker']?>.png'/>
                <?php echo htmlspecialchars($stock['stock_name'])?>
                <span style="color: gray;font-size: 24px;"><?php echo ' ('.htmlspecialchars($stock['ticker']).')'?></span>
                </h1>
            </div>
            <br />
            <!---------list of links--------------->
            <div style="margin-left:30%;">
            <ul >
                <li>Rank: <?php echo 1?></li>
                <br>
                <br>
                <span><li>Exchange: <?php echo htmlspecialchars($exchange)?></li></span>
                <br>                
                <br>
                <span><li>Earnings per Share: <?php echo htmlspecialchars($eps)?></li></span>
                <br>                
                <br>
                <!---Add additional relevant stock data to this section. Hide section until complete.--->
                <div style="display: none;">
                <span>Explorers: <a id = "explorers"><li></li></a></span>
                <br>
                <br>
                <span>Source Code: <a id = "source_code"><li ></li></a></span>
                </div>
            </ul>
            </div>
        </div>
        
        <div class="column right" style="margin-left: 5%;">
        <br>
        <!---------price and 24h percent change------------->
        <h1 style="font-size: 32px; font-weight: 2;"><?php echo '$'.number_format($stock['price'], 2, '.', ',')?> 
        <span style ="font-size: 18px;color:<?php echo percent_color(floatval($percent_change_24h))?>;" id = "percent_change">
        <?php echo '('.$percent_change_24h.'%'.')'?></span></h1>
        <br />
        <br />

        <table id="table" >
        <tr>
            <th>Market Cap</th>
            <th>Volume</th>
            <th>24h Low/High</th>
            <th>Shares</th>
        </tr>
        <tr>
            <td><?php echo '$'.number_format($stock['marketcap'], 2, '.', ',')?></td>
            <td><?php echo '$'.number_format($stock['volume'], 2, '.', ',')?></td>
            <td><?php echo '$'.number_format($low, 2, '.', ',')?> / <?php echo '$'.number_format($high, 2, '.', ',')?></td>
            <td><?php echo number_format($stock['shares'],0, '.', ',')?></td>
        </tr>
        </table>

        <table id= "scores_table">
        <tr>
            <th>Opening Price</th>
            <th>Yesterday's Close</th>
            <th>52 Week High</th>
            <th>52 Week Low</th>
        </tr>
        <tr>
            <td id ="open"><?php echo '$'.number_format($open, 2, '.', ',')?></td>
            <td id ="previous_close"><?php echo '$'.number_format($previous_close, 2, '.', ',')?></td>
            <td id ="year_high"><?php echo '$'.number_format($year_high, 2, '.', ',')?></td>
            <td id ="year_low"><?php echo '$'.number_format($year_low, 2, '.', ',')?></td>
        </tr>
        </table>

        </div>
    </div>
    
    <br />

    <section align ="center" name="Tradingview Chart Widget">
    <!-- TradingView Widget BEGIN -->
    <div class="tradingview-widget-container">
    <div id="tv-medium-widget"></div>
    <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/symbols/NASDAQ-AAPL/" rel="noopener" target="_blank"><span class="blue-text"><?php echo htmlspecialchars($stock_id)?> Quotes</span></a> by TradingView</div>
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script type="text/javascript">
    new TradingView.MediumWidget(
    {
    "container_id": "tv-medium-widget",
    "symbols": [
        "<?php echo htmlspecialchars($exchange).':'.htmlspecialchars($stock_id)?>|12m"
    ],
    "greyText": "Quotes by",
    "gridLineColor": "#e9e9ea",
    "fontColor": "#83888D",
    "underLineColor": "#dbeffb",
    "trendLineColor": "#4bafe9",
    "width": "1000px",
    "height": "400px",
    "locale": "en"
    }
    );
    </script>
    </div>
    <!-- TradingView Widget END -->
    </section>

    <br />
    <br />

    <section name="description">
    <div style="text-align: center;margin-right: 10%;margin-left:10%;">
        <hr class="border">
        <p id = "description">Additional Features Coming Soon</p>
        <hr class="border">
    </div>
    </section>
    <br>

   </body>

</html>