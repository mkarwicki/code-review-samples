import {Injectable} from '@angular/core';
import {AlertController} from '@ionic/angular';
import {Router} from '@angular/router';
import {AppService} from '../app.service';
import {SynchronizationService} from '../synchronization/synchronization.service';
import {TranslateService} from '../translate/translation.service';
import {LoaderService} from './loader.service';


@Injectable()
export class AlertService {

  constructor(
    public alertController: AlertController,
    private router: Router,
    private app: AppService,
    private sync: SynchronizationService,
    private translateService: TranslateService,
    private loaderService: LoaderService,
  ) {
  }


  async presentNotificationAlertConfirm(notification: any, redirect: boolean, redirect_type: any, redirect_path: any) {
    if (redirect_type) {
      const alertObj = await this.alertController.create({
        header: notification.title,
        message: notification.message,
        buttons: [
          {
            text: this.translateService.trans('SHARED_ALERT_NOTIFICATION_CANCEL'),
            role: 'cancel',
            cssClass: 'secondary',
          }, {
            text: this.translateService.trans('SHARED_ALERT_NOTIFICATION_VIEW'),
            handler: () => {
              if (redirect_type === 'in_app') {
                let msgId = false;
                /** IOS FCM RETURNS DOUBLE SLASHED STRING SO I NEED TO STRIP IT **/
                if (this.app.platformShortName === 'ios' && notification.additionalData.additional_data) {
                  const finalData = notification.additionalData.additional_data.replace(/\\/g, '');
                  const asJson = JSON.parse(finalData);
                  msgId = asJson.id;
                }
                if (notification.additionalData.additional_data.id > 0 || msgId) {
                  if (!msgId) {
                    msgId = notification.additionalData.additional_data.id;
                  }
                  if (this.sync.syncProcessActive || 1) {
                    this.loaderService.presentLoading();
                    setTimeout(() => {
                      this.loaderService.dismissLoading();
                      this.router.navigate([this.app.nav.inbox_message.path, {id: msgId}]);
                    }, 6000);
                  } else {
                    this.router.navigate([this.app.nav.inbox_message.path, {id: msgId}]);
                  }
                } else {
                  this.router.navigate([redirect_path]);
                }
              } else if (redirect_type === 'browser') {
                this.app.browser.openURL(redirect_path);
              }
            }
          }
        ]
      });
      await alertObj.present();
    } else {
      const alertObj = await this.alertController.create({
        header: notification.title,
        message: notification.message,
        buttons: [
          {
            text: this.translateService.trans('OK'),
            role: 'cancel',
            cssClass: 'secondary',
          }
        ]
      });
      await alertObj.present();
    }
  }


  async presentDefaultNotificationAlertConfirm(notification: any) {
    const alertObj = await this.alertController.create({
      message: notification,
      buttons: [
        {
          text: this.translateService.trans('OK'),
        }
      ]
    });
    await alertObj.present();
  }



   async openNativeBrowserOrClose(notification: any, browserLink: string){
     const alertObj = await this.alertController.create({
       message: notification,
       buttons: [

         {
           text: 'Cancel',
           role: 'cancel',
         } ,
         {
           text: 'Open browser',
           handler: () => {
               this.app.browser.openInBrowserURL(browserLink);
           }
         },
       ]
     });
     await alertObj.present();
   }


}
