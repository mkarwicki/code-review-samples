/*GLOBAL*/
import Api from "/assets/js/api/api.js";
import TraversingUtils from "/assets/js/utils/traversingUtils.js";
/*VISITORS*/
import VisitorsJobListBrowser from "/assets/js/visitors/jobs_list/visitorsJobListBrowser.js";
/*USERS COMMON*/
import ChangePasswordForm from "/assets/js/users/common/changePasswordForm.js";
/*CONTRACTORS*/
import EditContractorProfileForm from "/assets/js/users/contractors/editContractorProfileForm.js"
import EditContractorProfileNotificationForm from "/assets/js/users/contractors/editContractorNotificationForm.js"
import EditContractorProfileDetailsForm from "/assets/js/users/contractors/editContractorProfileDetailsForm.js"
import PostYourBidForm from "/assets/js/users/contractors/postYorBidForm.js"
/*COMPANY*/
import EditCompanyProfileForm from "/assets/js/users/companies/editCompanyProfileForm.js"
import CompanyPostAJobForm from "/assets/js/users/companies/companyPostAJobForm.js"



class ApplicationSingleton {
     constructor(api,traversUtil) {
        this.api =  api;
        this.traversUtil =  traversUtil;
    }
    invokeAjaxForms(){
        //CHANGE PASSWORD FORM
        if(this.traversUtil.elementExists('#changePasswordForm')){
            new ChangePasswordForm($('#changePasswordForm'));
        }
        //EDIT CONTRACTOR PROFILE FORM
        if(this.traversUtil.elementExists('#editContractorProfileForm')){
            new EditContractorProfileForm($('#editContractorProfileForm'));
        }
        //EDIT CONTRACTOR AND COMPANY PROFILE DETAILS FORM
        if(this.traversUtil.elementExists('#editContractorProfileDetailsForm')){
            new EditContractorProfileDetailsForm($('#editContractorProfileDetailsForm'));
        }
        //EDIT COMPANY PROFILE FORM
        if(this.traversUtil.elementExists('#editCompanyProfileForm')){
            new EditCompanyProfileForm($('#editCompanyProfileForm'));
        }
        //EDIT CONTRACTOR PROFILE NOTIFICATION FORM
        if(this.traversUtil.elementExists('#editContractorProfileNotificationForm')){
            new EditContractorProfileNotificationForm($('#editContractorProfileNotificationForm'));
        }
        //EDIT CONTRACTOR PROFILE NOTIFICATION FORM
        if(this.traversUtil.elementExists('#editContractorProfileNotificationForm')){
            new EditContractorProfileNotificationForm($('#editContractorProfileNotificationForm'));
        }
        //COMPANY POST A JOB FORM
        if(this.traversUtil.elementExists('#companyPostAJobForm')){
            new CompanyPostAJobForm($('#companyPostAJobForm'));
        }
        //POST A BID FORM
        if(this.traversUtil.elementExists('form[data-action=post-your-bid-form]')){
            new PostYourBidForm($('form[data-action=post-your-bid-form]'));
        }
    }

    invokeBrowsers(){
        if(this.traversUtil.elementExists('#visitorsUsersBrowser')){
            let visitorsJobListBrowser = new VisitorsJobListBrowser($('#visitorsUsersBrowser'));
        }
    }
}


let app = new ApplicationSingleton(new Api(),new TraversingUtils());
app.invokeAjaxForms();
app.invokeBrowsers();

















