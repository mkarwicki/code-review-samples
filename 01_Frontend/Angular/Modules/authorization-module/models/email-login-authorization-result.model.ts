import {DeserializableModel} from '../../../shared/models/deserializable.model';

export class EmailLoginAuthorizationResultModel implements DeserializableModel {
   public status: string;
   public api_key: string;
   public user_name: string;
   public error: string;


  deserialize(input: any): this {
    Object.assign(this, input);
    return this;
  }
}
