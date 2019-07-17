/*
 * Author: Meeshal k
 */

$(document).ready(function(e){

  //fetching tag options
  getTagsAsOptionsTo('tags');

  //initializing select2 on tags for department page
  $('.select2').select2({
    tags: true,
    createTag: function (tag) {
        return {
            id: tag.term,
            text: tag.term,
            isNew : true
        };
    }
    //adding new tags to database
  }).on("select2:select", function(e) {
    if(e.params.data.isNew){
      var fd = new FormData();
      fd.append('newTag', e.params.data.text);
      var tagId = null;
      doAjax(function(newTag){
          var r = $.parseJSON(newTag);
          if(r.status == 1){
            $(".select2").find('[value="'+e.params.data.text+'"]').replaceWith('<option selected value="'+r.tagId+'">'+r.tagName+'</option>');
          }
        }, '/backoffice/tag/add', 'POST', fd);

    }
  });


  //creating slug for department name
  $(document).on('input', "#addNewDept [name=name]", function(e){
    var source = $(this).val();
    var slug = $('#addNewDept [name=slug]');
    slug.val(slugGenerator(source));
  });

  $(document).on('input change', "#addNewDept [name=meta_desc]", function(e){
    var source = $(this);
    var length = source.val().length;
    source.siblings('.counter_box').html("Characters: "+length);
  });

  //form validation for add department page
  $('#addNewDept').validate({
      rules: {
          name: {
              required: true,
              minlength: 3
          },
          meta_desc: {
            required: true,
            minlength: 100,
            maxlength: 180
          },
          'tags[]': {
            required: true,
            minlength: 5
          }
      },
      messages: {
          name: {
              required: "Come on, you have to give a name, don't you?",
              minlength: "The name must consist of at least 3 characters."
          },
          meta_desc: {
            required: "This is important for search results, you know that.",
            minlength: "We need 100 characters, not less than that.",
            maxlength: "woah, thats way too much. maximum length can be 180 characters."
          },
          'tags[]': {
            required: "You need to enter few tags, gor better SEO.",
            minlength: "Please enter atlest 5 tags."
          }
      },
      errorPlacement: function(error, element) {
        if (element.attr("name") == "tags[]") {
          error.appendTo( element.parent(".form-group") );
        }else{
          error.insertAfter(element);
        }
      },
      submitHandler: function(form, e) {
          e.preventDefault();
          doAjax(function(data){
            var r = $.parseJSON(data);
            if(r.status == 1){
              removeModalContent();
              addModalContent('Success!', r.data, isHtml = false, m = "",
              btn ='<a href="/backoffice/department" class="btn btn-success">Okay</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
              $('.model-global').modal('show');
              disableForm(form.getAttribute('id'));
            }else if(r.status == 0){
              removeModalContent();
              addModalContent('Falied!', r.data, isHtml = false, m = "",
              btn ='<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
              $('.model-global').modal('show');
            }
          }, form.getAttribute('action'),
             form.getAttribute('method'),
             buildFormdata($("#"+form.getAttribute('id')))
          );
      }
  });
});
