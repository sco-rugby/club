import $ from 'jquery';

$(document).ready(function () {
    $('#import-form').form();
    $('#import_rapport [type=radio]').on('change', function (event) {
        $('.rapport-desc').hide();
        let id = '#rapport-' + $(this).val().toLowerCase();
        $(id).show();
    });
});