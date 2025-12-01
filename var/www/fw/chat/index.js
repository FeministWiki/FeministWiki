converse.initialize({
    locked_domain: "feministwiki.org",
    muc_domain: "groups.feministwiki.org",
    locked_muc_domain: true,
    allow_registration: false,
    websocket_url: "wss://xmpp.feministwiki.org:5443/ws",
    enable_smacks: true,
    view_mode: "fullscreen",
    theme: "dracula",
})

/*
document.addEventListener("DOMContentLoaded", function() {
    var header = document.getElementsByTagName("converse-brand-heading")[0]
    header.parentNode.removeChild(header)

    var labels = document.getElementsByTagName("label")
    for (var i = 0; i < labels.length; ++i) {
        if (labels[i].htmlFor == "converse-login-jid") {
            labels[i].innerHTML = "Username:"
            break
        }
    }
})
*/
