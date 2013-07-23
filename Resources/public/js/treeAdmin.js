var treeAdmin = (function () {
    return {
        init: function () {
            $("#rootElement")
                .jstree({
                    "plugins" : [ "contextmenu" ],
                    "core" : {
                        "initially_open" : [ ],
                        "load_open": true
                    },
                    "contextmenu": {
                        "items": {
                            "create": {
                                "label": "New Page",
                                "action": function (obj) {
                                    var path = $(obj.reference.parent()).data('path');
                                    window.location.href = globalConfig.createPageUrl + '?parent=' + path + '&meta=1';
                                }
                            },
                            "edit": {
                                "label": "Edit",
                                "action": function (obj) {
                                    var page_id = $(obj.reference.parent()).data('id');
                                    if (page_id !== undefined) {
                                        window.location.href = globalConfig.editPageUrl.replace('0', page_id);
                                    } else {

                                        $('.sonata-ba-filter')
                                            .after($('<div/>', {
                                                "html": '<button type="button" class="close" data-dismiss="alert">&times;</button>There is no page at this node.',
                                                "class": "floating-message no-node alert alert-block alert-error"
                                            }));

                                        setTimeout(function () {
                                            $(".no-node").remove();
                                        }, 3000);
                                    }
                                }
                            },
                            "delete": {
                                "label": "Delete",
                                "action": function (obj) {
                                    var page_id = $(obj.reference.parent()).data('id');
                                    window.location.href = globalConfig.deletePageUrl.replace('0', page_id);
                                }
                            },
                            "clone_to_url": {
                                "label": "Clone to new url",
                                "action": function (obj) {
                                    var page_id = $(obj.reference.parent()).data('id');
                                    window.location.href = globalConfig.clonePageToUrl.replace('0', page_id);
                                }
                            }
                        }
                    }
                });
        }
    };
}());

$(function () {
    treeAdmin.init();
});