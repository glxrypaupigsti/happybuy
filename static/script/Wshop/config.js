
var shoproot = location.pathname.substr(0, location.pathname.lastIndexOf('/') + 1);

require.config({
    paths: {
        util: 'util',
        Spinner: 'spin.min',
        jquery: 'http://cdn.bootcss.com/jquery/2.1.1/jquery.min',
        config: 'config',
        Cart: 'class/cart.class',
        Slider: 'class/slider.class',
        Tiping: 'class/tiping.class',
        pdCounter: 'class/pdcounter.class',
        lazyLoad: 'jquery.lazyload.min',
        touchSlider: 'class/jquery.touchslider.min'
    },
    shim: {
        'util': {
            exports: 'util'
        },
        'Spinner': {
            exports: 'Spinner',
            deps: ['util']
        },
        'jquery': {
            exports: '$',
            deps: ['config']
        },
        'lazyLoad': {
            deps: ['jquery']
        },
        'Cart': {
            deps: ['jquery']
        },
        'touchSlider': {
            deps: ['jquery']
        }
    },
    // urlArgs: "bust=1.5.3",
    urlArgs: "bust=" + (new Date()).getTime(),
    xhtml: true
});

define([], function () {
    var config = {};

    return config;
});