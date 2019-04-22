import {AfterViewChecked, AfterViewInit, Component, OnInit} from '@angular/core';

import {AppService} from '../../../../shared/services/app.service';
import {UserStore} from '../../../../shared/stores/user/user.store';
import {NavController} from '@ionic/angular';
import {environment} from '../../../../../environments/environment';
import {ApiService} from '../../../../shared/services/api/api.service';
import {ToastService} from '../../../../shared/services/utils/toast.service';
import {LoaderService} from '../../../../shared/services/utils/loader.service';

@Component({
  selector: 'app-notifications-settings',
  templateUrl: './notifications-settings.page.html',
  styleUrls: ['./notifications-settings.page.scss'],
})
export class NotificationsSettingsPage implements OnInit {
  public notificationsTypes;
  public notificationsByGroup = [];
  public pushNotificationSettingsTmp;
  public changesMade;


  private readonly environment = environment;

  constructor(
    public app: AppService,
    private userStore: UserStore,
    private navCtrl: NavController,
    private api: ApiService,
    private toast: ToastService,
    private loaderService: LoaderService
  ) {
    this.notificationsTypes = this.app.user.defaultNotificationSettings;
    this.getNotificationsByGroup();
    this.pushNotificationSettingsTmp = JSON.parse(JSON.stringify(this.app.user.notificationSettings));
  }


  getNotificationsByGroup() {
    this.notificationsByGroup = this.notificationsTypes.reduce((groups: [any], item) => {
      const group = (groups[item.group] || []);
      /** IF PPL ARE DISABLED **/
      if (!(item.group === 'Pay per Lead' && !this.app.user.leads_enabled)
        && !(item.slug === 'quote_tool_request_quote' && !this.app.user.quote_tool_enabled)) {
        group.push(item);
        groups[item.group] = group;
      }
      return groups;
    }, {});
  }

  /**
   * Order of the notification settings by id returned
   * in json from api.
   *
   * @param a
   * @param b
   */
  nativeOrder = (a, b) => {
    if (a.id > b.id) {
      return b.key;
    }
  };

  toggleNotificationOption(notification_id) {
    this.changesMade = true;
    this.pushNotificationSettingsTmp[notification_id].enabled = !this.pushNotificationSettingsTmp[notification_id].enabled;
  }


  async saveNotificationSettings() {
    const saveNotifications = this.prepareSaveNotificationsSettings(
      await this.userStore.getDeviceToken(),
      this.pushNotificationSettingsTmp
    );
    await this.loaderService.presentLoading();
    saveNotifications.subscribe(async (result: any) => {
        if (result.status === 'ok') {
          this.app.user.notificationSettings = this.pushNotificationSettingsTmp;
          this.userStore.updateUserData(this.app.user);
          this.navCtrl.navigateBack([this.app.nav.settings.path]);
          this.toast.presentSuccessToast('Saved');
        } else {
          this.toast.presentErrorToast('AUTH_CONNECTION_AUTH_PROBLEM_INFO');
        }
        await this.loaderService.dismissLoading();
      }, () => {
        this.toast.presentErrorToast('AUTH_CONNECTION_AUTH_PROBLEM_INFO');
      }
    );
  }


  /** prepare query for more messages */
  prepareSaveNotificationsSettings(device_key, settings) {
    return this.api.get(this.environment.apiEndpoints.user_device_notification_settings,
      {
        device_key: device_key,
        api_key: this.app.user.apiKey,
        settings: JSON.stringify(settings),
      }
    );
  }


  ngOnInit() {
  }

}

