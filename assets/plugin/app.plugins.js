import $ from 'jquery';
//import 'bootstrap';
// jQuery :contains Case-Insensitive
$.expr[":"].contains = $.expr.createPseudo(function (arg) {
    return function (elem) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

// jQuery plugins
(function ($) {
    "use strict";
    var defaults = {
        spinner: '#chargement',
        size: 'small'
    };
    var Form = function (element, options) {
        this.element = element;
        this.options = {
            'target': $(this.element).data('target'),
            'url': $(this.element).attr('action')
        };
        $.extend(this.options, defaults);
        $.extend(this.options, options);
        if (this.options.url == "#") {
            this.options.url = "";
        }
        ;
        if (this.options.target == "#" | typeof this.options.target == 'undefined') {
            this.options.target = "";
        }
        this.addEventListeners = function () {
            var _this2 = this;
            $(this.element).on('submit', function (event) {
                return _this2.submit(event);
            });
        };
        this.submit = function (event) {
            var form = $(this.element);
            var options = this.options;
            event.preventDefault();
            if (!options.url) {
                var msg;
                msg = "No url has been found"
                alert(msg);
                console.log(msg + " : " + $(this.element)[0].outerHTML);
                return false;
            }
            options.data = form.serialize();
            ajax_load(options.url, options.target, event, options, 'POST');
            //$(options.spinner).show();
            /*$.post(
             options.url,
             $(form).serialize()
             )
             .done(function (data) {
             if (options.target) {
             $(options.target).html(data);
             }
             })
             .fail(function (data, status) {
             bootbox.alert({
             size: options.size,
             title: "<span class='text-warning'> " + status + "</span>",
             message: data.responseText,
             buttons: {
             ok: {
             className: "btn-outline-warning"
             }
             }
             });
             })
             .always(function () {
             $(options.spinner).hide();
             });*/
            return true;
        };
    };
    var Liste = function (element, options) {
        this.element = element;
        this.options = {
            item: '.list-group-item-action',
            select: '[data-action=select]'
        };
        $.extend(this.options, defaults, options);
        this.init = function () {
            let first = $(this.element).find(this.options.item).first();
            this.search = new Filtre(this.element, this.options);
            this.select(first);
        };
        this.addEventListeners = function () {
            var _this2 = this;
            //var _search = $(this.element).find('[type=search]');
            this.search.addEventListeners();
            //
            /*$(_search).on('keyup', function (event) {
             return _this2.filter($(this).val());
             });
             $(_search).on('change', function (event) {
             return _this2.filter($(this).val());
             });
             $(_search).on('input', function (event) {
             return _this2.filter($(this).val());
             });*/
            // onClick
            let _selector = this.options.item + ' ' + this.options.select;
            $(this.element).find(_selector).each(function (i) {
                $(this).on('click', function (event) {
                    return _this2.onClickEvent(event);
                });
            });
        };
        this.onClickEvent = function (event) {
            let _select = $(event.currentTarget).closest('.list-group-item-action');
            $(_select).siblings().removeClass('active').removeClass('sticky-top');
            this.select(_select);
            return false;
        };
        /*this.filter = function (value) {
         if (value) {
         $(this.element).find(".search-content:not(:contains(" + value + "))").closest('.list-group-item-action').slideUp();
         $(this.element).find(".search-content:contains(" + value + ")").closest('.list-group-item-action').slideDown();
         } else {
         $(this.element).find("li").slideDown();
         }
         return false;
         };*/
        this.select = function (elmt) {
            $(elmt).addClass('active').addClass('sticky-top');
            let select = $(elmt).find('[data-action=select]');
            ajax_load($(select).attr('href'), $(select).data('target'), null, this.options);
        };
    };
    var Filtre = function (element, options) {
        this.element = element;
        this.options = {
            search: '[type=search]',
            element: '.search-content',
            item: '.list-group-item-action'
        };
        $.extend(this.options, defaults, options);

        this.addEventListeners = function () {
            var _this2 = this;
            var _search = $(this.element).find(this.options.search);
            //
            $(_search).on('keyup', function (event) {
                return _this2.filter($(this).val());
            });
            $(_search).on('change', function (event) {
                return _this2.filter($(this).val());
            });
            $(_search).on('input', function (event) {
                return _this2.filter($(this).val());
            });
        };
        this.filter = function (value) {
            if (value) {
                $(this.element).find(this.options.element + ":not(:contains(" + value + "))").closest(this.options.item).slideUp();
                $(this.element).find(this.options.element + ":contains(" + value + ")").closest(this.options.item).slideDown();
            } else {
                $(this.element).find(this.options.item).slideDown();
            }
            return false;
        };
    };
    var Confirm = function (element, options) {
        this.element = element;
        this.buttons = {
            confirm: {
                label: 'Supprimer',
                className: 'btn-outline-danger btn-action remove'
            },
            cancel: {
                label: 'Annuler',
                className: 'btn-outline-secondary btn-action cancel'
            }
        }
        $.extend(this.buttons, options.buttons);
        delete options.buttons;
        this.options = {
            setNull: false,
            title: $(element).attr('title'),
            confirm: $(element).data('confirm'),
            target: $(element).data('target'),
            url: $(element).attr('href'),
            method: 'GET'
        };
        $.extend(this.options, defaults, options);
        if (this.options.url == "#") {
            this.options.url = "";
        }
        ;
        if (this.options.target == "#") {
            this.options.target = "";
        }
        ;
        var confirm = this.options.confirm;
        if (this.options.confirm == "#") {
            this.options.confirm = "";
        }
        ;
        this.addEventListeners = function () {
            var _this2 = this;
            $(this.element).on('click', function (event) {
                return _this2.onClickEvent(event);
            });
        };
        this.onClickEvent = function (event) {
            var buttons = this.buttons;
            var options = this.options;
            event.preventDefault();
            if (!options.url || !options.confirm) {
                var msg;
                if (!options.url) {
                    msg = "No url has been found"
                }
                if (!options.confirm) {
                    msg = "No confimation messsage has been found"
                }
                _error(msg);
                console.log(msg + " : " + $(this.element)[0].outerHTML);
                return false;
            }
            /*            bootbox.confirm({
             size: options.size,
             title: options.title,
             message: '<p class="text-justify">' + confirm + '</p>',
             buttons: buttons,
             callback: function (result) {
             if (result === true) {
             $(options.spinner).show();
             $.ajax({
             method: options.method,
             url: options.url
             })
             .done(function (data) {
             if (data) {
             bootbox.alert({
             size: "small",
             title: '<span class="text-success">' + options.title + '</span>',
             message: '<p class="text-justify text-success">' + data + '</p>',
             buttons: {
             ok: {
             className: "btn-outline-success"
             }
             }
             });
             }
             if (options.set_null === true) {
             $(target).html('');
             }
             if (options.after) {
             for (var i = 0; i < options.after.length; i++) {
             eval(options.after[i]);
             }
             }
             })
             .fail(function (data, status) {
             bootbox.alert({
             size: "small",
             title: '<span class="text-warning">' + options.title + ' : ' + status + "</span>",
             message: data.responseText,
             buttons: {
             ok: {
             className: "btn-outline-warning"
             }
             }
             });
             })
             .always(function () {
             $(options.spinner).hide();
             });
             }
             }
             });
             */            return false;
        };
    };
    var Load = function (element, options) {
        this.element = element;
        this.options = {
            target: $(this.element).data('target'),
            url: $(this.element).attr('href'),
            method: 'GET'
        };
        $.extend(this.options, defaults);
        this.options.size = 'large';
        $.extend(this.options, options);
        if (this.options.url == "#") {
            this.options.url = "";
        }
        if (this.options.target == "#") {
            this.options.target = "";
        } else if (typeof this.options.target == 'undefined') {
            this.options.target = "";
        }
        /*this.addEventListeners = function () {
         var _this2 = this;
         $(this.element).on('click', function (event) {
         return _this2.onClickEvent(event);
         });
         };*/
        /*this.onClickEvent = function (event) {
         var url = this.options.url;
         var target = this.options.target;
         var options = this.options;
         event.preventDefault();
         ajax_load(url, target, event, options, options.method);
         return false;
         };*/
        this.init = function () {
            if (!this.options.url) {
                return _error("No url provided");
            }
            if (!this.options.target) {
                return _error("No target provided");
            }
        };
    };
    var Toggle = function (element, options) {
        this.element = element;
        this.checkbox = $(this.element).find('input[type="checkbox"]');
        this.addEventListeners = function () {
            var _this2 = this;
            $(this.element).on('click', function (event) {
                return _this2.onClickEvent(event);
            });
        };
        this.onClickEvent = function (event) {
            if ($(this.checkbox).val() == 'on') {
                $(this.element).removeClass('active');
                $(this.checkbox).val('off');
                $(this.checkbox).prop("checked", false);
            } else {
                $(this.element).addClass('active');
                $(this.checkbox).val('on');
                $(this.checkbox).prop("checked", true);
            }
        };
    };
    var Button = function (element, options) {
        this.element = element;
        this.options = {
            title: $(this.element).attr('title'),
            modal: $(this.element.data('bs-target')),
            content: '.modal-content'
        };
        $.extend(this.options, defaults, options);
        this.addEventListeners = function () {
            let _this2 = this;
            $(this.element).on('click', function (event) {
                return _this2.onClickEvent(event);
            });
        };
        this.onClickEvent = function (event) {
            event.preventDefault();
            let content = $(this.options.modal).find(this.options.content);
            $.extend(this.options, {target: content});
            let load = new Load(this.element, this.options);
            load.init();
            load.onClickEvent(event);
            if (this.options.title != '') {
                $(this.options.modal).find(".modal-title").html(this.options.title);
            }
            $(this.options.modal).show();
        };
        this.init = function () {
            $(this.options.modal).hide();
            var _this2 = this;
            $(this.options.modal).find("[data-bs-dismiss]").each(function () {
                $(this).on('click', function (event) {
                    return $(_this2.options.modal).hide();
                });
            });
        };
    };
    var Range = function (element, options) {
        this.element = element;
        this.options = {
            'target': $(this.element).siblings('.range-value')
        };
        $.extend(this.options, options);
        this.addEventListeners = function () {
            var _this2 = this;
            $(this.element).on('input', function (event) {
                return _this2.setValue(event);
            });
        };
        this.setValue = function () {
            let newValue = Number(($(this.element).val() - $(this.element).attr('min')) * 100 / ($(this.element).attr('max') - $(this.element).attr('min')));
            let newPosition = 10 - (newValue * 0.2);
            $(this.options.target).html('<span>' + $(this.element).val() + '</span>');
            let position = `left: calc(${newValue}% + (${newPosition}px))`;
            $(this.options.target).attr('style', position);
        };
        this.init = function () {
            this.setValue();
        };
    };
    function ajax_load(url, target, event, options = {}, method = 'GET') {
        if (!$(target).length) {
            return _error("target " + target + " not found");
        }
        if (options.after & !Array.isArray(options.after)) {
            return _error("The callbacks executed afterwards should be an array");
        }
        let requestData = null;
        if (options.data) {
            requestData = options.data;
        }
        $(options.spinner).show();
        return $.ajax({
            "url": url,
            "method": method,
            "data": requestData
        })
                .done(function (data) {
                    if (target) {
                        $(target).html(data);
                    }
                    if (options.after) {
                        for (var i = 0; i < options.after.length; i++) {
                            eval(options.after[i]);
                        }
                    }
                    return data;
                })
                .fail(function (data) {
                    _error(data.responseText);
                    /*bootbox.alert({
                     size: options.size,
                     title: "<span class='text-warning'> " + data.statusText + "</span>",
                     message: data.responseText,
                     buttons: {
                     ok: {
                     className: "btn-outline-warning"
                     }
                     }
                     });*/
                })
                .always(function (data, textStatus, errorThrown) {
                    $(options.spinner).hide();
                });
    }
    function _error(_msg, _alert = true) {
        if (_alert) {
            //let modal = bootstrap.Modal.getOrCreateInstance($('#errorModal'))
            /*bootbox.alert(_msg, function () {
             console.log(_msg);
             });*/
           // alert(_msg);
        }
        console.log(_msg);
        return false;
    }
    $.fn.modalButton = function (options) {
        return this.each(function () {
            let button = new Button($(this), options);
            button.init();
            button.addEventListeners();
        });
    };
    $.fn.ajaxLoad = function (options) {
        return this.each(function () {
            let load = new Load($(this), options);
            $(this).on('click', function (event) {
                event.preventDefault();
                ajax_load(load.options.url, load.options.target, event, load.options, load.options.method);
                return false;
            });
        });
    };
    $.fn.form = function (options) {
        return this.each(function () {
            let form = new Form($(this), options);
            form.addEventListeners();
        });
    };
    $.fn.liste = function (options) {
        return this.each(function () {
            let liste = new Liste($(this), options);
            liste.init();
            liste.addEventListeners();
        });
    };
    $.fn.filtre = function (options) {
        return this.each(function () {
            let filtre = new Filtre($(this), options);
            filtre.addEventListeners();
        });
    };
    $.fn.confirm = function (options) {
        return this.each(function () {
            let confirm = new Confirm($(this), options);
            confirm.addEventListeners();
        });
    };
    $.fn.switchToggle = function (options) {
        return this.each(function () {
            let toggle = new Toggle($(this), options);
            toggle.addEventListeners();
        });
    };
    $.fn.fromAjax = function (url, target, options = {}, method = 'GET'){
        $.extend(options, defaults);
        return ajax_load(url, target, null, options, method);
    };
    $.fn.range = function (options) {
        return this.each(function () {
            let range = new Range($(this), options);
            range.init();
            range.addEventListeners();
        });
    };
    $.fn.menu = function (options) {
        return this.each(function () {
            options = $.extend(options, {
                after: [
                    '$(event.currentTarget).parent().siblings().find("a").removeClass("active")',
                    '$(event.currentTarget).addClass("active")'
                ]
            }
            );
            let menu = new Load($(this), options);
            $(this).on('click', function (event) {
                event.preventDefault();
                ajax_load(menu.options.url, menu.options.target, event, menu.options, menu.options.method);
                return false;
            });
        });
    };
})(jQuery);