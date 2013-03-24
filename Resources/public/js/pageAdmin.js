var pageAdmin = (function(){

    /*
     * private vars and methods
     */
    var config = {
        previousUrlCheck: null,
        preventSpam: null,
        eventKeyUpTimeout: null,
        urlParentGroup: null,
        urlInputField: null
    };


    var urlChecker = function (url) {
        $.ajax({
            url: globalConfig.urlCheckPath,
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

    var addUrlCheckerListener = function () {
        config.urlInputField
            .keyup(function (event) {
                config.eventKeyUpTimeout = setTimeout(function () {
                    var $el = $(event.currentTarget)
                    var url = $el.val();

                    // empty url is not valid at all
                    if (url === '') {
                        config.urlParentGroup
                            .removeClass('success')
                            .addClass('error');
                        return;
                    }

                    // if url is different from previous one checked
                    if (url !== config.previousUrlCheck) {
                        config.previousUrlCheck = url;
                        urlChecker(url);
                    }

                }, 500);
            });
    }


    /*
     * public methods
     */

    return {

        init: function () {

            config.urlParentGroup = $('#tabMeta form.form-horizontal:eq(0) .control-group:eq(2)');
            config.urlInputField = $('#tabMeta form.form-horizontal:eq(0) input[type=text]:eq(1)');

            addUrlCheckerListener();

            this.setupElements();
        },

        setupElements: function () {

            this.pageLayoutSetup();

            this.setupDragDrop();

            this.setupModal();

            $(".row-fluid.draggable-source-block")
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                ;

            $(".raindrop_tips").tooltip();
        },

        removePopover: function (id) {
            $("#block-" + id).find('a.remove-popover').click();
        },

        removeBlock: function (id) {
            $.ajax({
               url: globalConfig.removeBlockPath.replace('0', id),
               type: 'POST',
               success: function (returnData) {
                   if (returnData.result) {
                       $("#block-" + id).find('a.remove-popover').click();
                       $("#block-" + id).remove();
                   }
               }
            });
        },

        setupModal: function () {

            var winWidth = $(window).width() - 80;
            var winHeight = $(window).height() - 200;

            $("body").prepend(
                $("<style/>", {
                    type: "text/css",
                    html:
                        '#tabLayout .modal {' +
                            'width: ' + (winWidth) + 'px;' +
                            'margin-left: -' + (winWidth/2) + 'px;' +
                        '}' +

                        '#tabLayout .modal .modal-body {' +
                            'max-height: ' + winHeight + 'px;' +
                        '}'

                })
            );

            $('#pagePreview').on('hide', function () {
                $(this).removeData("modal");
                $(this).find('.modal-body').html("");
            });
        },

        pageLayoutSetup: function () {

            $(".row-fluid.draggable-block:not(.hover-bound)")
                .addClass('hover-bound')
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                .find('.remove-popover')
                    .popover({
                        placement: 'top',
                        html: true
                    })
                .find(".btn-danger")
                    .click(function(){
                        var parentToDelete = $(this)
                            .parents(".row-fluid.draggable-block");

                        $.ajax({
                           url: globalConfig.removeBlockPath.replace('0', parentToDelete.data('id')),
                           type: 'POST',
                           success: function (returnData) {
                               if (returnData.result) {
                                   parentToDelete.remove();
                               }
                           }
                        });
                    })
                .end()
                ;
        },

        setupDragDrop: function () {

            $( ".draggable-source-block" )
                .draggable({
                    cancel: "a.ui-icon", // clicking an icon won't initiate dragging
                    revert: "invalid", // when not dropped, the item will revert back to its initial position
                    containment: "document",
                    helper: "clone",
                    connectWith: ".block-source"
                });

            // let the trash be droppable, accepting the gallery items
            $(".page-layout")
                .sortable({
                    placeholder: "ui-sortable-placeholder",
                    helper: "clone",
                    cursor: "move",
                    distance: 5,
                    stop: function( event, ui ) {

                        var post_data = {
                            ids: []
                        }

                        $(".page-layout .draggable-block")
                            .each(function(){
                                post_data.ids.push($(this).data('id'));
                            });

                        $.ajax({
                            url: globalConfig.orderBlocksPath,
                            type: "POST",
                            data: post_data
                        });
                    }
                });

            $(".page-layout").disableSelection();

            $(".page-layout")
                .droppable({
                    accept: "#tabLayout .draggable-source-block",
                    activeClass: "ui-state-highlight",
                    drop: function( event, ui ) {
                        pageAdmin.addBlockToLayout( ui.draggable );
                    }
                });
        },

        addBlockToLayout: function (draggable) {


            var url = globalConfig.addBlockPath;

            $.ajax({
                url: url.replace('block_type', draggable.data('block')),
                type: 'GET',
                success: function (returnData) {
                    if (returnData.result) {
                        $.ajax({
                            url: globalConfig.reloadBlocksPath,
                            type: 'GET',
                            success: function (returnData) {
                                if (returnData.result) {
                                    $(".page-layout-container")
                                        .html(returnData.result);
                                    pageAdmin.setupElements();
                                }
                            }
                        });
                    }
                }
            })
        }
    };
}());

$(function() {
    pageAdmin.init();
})