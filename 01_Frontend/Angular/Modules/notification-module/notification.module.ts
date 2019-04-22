import {IonicModule} from '@ionic/angular';
import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule} from '@angular/forms';
import {Push} from '@ionic-native/push/ngx';
import {AlertService} from '../../shared/services/utils/alert.service';


@NgModule({
  imports: [
    IonicModule,
    CommonModule,
    FormsModule,
  ],
  providers: [
    Push,
    AlertService
  ],
  declarations: [],
  entryComponents: [],
  exports: []
})
export class NotificationModule {
}


