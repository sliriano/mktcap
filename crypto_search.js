
function click_charts(){
  document.getElementById('markets').style.display = 'none';
  document.getElementById('candlestick_chart').style.display = 'none';
  document.getElementById('line_chart').style.display = 'initial';
  document.getElementById('chart_buttons').style.display = 'initial';
}

function click_markets() {
  document.getElementById('markets').style.display = 'initial';
  document.getElementById('candlestick_chart').style.display = 'none';
  document.getElementById('line_chart').style.display = 'none';
  document.getElementById('chart_buttons').style.display = 'none';
}

function display_candlestick() {
  document.getElementById('candlestick_chart').style.display = 'initial';
  document.getElementById('line_chart').style.display = 'none';
  document.getElementById('candlestick_button').style.display = 'none';
  document.getElementById('line_button').style.display = 'initial';

}

function display_line() {
  document.getElementById('candlestick_chart').style.display = 'none';
  document.getElementById('line_chart').style.display = 'initial';
  document.getElementById('candlestick_button').style.display = 'initial';
  document.getElementById('line_button').style.display = 'none';
}


// Fill page with crypto market data that is not stored in our database
function fillCryptoData(coin) {
  // Send API Reqeust
  const Http = new XMLHttpRequest();
  let url = 'https://api.coingecko.com/api/v3/coins/'+coin;
  Http.open("GET", url);
  Http.send();

  // After getting a response, fill data
  Http.onreadystatechange=(e)=> {
    const body = JSON.parse(Http.responseText);
      
    // Number formatter based on currency
    // Found Here: https://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-currency-string-in-javascript
    var formatter = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
    });

    // 24 hour high / low
    let low_high = document.getElementById("24h_high_low");
    let high = body['market_data']['high_24h']['usd'];
    let low = body['market_data']['low_24h']['usd'];
    low_high.innerHTML = formatter.format(low) + " / " + formatter.format(high);
    // Description
    let desc = document.getElementById('description');
    desc.innerHTML = body['description']['en']
    //scores
    let dev_score = document.getElementById('dev_score');
    dev_score.innerHTML = body['developer_score'];
    let com_score = document.getElementById('com_score');
    com_score.innerHTML = body['community_score'];
    let liq_score = document.getElementById('liq_score');
    liq_score.innerHTML = body['liquidity_score'];
    let pub_interest = document.getElementById('pub_interest');
    pub_interest.innerHTML = body['public_interest_score'];
    // explorer links
    let explorer = document.getElementById('explorers');
    let explore_link = body['links']['blockchain_site'][0]
    explorer.innerHTML = explore_link;
    explorer.href = explore_link;
    // source code links
    let source_code = document.getElementById('source_code');
    let source_code_link = body['links']['repos_url']['github'][0]
    source_code.innerHTML = source_code_link;
    source_code.href = source_code_link;
  }
}  
