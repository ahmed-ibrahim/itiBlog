$(document).ready(function(){
    $('#commentButton').click(function(){
        var commText = $('#commentText').val();
        
        if($.trim(commText)){
            $('#commentLoader').show();
            $.ajax({
                url: addCommentUrl+"/"+commText,
                success : function(msg){
                    if(msg != 'faild'){
                        $('#commentsDiv').append(msg);
                    }
                },
                complete : function(){
                    $('#commentLoader').hide();
                }
            });
        }
        return false;
    });
});