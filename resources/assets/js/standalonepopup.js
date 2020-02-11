$(document).on('click','.popup_selector',function (event) {
    event.preventDefault();
    //the popup_selector should look something like this -
//      <div class=".col-md-2">
//         <div class="dropify-wrapper popup_selector" data-type="[png,jpg,jpeg]" data-route="Events" data-inputid="Files" data-max-file-count="{{$max_files}}">
//         <div class="dropify-message">
//         <span class="file-icon"></span>
//         <p>{{ Lang::get('global.upload_files')}}</p>
//      </div>
//      </div>
//      </div>
//      <div id="uploaded-files">
//
//       </div>
// use dropify js to render the images correctly or use any of them by your selection.
    var updateID = $(this).attr('data-inputid'); // Btn id clicked
    var MaxFiles = $(this).attr('data-max-file-count');
    var Route = $(this).attr('data-route');
    var elfinderUrl = '/elfinder/popup/';

    // trigger the reveal modal with elfinder inside
    var triggerUrl = elfinderUrl + updateID + '/' + MaxFiles + '/' + Route;
    $.colorbox({
        href: triggerUrl,
        fastIframe: true,
        iframe: true,
        width: '70%',
        height: '70%'
    });

});
function alertWarning(max_files) {
    swal(Lang.get('JsTrans.max_files')+ " - " + max_files);
}

function validateFiles(type) {
    swal(Lang.get('JsTrans.file_type') +" - " + type);
}

/**
 * @return {boolean}
 */
function CheckCount(max_files,urls) {
    var count = $("#uploaded-files .col-md-2").length;
    console.log(count);
    var total = count + urls;
    if (total > max_files) {
        swal(Lang.get('JsTrans.max_files')+ " - " + max_files);
        return false;
    }  else if (total <= max_files){
        return true;
    }

}
// function to update the file selected by elfinder
//it will append the block to the div with the selected ID, fille callback will return following information, use it as you wish:
//     <input type="hidden" id="" name="Files[]" value="${file.path}"> file path
//     <input type="hidden" id="" name="Files_url[]" value="${file.url}"> full picture url
//     <input type="hidden" id="" name="Files_route[]" value="${route}"> route name you have given to it
//     <input type="hidden" id="" name="Files_name[]" value="${file.name}"> picture name like picture.jpg
//     <input type="hidden" id="" name="Files_mime[]" value="${file.mime}"> mime type
//     <input type="hidden" id="" name="Files_tmb[]" value="${file.tmb}"> thm url to use for example on the front side
//update the biew block for your needs. first please publish the views.
function processSelectedFile(file,route) {
    $('#uploaded-files').append(`<div class="col-md-2" style="float: left; padding: 10px;">
        <div class="dropify-wrapper has-preview" style="height: 200px; width: 200px">
        <button type="button" class="dropify-clear" onclick="admin.clear(this)">Remove</button>
        <div class="dropify-preview" style="display: block;">
        <span class="dropify-render">
        <img src="/${file.path}">
        </span>
        <div class="dropify-infos">
        <div class="dropify-infos-inner">
        <p class="dropify-filename">
        <span class="file-icon"></span>
        <span class="dropify-filename-inner">${file.name}</span>
        </p>
    </div>
    </div>
    <input type="hidden" id="" name="Files[]" value="${file.path}">
    <input type="hidden" id="" name="Files_url[]" value="${file.url}">
    <input type="hidden" id="" name="Files_route[]" value="${route}">
    <input type="hidden" id="" name="Files_name[]" value="${file.name}">
    <input type="hidden" id="" name="Files_mime[]" value="${file.mime}">
    <input type="hidden" id="" name="Files_tmb[]" value="${file.tmb}">
    
    </div>
    </div>
        </div>`)
}
