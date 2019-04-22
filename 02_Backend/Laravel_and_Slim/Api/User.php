<?php

namespace Apps\Api\Controller;

use Apps\Api\Controller;
use Illuminate\Pagination\Paginator;
use Lib\Auth\Auth;
use Lib\Auth\AuthBusiness;
use Lib\Auth\Social\Facebook;
use Lib\Dates;
use Orm\Dao\Api\Key;
use Orm\Dao\Auctions\Auctions;
use Orm\Dao\Users\Users;
use Orm\Dto\PushNotificationsSettings\PushNotificationsSettings;
use Orm\Model\Api\KeyLog;
use Orm\Model\Auctions\Participants;
use Orm\Model\Listings\Emails;
use Orm\Model\Users\DevicesMessages;
use Orm\Model\Users\Groups;
use Orm\Dao\Listings\Stats\Counters as CountersDao;
use Orm\Dao\Listings\Stats\Impressions as ImpressionsDao;
use Orm\Dao\Listings\Stats\Rankings as RankingsDao;
use Lib\Number as LibNumber;


class User extends Controller
{
    const ERROR_USER_NOT_FOUND = 'User does not exists';
    const ERROR_USER_WRONG_ROLE = 'User needs to be a business owner';
    const ERROR_USER_WRONG_PASSWORD = 'Wrong password';
    const ERROR_USER_DATA_NOT_PROVIDED = 'Login data not provided';

    public function __construct(\Slim\Slim $app)
    {
        parent::__construct($app);
        $this->language('api');
        $this->language('bma_score');
        $this->language('bma_inbox');
        $this->language('bma_auctions');
        $this->language('bma_listings_stats');
        $this->language('bma_general');
        $this->language('bma_dashboard');
    }

    public function apiKeyAction()
    {
        $this->app->contentType('application/json');

        $facebookToken = $this->app->request()->params('facebook_token');
        $googleToken = $this->app->request()->params('google_token');
        $email = $this->app->request()->params('email');
        $password = $this->app->request()->params('password');

        $deviceDetails = $this->app->request()->params('device_details');
        $deviceKey = $this->app->request()->params('device_key');
        $platform = $this->app->request()->params('platform');


        if (isset($facebookToken)) {
            $user = Facebook::getUserByFacebookToken($facebookToken);

            if ($user instanceof \Orm\Model\Users\Users == false) {
                $error = self::ERROR_USER_NOT_FOUND;
            } elseif ($user->group()->first()->id != Groups::GROUP_ID_USER_WITH_LISTING) {
                $error = self::ERROR_USER_WRONG_ROLE;
            }
        } elseif (isset($googleToken)) {

        } elseif (isset($email) && isset($password)) {
            $user = Users::getUserByEmail($email);

            if ($user instanceof \Orm\Model\Users\Users == false) {
                $error = self::ERROR_USER_NOT_FOUND;
            } else {
                $verifyPassword = Auth::verifyPassword($password, $user->pass, $user->password_salt, $user->password_hash);

                if ($verifyPassword != true) {
                    $error = self::ERROR_USER_WRONG_PASSWORD;
                } else {
                    if ($user->group()->first()->id != Groups::GROUP_ID_USER_WITH_LISTING) {
                        $error = self::ERROR_USER_WRONG_ROLE;
                    }
                }
            }
        } else {
            $error = self::ERROR_USER_DATA_NOT_PROVIDED;
        }

        if (!empty($deviceDetails) && !empty($deviceKey) && isset($user->user_email) && !empty($user->user_email)) {
            $pns = DevicesMessages::getPushNotificationsSettingsData();
            \Orm\Dao\Users\Devices::saveDevice($user->user_email, $deviceKey, $deviceDetails, $pns, $platform);
        }

        if (isset($error)) {
            echo json_encode([
                'error' => $error
            ]);

            KeyLog::create([
                'key_id' => 0,
                'ip' => $this->app->request()->getIp(),
                'log_type' => KeyLog::TYPE_ERROR,
                'log_details' => $error,
                'url' => $this->app->request()->getUrl(),
                'created_at' => Dates::nowDBTime()
            ]);
            exit;
        }

        if (empty($user->apiKey)) {
            $key = Key::generateNewKeyForUser($user->id);
        } else {
            $key = $user->apiKey->api_key;
        }

        // SETUP DEFAULT NOTIFICATION SETTINGS


        $userName = trim($user->user_first_name . ' ' . $user->user_last_name);

        echo json_encode([
            'status' => 'ok',
            'api_key' => $key,
            'user_name' => $userName
        ]);
        exit;
    }


