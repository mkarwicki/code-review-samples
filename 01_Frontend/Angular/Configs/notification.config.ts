/**
 * Config for notifications
 * all of the fields should be self explanatory
 *
 * REDIRECT TYPES:
 * in_app -> redirects to application route
 * browser -> redirects to external in app browser
 * none -> do not redirect
 *
 */

import {NavigationConfig} from './navigation.config';


export const NotificationConfig = {
  notifications_types: [
    /**
     * PPL NOTIFICATION SETTINGS
     */
    {
      id: 0,
      slug: 'ppl_auction_extended',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.leads.path
      }
    },
    {
      id: 1,
      slug: 'ppl_new_lead',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.leads.path
      }
    },
    {
      id: 2,
      slug: 'ppl_bid_lost',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.leads.path
      }
    },
    {
      id: 3,
      slug: 'ppl_bid_won',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.leads.path
      }
    },
    /**
     * SPECIAL OFFERS NOTIFICATION SETTINGS
     */
    {
      id: 4,
      slug: 'special_offers_claim_offer',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    },
    {
      id: 5,
      slug: 'special_offers_more_details_request',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    },
    /**
     * EVENTS NOTIFICATION SETTINGS
     */
    {
      id: 6,
      slug: 'events_sign_up_requests',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    },
    {
      id: 7,
      slug: 'events_details_requests',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    },
    /**
     * OTHER
     */
    {
      id: 8,
      slug: 'other_email_owner',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    },
    {
      id: 9,
      slug: 'other_contact_me',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    },
    /**
     * Quote tool
     */
    {
      id: 9,
      slug: 'quote_tool_request_quote',
      redirect: {
        type: 'in_app',
        path: NavigationConfig.inbox.path
      }
    }
  ],

};

