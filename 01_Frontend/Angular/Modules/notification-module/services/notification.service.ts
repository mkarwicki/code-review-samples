import {Injectable} from '@angular/core';
import {Platform} from '@ionic/angular';
import {Push, PushObject, PushOptions} from '@ionic-native/push/ngx';
import {Storage} from '@ionic/storage';
import {AlertService} from '../../../shared/services/utils/alert.service';
import {NotificationConfig} from '../../../../config/notification.config';
import {SynchronizationService} from '../../../shared/services/synchronization/synchronization.service';
import {AppService} from '../../../shared/services/app.service';
import {UserModel} from '../../../shared/models/user/user.model';
import {UserStore} from '../../../shared/stores/user/user.store';

@Injectable()
export class NotificationService {
  readonly options: PushOptions = {
    android: {
      senderID: 'xxx',
    },
    ios: {
      alert: 'true',
      badge: 'true',
      sound: 'true'
    },
  };

  private pushObject: PushObject;

  private _registrationId: string = null;

  private _isInBackground = false;

  public onNotificationEventSubscribed = false;


  constructor(
    private platform: Platform,
    private push: Push,
    private storage: Storage,
    private alertService: AlertService,
    private sync: SynchronizationService,
    private app: AppService,
    private userStore: UserStore,
  ) {
    this.platform.ready().then(() => {
      this.pushObject = this.push.init(this.options);
      this.register();
      if (this.app.user && this.app.user.isActive) {
        this.onNotificationReceived();
      }
    });
    this.platform.resume.subscribe(async e => {
      this.isInBackground = false;
    });
    this.platform.pause.subscribe(async e => {
      this.isInBackground = true;
    });
  }


  register() {
    this.clearNotificationMemory();
    this.platform.ready().then(() => {
      this.pushObject.on('registration')
        .subscribe((registration: any) => {
          this._registrationId = registration.registrationId;
          this.storage.set('device_key', registration.registrationId);
        });
      setTimeout(() => {
        this.onNotificationReceived();
      }, 1000);

      this.userStore.user.subscribe((user: UserModel) => {
        this.updateNotificationBadgeNumber();
      });
    });
  }

  async unregister() {
    await this.pushObject.unregister();
    this.pushObject = this.push.init(this.options);
    this.onNotificationEventSubscribed = false;
    await this.pushObject.setApplicationIconBadgeNumber(0);
    await this.register();
  }


  async getDeviceToken(): Promise<string> {
    return await this.storage.get('device_key').then(
      (deviceKey: string) => {
        if (deviceKey) {
          this._registrationId = deviceKey;
          return deviceKey;
        } else {
          this._registrationId = '';
          return '';
        }
      }
    );
  }


  updateNotificationBadgeNumber() {
    let sum = 0;
    if (this.app.user.leads.unread_total) {
      sum += this.app.user.leads.unread_total;
    }
    if (this.app.user.inbox.unread_total) {
      sum += this.app.user.inbox.unread_total;
    }
    this.pushObject.setApplicationIconBadgeNumber(sum);
  }

  onNotificationReceived() {
    if (!this.app.user || (this.app.user.isActive)) {
      if (!this.onNotificationEventSubscribed) {
        this.onNotificationEventSubscribed = true;
        this.pushObject.on('notification').subscribe(async (notification: any) => {
          /** IF APP IS IOS AND WE RECEIVED THE NOTIFICATION FROM IDLE STATE
           * DO NOT DISPLAY NOTIFICATION TWICE - I STORE ITS DATA IN LOCAL STORAGE**/
          if (this.app.platformShortName === 'ios') {
            const msgID = notification.additionalData['gcm.message_id'];
            const wasPresented = await this.wasNotificationPresented(msgID);
            await this.saveNotification(msgID);
            if (msgID && wasPresented) {
              return;
            }
          }
          if (!this.isInBackground) {
            this.sync.silentSyncAllUserData();
          }
          this.updateNotificationBadgeNumber();
          /** firs check in config what to do next - > open in app? browser? do nthg? **/
          const nc = NotificationConfig.notifications_types.find(x => x.slug === notification.additionalData.slug);
          if (!nc) {
            this.alertService.presentNotificationAlertConfirm(
              notification,
              false,
              false,
              false);
          } else {
            if (notification.additionalData.redirect_type === 'in_app') {
              /** DECIDE WHERE TO REDIRECT FORM APP **/
              this.alertService.presentNotificationAlertConfirm(
                notification,
                true,
                nc.redirect.type,
                nc.redirect.path);
            } else {
              /** DECIDE WHERE TO REDIRECT FORM API **/
              this.alertService.presentNotificationAlertConfirm(
                notification,
                true,
                notification.additionalData.redirect_type,
                notification.additionalData.redirect_path);
            }
          }
          if (this.app.platformShortName === 'ios') {
            this.pushObject.finish();
          }
        });
      }
    }
  }

  async hasPermisions() {
    return await this.push.hasPermission();
  }


  async clearNotificationMemory() {
    const notifications = [];
    return this.storage.set('ncp', notifications);
  }

  async saveNotification(notificationID) {
    const notifications = await this.getStoredNotifications();
    notifications.push(notificationID);
    return this.storage.set('ncp', notifications);
  }

  async getStoredNotifications() {
    return await this.storage.get('ncp').then(notifications => {
      if (notifications) {
        return notifications;
      } else {
        return [];
      }
    });
  }

  async wasNotificationPresented(notificationID) {
    const notifications = await this.getStoredNotifications();
    if (!notifications && notifications.length < 1) {
      return false;
    }
    if (notifications.indexOf(notificationID) > -1) {
      return true;
    }
    return false;
  }

  get registrationId(): string {
    return this._registrationId;
  }

  set registrationId(value: string) {
    this._registrationId = value;
  }


  get isInBackground(): boolean {
    return this._isInBackground;
  }

  set isInBackground(value: boolean) {
    this._isInBackground = value;
  }
}