    public function userReportABugAction()
    {
        $this->app->contentType('application/json');
        $msg = $this->app->request()->params('msg');
        $deviceKey = $this->app->request()->params('device_key');
        $route = $this->app->request()->params('route');
        $deviceID = 'Browser (no device id)';

        if ($deviceKey) {
            $device = \Orm\Dao\Users\Devices::getDevice($deviceKey);
            if($device){
                $deviceID = $device->id;
            }
        }


        $platform = $this->app->request()->params('platform');
        $device_details = $this->app->request()->params('device_details');
        $listing = $this->app->apiKey->user->listing;
        $user = $this->app->apiKey->user;


        $sender = new \Lib\Email\Sender();
        $emailStatus = $sender->send(\Orm\Model\Email\Templates::TEMPLATE_ADMIN_REPORT_APP_BUG, [
            'variables' => [
                'bug' => "
                   <p>
                     $msg
                  </p>
                  <p>
                     Listing title: $listing->title<br> 
                     User ID: $user->id  <br> 
                     Device ID: $deviceID  <br> 
                     Listing ID: $listing->id <br>  
                     Site: " . $this->app->config('general_site_name') . "  <br>  
                     Platform: " . json_encode($platform) . "  <br>  
                     Device Details: " . json_encode($device_details) . "  <br>  
                     Device Key: " . $deviceKey. "  <br>  
                     Last Activated Route: " . $route. "  <br>  
                  </p>
                "
            ]
        ]);


        echo json_encode([
            'status' => 'ok',
            'emailStatus' => $emailStatus,
        ]);
        exit;
    }


