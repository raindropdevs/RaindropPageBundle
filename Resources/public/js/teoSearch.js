var searchableList = (function () {

    var params = {
        container: '.list',
        listItem: 'li',
        searchableForm: '.search',
        content: 'p',
        eventKeyUpTimeout: null,
        previousSearch: null
    };

    var showSearchedResults = function (searchedString) {

        if (searchedString === '') {
             $(params.container).find(params.listItem).show();
        }

        $(params.container).find(params.listItem)
            .each(function (i) {
                var text = $(this).find(params.content).text();
                if (text.indexOf(searchedString) === -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
    };

    var searchableFormListener = function () {

        $(params.container).find(params.searchableForm)
            .keyup(function (event) {

                clearTimeout(params.eventKeyUpTimeout);
                params.eventKeyUpTimeout = setTimeout(function () {
                    var $el = $(event.currentTarget)
                    var searchedString = $el.val();

                    // if url is different from previous one checked
                    if (searchedString !== params.previousSearch) {
                        params.previousSearch = searchedString;
                        showSearchedResults(searchedString);
                    }

                }, 500);
            });
    };

    return {
        init: function (config) {
            params = $.extend(params, config);
            searchableFormListener();
        }
    };
}());