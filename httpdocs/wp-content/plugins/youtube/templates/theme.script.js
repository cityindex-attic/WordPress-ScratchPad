  
    $(document).ready(function(){
        
        $("#uploadYoutube a").click(function(e){
                tb_init("#youtube-popup-anchor");
                $('#youtube-popup-anchor').trigger('click');
                return false;
        });
        
        $(".recentYoutube").click(function(){
            var id = $(this).attr("id");
             tb_init("#"+id+"_run");
             $("#"+id+"_run").trigger('click');
             return false;
        });
        
	
        
        var url = window.location.href;
        $("#youtubeTitle").val("");
        $("#youtubeUrl").val("");
        $("#youtubeDesc").val("");
      
        // youtube upload popup
        
        $("#youtubeForm").submit(function(e){  
            var title  = $("#youtubeTitle").val();
            if(title=="" || title.length<3){
                $("#youtubeForm #result").html("Please enter title for your video");
                $("#youtubeForm #result").css("color", "#FF0000");
                return false;
            }
            var url  = $("#youtubeUrl").val();
            if(!ytVidId(url)){
                $("#youtubeForm #result").html("Please enter valid youtube url");
                 $("#youtubeForm #result").css("color", "#FF0000");
                return false;
            }
            $("#youtubeForm #result").css("color", "#000000");
            var description  = $("#youtubeDesc").val();
            var user  = $("#user_id").val();
            $("#youtubeForm #uploadMovie").css("background-position", "5px 8px");
            $("#youtubeForm #uploadMovie").val("Sending...");
           $.ajax({
                type:"POST",
                url: "/wp-admin/admin-ajax.php", 
                data: "action=addMovie&url="+url+"&description="+description+"&title="+title+"&user="+user,
                success:function(result){
                    if(result=="-1"){
                        $("#youtubeForm #result").html("You have to log in");
                    }
                    else{
                        $("#youtubeForm #result").html(result);
                    }
                    $("#youtubeForm #uploadMovie").css("background-position", "-100px 8px");
                    $("#youtubeForm #uploadMovie").val("Add movie");
                     $("#youtubeTitle").val("");
                     $("#youtubeUrl").val("");
                     $("#youtubeDesc").val("");
                }
                });
                e.preventDefault();
                return false;
        });
    });

function ytVidId(url) {
  var p = /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/;
  return (url.match(p)) ? true : false;
}
