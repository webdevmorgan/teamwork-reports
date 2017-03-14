$(document).ready(function() {
    setTimeout(function() {
        $('.image_loader').hide();
        $('#mainTable').show();
    }, 4000);
    $.collapseMil();
    //$.collapseClick();
    $.collapseTask();
    if($('.tablesorter').length > 0) {
        $.stickysort();
        $.strikeTlAll();
        $.strikeTasks();
    } else {
        $.tsorter($('.sortable'));
        $.strikeTlAlltl();
        $.strikeTaskstl();
    }
    $.strikeMilestone();
    $.strikeLists();
    $.strikeAll();
    $.downloadPDF();
    $.collapseComments();
    $('input[type="checkbox"], .with-tooltip').tooltip({delay: { "show": 400}});

    $(window).scroll(function () {
        pos = $(window).scrollTop();
        button  = $('.dl-to-pdf')[0];
        dl = $(button).find('.download-pdf');
        margin = ($(window).width() - $('.container').width()) / 2;
        if(pos > $(button).height()) {
            $(button).addClass('topFixed');
            $(button).removeClass('marginTop');
            dl.css('margin-right', margin - 30 +'px');
        } else {
            $(button).removeClass('topFixed');
            $(button).addClass('marginTop');
            dl.css('margin-right', '');
        }
    });

    $(".download-option-show-hide").click(function(){
        $( ".download-options-container" ).toggle( "slow", function() {
        });
    });

});