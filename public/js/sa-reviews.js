var sa_review_count = 20;
var sa_date_format = "F j, Y";

function saLoadScript(src) {
    var js = window.document.createElement("script");
    js.src = src;
    js.type = "text/javascript";
    document.getElementsByTagName("head")[0].appendChild(js);
}

saLoadScript("//" + shopperapproved.url + "/merchant/" + shopperapproved.siteId + ".js");