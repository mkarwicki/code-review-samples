/**
 * User data storage
 */

import {Injectable} from '@angular/core';

import {BehaviorSubject, Observable} from 'rxjs';
import {Storage} from '@ionic/storage';
import {UserModel} from '../../models/user/user.model';
import {UserFactoryService} from '../../services/user/user-factory.service';


@Injectable()
export class UserStore {

  private _user = new BehaviorSubject({});

  constructor(private storage: Storage, private userFactoryService: UserFactoryService) {

  }


  get user(): Observable<any> {
    return this._user.asObservable();
  }


  public async loadInitialData() {
    await this.storage.get('user').then(
      (user: UserModel) => {
        if (user) {
          const userModelData = this.userFactoryService.createUser().deserialize(user);
          this._user.next(userModelData);
        } else {
          const emptyUser = this.userFactoryService.createUser();
          emptyUser.isActive = false;
          this._user.next(emptyUser);
        }
      }
    );
  }

  public async updateUserData(user: UserModel) {
    await this.storage.set('user', user).then(() => {
      return this._user.next(user);
    });
  }


  public async clearUserData() {
    const emptyUser = this.userFactoryService.createUser();
    emptyUser.isActive = false;
    await this.storage.set('user', emptyUser).then(() => {
      return this._user.next(emptyUser);
    });
  }


  async getDeviceToken(): Promise<string> {
    return await this.storage.get('device_key').then(
      (deviceKey: string) => {
        if (deviceKey) {
          return deviceKey;
        } else {
          return '';
        }
      }
    );
  }


}


