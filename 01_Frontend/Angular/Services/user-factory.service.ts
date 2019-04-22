/**
 *  Handles also user useful methods.
 *
 *  If we might handle multiple user instances
 *  we can manage it here, the same if we would
 *  like to have different user types
 *
 */

import {Injectable} from '@angular/core';
import {UserFactoryInterface} from './user-factory.interface';
import {UserModel} from '../../models/user/user.model';

@Injectable()
export class UserFactoryService implements UserFactoryInterface {
  private currentUser: UserModel;

  constructor() {
  }

  public createUser(): UserModel {
    this.currentUser =  new UserModel()
    return this.currentUser;
  }




}


