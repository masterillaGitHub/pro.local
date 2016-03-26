$(document).load(updateChat());
var height = $('#chatBox').height();
console.log(height);
var url = window.location.pathname;
url = url.split('dialog')[1];


function scroll() {
    var scroll = $('#chatBox');
    var height = scroll[0].scrollHeight;
    scroll.scrollTop(height);
}

function updateChat() {
    $.ajax ({
                    type: "POST",
                    url: "/api/getmsg",
                    processData: false,
                    contentType: 'json',
                    cache: false,
                    success: function(data) {
                            $.each(data, function(index, value){
                                $('#chatBox').append('<div class="chat-box-left" id="'+value['id']+'" >'
                                    +value['text']+ '</br>'
                                    + value['createdDate'] +
                                    '</div>'
                                    +'<div class="chat-box-name-left">'+
                                        '<img src="http://train.local/img/id'+ value['userID'] +'/id'+ value['userID'] +'.jpg" class="img-circle" />'
                                        + value['userName'] +
                                    '</div>'+
                                    '<hr class="hr-clas" />'
                                );
                        });

                    }

            });
    scroll();
    $('#chatBox').animate({scrollTop:$('#chatBox').height()}, 'slow');
}

$('#myBtn').click(function () {
    event.preventDefault();
    $('#chatBox').empty();
    var data = $('#msg').val();
    console.log(data);
    data = 'msg={"msg": ' + JSON.stringify(data) +', "dialog": "'+ url +'"}';
    console.log(data);
    $.ajax ({
            type: "POST",
            url: "/api/sendmsg",
            data: data,

    });
    updateChat();
});