    public function userSummaryAction()
    {
        $this->app->contentType('application/json');
        $deviceKey = $this->app->request()->params('device_key');
        if ($deviceKey) {
            $device = \Orm\Dao\Users\Devices::getDevice($deviceKey);
        }
        $listing = $this->app->apiKey->user->listing;
        $user = $this->app->apiKey->user;
        $pns = DevicesMessages::getPushNotificationsSettingsData();

        $data = [
            'business-login-status' => AuthBusiness::forceSignIn($user->user_email),
            'name' => $user->user_first_name,
            'surname' => $user->user_last_name,
            'listing' => [
                'title' => $listing->title,
                'category_name' => (!empty($listing->category) ? $listing->category->title : ''),
                'location' => $this->translate('bma_score_location', [
                    'location' => (!empty($listing->location) ? $listing->location->getTitleFull() : ''),
                    'top_location' => (!empty($listing->location) ? $listing->location->getTop()->getShortName() : '')
                ]),
                'address' => $this->getListingAddress(),
                'score' => round($listing->score),
                'product_name' => $listing->order->pricing->product->name,
                'url' => $listing->getUrl(),
                'logo' => $listing->getLogo(),
                'edit_logo_url' => $this->app->urlFor('bma.listing_images_and_logo_edit', ['set_domain' => true]),
            ],
            'manage_listing_links' => [
                'listing_edit' => $this->app->urlFor('bma.listing_basic_details_edit', ['set_domain' => true]),
                'events_list' => $this->app->urlFor('bma.events_list', ['set_domain' => true]),
                'special_offers_list' => $this->app->urlFor('bma.special_offer_list', ['set_domain' => true]),
                'complete_listing' => $this->app->urlFor('bma.complete_your_listing', ['set_domain' => true]),
                'get_more_leads' => $this->app->urlFor('bma.how_to_get_more_leads', ['set_domain' => true]),
                'upgrade' => $this->app->urlFor('bma.packages', ['set_domain' => true]),
                'find_out_more' => $this->app->urlFor('bma.packages', ['set_domain' => true]) . '#why_choose_a_featured_listing'
            ],
            'menu_links' => [
                [
                    'title' => $this->translate('api_menu_links_speciall_offer'),
                    'link' => $this->app->urlFor('bma.special_offer_list', ['set_domain' => true]),
                ],
                [
                    'title' => $this->translate('api_menu_links_events'),
                    'link' => $this->app->urlFor('bma.events_list', ['set_domain' => true]),
                ],
                [
                    'title' => $this->translate('api_menu_links_booking'),
                    'link' => $this->app->urlFor('bma.bookings_set_up', ['set_domain' => true]),
                ],
                [
                    'title' => $this->translate('api_menu_links_reviews'),
                    'link' => $this->app->urlFor('bma.listing_reviews', ['set_domain' => true]),
                ],
                [
                    'title' => $this->translate('api_menu_links_awards'),
                    'link' => $this->app->urlFor('bma.badges_most_popular', ['set_domain' => true]),
                ],
                [
                    'title' => $this->translate('api_menu_links_partner_offers'),
                    'link' => $this->app->urlFor('bma.partner_offers', ['set_domain' => true]),
                ],
                [
                    'title' => $this->translate('api_menu_links_marketplaces'),
                    'link' => $this->app->urlFor('page.static_pages', ['set_domain' => true, 'friendly_url' => 'marketplaces']),
                ],
                [
                    'title' => $this->translate('api_menu_links_account'),
                    'link' => $this->app->urlFor('bma.user_account_edit', ['set_domain' => true]),
                ]
            ],
            'leads_enabled' => $this->app->config('auctions_enabled'),
            'quote_tool_enabled' => $this->app->config('quote_tool_enabled'),
            'leads' => $this->prepareLeads(),
            'no_leads_info_links' => [
                'inbox_complete_your_listing_link' => $this->app->urlFor('bma.complete_your_listing', ['set_domain' => true]),
                'inbox_update_your_categories_link' => $this->app->urlFor('bma.listing_categories_edit', ['set_domain' => true]),
                'inbox_add_more_suburbs_link' => $this->app->urlFor('bma.packages', ['set_domain' => true]),
                'leads_faq_ling' => $this->app->urlFor('page.static_pages', ['set_domain' => true, 'friendly_url' => 'faqs-pay-per-lead'])
            ],
            'inbox' => $this->prepareInbox(),
            'no_inbox_info_links' => [
                'inbox_upgrade_your_package' => $this->app->urlFor('bma.complete_your_listing', ['set_domain' => true]),
                'inbox_update_your_categories_link' => $this->app->urlFor('bma.listing_categories_edit', ['set_domain' => true]),
                'inbox_add_more_suburbs_link' => $this->app->urlFor('bma.packages', ['set_domain' => true]),
            ],
            'leads_menu_links' => $this->prepareLeedsMenuLinks(),
            'stats' => $this->prepareStats($listing),
            'defaultNotificationSettings' => $pns,
            'notificationSettings' => $deviceKey && $device ? $device->settings : $pns
        ];

        /**
         * I am using this md5 function to hash all of the data from API.
         * Then other endpoint can check it against
         * recent stored value. If they match it means that
         * no changes were made. If they differ it means that
         * changes were made and we need we need to perform actions
         * on data.
         */
        $data['data-signature-hash'] = md5(json_encode($data));
        echo json_encode($data);;
        exit;
    }

    public function userSilentAuthAction()
    {
        $this->app->contentType('application/json');
        $user = $this->app->apiKey->user;
        AuthBusiness::forceSignIn($user->user_email);
        exit;
    }


    public function getListingAddress()
    {
        $listing = $this->app->apiKey->user->listing;
        $data = '';
        if ($listing->isCompanyAddressAvaliable()) {
            $data .= $listing->getCompanyAddressLP();
        } else {
            $data .= $listing->getAddressLP();
        }
        return $data;
    }


