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
import {LoggerService} from '../../../../shared/services/logger/logger.service';
import {environment} from '../../../../../environments/environment';
import {BrowserService} from '../../../../shared/services/browser/browser.service';
import {GooglePlus} from '@ionic-native/google-plus/ngx';


@Injectable()
export class GoogleLoginCommand implements AuthorizationCommandInterface {
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
    private googlePlus: GooglePlus,
    private logger: LoggerService,
    private browserService: BrowserService
  ) {

  }

  async execute(): Promise<any> {
    await this.loaderService.presentLoading();
    this.googlePlus.login({
      'scopes': '',  // optional, space-separated list of scopes, If not included or empty, defaults to `profile` and `email`.
      /**
       * optional clientId of your Web application from Credentials settings of your project - On Android,
       * this MUST be included to get an idToken. On iOS, it is not required.
       *
       * https://controlpanel-alpha135.activeactivities.com.au/settings/api/
       */
      'webClientId': '136358993515-tgoq3akjvvn403ino7qn95dc06h92s73.apps.googleusercontent.com123',
      /**
       * Optional, but requires the webClientId - if set to true the plugin
       * will also return a serverAuthCode, which can be used to
       * grant offline access to a non-Google server
       */
      'offline': true
    })
      .then(async (user) => {
        await this.loaderService.dismissLoading();

        alert(JSON.stringify(user));
        alert(JSON.stringify(user.email));

        // this.nativeStorage.setItem('google_user', {
        //   name: user.displayName,
        //   email: user.email,
        //   picture: user.imageUrl
        // }).then(() => {
        //   this.router.navigate(['/user']);
        // }, error => {
        //   console.log(error);
        // });


      }, async err => {
        alert('error' + JSON.stringify(err));
        console.log(err);
        await this.loaderService.dismissLoading();
      });

  }


  async prepareRequestApiKeyCall(email: string, accessToken: string) {
    return await this.api.post(this.endpoints.user_api_key,
      {
        // name: name,
        email: email,
        facebook_token: accessToken,
        device_details: JSON.stringify(this.app.device),
        device_key: await this.notificationService.getDeviceToken(),
        platform: this.app.platformShortName,
      }
    );
  }


  async loginUser(loginData) {
    return await loginData.subscribe(async (result) => {
        const authData = new EmailLoginAuthorizationResultModel().deserialize(result);
        /**
         * If user login with auth token and password success
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
                this.loaderService.dismissLoading();
                // AUTO LOGIN USER
                this.browserService.authorizeUserByHiddenRequest(this.app.user.apiKey);
                /**
                 * REDIRECT TO LISTING SUMMARY PAGE
                 */
                await this.router.navigate([this.app.nav.listing_summary.path]);
              }, () => {
                this.loaderService.dismissLoading();
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


  async prepareRequestSummaryCall(apiKey) {
    return this.api.get(this.endpoints.user_summary,
      {
        api_key: apiKey,
        device_key: await this.notificationService.getDeviceToken()
      }
    );
  }


}
