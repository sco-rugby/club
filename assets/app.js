import './styles/app.scss';
import 'bs-stepper/dist/css/bs-stepper.css'

import $ from 'jquery';
import 'bootstrap';
import 'bootstrap-icons/bootstrap-icons.svg';
import 'material-icons';
import 'material-symbols';
import './plugin/app.plugins.js';
import './bootstrap.js';

$(document).ready(function () {
    $('#chargement').hide();
    $('#app-bar .nav-link').menu({target: '#content'});
    $('.navbar-page .nav-link').menu({target: '#module'});
});