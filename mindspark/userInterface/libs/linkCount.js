
$(document).ready(function()
    {
        $('.linkCount').click(function(event) {
//            event.preventDefault();
            var linkTo = $(this).attr("href");
//            var interface = 2;//For parent interface
            var fileName;
            fileName = linkTo.substring(linkTo.lastIndexOf("/"));
            fileName=fileName.replace('/','');
            $.ajax({
                url: "../userInterface/linkCount.php",
                type: "POST",
                data: {interface: interface, fileName: fileName},
                success: function(response) {
//                    window.open(linkTo,'_blank');
                },
                error: function(xhr, textStatus, thrownError) {
//                    alert("xhr status: " + xhr.statusText);
                },
            });
        });
    });