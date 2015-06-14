$( document ).ready(function(){
    $('a[data-toggle="tab"]').on('click', function (e) {
        var target = $(e.target).attr("href");

        $('')

        if ((target == '#youtube')) {
            alert('ok');
        }else{
            alert('not ok');
        }
    });
});
