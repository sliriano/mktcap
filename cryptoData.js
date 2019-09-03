// Fetches crypto data from coingecko API and creates table with the data.
function fillCryptoData() {

    // Send API Reqeust
    const Http = new XMLHttpRequest();
    let url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=100&page=1&sparkline=true&price_change_percentage=24h'
    Http.open("GET", url);
    Http.send();

    // After getting a response, create the table
    Http.onreadystatechange=(e)=> {
        const body = JSON.parse(Http.responseText);
        let table = document.getElementById('table');
        for (var i = 0; i<body.length; i++) {
            let coin = body[i];
              
            // create row and cells
            let row = table.insertRow(i+1);
            let rank = row.insertCell(0);
            let name = row.insertCell(1);
            let market_cap = row.insertCell(2);
            let price = row.insertCell(3);
            let volume = row.insertCell(4);
            let circ_supply = row.insertCell(5);
            let percent_change = row.insertCell(6);

            // Number formatter based on currency
            // Found Here: https://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-currency-string-in-javascript
            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
              });

            // fill cells with formatted text
            rank.innerHTML = coin['market_cap_rank'];
            name.innerHTML = "<span><img src='"+ coin['image'] +"' alt='logo'  height=18 width=18></img>" +"&nbsp;"+ coin['name'];
            market_cap.innerHTML = formatter.format(coin['market_cap']);
            price.innerHTML = "<span style='color: #1070e0;'>" + formatter.format(coin['current_price']) + "</span>";
            volume.innerHTML = "<span style='color: #1070e0;'>" + formatter.format(coin['total_volume']) + "</span>";
            circ_supply.innerHTML = coin['circulating_supply'].toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            // if percent change is under 0 color is red, over zero color is green
            let percent_change_color
            if (coin['price_change_percentage_24h_in_currency'].toFixed(2) < 0) {
                percent_change_color = "#e15241"
            }
            else if (coin['price_change_percentage_24h_in_currency'].toFixed(2) > 0) {
                percent_change_color = "#43aa05"
            }
            percent_change.innerHTML = "<span style='color:" +percent_change_color+"'>" +coin['price_change_percentage_24h_in_currency'].toFixed(2) + '%';

        }
    }
}