    public function removeDeviceAction()
    {
        $this->app->contentType('application/json');
        $deviceKey = $this->app->request()->params('device_key');

        if ($deviceKey) {
            $device = \Orm\Dao\Users\Devices::getDevicesForUser($this->app->apiKey->user->id)->where('device_id', $deviceKey)->first();
        }

        if (!isset($device) || empty($device)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Device does not exists'
            ]);
            exit;
        }

        $device->messages()->where('status', '=', DevicesMessages::STATUS_TO_SEND)->delete();
        $device->delete();

        echo json_encode([
            'status' => 'ok'
        ]);;
        exit;
    }

    public function userInboxAction()
    {
        $this->app->contentType('application/json');
        $page = $this->app->request()->params('page', 1);

        echo json_encode($this->prepareInbox($page));
        exit;
    }

    public function userLeadsAction()
    {
        $this->app->contentType('application/json');
        $page = $this->app->request()->params('page', 1);

        echo json_encode($this->prepareLeads($page));
        exit;
    }

    public function inboxDisplayAction()
    {
        $this->app->contentType('application/json');
        $id = $this->app->request()->params('id');
        $listing = $this->app->apiKey->user->listing;

        $message = Emails::where('id', '=', $id)
            ->where('listing_id', '=', $listing->id)
            ->first();

        if (empty($message)) {
            echo json_encode([
                'error' => 'Message does not exists'
            ]);
        } else {
            $message->display = 1;
            $message->save();

            echo json_encode([
                'status' => 'ok'
            ]);
        }

        exit;
    }

    public function leadsDisplayAction()
    {
        $this->app->contentType('application/json');
        $id = $this->app->request()->params('id');
        $listing = $this->app->apiKey->user->listing;

        $lead = Participants::where('id', '=', $id)
            ->where('listing_id', '=', $listing->id)
            ->first();

        if (empty($lead)) {
            echo json_encode([
                'error' => 'Lead does not exists'
            ]);
        } else {
            $lead->display = 1;
            $lead->save();

            echo json_encode([
                'status' => 'ok'
            ]);
        }

        exit;
    }

    private function prepareLeads($page = 1)
    {
        $listing = $this->app->apiKey->user->listing;

        $auctions = Auctions::getAuctionsByListingId($listing->id, 50, $page);
        $auctions->load('auction', 'auction.category', 'auction.location', 'auction.user', 'auction.bids');

        $results = [];
        if (!empty($auctions)) {
            foreach ($auctions as $row) {
                if ($row->auction->request_type == \Orm\Model\Auctions\Auctions::REQUEST_TYPE_CONTACT) {
                    $title = $this->translate('bma_auctions_contact_for', ['category' => $row->auction->category->title, 'location' => $row->auction->location->locationNameFormatted($this->app->config('location')['autocomplete_format'])]);
                } elseif ($row->auction->request_type == \Orm\Model\Auctions\Auctions::REQUEST_TYPE_BOOKING) {
                    $title = $this->translate('bma_auctions_booking_for', ['category' => $row->auction->category->title, 'location' => $row->auction->location->locationNameFormatted($this->app->config('location')['autocomplete_format'])]);
                }


                $createdAtDateFormat = date('d F Y\\, \\a\\t h:ia', strtotime(Dates::formatToTpl($row->auction->date_start, 'time_format')));
                $received = $this->translate('bma_auctions_lead_received_on_date', ['date' => $createdAtDateFormat]);


                $bid_info = '';
                $bid = $row->bid;
                if (!empty($bid) && $bid->stauts != 3) {
                    $bidActionTitme = date('d F Y\\, \\a\\t h:ma', strtotime(Dates::formatToTpl($bid->action_time)));
                    $bidAmount = \Lib\Number::decimalLikeCurrencyNumber($bid->amount);
                    $bid_info = $this->translate('bma_auctions_bids_made',
                        [
                            'bid_action_time' => $bidActionTitme,
                            'bid_amount' => $bidAmount
                        ]
                    );
                }


                $results[] = [
                    'id' => $row->auction->id,
                    'title' => $title,
                    'author' => $row->auction->getContactDetails()->first_name,
                    'created_at' => Dates::formatToTpl($row->auction->date_start, 'input_time_format'),
                    'received' => $received,
                    'status' => $row->getStatusClassForApi($listing->id),
                    'statusLabel' => $row->getStatusLabelForApi($listing->id),
                    'is_new' => ($row->display != 0 || $row->auction->status == \Orm\Model\Auctions\Auctions::STATUS_CLOSED ? false : true),
                    'closes_in' => Dates::formatToTpl($row->auction->date_end, 'date_format'),
                    'bid_info' => $bid_info,
                    'link' => $this->app->urlFor('bma.auctions_details', ['id' => $row->auction->id, 'set_domain' => true]),
                ];
            }
        }

        $unreadTotal = Participants::where('listing_id', '=', $listing->id)
            ->new()
            ->count();

        return [
            'unread_total' => $unreadTotal,
            'total' => $auctions->total(),
            'results' => $results
        ];
    }

    private function prepareInbox($page = 1)
    {
        $listing = $this->app->apiKey->user->listing;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $countNotDisplayed = $listing->emails()->NotDisplayed()->count();


        $emails = Emails::where('listing_id', '=', $listing->id)->orderBy('created_date', 'DESC')->paginate(50);

        $emails->each(function ($email) {
            if (!empty($email->user_id)) {
                $email->loadFromCache('user');
                if (!isset($email->user) and isset($email)) {
                    $user = \Orm\Model\Users\Users::withTrashed()->where('id', '=', $email->user_id)->first();
                    $email->user = $user;
                }
            }
        });


        $results = [];


        if (!empty($emails)) {
            foreach ($emails as $message) {
                $results[] = [
                    'id' => $message->id,
                    'title' => $this->translate('bma_inbox_type_' . $message->type),
                    'author' => $message->body_message['from_name'],
                    'created_at' => Dates::formatToTpl($message->created_date, 'input_date_format'),
                    'is_new' => ($message->display ? false : true),
                    'data' => (isset($message->body_message['message']) ? $message->body_message['message'] : ''),
                    'viewData' => $this->getMessageViewData($message)
                ];
            }
        }

        return [
            'unread_total' => $countNotDisplayed,
            'total' => $emails->total(),
            'results' => $results
        ];
    }


    function getMessageViewData($email)
    {
        $data = [];
        $data['parsed_message'] = '';
        $data['inbox_type'] = $email->type;
        $user = $this->app->apiKey->user;
        $emailBodyMessage = $email->body_message;

        /** IS INBOX NOT AVAILABLE */
        if (!isset($email->user) && !empty($email->user) && !empty($email->user['user_first_name']) && empty($email->body_from)) {
            $data['bma_inbox_not_available'] = $this->translate('bma_inbox_not_available');
        }


        /** GET EMAIL TITLE BASE ON EVENT TYPE */
        $titlePhrase = 'bma_inbox_type_' . $email->type;
        $data['email_title'] = $this->translate($titlePhrase);


        /** IF USER NO LONGER EXISTS */
        if ($email->user && $email->user->trashed()) {
            $delAt = date('d F Y\\', strtotime(Dates::formatToTpl($email->user->deleted_at)));
            $data['email_user_does_not_exists'] = $this->translate('bma_inbox_user_no_longer_available', ['deleted_at' => $delAt]);
        }


        if ($email->type == 1 || $email->type == 2 || $email->type == 3) {

            $data['inbox_good_news'] = $this->translate('bma_inbox_good_news');
            if ($user->user_first_name) {
                $data['user_first_name'] = $user->user_first_name;
            }
            $data['inbox_a_visitor_your_listing_has_contacted'] = $this->translate(
                'bma_inbox_a_visitor_your_listing_has_contacted', [
                    'listing_url' => $user->listing->getUrl(),
                    'listing_title' => $user->listing->title,
                    'site_name' => $this->app->config('general_site_name')
                ]
            );
            $data['inbox_listing_url'] = $user->listing->getUrl();
            $data['inbox_listing_title'] = $user->listing->title;
            $data['inbox_site_name'] = $this->app->config('general_site_name');


            $data['inbox_please_connect_with_this_visitor_using_details'] = $this->translate('bma_inbox_please_connect_with_this_visitor_using_details');
            $data['inbox_contact_details'] = $this->translate('bma_inbox_contact_details');
            /**FROM NAME**/
            if (isset($emailBodyMessage['from_name']) && strlen($emailBodyMessage['from_name']) > 0) {
                $data['inbox_name'] = '<b>' . $this->translate('bma_inbox_name') . '</b> ' . $emailBodyMessage['from_name'];
            }
            /**PHONE MOBILE**/
            if (isset($emailBodyMessage['phone_mobile']) && strlen($emailBodyMessage['phone_mobile']) > 0) {
                $data['inbox_phone'] = '<b>' . $this->translate('bma_inbox_phone') . '</b> ' . $emailBodyMessage['phone_mobile'];
            }
            /**FROM EMAIL */
            if (isset($emailBodyMessage['from_email']) && strlen($emailBodyMessage['from_email']) > 0) {
                $data['inbox_from_email'] = '<b>' . $this->translate('bma_inbox_from_email') . '</b> ' . $emailBodyMessage['from_email'];
            } elseif (isset($email->body_email) && strlen($email->body_email) > 0) {
                $data['inbox_from_email'] = '<b>' . $this->translate('bma_inbox_from_email') . '</b> ' . $email->body_email;
            }


            /** CUSTOM FIELDS */
            $data['inbox_visitor_expressed_an_interest_in'] = '';
            $wasTitle = false;
            if (isset($emailBodyMessage['custom_fields']) && !empty($emailBodyMessage['custom_fields'])) {
                $wasTitle = true;
                $data['inbox_visitor_expressed_an_interest_in'] .= '<b>' . $this->translate('bma_inbox_visitor_expressed_an_interest_in') . '</b> <br>';
                foreach ($emailBodyMessage['custom_fields'] as $key => $element):
                    $data['inbox_visitor_expressed_an_interest_in'] .= ucfirst($key) . ':' . strip_tags($element) . '<br>';
                endforeach;
            }

            if (isset($emailBodyMessage['categories']) && !empty($emailBodyMessage['categories'])) {
                if (!$wasTitle) {
                    $data['inbox_visitor_expressed_an_interest_in'] .= '<b>' . $this->translate('bma_inbox_visitor_expressed_an_interest_in') . '</b> <br>';
                }
                foreach ($emailBodyMessage['categories'] as $category):
                    $data['inbox_visitor_expressed_an_interest_in'] .= '-' . $category . '<br>';
                endforeach;
            }
            if (isset($emailBodyMessage['message']) && strlen($emailBodyMessage['message']) > 1):
                $data['inbox_message'] = '<b>' . $this->translate('bma_inbox_message') . '</b> <br> ' . $emailBodyMessage['message'];
            endif;


        } elseif ($email->type == 5 || $email->type == 6 || $email->type == 7 || $email->type == 8) {
            $data['inbox_good_news'] = $this->translate('bma_inbox_hi');
            if ($user->user_first_name) {
                $data['user_first_name'] = $user->user_first_name;
            }
            /** IF EMAIL IS EVENT RELATED */
            if (isset($email->event) && !(empty($email->event))) {
                $data['email_event_title'] = $email->event->title;
                $data['email_event_url'] = $email->event->getUrl();
                if ($email->type == 5) {
                    $data['bma_inbox_sentence_event'] = $this->translate('bma_inbox_sentence_event', [
                        'link' => $email->event->getUrl(),
                        'title' => $email->event->title,
                        'site_name' => $this->app->config('general_site_name')
                    ]);
                }
                if ($email->type == 6) {
                    $data['bma_inbox_sentence_event'] = $this->translate('bma_inbox_sentence_event_more_details', [
                        'link' => $email->event->getUrl(),
                        'title' => $email->event->title,
                        'site_name' => $this->app->config('general_site_name')
                    ]);
                }

                if ($email->type == 7) {
                    $data['bma_inbox_sentence_event'] = $this->translate('bma_inbox_sentence_special_offer', [
                        'link' => $email->event->getUrl(),
                        'title' => $email->event->title,
                        'site_name' => $this->app->config('general_site_name')
                    ]);
                }
                if ($email->type == 8) {
                    $data['bma_inbox_sentence_event'] = $this->translate('bma_inbox_sentence_special_offer_more_details', [
                        'link' => $email->event->getUrl(),
                        'title' => $email->event->title,
                        'site_name' => $this->app->config('general_site_name')
                    ]);
                }
            }

            $data['inbox_please_connect_with_this_visitor_using_details'] = $this->translate('bma_inbox_please_connect_with_this_visitor_using_details');
            $data['inbox_contact_details'] = $this->translate('bma_inbox_contact_details');
            /**FROM NAME**/
            if (isset($emailBodyMessage['from_name']) && strlen($emailBodyMessage['from_name']) > 0) {
                $data['inbox_name'] = '<b>' . $this->translate('bma_inbox_name') . '</b> ' . $emailBodyMessage['from_name'];
            }
            /**PHONE MOBILE**/
            if (isset($emailBodyMessage['phone_mobile']) && strlen($emailBodyMessage['phone_mobile']) > 0) {
                $data['inbox_phone'] = '<b>' . $this->translate('bma_inbox_phone') . '</b> ' . $emailBodyMessage['phone_mobile'];
            }
            /**FROM EMAIL */
            if (isset($emailBodyMessage['from_email']) && strlen($emailBodyMessage['from_email']) > 0) {
                $data['inbox_from_email'] = '<b>' . $this->translate('bma_inbox_from_email') . '</b> ' . $emailBodyMessage['from_email'];
            } elseif (isset($email->body_email) && strlen($email->body_email) > 0) {
                $data['inbox_from_email'] = '<b>' . $this->translate('bma_inbox_from_email') . '</b> ' . $email->body_email;
            }


            if (isset($emailBodyMessage['message']) && strlen($emailBodyMessage['message']) > 1):
                $data['inbox_message'] = '<b>' . $this->translate('bma_inbox_comments') . '</b> <br> ' . $emailBodyMessage['message'];
            endif;


        } else {
            foreach ($emailBodyMessage as $key => $element) {
                if (is_iterable($element) && is_array($element)) {
                    foreach ($element as $key2 => $val2) {
                        $data['parsed_message'] .= '<b>' . $key2 . ':</b> ' . addslashes(strip_tags($val2)) . '<br>';
                    }
                } else if (strlen(addslashes(strip_tags($element))) > 0) {
                    if ($key == 'from_name') {
                        $data['parsed_message'] .= '<b>' . $this->translate('bma_inbox_from_name') . '</b> ' . addslashes(strip_tags($element)) . '<br>';
                    } elseif ($key == 'from_email') {
                        $data['parsed_message'] .= '<b>' . $this->translate('bma_inbox_from_email') . '</b> ' . addslashes(strip_tags($element)) . '<br>';
                    } elseif ($key == 'phone_mobile') {
                        $data['parsed_message'] .= '<b>' . $this->translate('bma_inbox_phone') . '</b> ' . addslashes(strip_tags($element)) . '<br>';
                    } elseif ($key == 'message') {
                        $data['parsed_message'] .= '<b>' . $this->translate('bma_inbox_message') . '</b>' . nl2br(addslashes(strip_tags($element))) . '<br>';
                    }
                }
            }
        }
        return $data;
    }


    private function prepareStats($listing)
    {
        /** LEADS SETUP */
        $leadsNumber = CountersDao::getTotalLeadsForListingIdFromCache($listing->id)->getTotal();
        if ($leadsNumber <= 0) {
            $leads = $this->translate('bma_listings_stats_leads_are_still_being_collected');
            $leadsCounted = false;
        } else {
            $leads = $leadsNumber;
            $leadsCounted = true;
        }
        /** VISITS SETUP */
        $visitsInfo = CountersDao::getTotalVisitsForListingIdFromCache($listing->id)->getTotal();
        if ($visitsInfo <= 0) {
            $visits = $this->translate('bma_listings_stats_visits_are_still_being_collected');
            $visitsCounted = false;
        } else {
            $visits = $visitsInfo;
            $visitsCounted = true;
        }
        /** IMPRESSIONS SETUP */
        $impressionsInfo = ImpressionsDao::getTotalForListingIdFromCache($listing->id);
        if ($impressionsInfo <= 0) {
            $impressions = $this->translate('bma_listings_stats_impressions_are_still_being_collected');
            $impressionsInfoCounted = false;
        } else {
            $impressions = LibNumber::decimalNumber($impressionsInfo);
            $impressionsInfoCounted = true;
        }
        /** RANKINGS SETUP */
        $rankingsAverage = RankingsDao::getResultForListingAverageFromCache($listing->id);
        if ($rankingsAverage <= 0) {
            $rankings = $this->translate('bma_listings_stats_ranking_still_being_calculated');
            $rankingsCounted = false;
        } elseif ($rankingsAverage > 30) {
            $rankingsCounted = true;
            $rankings = 'over ' . LibNumber::ordinalNumber(30);
        } else {
            $rankingsCounted = true;
            $rankings = LibNumber::ordinalNumber($rankingsAverage);
        }
        return [
            [
                'id' => 0,
                'title' => $this->translate('bma_dashboard_leads'),
                'icon' => 'notifications',
                'color' => '#00a65a',
                'counted' => $leadsCounted,
                'number' => $leads,
                'link' => $this->app->urlFor('bma.listing_leads', ['set_domain' => true])
            ],
            [
                'id' => 1,
                'title' => $this->translate('bma_dashboard_visits'),
                'icon' => 'eye',
                'color' => '#f39c12',
                'counted' => $visitsCounted,
                'number' => $visits,
                'link' => $this->app->urlFor('bma.listing_visits', ['set_domain' => true])
            ],
            [
                'id' => 2,
                'title' => $this->translate('bma_dashboard_impressions'),
                'icon' => 'list-box',
                'color' => '#f56954',
                'counted' => $impressionsInfoCounted,
                'number' => $impressions,
                'link' => $this->app->urlFor('bma.listing_impressions', ['set_domain' => true])
            ],
            [
                'id' => 3,
                'title' => $this->translate('bma_dashboard_ranking'),
                'icon' => 'stats',
                'color' => '#00c0ef',
                'counted' => $rankingsCounted,
                'number' => $rankings,
                'link' => $this->app->urlFor('bma.listing_ranking', ['set_domain' => true])
            ],
        ];
    }


    private function prepareLeedsMenuLinks()
    {
        return [
            [
                'title' => $this->translate('bma_general_menu_auctions_status'),
                'link' => $this->app->urlFor('bma.auctions_status', ['set_domain' => true]),
            ],
            [
                'title' => $this->translate('bma_general_menu_auctions_history'),
                'link' => $this->app->urlFor('bma.auctions_history', ['set_domain' => true]),
            ],
            [
                'title' => $this->translate('bma_general_menu_auctions_settings'),
                'link' => $this->app->urlFor('bma.auctions_settings', ['set_domain' => true]),
            ],
            [
                'title' => $this->translate('bma_general_menu_auctions_how_it_works'),
                'link' => $this->app->urlFor('page.pay_per_lead', ['set_domain' => true]),
            ],
            [
                'title' => $this->translate('bma_general_menu_auctions_faq'),
                'link' => $this->app->urlFor('page.static_pages', ['set_domain' => true, 'friendly_url' => 'faqs-pay-per-lead']),
            ],
        ];
    }

    public function updateDeviceNotificationSettingsAction()
    {
        $this->app->contentType('application/json');
        $deviceKey = $this->app->request()->params('device_key');
        $settings = $this->app->request()->params('settings');
        \Orm\Dao\Users\Devices::updateDeviceSettings($deviceKey, json_decode($settings));
        echo json_encode([
            'status' => 'ok',
        ]);
        exit;
    }
}



