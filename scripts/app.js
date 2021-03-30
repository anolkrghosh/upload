let drop = $('#drop'), fileurl, fileArray = [];

drop.on('dragenter dragover dragleave drop', (e) => {
    e.preventDefault();
    e.stopPropagation();
})

drop.on('dragenter dragover', (e) => {
    drop.addClass("active");
});
drop.on('dragleave drop', (e) => {
    drop.removeClass("active");
})

drop.on('drop', handelSelect);

$('#triggerFile').on('click', (e) => {
    e.preventDefault();
    $('input').click();
    $('input').change(handelSelect);
});

function handelSelect(e) {
    let files;
    if (e.type == 'drop') {
        files = e.originalEvent.dataTransfer.files;
    } else {
        files = e.target.files;
    }
    if (files.length > 0) handleFiles(files);
}

function handleFiles(files) {
    //files template
    function template(file, index) {
        return `<div class="file __file_${index}">
                <div class="name"><span>${file}</span></div>
                <div class="progress"></div>
                <div class="percentage"><span>0%</span></div>
                <div class="done">
	                <a href="" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 1000 1000">
		                    <g><path id="path" d="M500,10C229.4,10,10,229.4,10,500c0,270.6,219.4,490,490,490c270.6,0,490-219.4,490-490C990,229.4,770.6,10,500,10z M500,967.7C241.7,967.7,32.3,758.3,32.3,500C32.3,241.7,241.7,32.3,500,32.3c258.3,0,467.7,209.4,467.7,467.7C967.7,758.3,758.3,967.7,500,967.7z M748.4,325L448,623.1L301.6,477.9c-4.4-4.3-11.4-4.3-15.8,0c-4.4,4.3-4.4,11.3,0,15.6l151.2,150c0.5,1.3,1.4,2.6,2.5,3.7c4.4,4.3,11.4,4.3,15.8,0l308.9-306.5c4.4-4.3,4.4-11.3,0-15.6C759.8,320.7,752.7,320.7,748.4,325z"</g>
		                </svg>
					</a>
                </div>
            </div>
        `;
    }

    $("#drop").addClass("hidden");
    $("footer").addClass("hasFiles");
    Object.keys(files).forEach((file) => {
        
        setTimeout(() => {
            
            $(".list-files").append(template(files[file].name,file))
            .animate({scrollTop: $('.list-files').prop("scrollHeight")});
            uploadFile(files[file], file).then((e)=>{
                $(".importar").addClass("active");
            })
            .catch((e)=>console.log(e))
        }, file*2500);
    });
}

$('.importar').click(importer)
function importer() {
    $('input').val("");
    $('.list-files').fadeOut(500, function () {
        $(this).empty().show();
        $(".importar").removeClass("active");
    })
        .promise()
        .then($("footer").removeClass("hasFiles"))
        .then(() => {
            setTimeout(() => {
                $("#drop").removeClass("hidden");
            }, 500);
        });
}
function uploadFile(file,i) {
    return new Promise((resolve, reject) => {
        let fd = new FormData();
        fd.append('file', file);
        $.ajax({

            type: 'POST',
            url: "./server.php",
            data: fd,
            processData: false,
            contentType: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        $('.__file_' + i).find('.progress').css(`background-size`, `${percentComplete}% 100%`);
                        $('.__file_' + i).find('.percentage').html(`<span>${Math.round(percentComplete)}%</span>`)

                    }
                }, false);
                return xhr;
            },
            success: function (data) {
                data = JSON.parse(data);
                if(data.status == 200 || data.status == 102){
                    $('.__file_' + i).find('.done').addClass('anim').children().attr('href','/files/'+data.id);
                }
               resolve(data);
            },
            error:(data)=>{
                reject(data);
            }
        });
    });
}