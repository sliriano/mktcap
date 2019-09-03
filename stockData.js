function stocks() {
    // Send API Reqeust
    const Http = new XMLHttpRequest();
    let url = 'INSERT STOCK MARKET API URL HERE'
    Http.open("GET", url);
    Http.send();

    // After getting a response, create the table
    Http.onreadystatechange=(e)=> {
        // parse response if returned as string and console log it
        console.log(JSON.parse(Http.responseText));
    }
}
