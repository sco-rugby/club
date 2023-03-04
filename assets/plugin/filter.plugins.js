import $ from 'jquery';
/*import jQueryBridget from 'jquery-bridget';
 import Isotope from 'isotope-layout';
 jQueryBridget('isotope', Isotope, $);*/

(function ($) {
    "use strict";
    var defaults = {
        spinner: '#chargement',
        size: 'default',
        selector = {
            pagination: '.pagination',
            item: ".grid-item"
        }
    };
    var Pagination = function (element, isotope, options) {
        this.element = element;
        this.options = {
            itemsPerPage = 5,
            template = {
                itemWrapper: '<li class="page-item"></li>',
                item: '<a class="page-link" href="#"></a>',
                next: '<span aria-hidden="true">&raquo;</span>',
                previous: '<span aria-hidden="true">&laquo;</span>'
            }
        };
        $.extend(this.options, defaults, options);
        this.init = function () {
            /*let first = $(this.element).find(this.options.item).first();
             this.search = new Filtre(this.element, this.options);
             this.select(first);*/
        };
        this.setPagination = function () {

        };
        var SettingsPagesOnItems = function () {
            let _selectorItem = selector.item;
            var itemsLength = $grid.children(_selectorItem).length;
            var pages = Math.ceil(itemsLength / itemsPerPage);
            var item = 1;
            var page = 1;
            var exclusives = [];
            // for each box checked, add its value and push to array
            $checkboxes.each(function (i, elem) {
                if (elem.checked) {
                    _selectorItem += (currentFilter != '*') ? '.' + elem.value : '';
                    exclusives.push(_selectorItem);
                }
            });
            // smash all values back together for 'and' filtering
            filterValue = exclusives.length ? exclusives.join('') : '*';
            // find each child element with current filter values
            $grid.children(filterValue).each(function () {
                // increment page if a new one is needed
                /*if (item > itemsPerPage) {
                 page++;
                 item = 1;
                 }*/
                // add page number to element as a class
                /*wordPage = page.toString();
                 
                 var classes = $(this).attr('class').split(' ');
                 var lastClass = classes[classes.length - 1];
                 // last class shorter than 4 will be a page number, if so, grab and replace
                 if (lastClass.length < 4) {
                 $(this).removeClass();
                 classes.pop();
                 classes.push(wordPage);
                 classes = classes.join(' ');
                 $(this).addClass(classes);
                 } else {
                 // if there was no page number, add it
                 $(this).addClass(wordPage);
                 }*/
                item++;
            });
            currentNumberPages = page;
        }();
        this.createHtml = function () {
            $(this.options.selector.pagination).html('');
            if (currentNumberPages > 1) {
                this.addPage(template.previous);
                for (var i = 0; i < currentNumberPages; i++) {
                    let $pager = $(template.item);
                    $pager.val(i + 1);
                    $pager.data('page', i + 1);
                    $pager.click(function () {
                        var page = $(this).eq(0).attr(pageAttribute);
                        goToPage(page);
                    });
                    this.addPage($pager);
                }
                this.addPage(template.next);
            }
        };
        this.addPage = function (content) {
            let $itemWrapper = $(this.options.template.itemWrapper);
            $itemWrapper.append(content);
            $itemWrapper.appendTo($(this.options.selector.pagination));
        };
        this.goToPage = function (n) {
            currentPage = n;
            var _selectorItem = selector.item;
            var exclusives = [];
            // for each box checked, add its value and push to array
            $checkboxes.each(function (i, elem) {
                if (elem.checked) {
                    _selectorItem += (currentFilter != '*') ? '.' + elem.value : '';
                    exclusives.push(_selectorItem);
                }
            });
            // smash all values back together for 'and' filtering
            filterValue = exclusives.length ? exclusives.join('') : '*';

            // add page number to the string of filters
            var wordPage = currentPage.toString();
            filterValue += ('.' + wordPage);

            changeFilter(filterValue);
        };
        this.addEventListeners = function () {
            var _this2 = this;
            /*$(this.element).on('submit', function (event) {
             return _this2.submit(event);
             });*/
        };
    };
    var Filter = function (element, isotope, options) {
        this.change = function () {
            //$grid.isotope({filter: _selector});
        };
        this.clear = function () {
            /*$checkboxes.each(function (i, elem) {
             if (elem.checked) {
             elem.checked = null;
             }
             });*/
            /*currentFilter = '*';
             setPagination();
             goToPage(1);*/
            return false;
        };
    };
    var Search = function (element, isotope, options) {
        this.element = element;
        this.options = {
            search: '[type=search]',
            element: '.search-content',
            item: '.list-group-item-action'
        };
        $.extend(this.options, defaults, options);
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
    $.fn.pagination = function (isotope, options) {
        return this.each(function () {
            let pagination = new Pagination($(this), options);
            pagination.init();
            pagination.addEventListeners();
        });
    };
    $.fn.filter = function (isotope, options) {
        return this.each(function () {
            let filter = new Filter($(this), isotope, options);
            let _filter = filter;
            $(this).on('click', function (isotope) {
                var filterValue = $(this).val();
                // use filterFn if matches value
                //filterValue = filterFns[ filterValue ] || filterValue;
                isotope({filter: filterValue});
            });
            /*$(this).on('click', function () {
             return _filter.clear();
             });*/
        });
    };
    $.fn.search = function (isotope, options) {
        return this.each(function () {
            let filter = new Filter($(this), options);
            let _filter = filter;
            var _search = $(this).find(filter.options.search);
            //
            $(_search).on('keyup', function (event) {
                return _filter.search($(this).val());
            });
            $(_search).on('change', function (event) {
                return _filter.search($(this).val());
            });
            $(_search).on('input', function (event) {
                return _filter.search($(this).val());
            });
        });
    };
    $.fn.clearFilter = function (isotope, options) {
        return this.each(function () {
            let filter = new Filter($(this), options);
            let _filter = filter;
            $(this).on('click', function () {
                return _filter.clear();
            });
        });
    };

    // debounce so filtering doesn't happen every millisecond
    function debounce(fn, threshold) {
        var timeout;
        threshold = threshold || 100;
        return function debounced() {
            clearTimeout(timeout);
            var args = arguments;
            var _this = this;
            function delayed() {
                fn.apply(_this, args);
            }
            timeout = setTimeout(delayed, threshold);
        };
    }
});