import $ from 'jquery';
import './plugin//app.plugins.js';

$(document).ready(function () {
    //bootbox.setLocale('fr');
    $('#chargement').hide();
    $('.navbar-page .nav-link').menu({target: '#content'});
});
