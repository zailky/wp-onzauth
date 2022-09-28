<?php
?>
<div id="container"></div>
<div id="onzauth">
    <h3 class="onzauth-title">Sign In / Register</h3>
    <form class="onzauth-form" onsubmit="handleLogin(event)">
        <button type="submit">Log In</button>
    </form>
</div>
<!-- From wp-login.php line 285-305 -->
<p id="backtoblog">
    <?php
    $html_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url( home_url( '/' ) ),
        sprintf(
            /* translators: %s: Site title. */
            _x( '&larr; Go to %s', 'site' ),
            get_bloginfo( 'title', 'display' )
        )
    );
    /**
     * Filter the "Go to site" link displayed in the login page footer.
     *
     * @since 5.7.0
     *
     * @param string $link HTML link to the home URL of the current site.
     */
    echo apply_filters( 'login_site_html_link', $html_link );
    ?>
</p>