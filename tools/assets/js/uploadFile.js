function uploadFileClose(name){
    $('#uploadfile'+name).popover('hide');
    // Hover befor close the preview
    $('#uploadfile'+name).hover(
        function () {
           $('#uploadfile'+name).popover('show');
        },
         function () {
           $('#uploadfile'+name).popover('hide');
        }
    );
};
function uploadFileOpen(name, uploadText, changeText, previewText = "Preview", contenMsg= "There's no image") {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class", "close pull-right");
    closebtn.attr("onclick", "uploadFileClose('"+name+"')");
    // Set the popover default content
    $('#uploadfile'+name).popover({
        trigger:'manual',
        html:true,
        title: "<strong>"+previewText+"</strong>"+$(closebtn)[0].outerHTML,
        content: contenMsg, //TODO revoir la langue avec Preview aussi
        placement:'bottom'
    });
    // Clear event
    $('#uploadfileclear'+name).click(function(){
        $('#uploadfile'+name).attr("data-content","").popover('hide');
        $('#uploadfilefilename'+name).val("");
        $('#uploadfileclear'+name).hide();
        $('#uploadfileinput'+name+' uploadfilefile'+name).val("");
        $('#uploadfileinputtitle'+name).text(uploadText);
    });
    // Create the preview image
    $('#uploadfileinput'+name+' #uploadfilefile'+name).change(function (){
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200
        });
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $('#uploadfileinputtitle'+name).text(changeText);
            $('#uploadfileclear'+name).show();
            $('#uploadfilefilename'+name).val(" "+file.name);
            img.attr('src', e.target.result);
            $('#uploadfile'+name).attr("data-content",$(img)[0].outerHTML).popover("show");
        }
        reader.readAsDataURL(file);
    });
};