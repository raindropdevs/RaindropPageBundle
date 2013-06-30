var remoteSearchListener = (function(){

    var params = {
        endpoint: null,
        container: '.list',
        listItem: 'li',
        searchableForm: '.search',
        content: 'p',
        eventKeyUpTimeout: null,
        previousSearch: null,
        callback: function () {

        }
    };

    var loadResults = function (searchedString) {
        $.ajax({
            url: params.endpoint,
            type: 'POST',
            data: {
                key: searchedString,
                country: globalConfig.menuCountry
            },
            success: function (rData) {
                $(".page-source").find('ul')
                    .html(rData.html);

                params.callback();
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
                    loadResults(searchedString);
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

