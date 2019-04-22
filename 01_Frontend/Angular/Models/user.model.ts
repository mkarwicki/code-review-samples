/**
 * User Data Model.
 *
 *  Users should be created only by users factory
 */
import {DeserializableModel} from '../deserializable.model';
import {ListingModel} from '../listing/listing.model';
import {LeadModel} from '../lead/lead.model';
import {InboxModel} from '../inbox/inbox.model';
import {NotificationSettingModel} from '../notification/notification-setting.model';
import {StatsModel} from '../stats/stats.model';
import {LinkModel} from '../styles_examples/link.model';

export class UserModel implements DeserializableModel {
  private _name: string;
  private _surname: string;
  private _isActive: boolean;
  private _apiKey: string;
  private _listing: ListingModel;
  private _leads_enabled: boolean;
  private _quote_tool_enabled: boolean;
  private _leads: {
    unread_total: number,
    total: number,
    results: LeadModel[]
  };
  private _no_leads_info_links: {
    inbox_complete_your_listing_link: string,
    inbox_update_your_categories_link: string,
    inbox_add_more_suburbs_link: string,
    leads_faq_ling: string,
  };
  private _leads_menu_links: LinkModel[];
  private _inbox: {
    unread_total: number,
    total: number,
    results: InboxModel[]
  };
  private _no_inbox_info_links: {
    inbox_upgrade_your_package: string,
    inbox_update_your_categories_link: string,
    inbox_add_more_suburbs_link: string,
  };


  private _pushNotificationEnabled = true;
  private _notificationSettings: NotificationSettingModel[];
  private _defaultNotificationSettings: NotificationSettingModel[];
  private _stats: StatsModel[];
  /** As the saying goes, “A typed array keeps the errors away”… **/
  private _menu_links: LinkModel[];
  private _manage_listing_links: {
    listing_edit: string,
    events_list: string,
    special_offers_list: string,
    complete_listing: string,
    get_more_leads: string,
    upgrade: string,
    find_out_more: string,
  };


  get name(): string {
    return this._name;
  }

  set name(value: string) {
    this._name = value;
  }

  get surname(): string {
    return this._surname;
  }

  set surname(value: string) {
    this._surname = value;
  }

  get isActive(): boolean {
    return this._isActive;
  }

  set isActive(value: boolean) {
    this._isActive = value;
  }

  get apiKey(): string {
    return this._apiKey;
  }

  set apiKey(value: string) {
    this._apiKey = value;
  }

  get listing(): ListingModel {
    return this._listing;
  }

  set listing(value: ListingModel) {
    this._listing = value;
  }


  get leads(): { unread_total: number; total: number; results: LeadModel[] } {
    return this._leads;
  }

  set leads(value: { unread_total: number; total: number; results: LeadModel[] }) {
    this._leads = value;
  }


  get leads_enabled(): boolean {
    return this._leads_enabled;
  }

  set leads_enabled(value: boolean) {
    this._leads_enabled = value;
  }

  get quote_tool_enabled(): boolean {
    return this._quote_tool_enabled;
  }

  set quote_tool_enabled(value: boolean) {
    this._quote_tool_enabled = value;
  }

  get no_leads_info_links(): {
    inbox_complete_your_listing_link: string;
    inbox_update_your_categories_link: string;
    inbox_add_more_suburbs_link: string,
    leads_faq_ling: string
  } {
    return this._no_leads_info_links;
  }

  set no_leads_info_links(value: {
    inbox_complete_your_listing_link: string;
    inbox_update_your_categories_link: string;
    inbox_add_more_suburbs_link: string,
    leads_faq_ling: string,
  }) {
    this._no_leads_info_links = value;
  }

  get leads_menu_links(): LinkModel[] {
    return this._leads_menu_links;
  }

  set leads_menu_links(value: LinkModel[]) {
    this._leads_menu_links = value;
  }

  get inbox(): { unread_total: number; total: number; results: InboxModel[] } {
    return this._inbox;
  }

  set inbox(value: { unread_total: number; total: number; results: InboxModel[] }) {
    this._inbox = value;
  }


  get no_inbox_info_links(): {
    inbox_upgrade_your_package: string;
    inbox_update_your_categories_link: string;
    inbox_add_more_suburbs_link: string
  } {
    return this._no_inbox_info_links;
  }

  set no_inbox_info_links(value: {
    inbox_upgrade_your_package: string;
    inbox_update_your_categories_link: string;
    inbox_add_more_suburbs_link: string
  }) {
    this._no_inbox_info_links = value;
  }

  get pushNotificationEnabled(): boolean {
    return this._pushNotificationEnabled;
  }

  set pushNotificationEnabled(value: boolean) {
    this._pushNotificationEnabled = value;
  }


  get notificationSettings(): NotificationSettingModel[] {
    return this._notificationSettings;
  }

  set notificationSettings(value: NotificationSettingModel[]) {
    this._notificationSettings = value;
  }

  get defaultNotificationSettings(): NotificationSettingModel[] {
    return this._defaultNotificationSettings;
  }

  set defaultNotificationSettings(value: NotificationSettingModel[]) {
    this._defaultNotificationSettings = value;
  }

  get stats(): StatsModel[] {
    return this._stats;
  }

  set stats(value: StatsModel[]) {
    this._stats = value;
  }


  get menu_links(): LinkModel[] {
    return this._menu_links;
  }

  set menu_links(value: LinkModel[]) {
    this._menu_links = value;
  }

  get manage_listing_links(): {
    listing_edit: string; events_list: string; special_offers_list: string;
    complete_listing: string; get_more_leads: string; upgrade: string;
    find_out_more: string
  } {
    return this._manage_listing_links;
  }

  set manage_listing_links(value: {
    listing_edit: string; events_list: string; special_offers_list: string;
    complete_listing: string; get_more_leads: string; upgrade: string;
    find_out_more: string
  }) {
    this._manage_listing_links = value;
  }


  deserialize(input: any) {
    Object.assign(this, input);
    return this;
  }


  /* CUSTOM METHODS */
  getFullName(): string {
    let fullName;
    if (this.name.length > 0 && this.surname.length > 0) {
      fullName = this.name + ' ' + this.surname;
    } else {
      fullName = this.name + this.surname;
    }
    return fullName;
  }


}
