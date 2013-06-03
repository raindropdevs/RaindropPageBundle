var menuAdmin = (function () {
    return {
        init: function () {
            this.setupPagesButton();

            this.setupMouseover();

            this.setupDragAndDrop();

            // this.setupSearch();

            this.setupPopover();
        },

        setupPopover: function () {
            $(".remove-popover")
                .popover({
                    placement: 'top',
                    html: true
                });
        },

        setupSearch: function () {
            var options = {
                valueNames: [ 'name' ]
            };
            var pagesList = new List('page-list', options);
        },

        setupPagesButton: function () {
            $(".raindrop-add-menu-button:not(.click-bound)")
                .addClass('click-bound')
                .click(function(){
                    $(".page-source").toggle();
                });
        },

//        closePopover: function (menu_id) {
//            $("#menu-" + menu_id).find('a.remove-popover').click();
//        },
//
//        removeMenu: function (menu_id) {
//            $.ajax({
//               url: globalConfig.removeMenu.replace('0', menu_id),
//               type: 'POST',
//               success: function (returnData) {
//                   if (returnData.result) {
//                       $("#menu-" + id).find('a.remove-popover').click();
//                       $("#menu-" + id).remove();
//                   }
//               }
//            });
//        },

        setupMouseover: function () {

            $(".row-fluid.draggable-menu:not(.hover-bound)")
                .addClass('hover-bound')
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                ;

            $(".row-fluid.draggable-source-page:not(.hover-bound)")
                .addClass('hover-bound')
                .mouseenter(function(){
                    $(this).addClass('hover');
                })
                .mouseleave(function(){
                    $(this).removeClass('hover');
                })
                ;
        },

        setupDragAndDrop: function () {
            $( ".draggable-source-page" )
                .draggable({
                    cancel: "a.ui-icon", // clicking an icon won't initiate dragging
                    revert: "invalid", // when not dropped, the item will revert back to its initial position
                    containment: "document",
                    helper: "clone",
                    appendTo: '.tabbable'
                });

            $(".droppable")
                .droppable({
                    accept: ".draggable-source-page",
                    activeClass: "ui-state-highlight",
                    drop: function( event, ui ) {
                        menuAdmin.addPageToMenu( ui.draggable, event.target );
                    }
                });

            $(".sortable")
                .sortable({
                    stop: function ( event, ui ) {

//                        var children = $(event.target).find('> li');
                        var children = $(".menu-layout-container").find('li');
                        var ids = [];
                        children.each(function () {
                            if ($(this).data('id') !== undefined) {
                                ids.push($(this).data('id'));
                            }
                        });

                        $.ajax({
                            url: globalConfig.reorderMenu,
                            type: 'POST',
                            data: { ids: ids }
                        });
                    }
                });
        },

        addPageToMenu: function (elem, target) {

            var url = globalConfig.addMenuItemPath;
            var post_data = {
                'menu_id': globalConfig.menuId,
                'page_id': elem.data('id')
            };

            $.ajax({
                url: url,
                data: post_data,
                success: function (success) {
                    window.location.reload();
                }
            })
        }
    };
}());

$(function () {
    menuAdmin.init();
})

