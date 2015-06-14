$('a[data-toggle="tab"]').on('click', function (e) {
    var target = $(e.target).attr("href");

    if(target == '#youtube') {
        $('#description').hide();
        $('#specialized').hide();
        $('#clients').hide();
        $(target).show();
    }
    else if (target == '#description') {
        $('#youtube').hide();
        $('#specialized').hide();
        $('#clients').hide();
        $(target).show();
    }
    else if (target == '#specialized') {
        $('#youtube').hide();
        $('#description').hide();
        $('#clients').hide();
        $(target).show();
    }
    else if (target == '#clients') {
        $('#youtube').hide();
        $('#description').hide();
        $('#specialized').hide();
        $(target).show();
    }

});