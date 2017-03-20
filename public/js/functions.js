$(document).ready(function (){
    //image loader
    $.showLoader = function () {
        $.blockUI({
            message: '<h1><img src="/images/loader.gif" alt="100%"/><label id="progress_percent" style="margin-top:10px;"></label></h1>',
            css: {
                width: '50px',
                padding: '10px',
                border: 'none',
                borderRadius: '10px',
                left: '48%'
            }
        });
    };

    $.showMiniLoader = function () {
        $.blockUI({
            message: '<img src="/images/loader.gif" alt=""/>',
            css: {
                width: '50px',
                padding: '10px',
                border: 'none',
                borderRadius: '10px',
                left: '48%'
            }
        });
    }


    $.progressLoader = function (width, height, left) {
        $('body').block({
            message: '<img style="margin-right:5px;" src="/images/loader.gif" alt="Loading.."/><span style="font-weight:bold;" id="progress_percent"></span>',
            css    : {
                width       : width,
                height        : height,
                padding     : '5px',
                border      : 'none',
                top         : '200px',
                borderRadius: '10px',
                left        : left,
                'z-index'   : '99999'
            },
            baseZ  : 99999
        });
    };

    $.showGrowl = function (message) {
        $.blockUI({
            message: '<h1><img src="/images/loader.gif" alt=""/><label id="progress_percent"></label>Please wait while the file is being generated...</h1>',
            fadeIn: 700,
            fadeOut: 700,
            timeout: 2000,
            showOverlay: false,
            centerY: false,
            css: {
                width: '350px',
                top: '10px',
                left: '',
                right: '10px',
                border: 'none',
                padding: '5px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .6,
                color: '#fff'
            }
        });
    }

    //table sorter
    $.tsorter = function (elem) {
        elem.tablesorter({
            selectorHeaders: '> thead > tr > th',
            cssInfoBlock : "tablesorter-no-sort",
            cssChildRow: "tablesorter-childRow",
            widgets: ['filter' ],
            widgetOptions: {
                filter_childRows  : false,
                filter_cssFilter  : 'tablesorter-filter',
                filter_startsWith : false,
                filter_ignoreCase : true
            }
        });
    };

    //table sorter with sticky header
    $.stickysort = function () {
        $("table.tablesorter").tablesorter({
            cssChildRow: "tablesorter-childRow",
            widgets: ["filter"],
            widgetOptions: {
                filter_childRows  : false,
                filter_cssFilter  : 'tablesorter-filter',
                filter_startsWith : false,
                filter_ignoreCase : true
            }
        });
    };

    $.tasksort = function (elem) {
        elem.tablesorter({
            selectorHeaders: '> thead > tr > th',
            cssInfoBlock : "tablesorter-no-sort",
            widgets: [ 'zebra', 'filter' ],
            widgetOptions: {
                filter_childRows  : false,
                filter_cssFilter  : 'tablesorter-filter',
                filter_startsWith : false,
                filter_ignoreCase : true
            }
        });
    }

    //strikethrough when milestone checkbox is clicked
    $.strikeMilestone = function () {
        $('input[name="c_milestone"]').click(function(){
            var id = $(this).parent().parent().data('id');
            var child_row = $('tr.child-row-'+id);
            var milestone = $('tr.child-milestone-'+id);
            var tasklist = $('tr.child-mtasklist-'+id);
            var tasklist_all = $('tr#mil-'+id);
            var total_time = $('.milestone-total-'+id);
            if($(this).is(':checked')){
                $(this).parent().siblings('td:gt(0)').addClass('strikedout');
                child_row.find('input').prop('checked', true);
                child_row.find('td:gt(1)').addClass('strikedout');
                milestone.find('input').prop('checked', true);
                milestone.find('td:gt(1)').addClass('strikedout');
                tasklist.find('input').prop('checked', true);
                tasklist.find('th:gt(0)').addClass('strikedout');
                tasklist_all.find($('input[name="c_tasklist_all"]')).prop('checked', 'checked');
                total_time.find('.total-over-under').addClass('strikedout');
            } else {
                $(this).parent().siblings().removeClass('strikedout');
                child_row.find('input').prop('checked', false);
                child_row.find('td:gt(1)').removeClass('strikedout');
                milestone.find('input').prop('checked', false);
                milestone.find('td:gt(1)').removeClass('strikedout');
                tasklist.find('input').prop('checked', false);
                tasklist.find('th:gt(0)').removeClass('strikedout');
                tasklist_all.find($('input[name="c_tasklist_all"]')).prop('checked', false);
                total_time.find('.total-over-under').removeClass('strikedout');

            }

            /*if ($.getTotalChecked('c_milestone') > 0) {
             $('input[name=all_milestone]').prop('checked', false);
             } else {
             $('input[name=all_milestone]').prop('checked', true);
             }*/
        });
    }

    $.getTotalChecked = function (name) {
        return $('input[name='+name+']:not(:checked)').length;
    }

    //strikethrough when task checkbox is check
    $.strikeTasks = function () {
        $('input[name="c_task"]').click(function(){
            var id = $(this).parent().parent().data('id');
            var task = $(this).closest('.child-row-task');
            var parent_id = $(this).parent().parent().data('parent-id');
            if($(this).is(':checked')){
                //$(this).parent().siblings().addClass('strikedout');
                task.find('input').prop('checked', 'checked');
                task.find('td:gt(1)').addClass('strikedout');
                $('tr[data-id="'+id+'"] td:gt(1)').addClass('strikedout');
                if($('tr[data-parent-id="'+id+'"]').length > 0){
                    $('tr[data-parent-id="'+id+'"] td:gt(1)').addClass('strikedout');
                    $('tr[data-parent-id="'+id+'"]').find('input').prop('checked', true);
                }
            } else {
                //$(this).parent().siblings().removeClass('strikedout');
                task.find('input').prop('checked', false);
                task.find('td:gt(0)').removeClass('strikedout');
                $('tr[data-id="'+id+'"] td:gt(1)').removeClass('strikedout');
                if($('tr[data-parent-id="'+id+'"]').length > 0){
                    $('tr[data-parent-id="'+id+'"] td:gt(1)').removeClass('strikedout');
                    $('tr[data-parent-id="'+id+'"]').find('input').prop('checked', false);
                }
            }

            var id =  $(this).closest('.mil-task-list').attr('id').replace('task-', "");
            var mid = $(this).closest('.tablesorter-childRow').attr('id').replace('mil-', '');
            if($.getTotalChecked('c_task') > 0) {
                $('tr[data-id='+id+']').find('th').removeClass('strikedout');
                $('tr[data-id='+id+']').find('input').prop('checked', false);
                $('tr[data-id='+mid+'] td:gt(0)').removeClass('strikedout');
                $('tr[data-id='+mid+'] td:first input').prop('checked', false);
                $('tr#mil-'+mid).find('input[name=c_tasklist_all]').prop('checked', false);
                $('.tasklist-total-'+id).find('td:gt(0)').removeClass('strikedout');
                if($('tr[data-id="'+parent_id+'"]').length > 0){
                    var first_tr = $('tr[data-id="'+parent_id+'"]');
                    first_tr.find('input[name=c_task]').prop('checked', false);
                    first_tr.find('td:gt(0)').removeClass('strikedout');
                }
            } else {
                $('tr[data-id='+id+']').find('th').addClass('strikedout');
                $('tr[data-id='+id+']').find('input').prop('checked', true);
                $('tr[data-id='+mid+'] td:gt(1)').addClass('strikedout');
                $('tr[data-id='+mid+'] td:first input').prop('checked', true);
                $('tr#mil-'+mid).find('input[name=c_tasklist_all]').prop('checked', true);
                $('.tasklist-total-'+id).find('td:gt(0)').removeClass('strikedout');
            }
        });
    };

    $.strikeTaskstl = function () {
        $('input[name="c_task"]').click(function(){
            var id = $(this).parent().parent().data('id');
            var task = $(this).closest('.child-row-task');
            var parent_id = $(this).parent().parent().data('parent-id');
            if($(this).is(':checked')){
                task.find('input').prop('checked', 'checked');
                task.find('td:gt(1)').addClass('strikedout');
                $('tr[data-id="'+id+'"] td:gt(1)').addClass('strikedout');
                if($('tr[data-parent-id="'+id+'"]').length > 0){
                    $('tr[data-parent-id="'+id+'"] td:gt(1)').addClass('strikedout');
                    $('tr[data-parent-id="'+id+'"]').find('input').prop('checked', true);
                }
            } else {
                task.find('input').prop('checked', false);
                task.find('td:gt(1)').removeClass('strikedout');
                $('tr[data-id="'+id+'"] td:gt(1)').removeClass('strikedout');
                if($('tr[data-parent-id="'+id+'"]').length > 0){
                    $('tr[data-parent-id="'+id+'"] td:gt(1)').removeClass('strikedout');
                    $('tr[data-parent-id="'+id+'"]').find('input').prop('checked', false);
                }
            }
            var id = $(this).closest('.mil-task-list').attr('id').replace('task-', '');
            if($.getTotalChecked('c_task') > 0) {
                $('tr[data-id='+id+'] th:first input').prop('checked', false);
                $('tr[data-id='+id+']').find('th').removeClass('strikedout');
                $('.tasklist-total-'+id).find('td:gt(0)').removeClass('strikedout');
                if($('tr[data-id="'+parent_id+'"]').length > 0){
                    var first_tr = $('tr[data-id="'+parent_id+'"]');
                    first_tr.find('input[name=c_task]').prop('checked', false);
                    first_tr.find('td:gt(0)').removeClass('strikedout');
                }
            } else {
                $('tr[data-id='+id+'] th:first input').prop('checked', true);
                $('tr[data-id='+id+']').find('th').addClass('strikedout');
                $('.tasklist-total-'+id).find('td:gt(1)').addClass('strikedout');
            }

        });
    };

    //strikethrough when tasklist checkbox is clicked
    $.strikeLists = function () {
        $('input[name="c_tasklist"]').click(function(){
            var id =  $(this).closest('tr').data('id');
            var children = $('#task-'+id);
            var total =  $('.tasklist-total-'+id);
            if($(this).is(':checked')){
                $(this).parent().siblings('td:gt(0)').addClass('strikedout');
                total.find('td:gt(0)').addClass('strikedout');
                children.find('tr').each(function () {
                    $(this).find('td:gt(0)').addClass('strikedout');
                    $(this).find('input').prop('checked', 'checked');

                });
                /*
                $(this).parent().siblings('th').addClass('strikedout');
                total.find('th:gt(0)').addClass('strikedout');
                children.find('tr').each(function () {
                    $(this).find('th:gt(0)').addClass('strikedout');
                    $(this).find('input').prop('checked', 'checked');

                });
                */
            } else {
                $(this).parent().siblings().removeClass('strikedout');
                total.find('td:gt(0)').removeClass('strikedout');
                children.find('tr').each(function () {
                    $(this).find('td:gt(0)').removeClass('strikedout');
                    $(this).find('input').prop('checked', false);
                });
                /*
                $(this).parent().siblings().removeClass('strikedout');
                total.find('th:gt(0)').removeClass('strikedout');
                children.find('tr').each(function () {
                    $(this).find('th:gt(0)').removeClass('strikedout');
                    $(this).find('input').prop('checked', false);
                });
                */
            }

            if($(this).closest('.tablesorter-childRow').length > 0) {
                var id =  $(this).closest('.tablesorter-childRow').attr('id').replace('mil-', "");
                if($.getTotalChecked('c_tasklist') > 0) {
                    $(this).closest('.tablesorter-childRow').find('.tasklist_header input').prop('checked', false);
                    $('tr[data-id='+id+'] td:first input').prop('checked', false);
                    $('tr[data-id='+id+'] .indicator input').prop('checked', false);
                    $('tr[data-id='+id+'] td:gt(1)').removeClass('strikedout');
                } else {
                    $(this).closest('.tablesorter-childRow').find('th input').prop('checked', true);
                    $('tr[data-id='+id+'] .indicator input').prop('checked', true);
                    $('tr[data-id='+id+'] td:gt(1)').addClass('strikedout');
                }
            }


        });
    }

    //strikethough all milestone
    $.strikeAll = function () {
        $('input[name="all_milestone"]').click(function(){
            var milestone_row = $('.milestone_row');
            var mtasklist = $('.child-mtasklist');
            var row_task = $('.child-row-task');
            var total_time = $('.tasklist-total');
            if($(this).is(':checked')){
                milestone_row.find('td:gt(1)').addClass('strikedout');
                milestone_row.find('input').prop('checked', 'checked');
                mtasklist.find('input').prop('checked', 'checked');
                mtasklist.find('td:gt(1)').addClass('strikedout');
                mtasklist.find('th:gt(0)').addClass('strikedout');
                row_task.find('input').prop('checked', 'checked');
                row_task.find('td:gt(1)').addClass('strikedout');
                $('input[name="c_tasklist_all"]').prop('checked', 'checked');
                total_time.find('.total-over-under').addClass('strikedout');
            } else {
                milestone_row.find('td:gt(1)').removeClass('strikedout');
                milestone_row.find('input').prop('checked', false);
                mtasklist.find('input').prop('checked', false);
                mtasklist.find('td:gt(1)').removeClass('strikedout');
                mtasklist.find('th:gt(0)').removeClass('strikedout');
                row_task.find('input').prop('checked', false);
                row_task.find('td:gt(1)').removeClass('strikedout');
                $('input[name="c_tasklist_all"]').prop('checked', false);
                total_time.find('.total-over-under').removeClass('strikedout');
            }

        });
    }

    $.strikeTlAlltl = function () {
        $('input[name="c_tasklist_all"]').click(function(){
            var mtasklist = $('.child-mtasklist');
            var rowtask = $('.child-row-task');
            var total_time = $('.tasklist-total');
            if($(this).is(':checked')){
                mtasklist.find('input').prop('checked', 'checked');
                mtasklist.find('td:gt(1)').addClass('strikedout');
                rowtask.find('input').prop('checked', 'checked');
                rowtask.find('td:gt(1)').addClass('strikedout');
                mtasklist.find('th:gt(0)').addClass('strikedout');
                total_time.find('.total-over-under').addClass('strikedout');
            } else {
                mtasklist.find('input').prop('checked', false);
                mtasklist.find('td:gt(1)').removeClass('strikedout');
                rowtask.find('input').prop('checked', false);
                rowtask.find('td:gt(1)').removeClass('strikedout');
                mtasklist.find('th:gt(0)').removeClass('strikedout');
                total_time.find('.total-over-under').removeClass('strikedout');
            }

        });
    }

    //strikethrough all tasklists
    $.strikeTlAll = function () {
        $( document ).on( "click", 'input[name="c_tasklist_all"]', function(){
            var id = $(this).closest('.tablesorter-childRow').attr('id').replace("mil-", "");
            var mtasklist = $('.child-mtasklist-'+id);
            var rowtask = $('.child-row-'+id);
            var total_time = $('.milestone-total-'+id);
            if($(this).is(':checked')){
                mtasklist.find('input').prop('checked', 'checked');
                mtasklist.find('td:gt(1)').addClass('strikedout');
                rowtask.find('input').prop('checked', 'checked');
                rowtask.find('td:gt(1)').addClass('strikedout');
                mtasklist.find('th:gt(0)').addClass('strikedout');
                total_time.find('.total-over-under').addClass('strikedout');
            } else {
                mtasklist.find('input').prop('checked', false);
                mtasklist.find('td:gt(1)').removeClass('strikedout');
                rowtask.find('input').prop('checked', false);
                rowtask.find('td:gt(1)').removeClass('strikedout');
                mtasklist.find('th:gt(0)').removeClass('strikedout');
                total_time.find('.total-over-under').removeClass('strikedout');
            }

        });
    }

    $.collapseDefault = function (selector) {
        var dis           = $(selector);
        var height = dis.height();

        var line_height   = dis.css('line-height');
        line_height       = parseFloat(line_height);
        var rows          = Math.round(height / line_height);
        var collapse      = Math.round((line_height * 3));

        if (rows > 3) {
            dis.css({
                'height': collapse + 'px',
                'overflow-y': 'hidden'
            });
        }

        return rows;
    }
    //collapse desc
    $.collapse = function (selector) {
        var dis           = $(selector);
        if(dis.closest('.mil-task-list').is(':hidden')) {
            dis.closest('.mil-task-list').show();
            var height = dis.height();
            dis.closest('.mil-task-list').hide();
        } else {
            var height = dis.height();
        }

        var line_height   = dis.css('line-height');
        line_height       = parseFloat(line_height);
        var rows          = Math.round(height / line_height);
        var collapse      = Math.round((line_height * 3));

        if (rows > 3) {
            dis.css({
                'height': collapse + 'px',
                'overflow-y': 'hidden'
            });
        }

        return rows;
    }


    //description collapse
    $.collDesc = function (elem) {
        elem.find('.description').each(function(){
            var dis  = $(this);
            var rows = $.collapse(dis);

            if (rows> 3) {
                dis.after('<a class="collapse"><i class="fa fa-plus-square-o fa-lg"></i></a>');
            }
        });
    }

    //description collapse
    $.collapseClick = function (task_id) {
        $('.collapse_'+task_id).click(function(){
            var parent = $(this).siblings();
            var dis    = $(this);
            if (dis.html() == '<i class="fa fa-plus-square-o fa-lg"></i>') {
                parent.css({
                    'height': 'auto',
                    'overflow-y': 'auto'
                });
                dis.html('<i class="fa fa-minus-square-o fa-lg"></i>');
            } else {
                dis.html('<i class="fa fa-plus-square-o fa-lg"></i>');
                $.collapse(parent);
            }
        });
    }

    $.hideChildrow = function () {
        $('.tablesorter-childRow').each(function(){
            $(this).hide();
            $(this).addClass("hide-mil");
        });
    }

    $.collapseMil = function () {
        $('.collapse-mil').click(function(){
            var tr = $(this).closest('tr');
            var mil_id = tr.data('id');
            if($('#mil-'+mil_id).hasClass('hide-mil')){
                $('#mil-'+mil_id).show();
                $('#mil-'+mil_id).removeClass("hide-mil");
                $('#mil-'+mil_id).addClass("show-mil");
                $(this).html('<i class="fa fa-minus-square-o fa-lg"></i>');
                //$.tsorter($('#mil-'+mil_id).find('.sortable'));
                $.tasksort($('#mil-'+mil_id).find('.sortable'));
                $('input[type="checkbox"]').tooltip();
            } else if($('#mil-'+mil_id).hasClass('show-mil')) {
                $('#mil-'+mil_id).hide();
                $('#mil-'+mil_id).removeClass("show-mil");
                $('#mil-'+mil_id).addClass("hide-mil");
                $(this).html('<i class="fa fa-plus-square-o fa-lg"></i>');
            }

        });
    }

    $.collapseTasklist = function () {
        $('.mil-task-list').each(function(){
            $(this).hide();
            $(this).addClass("hide-task");
        });
    }

    $.showHideTask = function (el,task_id) {
        if($('#task-'+task_id).hasClass('hide-task')){
            $('#task-'+task_id).show();
            $('#task-'+task_id).removeClass("hide-task");
            $('#task-'+task_id).addClass("show-task");
            el.html('<i class="fa fa-minus-square-o fa-lg"></i></i>');
            $('.tasklist-total-'+task_id).show();
        } else if($('#task-'+task_id).hasClass('show-task')) {
            $('#task-'+task_id).hide();
            $('#task-'+task_id).removeClass("show-task");
            $('#task-'+task_id).addClass("hide-task");
            el.html('<i class="fa fa-plus-square-o fa-lg"></i>');
            $('.tasklist-total-'+task_id).hide();
        }
    }

    $.collapseTask = function () {
        $('.collapse-task').on('click',function(){
            var el = $(this);
            var tr =  el.closest('tr');
            var parent = el.closest('.tablesorter-no-sort');
            var task_id = tr.data('id');
            var milestone = el.closest('.tablesorter-childRow').prop('id');

            $.showHideTask(el,task_id);

            if(!el.hasClass('opened')){
                parent.next().find('.description').each(function(){
                    var dis  = $(this);
                    var rows = $.collapseDefault(dis);
                    if (rows> 3) {
                        dis.after('<a class="desc-collapse collapse_'+task_id+'"><i class="fa fa-plus-square-o fa-lg"></i></a>');
                    }
                });
            }

            el.addClass('opened');
            $.collapseClick(task_id);
        });
    }

    $.countColSpan = function (sections) {
        var no_of_sections = sections.length;
        var colspan = 0;

        if($.inArray('time-entry', sections) != -1 ){
            no_of_sections = no_of_sections + 2;
        }

        if($.inArray('date-entries', sections) != -1 ){
            no_of_sections = no_of_sections + 1;
        }

        if($.inArray('desc-entry', sections) != -1 ){
            no_of_sections = no_of_sections - 1;
        }

        if($.inArray('comment-entry', sections) != -1 ){
            no_of_sections = no_of_sections - 1;
        }

        colspan = 12 - no_of_sections;

        return colspan;
    }

    $.countOverUnderSpan = function (sections) {
        var no_of_sections = sections.length;
        var colspan = 0;

        if($.inArray('time-entry', sections) != -1 ){
            no_of_sections = no_of_sections + 2;
        }

        if($.inArray('date-entries', sections) != -1 ){
            no_of_sections = no_of_sections + 1;
        }

        if($.inArray('desc-entry', sections) != -1 ){
            no_of_sections = no_of_sections - 1;
        }

        if($.inArray('comment-entry', sections) != -1 ){
            no_of_sections = no_of_sections - 1;
        }

        colspan = 9 - no_of_sections;

        return colspan;
    }

    /*$('.test-click').click(function (){
     var sections = new Array();
     $.each($(".downloadable-form-options input[name='sections[]']:checked"), function() {
     sections.push($(this).val());
     });
     var colspan = $.countColSpan(sections);
     console.log(colspan);
     var colspan = sections.length;

     if($.inArray('time-entries', sections) != -1 ){
     colspan = colspan + 2;
     }

     //console.log(sections);
     var content = $.cleanHTML($('.content_wrapper').html(), sections);
     });*/
    $.downloadPDF = function () {
        $('.download-pdf').click(function (){
            var sections = new Array();
            $.each($(".downloadable-form-options input[name='sections[]']:checked"), function() {
                sections.push($(this).val());
            });
            var content = $.cleanHTML($('.content_wrapper').html(), sections);
            $.ajax({
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            var percent = percentComplete.toFixed(2);
                            var final_ = parseInt(percent*100, 10);
                            $('#progress_percent').text( final_ +"%");
                        }
                    }, false);
                    xhr.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                        }
                    }, false);
                    return xhr;
                },
                url: "/ajax/createPDF",
                data:{'content': content},
                beforeSend: function () {
                    $.progressLoader('75px', '28px', '48%');
                },
                method: 'post',
                success: function (data) {
                    $.unblockUI();
                    window.location = '/download?file='+data.filename;
                }
            });
        });
    }

    $.collapseComments = function() {
        $('.collapse-comments').on('click',function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            if($(this).hasClass('opened')) {
                $('.comment_wrap_'+id).show();
                $(this).html('<i class="fa fa-minus-square-o fa-lg"></i></i>');
                $(this).removeClass('opened');
                $(this).addClass('closed');
            } else {
                $('.comment_wrap_'+id).hide();
                $(this).addClass('opened');
                $(this).html('<i class="fa fa-plus-square-o fa-lg"></i></i>');
                $(this).removeClass('closed');
            }
        });
    }

    $.cleanHTML = function (html, sections) {
        var body = $(html);
        console.log(sections);

        var colspan = $.countColSpan(sections);
        //countOverUnderSpan
        var over_under_colspan = $.countOverUnderSpan(sections);

        body.find(".main_header th").removeAttr('style');
        body.find(".main_header th").removeAttr('width');
        /*body.find(".main_header th:eq(0)").attr('style', 'width:2%;');
         body.find(".main_header th:eq(1)").attr('style', 'width:1%;');
         body.find(".main_header th:eq(2)").attr('style', 'width:17%');
         body.find(".main_header th:eq(3)").attr('style', 'width:13%');
         body.find(".main_header th:eq(4)").attr('style', 'width:13%');
         body.find(".main_header th:eq(5)").attr('style', 'width:13%');*/
        body.find(".sortable th").removeAttr('width');
        body.find(".sortable th").removeAttr('style');
        body.find(".tasklist_header th").removeAttr('style');
        body.find(".tasklist_header th").removeAttr('width');
        body.find(".tablesorter-headerRow th").removeAttr('style');
        body.find(".tablesorter-headerRow th").removeAttr('width');
        /*body.find(".tasklist_header th:eq(1)").attr('style', 'width:10');
         body.find(".tasklist_header th:eq(2)").attr('style', 'width:10%');
         body.find(".tasklist_header th:eq(3)").attr('style', 'width:20%');
         body.find(".tablesorter-headerRow th:eq(1)").attr('style', 'width:10');
         body.find(".tablesorter-headerRow th:eq(2)").attr('style', 'width:10%');
         body.find(".tablesorter-headerRow th:eq(3)").attr('style', 'width:15%');*/
        body.find(".download-pdf").remove();
        body.find('td.strikedout').closest('tr').remove();
        body.find('th.strikedout').closest('tr').remove();
        body.find('td.description p').removeAttr('style');
        body.find("tr.hide-mil").remove();
        body.find("tr.hide-task").remove();
        body.find("input[type=checkbox]").remove();
        body.find('.tablesorter-stickyHeader').remove();
        body.find('.tablesorter-filter-row').remove();
        body.find('colgroup').remove();
        body.find('.header_mile').replaceWith(body.find('.header_mile').contents())
        body.find('.comp_logo').show();
        body.find('.hide-for-downloadables').remove();
        body.find('.description').removeAttr('style');

        $.each( sections, function( key, value ) {
            body.find("."+value).remove();
        });

        body.find('.child-mtasklist-title').attr('colspan',colspan);
        body.find('.total-offset').attr('colspan',over_under_colspan);

        body.find('.tasklisttable tr th:first-child').remove();
        body.find('.task tr th:first-child').remove();
        body.find('.mil-task-list tr td.tablesorter-inner-indicator').remove();

        //Change colspan value
        if(sections.length > 0){
            var comments_row_colspan = 11;
            var sections_length = 0;
            if(sections.indexOf("time-entry") >= 0){
                sections_length += 2;
            }
            if(sections.indexOf("date-entries") >= 0){
                sections_length += 1;
            }
            if(sections.indexOf("desc-entry") >= 0){
                sections_length -= 1;
            }
            if(sections.indexOf("comment-entry") >= 0){
                sections_length -= 1;
            }


            sections_length += sections.length;

            comments_row_colspan = comments_row_colspan - sections_length;

            body.find('.tasklisttable tr td.desc_area').attr('colspan',comments_row_colspan);
            body.find('.tasklisttable tr td.comments-row').attr('colspan',comments_row_colspan);
        }

        $(body.find('.sortable')).each(function () {
            if($(this).find('.mil-task-list').children().length == 0) {
                //$(this).closest('.tablesorter-childRow ').remove();
            }
        });
        return $(body.get(2)).html();
    }
});