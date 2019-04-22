/**
 * Service used to login/logout users
 */

import {Injectable} from '@angular/core';
import {ApiService} from '../../../shared/services/api/api.service';
import {LoaderService} from '../../../shared/services/utils/loader.service';
import {EmailLoginAuthorizationResultModel} from '../models/email-login-authorization-result.model';
import {ToastService} from '../../../shared/services/utils/toast.service';
import {UserStore} from '../../../shared/stores/user/user.store';
import {AppService} from '../../../shared/services/app.service';
import {Router} from '@angular/router';
import {NotificationService} from '../../notification-module/services/notification.service';
import {EmailLoginCommand} from './authorization-commands/email-login-command';
import {FacebookLoginCommand} from './authorization-commands/facebook-login-command';
import {BrowserService} from '../../../shared/services/browser/browser.service';
import {GoogleLoginCommand} from './authorization-commands/google-login-command';
import {environment} from '../../../../environments/environment';
import {AlertController, NavController} from '@ionic/angular';
import {TranslateService} from '../../../shared/services/translate/translation.service';


@Injectable()
export class AuthorizationService {
  private readonly endpoints = environment.apiEndpoints;

  constructor(
    private api: ApiService,
    private loaderService: LoaderService,
    private toast: ToastService,
    private userStore: UserStore,
    private app: AppService,
    private router: Router,
    private notificationService: NotificationService,
    private emailLoginCommand: EmailLoginCommand,
    private facebookLoginCommand: FacebookLoginCommand,
    private googleLoginCommand: GoogleLoginCommand,
    private browserService: BrowserService,
    private navCtrl: NavController,
    public alertController: AlertController,
    private translateService: TranslateService
  ) {


  }

  async loginUserByEmail(email, password) {
    this.emailLoginCommand.name = name;
    this.emailLoginCommand.email = email;
    this.emailLoginCommand.password = password;
    this.emailLoginCommand.execute();
  }


  async loginUserWithFacebook() {
    this.facebookLoginCommand.execute();
  }


  async loginUserWithGoogle() {
    this.googleLoginCommand.execute();
  }


  async logoutUser() {

    const logout = this.api.get(
      this.endpoints.user_remove_device,
      {
        device_key: await this.notificationService.getDeviceToken(),
        api_key: await this.app.user.apiKey
      }
    );


    const alertObj = await this.alertController.create({
      header: 'Logout',
      message: 'Are you sure?',
      buttons: [
        {
          text: this.translateService.trans('Cancel'),
          role: 'cancel',
          cssClass: 'secondary',
        },
        {
          text: this.translateService.trans('Logout'),
          handler: () => {
            this.loaderService.presentLoading();
            logout.subscribe(async (result) => {
                /** Logout from In App Browser **/
                await this.browserService.unAuthorizeUserByHiddenRequest();
                this.app.user.isActive = false;
                await this.userStore.updateUserData(this.app.user);
                setTimeout(async () => {
                  /** Unregister notifications **/
                  await this.notificationService.unregister();
                }, 1000);
                this.loaderService.dismissLoading();
                this.toast.presentSuccessToast('AUTH_LOGGED_OUT_INFO');
                this.navCtrl.navigateRoot([this.app.nav.welcome.path]);
              },
              () => {
                this.loaderService.dismissLoading();
                this.toast.presentErrorToast('AUTH_CONNECTION_AUTH_PROBLEM_INFO');
              }
            );
          }
        }
      ]
    });
    await alertObj.present();
  }


  async logoutOnUpdate() {
    await this.browserService.unAuthorizeUserByHiddenRequest();
    await this.notificationService.unregister();
    const logout = this.api.get(
      this.endpoints.user_remove_device,
      {
        device_key: await this.notificationService.getDeviceToken(),
        api_key: await this.app.user.apiKey
      }
    );
    logout.subscribe();
  }


}
