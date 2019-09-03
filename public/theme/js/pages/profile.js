/*
 * Author: Meeshal k
 */

 $(document).ready(function(){

   // $('.model-global').on('hidden.bs.modal', function () {
   //   truelocation.reload();
   // });

   //form validation for add department page
   $('#changePasswordForm').validate({
       rules: {
           "current-password": {
               required: true,
               minlength: 6
           },
           "new-password": {
             required: true,
             minlength: 6
           },
           "new-password_confirmation": {
             required: true,
             equalTo : "#new-password"
           }
       },
       messages: {
           "current-password": {
               required: "Current password is requeried.",
               minlength: "Current password must consist of at least 6 characters."
           },
           "new-password": {
             required: "New password is requeried.",
             minlength: "New password must consist of at least 6 characters."
           },
           "new-password_confirmation": {
             required: "Confirm password is requeried.",
             equalTo: "Confirm password must match to New password."
           }
       },
       submitHandler: function(form, e) {
           e.preventDefault();
           form.submit();
           // loadS();
           // var formData = buildFormdata($("#"+form.getAttribute('id')));
           // formData.append('csv', $('#file')[0].files[0]);
           // doAjax(function(data){
           //   console.log(data);
           //   var r = $.parseJSON(data);
           //   if(r.status == 1){
           //     removeModalContent();
           //     addModalContent('Success!', r.data, isHtml = true, m = "",
           //     btn ='<a href="/seed" class="btn btn-success">Okay</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
           //     $('.model-global').modal('show');
           //     disableForm(form.getAttribute('id'));
           //   }else if(r.status == 0){
           //     removeModalContent();
           //     addModalContent('Falied!', r.data, isHtml = true, m = "",
           //     btn ='<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
           //     $('.model-global').modal('show');
           //   }
           //   loadH();
           // }, form.getAttribute('action'),
           //    form.getAttribute('method'),
           //    formData
           // );

       }
   });

 });
