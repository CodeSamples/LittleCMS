
(function($) {
    $.fn.messageManager = function(data) {
        var html='';
        $(this).hide();
        
        data["errors"].forEach(function(entry) {
           html+='<div class="ui-state-error ui-corner-all">';
           html+='<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';
           html+=entry+'</p></div>';
        });
        
        data["messages"].forEach(function(entry) {
           html+='<div class="ui-state-highlight ui-corner-all">';
           html+='<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>';
           html+=entry+'</p></div>';
        });
        
        $(this).html(html);
        $(this).fadeIn();
        
        setTimeout(function(){
            $(".auto-hidden").fadeOut();
        }, 5000);
        
        return this;
    };
}(jQuery));