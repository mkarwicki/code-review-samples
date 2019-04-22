export default class AjaxForm {

    constructor(form) {
        this.form =  form;
        this.options = {
            target:        false,   // target element(s) to be updated with server response
            beforeSubmit:  this.beforeSubmit,  // pre-submit callback
            success:       this.success,  // post-submit callback
            error:         this.error,
        };
        this.invokeForm();
    }


    invokeForm(){
        let options = (this.options);
        this.form.submit(function(event) {
            $(this).ajaxSubmit(options);
            event.preventDefault();
            return false;
        });
    }

    beforeSubmit(formData, jqForm, options){
        $('.ajax-form').addClass('ajax-form-loading');
        $('.ajax-form *').attr('disabled', true);
    }

    success(responseText, statusText, xhr, $form){
        let formData=$(responseText).find('.ajax-form');
        $('.ajax-form').removeClass('ajax-form-loading');
        $('.ajax-form *').attr('disabled', false);
        $('.ajax-form').html(formData.html())
    }

    error(jqXHR,textStatus,errorThrown ){
        $('.ajax-form').removeClass('ajax-form-loading');
        $('.ajax-form *').attr('disabled', false);
        alert('error'+textStatus);
    }


}
