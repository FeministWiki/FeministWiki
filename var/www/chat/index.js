converse.initialize({
    view_mode: 'fullscreen',
    websocket_url: "wss://xmpp.feministwiki.org:5280/websocket",
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
})
