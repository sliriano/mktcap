// stock images: <img src='https://storage.googleapis.com/iex/api/logos/AAPL.png'/>
function stocks() {
    // Send API Reqeust
    const Http = new XMLHttpRequest();
    let url = 'https://api.worldtradingdata.com/api/v1/stock?symbol=AAPL,MSFT,HSBA.L&api_token=SFPEhW2FhtpmOkmGBlM6n1Sv0fesimu4ZP9dMo6dLhkgNFWEbJ1TIihQgBJo'
    Http.open("GET", url);
    Http.send();

    // After getting a response, create the table
    Http.onreadystatechange=(e)=> {
        // parse response if returned as string and console log it
        console.log(JSON.parse(Http.responseText));
    }
}
