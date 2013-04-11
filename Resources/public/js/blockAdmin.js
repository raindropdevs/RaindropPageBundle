
var blockAdmin = (function(){
    return {
        init: function () {
            this.setupEverything();
        },

        setupEverything: function () {
            $(".raindrop_tips").tooltip();
        }
    };
}());

$(function(){
    blockAdmin.init();
});
