export const environmentPROD = {
  production: true,
  api: 'xxx',
  apiEndpoints: {
    user_summary: 'user-summary',
    user_silent_auth: 'user-silent-auth',
    user_silent_un_auth: 'user_silent_un_auth',
    user_api_key: 'user-api-key',
    user_leads: 'user-leads',
    user_leads_display: 'user-leads-display',
    user_inbox: 'user-inbox',
    user_inbox_display: 'user-inbox-display',
    user_device_notification_settings: 'update-device-notification-settings',
    user_remove_device: 'user-remove-device',
    user_report_a_bug: 'user-report-a-bug'
  },
  static_nav: {
    register: {
      'slug': 'register',
      'path': 'xxx',
    },
    forgot_password: {
      'slug': 'forgot_password',
      'path': 'xxx',
    },
  },
  inbox: {
    pagination: 50
  },
  leads: {
    pagination: 50
  }
};
export const environment = environmentPROD;

