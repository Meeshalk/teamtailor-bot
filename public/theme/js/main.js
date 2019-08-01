
$(document).ready(function(){

  // $('input[type=checkbox].truefalse').change(function(){
  //    if($(this).prop('checked')){
  //         $(this).val('TRUE');
  //    }else{
  //         $(this).val('FALSE');
  //         $(this).prop('checked', false);
  //    }
  // });

  $('.ajax-delete').on('click', function(e){
    e.preventDefault();
    var form = $(this).parent('form');
    var action = form.attr('action');
    var msg = form.data('msg');
    removeModalContent();
    addModalContent('Alert!', '<span class="text-red"><strong>'+msg+'</strong></span>', isHtml = true, m = "",
    btn ='<button class="btn btn-primary btnNo">No</button><button class="btn btn-danger btnYes">Yes, Delete It</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
    $('.model-global').modal('show');
    dialog('.model-global', function(response) {
      $('.model-global').modal('hide');
        if(response){
          form.submit();
        }else{
          return;
        }
      }
    );
  });


  $("#global-alert").fadeTo(4000, 500).fadeOut(1000, function(){
    $("#global-alert").fadeOut(5000);
  });

  $(".page-alert").fadeTo(6000, 500).fadeOut(1000, function(){
    $(".page-alert").fadeOut(5000);
  });
});

function handleFileSelect(evt, id) {
    var files = evt.target.files; // FileList object

    // Loop through the FileList and render image files as thumbnails.
    if(id != 'na'){
      var out = $('#'+id);
      out.html('');
    }

    var tSize = 0;
    for (var i = 0, f; f = files[i]; i++) {
      // Only process image files.
      tSize += f.size;

      if (!(f.type.match('image.*') || f.type.match('video.*'))) {
        continue;
      }

      if(f.size == 0 || id != 'na'){
        continue;
      }
      var reader = new FileReader();

      // Closure to capture the file information.
      reader.onload = (function(theFile) {
        return function(e) {
          // Render thumbnail.
          var wSpan = document.createElement('span');
          if(theFile.type.match('image.*')){
            wSpan.innerHTML = ['<img class="upload thumb" src="', e.target.result,
                              '" title="', escape(theFile.name), '"/><span class="imgFooter">Size: ', formatBytes(theFile.size) ,' <span class="imgDimention"></span></span>'].join('');
          }else if(theFile.type.match('video.*')){
            wSpan.innerHTML = ['<video controls class="upload vidThumb" src="', e.target.result,
                             '" title="', escape(theFile.name), '" type="' , theFile.type , '" width="225" /></video><span class="imgFooter"> Size: ', formatBytes(theFile.size) ,' <span class="imgDimention"></span><span>'].join('');
          }
            out.append(wSpan);
        };
      })(f);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f);
    }
    return tSize;
  }

  function formatBytes(bytes, decimals = 1) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
  }

function dialog(dialog, dialogCallback) {
  var dialog = $(dialog);
  dialog.find('.btnYes').click(function() {
    dialogCallback(true);
  });
  dialog.find('.btnNo').click(function() {
    dialogCallback(false);
  });
}



function getTagsAsOptionsTo(id){
  doAjax(function(tagOptions){
    var r = $.parseJSON(tagOptions);
    if(r.status == 1){
      $("#"+id).html(r.options);
    }
  }, '/backoffice/tag/options/get', 'GET', '');
}


function getDepartmentsAsOptionsTo(id){
  doAjax(function(tagOptions){
    var r = $.parseJSON(tagOptions);
    if(r.status == 1){
      $("#"+id).html(r.options);
    }
  }, '/backoffice/department/options/get', 'GET', '');
}




function disableForm(id){
  $('#'+id+' :input').attr('disabled', 'disabled');
  $('#'+id).fadeTo( "slow", 1, function() {
      $(this).find(':input').attr('disabled', 'disabled');
      $(this).find('label').css('cursor','default');
  });
}



function slugGenerator(str){
  return str
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
}


function disablemlem(elem){
  elem.prop('disabled', true);
}

function emablemlem(elem){
  elem.prop('disabled', false);
}

function print(doc) {
      var objFra = document.createElement('iframe');   // CREATE AN IFRAME.
      objFra.style.display = "none";    // HIDE THE FRAME.
      objFra.src = doc;                      // SET SOURCE.
      document.body.appendChild(objFra);  // APPEND THE FRAME TO THE PAGE.
      objFra.contentWindow.focus();       // SET FOCUS.
      objFra.contentWindow.print();      // PRINT IT.
}

function doAjax(retResult, url, type, data = ""){
  updateProgress(0);
  if(data == ""){
    var formData = new FormData();
    formData.append("data", "NA");
  }
  $.ajax({
    xhr: function()
    {
      var xhr = new window.XMLHttpRequest();
      //Upload progress
      xhr.upload.addEventListener("progress", function(evt){
        if (evt.lengthComputable) {
          var percentComplete = evt.loaded / evt.total;
          updateProgress(randnum(60, 80));
        }
      }, false);
      //Download progress
      xhr.addEventListener("progress", function(evt){
        if (evt.lengthComputable) {
          var percentComplete = evt.loaded / evt.total;
          updateProgress(percentComplete);
        }
      }, false);
      return xhr;
    },
    type: type,
    url: url,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
    },
    data: data,
    processData: false,
    contentType: false,
    success: function(result){
      retResult(result);
      updateProgress(100);
      $(".page-alert").fadeTo(6000, 500).fadeOut(1000, function(){
        $(".page-alert").fadeOut(5000);
      });
    },
    error: function(result){
      updateProgress(0);
    }
  });
}



