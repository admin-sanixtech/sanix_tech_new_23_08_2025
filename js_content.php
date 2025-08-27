<?php

function js_content_fun()
{
    $scripts = [
        'assets/js/jquery.js',
        'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',
        'assets/js/bootstrap.min.js',
        'assets/js/bootsnav.js',
        'assets/js/jquery.hc-sticky.min.js',
        'assets/js/jquery.magnific-popup.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js',
        'assets/js/owl.carousel.min.js',
        'assets/js/jquery.counterup.min.js',
        'assets/js/waypoints.min.js',
        'assets/js/jak-menusearch.js',
        'assets/js/custom.js'
    ];

    foreach ($scripts as $script) {
        echo '<script src="' . $script . '"></script>' . PHP_EOL;
    }
}

?>
