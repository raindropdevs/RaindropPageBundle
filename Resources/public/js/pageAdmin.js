
(function($){
    $.fn.setCursorToTextEnd = function() {
        $initialVal = this.val();
        this.val('');
        this.val($initialVal);
    };
})(jQuery);


var pageAdmin = (function () {

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

        var paragraphConfig;

        $.ajax({
            url: globalConfig.urlCheckPath,
            type: 'POST',
            data: 'url=' + url,
            success: function (result) {
                if (result.available) {
                    config.urlParentGroup
                        .removeClass('error')
                        .addClass('success');

                    paragraphConfig = {
                        "html": "Url is not in use at the moment",
                        "class": "",
                        "style": "display:inline; margin-left: 40px; color: #007A2F"
                    };

                } else {
                    config.urlParentGroup
                        .removeClass('success')
                        .addClass('error');

                    paragraphConfig = {
                        "html": "Url is already taken by page named '" + result.page + "'",
                        "class": "",
                        "style": "display:inline; margin-left: 40px; color: #BD0000"
                    };
                }

                config.urlInputField
                    .parent()
                        .find('p').remove()
                    .end()
                    .append($('<p/>', paragraphConfig))
                    ;
            }
        });
    }

    var addUrlCheckerListener = function () {
        config.urlInputField
            .keyup(function (event) {
                clearTimeout(config.eventKeyUpTimeout);
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

            // TODO: fix following weak selector.
            config.urlParentGroup = $(".raindropPageBundleUrl").eq(0).parent();
            config.urlInputField = $(".raindropPageBundleUrl").eq(0);

            /*
             * Add listener for url input field
             */
            addUrlCheckerListener();

            this.setupElements();
        },

        /*
         * Setup page elements
         */
        setupElements: function () {

            /*
             * Blocks mouse over
             */
            this.setupMouseover();

            /*
             * Blocks popover to delete
             */
            this.setupPopover();

            /*
             * drag and drop
             */
            this.setupDragDrop();

            /*
             * force modal window size
             */
            this.setupModal();

            /*
             * Setup "Add block button" to show list up
             */
            this.setupBlockButton();

            $(".raindrop_tips").tooltip();

            this.focusOnUrl();

            this.setupScrollToolbar();
        },

        setupScrollToolbar: function () {
            $(window).scroll(function(){
                if ($(window).scrollTop() > 240)
                {
                    $(".raindrop-page-editor-toolbar")
                        .css({
                            'position': 'fixed',
                            'top': '70px',
                            'right': '20px'
                        })
                }
                else
                {
                    $(".raindrop-page-editor-toolbar")
                        .css({
                            'position': 'inherit'
                        })
                }
            });
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

        focusOnUrl: function () {
            if (window.location.href.indexOf('parent=') !== -1) {
                $(".raindropPageBundleUrl").eq(0).focus().setCursorToTextEnd();
            }
        },

        setupBlockButton: function () {
            $(".raindrop-add-block-button:not(.click-bound)")
                .addClass('click-bound')
                .click(function(){
                    $(".block-source").toggle();
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
        },

        setupMouseover: function () {

            $(".row-fluid.draggable-block:not(.hover-bound)")
                .addClass('hover-bound')
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                ;

            $(".row-fluid.draggable-source-block")
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                ;
        },

        setupPopover: function () {
            $(".draggable-block:not(.popover-bound)")
                .addClass('hover-bound')
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

            // make the blocks sortable
            $( ".draggable-source-block" )
                .draggable({
                    cancel: "a.ui-icon", // clicking an icon won't initiate dragging
                    revert: "invalid", // when not dropped, the item will revert back to its initial position
                    containment: "document",
                    helper: "clone",
                    connectWith: ".block-source",
                    appendTo: ".tabbable"
                });

            // let the trash be droppable, accepting the gallery items
            $(".raindrop-layout-sortable")
                .sortable({
//                    placeholder: "ui-sortable-placeholder",
                    helper: "clone",
                    cursor: "move",
                    connectWith: ".raindrop-layout-droppable",
                    distance: 5,
                    handle: "> .pageBlockToolbar > .drag-drop",
                    stop: function( event, ui ) {
                        $block = ui.item;
                        $target = $block.parent();

                        var post_data = {
                            ids: [],
                            move: $block.data('id'),
                            to: $target.data('target')
                        }

                        $(event.target).find('> .draggable-block')
                            .each(function(){
                                post_data.ids.push($(this).data('id'));
                            });

                        $.ajax({
                            url: globalConfig.orderBlocksPath,
                            type: "POST",
                            data: post_data,
                            error: function (data, asd) {
                                data = JSON.parse(data.responseText);
                                alert(data.error);
                            }
                        });
                    }
                });

            $(".page-layout").disableSelection();

            $(".raindrop-layout-droppable")
                .droppable({
                    accept: ".draggable-source-block",
                    activeClass: "ui-state-highlight",
                    drop: function( event, ui ) {
                        pageAdmin.addBlockToLayout( ui.draggable, event.target );
                    }
                });
        },

        addBlockToLayout: function (draggable, target) {

            var url = globalConfig.addBlockPath;

            // replace template url with proper value
            var real_url = url.replace('block_type', draggable.data('block')) + '/' + $(target).data('target');

            $.ajax({
                url: real_url,
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
        },

        switchToColor: function (elem, color) {
            $(elem).parents(".modal")
            .css({
                "background-color": color
            });
        }
    };
}());

$(function() {
    pageAdmin.init();
})