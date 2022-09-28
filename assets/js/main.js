// Initialisation
const auth = new onz.Auth({
    clientID: onzauth_wp.client_id, // Option
});
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

/* Login Handler */
const handleLogin = async (e) => {
    e.preventDefault();
    document.body.innerHTML += '<div id="loader" class="loader"><div id="spinner" class="spinner"></div></div>';
    auth.showLogin(); // Shows the login popup
};

let logoutLink = "";

jQuery(function ($) {
    var wooLogout = $(".woocommerce-MyAccount-navigation-link--customer-logout a");
    if (wooLogout.length > 0) {
        var wcLink = wooLogout.attr('href');
        wooLogout.click(function (e) {
            e.preventDefault();
            handleLogout(wcLink);

        });
    }
    var wpLogout = $("#wp-admin-bar-logout a");
    if (wpLogout.length > 0) {
        var wpLink = wpLogout.attr('href');
        wpLogout.click(function (e) {
            e.preventDefault();
            handleLogout(wpLink);
        });
    }
    if ($('#onzauth-already-logged').length > 0) {
        document.location.href = onzauth_wp.redirect_uri;
    }
});

/* Logout Handler */
const handleLogout = (link) => {
    if (auth.isAuthenticated()) {
        document.body.innerHTML += '<div id="loader" class="loader"><div id="spinner" class="spinner"></div></div>';
        auth.logout();
        logoutLink = link;
    }
};

// on logged_out event
auth.on("logged_out", () => {
    if (logoutLink) {
        document.location.href = logoutLink;
    }
    logoutLink = "";
    var el = document.getElementById('loader');
    if (el) el.remove();
});

// Authenticated event, after log in successful, contains accessToken, idToken, refreshToken, expiry
auth.on("authenticated", (authResult) => {
    var el = document.getElementById('loader');
    validateToken(authResult.idToken);
    if (el) el.remove();
});

// Error message
auth.on("error", (errorMessage) => {
    var el = document.getElementById('loader');
    console.error('authentication error', errorMessage);
    alert(errorMessage);
    if (el) el.remove();
});

// On popup or iframe closed
auth.on("closed", () => {
    console.log('iframe or popup is closed'); 
    var el = document.getElementById('loader');
    if (el) el.remove();
});

// Validate Token default
const validateToken = async (didToken) => {
    var el = document.getElementById('loader');
    fetch(onzauth_wp.api_uri + 'onzauth/v1/auth/', {
        headers: {
            Authorization: 'Bearer ' + didToken
        }
    })
    .then(response => {
        if (response.status !== 200) {
            if (el) el.remove();
            alert('Error validating token');
            throw new Error('An error has occured while validating token!  Status Code: ' + response.status);
        }
        response.text().then(function (text) {
            var link = text.replace(/"/g, "");
            document.location.href = link;
        });
    })
    .catch(function (err) {
        if (el) el.remove();
        alert('Error validating token');
        console.log('Error: ', err);
        auth.logout();
    });
};