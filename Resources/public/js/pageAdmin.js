var pageAdmin = (function(){

    var config = {
        previousUrlCheck: null,
        preventSpam: null,
        eventKeyUpTimeout: null,
        urlParentGroup: null,
        urlInputField: null
    };

    return {

        addUrlCheckerListener: function () {
            config.urlInputField
            .keyup(function (event) {
                config.eventKeyUpTimeout = setTimeout(function () {
                    var $el = $(event.currentTarget)
                    var url = $el.val();

                    if (url === '') {
                        config.urlParentGroup
                            .removeClass('success')
                            .addClass('error');
                        return;
                    }

                    if (url !== config.previousUrlCheck) {
                        config.previousUrlCheck = url;
                        pageAdmin.urlChecker(url);
                    }

                }, 500);
            });
        },

        init: function () {
            config.urlParentGroup = $('#tab1 form.form-horizontal:eq(0) .control-group:eq(2)');
            config.urlInputField = $('#tab1 form.form-horizontal:eq(0) input[type=text]:eq(1)');
            this.addUrlCheckerListener();
        },

        urlChecker: function (url) {
            $.ajax({
                url: '/app_dev.php/admin/page/url/check',
                type: 'POST',
                data: 'url=' + url,
                success: function (result) {

                    if (result.available) {
                        config.urlParentGroup
                            .removeClass('error')
                            .addClass('success');
                    } else {
                        config.urlParentGroup
                            .removeClass('success')
                            .addClass('error');
                    }
                }
            });
        }
    };
}());

$(function() {
    pageAdmin.init();
})