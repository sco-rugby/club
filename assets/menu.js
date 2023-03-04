import $ from 'jquery';
import './plugin//app.plugins.js';

$(document).ready(function () {
    $('.navbar-page .nav-link').menu({target: '#content'});
});
