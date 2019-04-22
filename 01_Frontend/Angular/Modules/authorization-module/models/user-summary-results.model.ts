import {DeserializableModel} from '../../../shared/models/deserializable.model';
import {UserModel} from '../../../shared/models/user/user.model';

export class UserSummaryResultsModel implements DeserializableModel {
   public status: string;
   public msg: string;
   public user: UserModel;


  deserialize(input: any): this {
    Object.assign(this, input);
    return this;
  }
}
