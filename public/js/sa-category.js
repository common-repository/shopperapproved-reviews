/*global shopperapproved */
function saLoadScript(src) {
    var js = window.document.createElement("script");
    js.src = src;
    js.type = "text/javascript";
    document.getElementsByTagName("head")[0].appendChild(js);
}

saLoadScript("//" + shopperapproved.url + "/widgets/group2.0/" + shopperapproved.siteId + ".js");