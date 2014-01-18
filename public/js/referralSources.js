$(document).ready(function () {

    var source, template, resp, formVals;

    var oTable = $('#refTable').dataTable({ });

    $('.ref-add').click(function (e) {
        e.preventDefault();
        source   = $('#add-template').html();
        template = Handlebars.compile(source);
        $('.ref-container').html(template());

        $('form').on('submit', function (e) {
            e.preventDefault();
            formVals = $(this).serializeArray();
            $.post('../private/referral_crud.php', formVals, function (data){
                var resp = $.parseJSON(data);
                if (resp.status === 'OK'){
                    $('.notify-text').html(resp.message);
                    $('.notify').addClass('alert-success').show().delay(2500).fadeOut();
                    $.post('../private/referral_crud.php',{'action':'read','id': resp.last_id}, function (d){
                        resp = $.parseJSON(d);
                        source   = $('#view-template').html();
                        template = Handlebars.compile(source);
                        $('.ref-container').html(template(resp));
                    });
                } else {
                    $('.notify-text').html(resp.message);
                    $('.notify').addClass('alert-danger').show().delay(2500).fadeOut();

                }
            });

        });

        $('form').validate({ });

    });

    //Request Edit
    $('.container').on('click', '.ref-edit', function (e) {
        var itemId = $(this).attr('data-id');
        $.post('../private/referral_crud.php', {'action': 'read', 'id': itemId}, function (data){
            resp = $.parseJSON(data);
            source   = $('#update-template').html();
            template = Handlebars.compile(source);
            $('.ref-container').html(template(resp));
            $('form').validate({ });
        });
    });

    //Edit referral
    $('.container').on('click','.ref-update', function (e) {
        e.preventDefault();
        formVals = $(this).closest('form').serializeArray();
        $.post('../private/referral_crud.php', formVals, function (data){
            var resp = $.parseJSON(data);
            if (resp.status === 'OK'){
                $('.notify-text').html(resp.message);
                $('.notify').addClass('alert-success').show().delay(2500).fadeOut();
                var itemId = resp.last_id;
                $.post('../private/referral_crud.php', {'action':'read','id': itemId}, function (data){
                    resp = $.parseJSON(data);
                    source   = $('#view-template').html();
                    template = Handlebars.compile(source);
                    $('.ref-container').html(template(resp));
                });
            } else {

            }
        });
    });

    //Cancel edit
    $('.container').on('click','.ref-cancel-update', function (e) {
        var itemId = $(this).attr('data-id');
        $.post('../private/referral_crud.php',{'action':'read','id': itemId}, function (d){
            resp = $.parseJSON(d);
            source   = $('#view-template').html();
            template = Handlebars.compile(source);
            $('.ref-container').html(template(resp));
        });
    });

    //Delete Referral
    $('.container').on('click','.ref-delete', function (e) {
        e.preventDefault();
        var ok = confirm('This will delete this record.  Are You Sure?');
        if (ok){
            var itemId = $(this).attr('data-id');
            $.post('../private/referral_crud.php', {'action':'delete','id': itemId}, function (data){
                var resp = $.parseJSON(data);
                if (resp.status === 'OK'){
                    $('.notify-text').html(resp.message);
                    $('.notify').addClass('alert-success').show().delay(2500).fadeOut();
                    $.post('../private/referral_crud.php',{'action':'read','id': resp.last_id}, function (d){
                        resp = $.parseJSON(d);
                        source   = $('#deleted-template').html();
                        template = Handlebars.compile(source);
                        $('.ref-container').html(template(resp));
                    });
                } else {
                    $('.notify-text').html(resp.message);
                    $('.notify').addClass('alert-danger').show().delay(2500).fadeOut();

                }
            });
        }
    });

    //Click a table row
    $('tbody tr').click(function (event){
        event.preventDefault();
        var itemId = $(this).attr('data-id');
        $.post('../private/referral_crud.php', {'action':'read','id': itemId}, function (data){
            resp = $.parseJSON(data);
            source   = $('#view-template').html();
            template = Handlebars.compile(source);
            $('.ref-container').html(template(resp));
        });
    });

});
