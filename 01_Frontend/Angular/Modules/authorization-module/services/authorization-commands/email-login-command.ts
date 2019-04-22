import {AuthorizationCommandInterface} from './authorization.command.interface';
import {Injectable} from '@angular/core';
import {ApiService} from '../../../../shared/services/api/api.service';
import {LoaderService} from '../../../../shared/services/utils/loader.service';
import {ToastService} from '../../../../shared/services/utils/toast.service';
import {UserStore} from '../../../../shared/stores/user/user.store';
import {AppService} from '../../../../shared/services/app.service';
import {Router} from '@angular/router';
import {NotificationService} from '../../../notification-module/services/notification.service';
import {EmailLoginAuthorizationResultModel} from '../../models/email-login-authorization-result.model';
import {SynchronizationService} from '../../../../shared/services/synchronization/synchronization.service';
import {environment} from '../../../../../environments/environment';
import {BrowserService} from '../../../../shared/services/browser/browser.service';
import {NavController} from '@ionic/angular';


@Injectable()
export class EmailLoginCommand implements AuthorizationCommandInterface {
  public name;
  public email;
  public password;
  private readonly endpoints = environment.apiEndpoints;

  constructor(
    private api: ApiService,
    private loaderService: LoaderService,
    private toast: ToastService,
    private userStore: UserStore,
    private app: AppService,
    private router: Router,
    private notificationService: NotificationService,
    private sync: SynchronizationService,
    private browserService: BrowserService,
    private navCtrl: NavController,
  ) {

  }

  async execute() {
    await this.loaderService.presentLoading();
    /** PREPARE API KEY CALL DATA */
    const loginData = await this.prepareRequestApiKeyCall(this.email, this.password);
    /** GET API KEY */
    await this.loginUser(loginData);
  }


  async prepareRequestApiKeyCall(email, password) {
    return this.api.post(this.endpoints.user_api_key,
      {
        email: email,
        password: password,
        device_details: JSON.stringify(this.app.device),
        device_key: await this.notificationService.getDeviceToken(),
        platform: this.app.platformShortName,
      }
    );
  }


  async prepareRequestSummaryCall(apiKey) {
    return this.api.get(this.endpoints.user_summary,
      {
        api_key: apiKey,
        device_key: await this.notificationService.getDeviceToken()
      }
    );
  }


  async loginUser(loginData) {
    return await loginData.subscribe(async (result) => {
        const authData = new EmailLoginAuthorizationResultModel().deserialize(result);
        /**
         * If user login with email and password success
         * we have api_key
         */
        if (authData.status === 'ok') {
          if (authData.api_key) {
            this.app.user.apiKey = authData.api_key;
            /**
             * Now we request user data with api key
             * and store it user storage
             */
            const summaryData = await this.prepareRequestSummaryCall(this.app.user.apiKey);
            await summaryData.subscribe(async (summary) => {
                this.app.user.deserialize(summary);
                this.app.user.isActive = true;
                await this.userStore.updateUserData(this.app.user);
                await this.toast.presentSuccessToast(
                  'AUTH_HI_USER_LOGGED_IN_SUCCESSFULLY_INFO',
                  {username: this.app.user.getFullName()}
                );
                setTimeout(async () => {
                  this.notificationService.register();
                  this.notificationService.updateNotificationBadgeNumber();
                }, 1000);
                this.loaderService.dismissLoading();
               // Login user to bma
                this.browserService.authorizeUserByHiddenRequest(this.app.user.apiKey);
                /**
                 * REDIRECT TO LISTING SUMMARY PAGE
                 */
                await this.navCtrl.navigateRoot([this.app.nav.listing_summary.path]);
              }, () => {
                this.toast.presentErrorToast('AUTH_CONNECTION_AUTH_PROBLEM_INFO');
              }
            );
          } else {
            this.loaderService.dismissLoading();
            this.toast.presentErrorToast('API_KEY_ERROR');
          }
        } else {
          this.loaderService.dismissLoading();
          this.toast.presentErrorToast(authData.error);
        }
      },
      () => {
        this.loaderService.dismissLoading();
        this.toast.presentErrorToast('AUTH_CONNECTION_AUTH_PROBLEM_INFO');
      }
    );
  }


}
