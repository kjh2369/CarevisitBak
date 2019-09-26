(function($){
    $.fn.touchToScroll = function() {

        var touchToScrollHandlers = {
            touchStart : function(event) {
                var e = $(this);
                var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                var data = { element: e, x: touch.pageX, y: touch.pageY, scrollX: e.scrollLeft(), scrollY: e.scrollTop() };
                $(document).bind("touchend", data, touchToScrollHandlers.touchEnd);
                $(document).bind("touchmove", data, touchToScrollHandlers.touchMove);
            },
            touchMove : function(event) {
                event.preventDefault();
                var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                var delta = {x: (touch.pageX - event.data.x), y: (touch.pageY - event.data.y) };
                event.data.element.scrollLeft(event.data.scrollX - delta.x);
                event.data.element.scrollTop(event.data.scrollY - delta.y);
            },
            touchEnd : function(event) {
                $(document).unbind("touchmove", touchToScrollHandlers.touchMove);
                $(document).unbind("touchend", touchToScrollHandlers.touchEnd);
            }
        }

        this.bind("touchstart", touchToScrollHandlers.touchStart);
        return this;
    };
})( jQuery );