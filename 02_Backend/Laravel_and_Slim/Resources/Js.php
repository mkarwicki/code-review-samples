<?php

namespace Apps\Page\Controller\Resources;

use Apps\Resources\ResourcesAbstract,
    Extension\Debugger\SlimDebugBarMiddleware;



class Js extends ResourcesAbstract {

    /**
     * Type file
     */
    const PREFIX_NAME = '.js';

    /**
     * Files
     *
     * @var array
     */
    private static $_group_main = array(
        'main' => array(
            self::PREFIX_FILE => array(
                '/Js/main/jquery.min.js',
                '/Js/main/bootstrap.min.js',
                '/Js/main/menu.js',
                '/Js/libs/disable_debugger.js',
                '/Js/main/main.js',
                '/Js/plugins/moment.js',
                '/Js/plugins/*',
                '/twitter/typeahead.js/dist/typeahead.bundle.min.js',
                '/kartik-v/bootstrap-star-rating/js/star-rating.min.js',
                '/Js/libs/base_autocomplete.js',
                '/Js/libs/category_autocomplete.js',
                '/Js/libs/location_autocomplete.js',
                '/Js/libs/category_listing_autocomplete.js',
                '/Js/libs/menu_search_autocomplete.js',
                '/Js/libs/menu_search_browse_category.js',
                '/Js/libs/customer_feedback.js',
                '/Js/libs/ppl_tracker.js',
                '/Js/modules/search_menu.js',
                '/Js/modules/customer_feedback.js',
                '/Js/modules/stat_cookie.js',
                '/Js/modules/ppl_tracker.js',
                '/Js/libs/admin_top_bar.js',
                '/Js/modules/components/slider/Slick.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        )
    );

    /**
     * Group libs
     *
     * @var array
     */
    private static $_group_libs = array(
        'libs_login_visitors' => array(
            self::PREFIX_FILE => array(
                '/Js/libs/login_visitors.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'libs_load_more' => array(
            self::PREFIX_FILE => array(
                '/Js/libs/load_more.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
    );

    /**
     * Group modules
     *
     * @var array
     */
    private static $_group_modules = array(
        'modules_search' => array(
            self::PREFIX_FILE => array(
                '/Js/plugins/formValidation.min.js',
                '/Js/plugins/formValidationBootstrap.min.js',
                '/Js/plugins/jquery.bootstrap.wizard.min.js',
                '/Js/plugins/readmore.js',
                '/Js/libs/save_lisitng.js',
                '/Js/libs/stats_listing.js',
                '/Js/libs/ppl_forms.js',
                '/Js/modules/default.js',
                '/Js/modules/search_filters.js',
                '/Js/modules/search.js',
                '/Js/single/google/ads.js',
                '/Js/modules/search/listing2018.js',
                '/Js/libs/filters.js',
                '/Js/libs/serp_sorting.js',
                '/Js/modules/event_contact.js',
                '/Js/modules/listing_contact_modal.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        /* Old */
        'modules_map' => array(
            self::PREFIX_FILE => array(
                '/Js/single/google/map-loader.js',
                '/Js/single/google/gmaps.js',
                '/Js/single/google/infobox.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_map_google_search' => array(
            self::PREFIX_FILE => array(
                '/Js/single/google/markerclusterer_packed.js',
                '/Js/libs/map_cluster.js',
                '/Js/single/google/gmaps.js',
                '/Js/single/google/infobox.js',
                '/Js/single/maps.provider.google.js',
                '/Js/libs/map_listing_info.js',
                '/Js/libs/map_listing.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_map_mapbox_search' => array(
            self::PREFIX_FILE => array(
                '/Js/libs/map_cluster.js',
                '/Js/single/maps.provider.mapbox.js',
                '/Js/libs/map_listing_info.js',
                '/Js/libs/map_listing.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_map_google' => array(
            self::PREFIX_FILE => array(
                '/Js/single/google/gmaps.js',
                '/Js/single/google/infobox.js',
                '/Js/single/maps.provider.google.js',
                '/Js/libs/map_listing_info.js',
                '/Js/libs/map_listing.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_map_mapbox' => array(
            self::PREFIX_FILE => array(
                '/Js/single/maps.provider.mapbox.js',
                '/Js/libs/map_listing_info.js',
                '/Js/libs/map_listing.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_map_loader' => array(
            self::PREFIX_FILE => array(
                '/Js/single/google/map-loader.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_ie' => array(
            self::PREFIX_FILE => array(
                '/Js/ie/html5shiv.js',
                '/Js/ie/respond.min.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_search_empty' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/default.js',
                '/Js/modules/search_filters.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_listing_page' => array(
            self::PREFIX_FILE => array(
                '/Js/plugins/formValidation.min.js',
                '/Js/plugins/formValidationBootstrap.min.js',
                '/Js/plugins/jquery.bootstrap.wizard.min.js',
                '/Js/libs/save_lisitng.js',
                '/Js/libs/load_more.js',
                '/Js/libs/stats_listing.js',
                '/Js/libs/ppl_forms.js',
                '/Js/libs/slider.js',
                '/Js/libs/map_listing_distance.js',
                '/Js/libs/listing_buttons.js',
                '/Js/modules/default.js',
                '/Js/modules/listings_single.js',
                '/Js/plugins/owl.carousel.min.js',
                '/Js/modules/event_contact.js',
                '/Js/modules/components/slider/Slick.js',
                '/Js/modules/components/slider/DefaultSlider.js',
                '/Js/modules/listing_contact_modal.js',
                '/Js/modules/components/navigation/admin_bar/free_listing_preview_admin_bar.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_listing_contact' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/listing_contact.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_listing_review' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/default.js',
                '/Js/modules/listing_review.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_event_page' => array(
            self::PREFIX_FILE => array(
                '/Js/libs/load_more.js',
                '/Js/modules/default.js',
                '/Js/modules/events.js',
                '/Js/modules/search_filters.js',
                '/Js/modules/events_filters.js',
                '/Js/modules/event_contact.js',
                '/Js/libs/save_lisitng.js',
                '/Js/libs/stats_listing.js',
                '/Js/libs/ppl_forms.js',
                '/Js/modules/eso.js',
                '/Js/modules/search/listing2018.js',
                '/Js/libs/filters.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_event_single_page' => array(
            self::PREFIX_FILE => array(
                '/Js/libs/stats_listing.js',
                '/Js/modules/default.js',
                '/Js/modules/events_single.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_event_contact' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/default.js',
                '/Js/modules/event_contact.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_blog' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/default.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_resources' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/default.js',
                '/Js/libs/load_more.js',
                '/Js/modules/resources.js',
                '/Js/modules/plugins/masonry.pkgd.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_home_page' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/home_page.js',
                '/Js/single/responsive-tabs.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_contact_us' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/contact_us.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_register' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/register.js',
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_login_visitors' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/login_visitors.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_visitors_profile' => array(
            self::PREFIX_FILE => array(
                '/Js/libs/categories_tree.js',
                '/Js/modules/visitor_profile.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_visitors_settings' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/visitor_settings.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_visitors_reviews' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/visitor_reviews.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
        'modules_visitors_auctions_history' => array(
            self::PREFIX_FILE => array(
                '/Js/modules/visitor_auctions_history.js'
            ),
            self::PREFIX_PATH => '/js/page/',
            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
        ),
    );

    /**
     * Group single
     *
     * @var array
     */
//    private static $_group_single = array(
//        'single_gmaps' => array(
//            self::PREFIX_FILE => array(
//                '/Js/single/google/gmaps.js',
//                '/Js/single/google/infobox.js',
//                '/Js/single/google/markerclusterer_packed.js',
//                '/Js/libs/map_listing_info.js',
//                '/Js/libs/map_cluster.js',
//                '/Js/libs/map_listing.js'
//            ),
//            self::PREFIX_PATH => '/js/page/',
//            self::PREFIX_FILTER => 'Assetic\Filter\PackerFilter',
//        ),
//    );

    /**
     *
     *
     * @param \Slim\Slim $app
     */
    public function __construct(\Slim\Slim &$app) {
        parent::__construct($app, APP_PATH . '/Page/Resources/');
        $this->setReplacePath(SPECIFIC_PATH . '/Page/Resources/');
        $this->setCommonPath(APP_PATH . '/Resources/');
    }

    public function indexAction() {
        parent::indexAction();
//Generate js for debuggbar
        $debugbar = SlimDebugBarMiddleware::getSlimDebugBar($this->app);
        $renderer = $debugbar->getJavascriptRenderer();
        $path = PUBLIC_PATH . '/js/_debugger/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $renderer->dumpJsAssets($path . 'dump.js');
    }

}
