/*
 * Author: Meeshal k
 */

 $(document).ready(function(){

   $('.model-global').on('hidden.bs.modal', function () {
     truelocation.reload();
   });

   $(document).on('input change', "#addSeedFile [name=note]", function(e){
     var source = $(this);
     var length = source.val().length;
     source.siblings('.counter_box').html("Characters: "+length);
   });

   $('#file').on('change', function(e){
    var elem = $(this);
    if (window.File && window.FileReader && window.FileList) {
      var size = handleFileSelect(e, 'na');
      $(this).siblings('.counter_box').html('<span class="hidden sizeInBytes">'+size+'</span>Total Size: '+formatBytes(size));
    } else {
      var out = elem.siblings('#fileErrors').addClass('alert-note').html("<code>Alert! We cannot show Image preview because your browser does not support File APIs from HTML5, please update your browser or use Google's Chrome latest version.</code>");
    }
  });

  $.validator.addMethod('filesize', function(value, element, param) {
    var fileSize = 0;
    $.each(element.files, function(i, e){
      fileSize += e.size;
    });
    return this.optional(element) || (fileSize <= param);
  });

  $.validator.addMethod("extension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "csv|png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
  }, $.format("Please enter a value with a valid extension."));

   //form validation for add department page
   $('#addSeedFile').validate({
       rules: {
           name: {
               required: true,
               minlength: 3,
               maxlength: 180
           },
           note: {
             maxlength: 180
           },
           file: {
             required: true,
             filesize: 52428800,
             extension:"csv"
           }
       },
       messages: {
           name: {
               required: "Come on, you have to give a name, don't you?",
               minlength: "The name must consist of at least 3 characters.",
               maxlength: "woah, thats way too much. maximum length can be 180 characters."
           },
           note: {
             maxlength: "woah, thats way too much. maximum length can be 180 characters."
           },
           file: {
             required:'You need to add the csv file, right?',
            filesize:'The videos must be under 50MB.',
            extension:'Only <code>.csv</code> files are accepted.'
           }
       },
       submitHandler: function(form, e) {
           e.preventDefault();
           loadS();
           var formData = buildFormdata($("#"+form.getAttribute('id')));
           formData.append('csv', $('#file')[0].files[0]);
           doAjax(function(data){
             console.log(data);
             var r = $.parseJSON(data);
             if(r.status == 1){
               removeModalContent();
               addModalContent('Success!', r.data, isHtml = true, m = "",
               btn ='<a href="/seed" class="btn btn-success">Okay</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
               $('.model-global').modal('show');
               disableForm(form.getAttribute('id'));
             }else if(r.status == 0){
               removeModalContent();
               addModalContent('Falied!', r.data, isHtml = true, m = "",
               btn ='<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
               $('.model-global').modal('show');
             }
             loadH();
           }, form.getAttribute('action'),
              form.getAttribute('method'),
              formData
           );

       }
   });

 });
