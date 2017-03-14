$(document).ready(function() {

    $('.currency').autoNumeric('init');

    $(".members-rate").blur(function(){
        var id = $(this).data('memberid');
        var rate = $(this).val();
        $.ajax({
            url:'/members/update',
            type:'post',
            data: {'id': id, 'rate': rate },
            context: this,
            beforeSend: function() {
                $.showMiniLoader();
            },
            success: function(data) {
                $.unblockUI();
            }
        });
    });

    $("table.sortabletable").tablesorter({
        widgets: ["zebra", "filter"]
    });
});