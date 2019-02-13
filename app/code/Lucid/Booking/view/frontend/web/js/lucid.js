requirejs(['jquery', 'jquery/ui'], function ($) {

   $(document).ready(function () {
        $('.block.block-search:after').click(function(){
            $(this).toggleClass('active');
        });
    });

})(jQuery);