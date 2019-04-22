/**
 * LeadModel Model.
 */
import {DeserializableModel} from '../deserializable.model';

export class DeviceModel implements DeserializableModel {



  /** Get the version of Cordova running on the device. */
  public _cordova: string;
  /**
   * The device.model returns the name of the device's model or product. The value is set
   * by the device manufacturer and may be different across versions of the same product.
   */
  public _model: string;
  /** Get the device's operating system name. */
  public _platform: string;
  /** Get the device's Universally Unique Identifier (UUID). */
  public _uuid: string;
  /** Get the operating system version. */
  public _version: string;
  /** Get the device's manufacturer. */
  public _manufacturer: string;
  /** Whether the device is running on a simulator. */
  public _isVirtual: boolean;
  /** Get the device hardware serial number. */
  public _serial: string;


  get cordova(): string {
    return this._cordova;
  }

  set cordova(value: string) {
    this._cordova = value;
  }

  get model(): string {
    return this._model;
  }

  set model(value: string) {
    this._model = value;
  }

  get platform(): string {
    return this._platform;
  }

  set platform(value: string) {
    this._platform = value;
  }

  get uuid(): string {
    return this._uuid;
  }

  set uuid(value: string) {
    this._uuid = value;
  }

  get version(): string {
    return this._version;
  }

  set version(value: string) {
    this._version = value;
  }

  get manufacturer(): string {
    return this._manufacturer;
  }

  set manufacturer(value: string) {
    this._manufacturer = value;
  }

  get isVirtual(): boolean {
    return this._isVirtual;
  }

  set isVirtual(value: boolean) {
    this._isVirtual = value;
  }

  get serial(): string {
    return this._serial;
  }

  set serial(value: string) {
    this._serial = value;
  }

  deserialize(input: any) {
    Object.assign(this, input);
    return this;
  }

}
