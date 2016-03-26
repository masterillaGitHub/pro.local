$(document).ready(function (){

    $('#text-post').change(function() {
       $('#input-area').html('<textarea class="form-control" name="content" placeholder="Update your status"></textarea>');
    });
    $('#media-post').change(function() {
        $('#input-area').html('<input type="file" name="img" >');
    });

    $('#sendPost').click(function(){
        event.preventDefault();
        var titlePost = $('post-title').val();
        var contentPost = $('post-content').val();
        var typePost;
        if ($('#text-post').is(':checked')) {
            typePost = 'text';
        };

        if ($('#media-post').is(':checked')) {
            typePost = 'media';
        };
        alert('Type post ' + typePost);

        $.ajax({
            url: '/api/sendpost',
            type: 'POST',
            data: post = {title: titlePost, contetn: contentPost, type: typePost},
        })
        .done(function() {
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

});

