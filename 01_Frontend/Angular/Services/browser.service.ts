/**
 *  Browser service to open in app browser on mobile device
 *  or in new window on web.
 */

import {Injectable} from '@angular/core';
import {InAppBrowser, InAppBrowserObject} from '@ionic-native/in-app-browser/ngx';
import {environment} from '../../../../environments/environment';
import {Platform} from '@ionic/angular';
import {BehaviorSubject, Observable} from 'rxjs';


@Injectable()
export class BrowserService {
  private browser: InAppBrowserObject;
  private readonly endpoints = environment.apiEndpoints;
  private readonly apiEndpoint = environment.api;
  private _browserClosed = new BehaviorSubject({});

  //  private timer: Date;


  constructor(private iab: InAppBrowser, private platform: Platform) {


  }


  openURL(url) {
    if (this.platform.is('ios')) {
      this.browser = this.iab.create(url, '_blank', {zoom: 'no', closebuttoncaption: 'Go back', location: 'no'});
    } else {
      this.browser = this.iab.create(url, '_blank', {zoom: 'no', closebuttoncaption: 'Go back'});
    }

    this.browser.on('loadstart').subscribe(event => {
      this.browser.executeScript({ code: 'document.cookie = "dont-ask-again-to-download-app = true ; 31536000000; path=/";' });
    });
    //  this.timer = new Date();
    this.browser.on('exit').subscribe(async (result) => {
      // const endTime: Date = new Date();
      // const diff: any = endTime.getTime() - this.timer.getTime();
      /** IF BROWSER WAS OPENED FOR MORE THEN 10 seconds - update might been made - im the end it is not good idea -
       * cos we might be opening a new lead for a second and we want to update it **/
      // if (diff > 10000) {
      this._browserClosed.next(result);
      // }
    });
  }


  openInBrowserURL(url) {
    this.iab.create(url, '_system');
  }


  get browserClosed(): Observable<any> {
    return this._browserClosed.asObservable();
  }


  /**
   * Send a pre fly request to server and log in user
   * to the web app, so he has access to BMA.
   */
  authorizeUserByHiddenRequest(apiKey) {
    if (this.platform.is('cordova') && (this.platform.is('ios') || this.platform.is('android'))) {
      const url = this.apiEndpoint + '/' + this.endpoints.user_silent_auth + '?api_key=' + apiKey;
      this.iab.create(url, '', {hidden: 'yes', zoom: 'no'});
    }
  }

  /**
   * Send a pre fly request to server and log out user
   *
   */
  unAuthorizeUserByHiddenRequest() {
    if (this.platform.is('cordova') && (this.platform.is('ios') || this.platform.is('android'))) {
      const url = this.apiEndpoint + '/' + this.endpoints.user_silent_un_auth;
      this.iab.create(url, '', {hidden: 'yes', zoom: 'no'});
    }
  }


}

