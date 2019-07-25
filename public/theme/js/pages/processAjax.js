$(document).ready(function(){
  // $('.model-global').on('hidden.bs.modal', function () {
  //   location.reload();
  // });

  $('#domainProcess').submit(function(e){
    e.preventDefault();
    loadS();
    var form = $(this);
    btnD(form.find('input[type=submit]'));
    var formData = new FormData();
    $.each(form.serializeArray(), function(i, field) {
      formData.append(field.name, field.value);
    });
    var chunkSize = $(this).find('input[name=chunk_size]').val();
    var steps = $(this).find('input[name=steps]').val();
    var step = $(this).find('input[name=current_step]').val();
    doAjaxChunked(function(data){
      //console.log(data);
      var r = $.parseJSON(data);
      if(r.keepGoing == 1){

        var skip = r.currentStep*r.chunkSize;
        var total = r.steps*r.chunkSize;
        $('#processedUpto').html("Processed <code>&nbsp;"+skip+"&nbsp;</code> out of <code>&nbsp;"+total+"&nbsp;</code> domains.");
      }

      if(r.keepGoing == 0 && r.status == 1){
        btnA(form.find('input[type=submit]'));
        loadH();
        removeModalContent();
        addModalContent('Success!', '<strong><span class="text-green">Success: All the domains are processed, do you want to see the results?</span></strong>', isHtml = true, m = "",
        btn ='<a href="/seed/'+r.seedId+'" class="btn btn-success">Yes</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
        $('.model-global').modal('show');
        //disableForm(form.attr('id'));
      }

    }, form.attr('action'),
       form.attr('method'),
       formData, step, steps, chunkSize);
  });

});

function chunkProgress(p, pp = $('#chunkProgress')){
     if((p == 0) || (p == 100)){
       pp.removeClass('active').addClass('notransition').attr("aria-valuenow", p).css('width', p+'%').html(p+" %");
     }else{
       pp.removeClass('notransition').addClass('active').attr("aria-valuenow", p).css('width', p+'%').html(p+" %");
     }
}

function mainProgress(p, pp = $('#mainProgress')){
     if((p == 0) || (p == 100)){
       pp.removeClass('active').addClass('notransition').attr("aria-valuenow", p).css('width', p+'%').html(p+" %");
     }else{
       pp.removeClass('notransition').addClass('active').attr("aria-valuenow", p).css('width', p+'%').html(p+" %");
     }
}