function doAjaxChunked(retResult, url, type, data, step, steps, chunkSize){
  $.ajax({
    xhr: function()
    {
      var xhr = new window.XMLHttpRequest();
      //Upload progress
      xhr.upload.addEventListener("progress", function(evt){
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          chunkProgress(randnum(60, 80));
        }
      }, false);
      //Download progress
      xhr.addEventListener("progress", function(evt){
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          chunkProgress(randnum(60, 80));
        }
      }, false);
      return xhr;
    },
    type: type,
    url: url,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
    },
    data: data,
    processData: false,
    contentType: false,
    success: function(result){
      var r = $.parseJSON(result);

      if(r.keepGoing == 1){
        var formData = new FormData();
        formData.append('chunk_size', r.chunkSize);
        formData.append('steps', r.steps);
        formData.append('current_step', r.currentStep);
        formData.append('seed_id', r.seedId);
        mainProgress(rN((100/r.steps)*r.currentStep, 1));
        retResult(result);
        doAjaxChunked(function(result2){
          retResult(result2);
        }, r.url, r.type, formData);
      }else{
        mainProgress(100);
        retResult(result);
      }

      //while loop, step 0-9, mainProgress with result data
      $(".page-alert").fadeTo(6000, 500).fadeOut(1000, function(){
        $(".page-alert").fadeOut(5000);
      });
    },
    error: function(result){
      chunkProgress(0);
      mainProgress(0);
    }
  });
}

function buildFormdata(r){;
  var formData = new FormData();
  $.each(r.serializeArray(), function(i, field) {
    formData.append(field.name, field.value);
  });
  return formData;
}

function rN(num, scale) {
  if (Math.round(num) != num) {
    if (Math.pow(0.1, scale) > num) {
      return 0;
    }
    var sign = Math.sign(num);
    var arr = ("" + Math.abs(num)).split(".");
    if (arr.length > 1) {
      if (arr[1].length > scale) {
        var integ = +arr[0] * Math.pow(10, scale);
        var dec = integ + (+arr[1].slice(0, scale) + Math.pow(10, scale));
        var proc = +arr[1].slice(scale, scale + 1)
        if (proc >= 5) {
          dec = dec + 1;
        }
        dec = sign * (dec - Math.pow(10, scale)) / Math.pow(10, scale);
        return dec;
      }
    }
  }
  return num;
}



//modal
function addModalContent(title, body, isHtml = true, m = "", btn =""){
  if(m != "")
      var elem = m;
  else
      var elem = $('.model-global');
  if( btn != "" )
      elem.find('.modal-footer').html(btn);
  if( isHtml )
      elem.find('.modal-body').html(body);
  else
      elem.find('.modal-body').text(body);
  elem.find('.modal-title').text(title);
}

function removeModalContent(show = true, m = ""){
  if(m != "")
      var elem = m;
  else
      var elem = $('.model-global');
  elem.find('.modal-title').text("");
  elem.find('.modal-body').html("");
  elem.find('.modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
  if(show){
    elem.modal('hide');
  }
}


// progress bar
function updateProgress(p, pp = $('.progress-bar')){
    if((p == 0)){
       pp.removeClass('active');
       pp.css('width', 0+'%');
       pp.attr("aria-valuenow", 0);
       pp.find('span').html("");
     }else if(p == 100){
       pp.addClass('active');
       pp.attr("aria-valuenow", p);
       pp.css('width', p+'%');
       pp.find('span').html(p+"% Complete (Success)");
       setTimeout(function() { pp.addClass('notransition'); updateProgress(0);  }, 2000);
     }else{
       pp.removeClass('notransition');
       pp.addClass('active');
       pp.attr("aria-valuenow", p);
       pp.css('width', p+'%');
       pp.find('span').html(p+"% Complete (Success)");
     }
}

function randnum(min,max){
   return Math.floor(Math.random()*(max-min+1)+min);
 }

// alert data
function showAlert(msg, type, ement = "", time = 5000){
  var block_elem = $('#global-alert-js-block');
  var idOfElem = "global-alert-js_"+(block_elem.find('.alert').length + 1);
  block_elem.append("<div class='alert alert-dismissible fade in' id='"+idOfElem+"'><button type='button' class='close' data-dismiss='alert' aria-label='close'>&times</button><span class='alert-mgs'></span></div>");
  msg = "<strong style='text-transform:capitalize;'>"+type+"!</strong> "+msg;
  if(ement != ""){
    ement.focus();
    setValidate(ement, type);
  }
  var elem = $("#"+idOfElem);
  elem.addClass(("alert-"+type));
  elem.find('.alert-mgs').html(msg);
  elem.alert();
  setTimeout(function() { hideAlert(elem); }, time);
}

function hideAlert(elem){
  elem.removeClass("alert-error alert-waring alert-info alert-success");
  elem.find('.alert-mgs').html("");
  elem.alert('close');
}

function loadS(){
  $('#laodingDivUniversal').show();
}

function loadH(){
  $('#laodingDivUniversal').hide();
}

function btnA(sel, active = true, animate = true){
  sel.prop('disabled', false);
}

function btnD(sel, active = true, animate = true){
  sel.prop('disabled', true);
}


function doRedirect(loc){
  window.location = loc;
}



// validate form elements
function setValidate(elem, type){
  elem.parents('.form-group').addClass(("has-"+type));
}

function hideValidate(elem){
  elem.parents('.form-group').removeClass('has-error has-warning has-info');
}

function hideValidateSuccess(elem){
  elem.parents('.form-group').removeClass('has-error has-warning has-info has-success');
}
