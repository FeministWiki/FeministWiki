converse.initialize({
    view_mode: "fullscreen",
    websocket_url: "wss://xmpp.feministwiki.org:5443/ws",
    locked_domain: "feministwiki.org",
    muc_domain: "groups.feministwiki.org",
    allow_registration: false
})

document.addEventListener("DOMContentLoaded", function() {
    var heading = document.getElementsByClassName("brand-heading")[0]
    heading.innerHTML = "FeministChat"

    var subtitles = document.getElementsByClassName("brand-subtitle")
    while (subtitles.length > 0) {
        subtitles[0].parentNode.removeChild(subtitles[0])
    }

    var labels = document.getElementsByTagName("label")
    for (var i = 0; i < labels.length; ++i) {
        if (labels[i].htmlFor = "converse-login-jid") {
            labels[i].innerHTML = "Username:"
            break
        }
    }
})
