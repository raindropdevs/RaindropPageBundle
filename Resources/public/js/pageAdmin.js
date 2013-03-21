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

            config.urlParentGroup = $('#tab1 form.form-horizontal:eq(0) .control-group:eq(2)');
            config.urlInputField = $('#tab1 form.form-horizontal:eq(0) input[type=text]:eq(1)');

            addUrlCheckerListener();

            this.setupElements();
        },

        setupElements: function () {

            this.pageLayoutSetup();

            this.setupDragDrop();

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

        pageLayoutSetup: function () {

            $(".row-fluid.draggable-block:not(.hover-bound)")
                .addClass('hover-bound')
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                .find(".close")
                    .click(function(){

                        var parentToDelete = $(this)
                                .parents(".row-fluid.draggable-block");

                        var modalHtml =
                        '<div class="alert alert-error fade in">' +
//                            '<h4 class="alert-heading">Do you want to remove this element?</h4>'+
//                            '<p>'+
                              'WARNING: You are about to delete the block, its variables and all its children. '+
                              '<a href="#" class="btn btn-danger">Delete</a> or <a href="#" class="btn">Close</a>'+
//                            '</p>'+
                        '</div>';

                        $("#tab2")
                            .prepend(modalHtml);

                        $("#tab2")
                            .find('.alert .btn')
                            .click(function(){
                                $(".alert-error").remove();
                            })
                            .end()
                            .find(".btn-danger")
                            .click(function(){
                                $.ajax({
                                   url: globalConfig.removeBlockPath.replace('0', parentToDelete.data('id')),
                                   type: 'POST',
                                   success: function (returnData) {
                                       if (returnData.result) {
                                           parentToDelete.remove();
                                       }
                                   }
                                });

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
                    accept: "#tab2 .draggable-source-block",
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
//                        var newElement = draggable.clone();
//                        newElement
//                            .removeClass('fixed-width-200')
//                            .removeClass('draggable-source-block')
//                            .addClass('draggable-block')
//                            .append('<a class="close">&times;</a>')
//                            ;
//
//                        $(".page-layout").append(newElement);
//
//                        var modalHtml =
//                        '<div class="alert alert-success fade in">' +
//                              'block successfully created' +
//                              '<button data-dismiss="alert" class="close" type="button">×</button>' +
//                        '</div>';
//
//                        $("#tab2")
//                            .prepend(modalHtml);
                        $.ajax({
                            url: globalConfig.reloadBlocksPath,
                            type: 'GET',
                            success: function (returnData) {
                                if (returnData.result) {
                                    $(".page-layout")
                                        .html(returnData.result);
                                }
                            }
                        });

                        pageAdmin.pageLayoutSetup();
                    }
                }
            })
        }
    };
}());

$(function() {
    pageAdmin.init();
})