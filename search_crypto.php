<?php
    // get id of coin from URL query string
    $coin_id = $_GET["coin"];
    
    $url = "https://api.coingecko.com/api/v3/coins/".$coin_id;
    $coin_data = file_get_contents($url);
    $coin_data = json_decode($coin_data, true);
    
    $conn = mysqli_connect('ip_address', 'user', 'password', 'market_data');

    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();
    }

    $sql = 'SELECT * FROM crypto WHERE id = '.'"'.$coin_id.'"';    
    $result = mysqli_query($conn, $sql);

    $coin = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    mysqli_free_result($result);

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
   
   <body onLoad="fillCryptoData(<?php echo "'".htmlspecialchars($coin_id)."'"?>)">
   <a style = "color: black; text-decoration: none;"href = "/mktcap/index.php"><h1 style="margin-left:5%;margin-right:15%;text-align: left">MktCap</h1></a>
   <hr class="border">
   <br />

    <section >
    <div class="row">

        <div class="column left">
            <!------Coin Name and Ticker-------->
            <div style="text-align: center;">
                <h1 style="font-size: 32px;"> <img height=32 width=32 src=<?echo $coin['image_url']?>/>
                <?php echo htmlspecialchars($coin['cryptocurrency_name'])?>
                <span style="color: gray;font-size: 24px;"><?php echo ' ('.htmlspecialchars($coin['ticker']).')'?></span>
                </h1>
            </div>
            <br />
            <!---------list of links--------------->
            <div style="margin-left:30%;">
            <ul >
                <li>Rank: <?php echo $coin['rank']?></li>
                <br>
                <br>
                <a target = "_blank" href="<?php echo htmlspecialchars($coin_data['links']['homepage'][0])?>"><li><span style="color: black">Website:</span> <?php echo htmlspecialchars($coin_data['links']['homepage'][0])?></li></a>
                <br>                
                <br>
                <a target = "_blank" href="<?php echo htmlspecialchars($coin_data['links']['official_forum_url'][0])?>"><li><span style="color: black">Community:</span> <?php echo htmlspecialchars($coin_data['links']['official_forum_url'][0])?></li></a>
                <br>                
                <br>
                <span>Explorers: <a id = "explorers"><li></li></a></span>
                <br>
                <br>
                <span>Source Code: <a id = "source_code"><li ></li></a></span>
            </ul>
            </div>
        </div>
        
        <div class="column right" style="margin-left: 5%;">
        <br>
        <!---------price and 24h percent change------------->
        <h1 style="font-size: 32px; font-weight: 2;"><?php echo '$'.number_format($coin['price'], 2, '.', ',')?> 
        <span style ="font-size: 18px; color: <?php echo percent_color($coin_data['market_data']['price_change_percentage_24h_in_currency']['usd'])?>">
        <?php echo '('.htmlspecialchars($coin_data['market_data']['price_change_percentage_24h_in_currency']['usd']).'%)'?></span></h1>
        <br />
        <br />

        <table id="table" >
        <tr>
            <th>Market Cap</th>
            <th>Volume</th>
            <th>24h Low/High</th>
            <th>Circulating Supply</th>
        </tr>
        <tr>
            <td><?php echo '$'.number_format($coin['marketcap'], 2, '.', ',')?></td>
            <td><?php echo '$'.number_format($coin['volume'], 2, '.', ',')?></td>
            <td id ="24h_high_low"></td>
            <td><?php echo number_format($coin['supply'],0, '.', ',')?></td>
        </tr>
        </table>

        <table id= "scores_table">
        <tr>
            <th>Developer Score</th>
            <th>Community Score</th>
            <th>Liquidity Score</th>
            <th>Public Interest</th>
        </tr>
        <tr>
            <td id ="dev_score"></td>
            <td id ="com_score"></td>
            <td id ="liq_score"></td>
            <td id ="pub_interest"></td>
        </tr>
        </table>

        </div>
    </div>

    <!-------Fetch exchange data from api------->
    <?php
        $url = 'https://api.coingecko.com/api/v3/coins/'.$coin_id;
        $coin_info = file_get_contents($url);
        $coin_info = json_decode($coin_info, true);
        $pair = $coin_info['tickers'][0]['market']['name'].":".$coin_info['tickers'][0]['base'].$coin_info['tickers'][0]['target'];
    ?>

    </section>
    <hr class = "border"style="margin-right:8%; margin-left:8%">
    <br />
    
    <div style="margin-left: 15%;">
    <ul>
    <li><a class="buttons" onClick="click_charts();">Charts</a>&nbsp;|</li>
    <li><a class="buttons" onClick = "click_markets();">Markets</a>&nbsp;</li>
    </ul>
    </div>

    <br/>
    <div style="text-align:right; margin-right:22%;">
    <div id = "chart_buttons">
    <button style= "color: #1070e0;"id = "candlestick_button"class="buttons" onClick = "display_candlestick()" >Candlestick Chart</button>
    <button style= "color: #1070e0;display: none;"id = "line_button"class="buttons" onClick = "display_line()" >Line Chart</button>
    </div>
    </div>

    <section align = "center" name = "tradingview widgets">
    <div id = "line_chart" class="tradingview-widget-container">
        <div id="tv-medium-widget"></div>
        <div class="tradingview-widget-copyright"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
        new TradingView.MediumWidget(
        {
        "container_id": "tv-medium-widget",
        "symbols": [
            "<?php echo htmlspecialchars($pair)?>|12m"
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
        <!-- TradingView Widget BEGIN -->
        <div style="display:none;"id = "candlestick_chart"class="tradingview-widget-container">
        <div id="tradingview_e28e2"></div>
        <div class="tradingview-widget-copyright"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
        new TradingView.widget(
        {
        "width": 980,
        "height": 610,
        "symbol": "<?php echo htmlspecialchars($pair)?>",
        "interval": "D",
        "timezone": "Etc/UTC",
        "theme": "Light",
        "style": "1",
        "locale": "en",
        "toolbar_bg": "#f1f3f6",
        "enable_publishing": false,
        "allow_symbol_change": true,
        "container_id": "tradingview_e28e2"
        }
        );
        </script>
        </div>
        <!-- TradingView Widget END -->
    </section>

    <section style="display:none;" id = "markets" name = "market_data">
    <h1 style="text-align:center"><?php echo htmlspecialchars($coin_info['name'])?> Markets</h1>
    <table id="table" align="center">
        <tr>
            <th>#</th>
            <th>Exchange</th>
            <th>Pair</th>
            <th>Price</th>
            <th>Volume</th>
            <th>Trust Score</th>
        </tr>
        <!------Loops through top 100 coins in database and inserts them into html table------->
        <?php 
            $rank = 0;
            $chosen_price = floatval($coin_info['market_data']['current_price']['usd']);
        ?>
        <?php foreach($coin_info['tickers'] as $ticker){ ?>
            <?php $rank+=1;?>
            <tr>
                <td><?php echo $rank?></td>
                <td>
                <a style="text-decoration:none;color:#1070e0;" target="_blank"href="<?php echo $ticker['trade_url']?>">
                <?php echo htmlspecialchars($ticker['market']['name'])?></a> 
                </span>
                </td>
                <td style='color: #1070e0;'><?php echo htmlspecialchars($ticker['base'].$ticker['target'])?></td>
                <td style=''><?php echo '$'.number_format($ticker['last'], 2, '.', ',')?></td>
                <td style=''><?php echo '$'.number_format((floatval($ticker['volume'])*$chosen_price), 2, '.', ',')?></td>
                <td style="color:<?php echo htmlspecialchars(ucfirst($ticker['trust_score']))?>"><?php echo htmlspecialchars(ucfirst($ticker['trust_score']))?></td>
            </tr>
        <?php } ?>
    </table>
    </section>

    <br />
    <br />

    <!------Description of Coin-------->
    <section name="description">
    <div style="margin-right: 10%;margin-left:10%;">
        <hr class="border">
        <p id = "description">Description</p>
        <hr class="border">
    </div>
    </section>
    <br>

   </body>

</html>