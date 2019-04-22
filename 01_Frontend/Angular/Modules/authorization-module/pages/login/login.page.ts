import {Component, OnInit} from '@angular/core';
import {AuthorizationService} from '../../services/authorization.service';
import {ToastService} from '../../../../shared/services/utils/toast.service';
import {TextService} from '../../../../shared/services/utils/text.service';
import {AppService} from '../../../../shared/services/app.service';
import {environment} from '../../../../../environments/environment';


@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: [
    '../../styles/authorization.module.global.scss',
    './login.page.scss'
  ],
})
export class LoginPage implements OnInit {

  private readonly staticNav = environment.static_nav;
  public email = '';



  public password = '';
  public forgotPasswordLink: string;


  constructor(
    public app: AppService,
    public auth: AuthorizationService,
    private toast: ToastService,
    private textService: TextService,
  ) {
     this.forgotPasswordLink = this.staticNav.forgot_password.path;
  }

  ngOnInit() {


  }

  /**
   * This method should only prepare data for login
   * and execute Login in service, and after
   * this store data in local storage of app.
   */
  async doLogin() {
    /**
     * Form basic validation
     */
    /** NO EMAIL AND PASSWORD */
    if (this.email.length < 1 && this.password.length < 1) {
      this.toast.presentErrorToast('AUTH_NO_EMAIL_AND_PASSWORD_MSG');
      /** NO EMAIL */
    } else if (this.email.length < 1) {
      this.toast.presentErrorToast('AUTH_NO_EMAIL_MSG');
      /** NO PASSWORD */
    } else if (this.password.length < 1) {
      this.toast.presentErrorToast('AUTH_NO_PASSWORD_MSG');
      /** INVALID EMAIL */
    } else if (!this.textService.validateEmail(this.email)) {
      this.toast.presentErrorToast('AUTH_INVALID_EMAIL');
    } else {
      /**
       * Validation success - login user
       */
      this.auth.loginUserByEmail(this.email, this.password);
    }
  }
}



