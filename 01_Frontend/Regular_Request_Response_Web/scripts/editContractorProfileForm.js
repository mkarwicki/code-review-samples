import AjaxForm from "/assets/js/utils/ajaxForm.js";


export default class EditContractorProfileForm extends AjaxForm {
    constructor(form) {
        super(form);
        this.addUploadPhotoEventSubscriber();
        this.setupSectorWidget();
    }

    addUploadPhotoEventSubscriber(){
        $(document).on('click','.profile-photo-container .photo',function(event){
            event.preventDefault();
            $(this).parent().find('input').trigger('click');
        })
    }

    setupSectorWidget(){
        $(document).on('click','[data-action=add-new-sector]',function(event){
            event.preventDefault();
            $('[data-action=hidden-sector-stack] .user-sector-row').clone().appendTo($('[data-action=user-sectors-section]'));
        });
        $(document).on('click','[data-action=remove-sector]',function(event){
            event.preventDefault();
            let sectorsLength = $('[data-action=user-sectors-section] .user-sector-row').length;
            if(sectorsLength<=1){
                alert('One sector is required')
            }else{
                $(this).closest('.user-sector-row').remove();
            }
        });
    }
}





