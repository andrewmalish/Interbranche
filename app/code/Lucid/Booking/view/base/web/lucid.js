requirejs(['jquery', 'jquery/ui'], function (jQuery) {
    jQuery(function ($) {
        $(document).ready(function () {
            $('.account .tabs > li').click(function () {
                $('.step-container > div, .tabs > li').removeClass('active success');
                $(this).addClass('active').prevAll().addClass('success');
                $('.step-container > div').eq($(this).index()).addClass('active');
            });


            $(window).scroll(function() {
                var $fixed = $('body');
                var top = 35;
                if($fixed.hasClass('cms-home') && $(window).width() < 767) {
                    top = 0;
                }
                if ($(window).scrollTop() > top)
                    $fixed.addClass('fixed');
                else
                    $fixed.removeClass('fixed');

            });



            $('.block.block-search .block-title').click(function(e){
                if ($(e.target).closest(e).length) return;
                $(this).parent().toggleClass('active');
            });


            $(document).mouseup(function(e)
            {
                var container = $(".block.block-search");

                // if the target of the click isn't the container nor a descendant of the container
                if (!container.is(e.target) && container.has(e.target).length === 0)
                {
                    container.removeClass('active');
                }
            });

        });

        $('.prev').click(function () {
            current = $('.tabs > li.active');
            if(current.prev().length === 0 && !$('body').hasClass('booking-index-newcheckout')){
                location.href = '/motive.html';
                console.log('go to motive page');
                return false;
            }
            $('.step-container > div, .tabs > li').removeClass('active success');
            current.prev().addClass('active').prevAll().addClass('success');
            $('.step-container > div').eq(current.prev().index()).addClass('active');
        });

    });
});