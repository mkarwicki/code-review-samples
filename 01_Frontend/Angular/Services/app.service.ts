/**
 * This is global app-wide singleton app service that uses composition to ease working with app.
 */
import {Injectable} from '@angular/core';
import {UserModel} from '../Models/user/user.model';
import {UserStore} from '../stores/user/user.store';
import {BehaviorSubject, Observable} from 'rxjs';
import {NavigationConfig as nc} from '../../../config/navigation.config';
import {BrowserService} from './browser/browser.service';
import {DeviceService} from './device/device.service';
import {DeviceModel} from '../Models/device/device.model';
import {Platform} from '@ionic/angular';
import {map} from 'rxjs/operators';
import {CommunicationService} from './communication/communication.service';

@Injectable({
  providedIn: 'root',
})
export class AppService {

  private _userObserver = new BehaviorSubject({});
  private _user: UserModel;
  private _nav: any;
  private _browser: BrowserService;
  private _device: DeviceModel;
  private _isInBackground = false;
  private _communication: CommunicationService;

  public paymentsEnabled = false;


  constructor(
    private userStore: UserStore,
    private browserService: BrowserService,
    private deviceService: DeviceService,
    private platform: Platform,
    private communicationService: CommunicationService
  ) {
    /* WAIT FOR PLATFORM TO BE READY */
    this.platform.ready().then(() => {
      /** USER - and subscribe to any changes from user data storage **/
      this.userStore.user.subscribe((user: UserModel) => {
        this._userObserver.next(user);
        /**EMIT**/
        this.user = user;
      });
      /** Other compositions are hardly to ever change **/
      /** NAV **/
      this._nav = nc;
      /** Browser **/
      this._browser = browserService;
      /** Device **/
      this._device = deviceService.device;
    });

    /** BIND COMUNICATIONS SERVUCE**/
    this.communication = communicationService;

    /** WHEN RESUME APP, RELOAD DATA AND TRY TO REINVOKE IN APP BROWSER **/
    this.platform.resume.subscribe(async e => {
         this.isInBackground = false;
    });

    this.platform.pause.subscribe(async e => {
         this.isInBackground = true;
    });

    /** IF NOT IOS ENABLE IN APP PAYMENTS - reference links **/
    if (!this.platform.is('ios')) {
      this.paymentsEnabled = true;
    }

  }

  get nav(): any {
    return this._nav;
  }

  set nav(value: any) {
    this._nav = value;
  }

  get userObserver(): Observable<any> {
    return this._userObserver.asObservable();
  }


  get user(): UserModel {
    return this._user;
  }

  set user(value: UserModel) {
    this._user = value;
  }


  get browser(): BrowserService {
    return this._browser;
  }

  set browser(value: BrowserService) {
    this._browser = value;
  }


  get device(): DeviceModel {
    return this._device;
  }

  set device(value: DeviceModel) {
    this._device = value;
  }


  get platformShortName(): string {
    if (this.platform.is('ios')) {
      return 'ios';
    }
    if (this.platform.is('android')) {
      return 'android';
    }
    return '';
  }

  get communication(): CommunicationService {
    return this._communication;
  }

  set communication(value: CommunicationService) {
    this._communication = value;
  }

  get isInBackground(): boolean {
    return this._isInBackground;
  }

  set isInBackground(value: boolean) {
    this._isInBackground = value;
  }
}
