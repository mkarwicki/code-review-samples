<?php

namespace Apps\Page\Controller\Resources;

use Apps\Resources\ResourcesAbstract,
    Extension\Debugger\SlimDebugBarMiddleware;



class Css extends ResourcesAbstract {

    /**
     * Type file
     */
    const PREFIX_NAME = '.css';

    /**
     * Files
     *
     * @var array
     */
    private static $_group_main = array(
        '404_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'result_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/kartik-v/bootstrap-star-rating/css/star-rating.min.css',
                '/Css/bootstrap-datetimepicker.min.css',
	            '/Css/bootstrap-select.min.css',
                '/Css/search.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less',
                '/Css/less/auctions/auctions.less',
                '/Css/less/auctions/auctions_modals.less',
                '/Css/less/search/listing2018/listing2018.less',
                '/Css/less/search/filters/filters.less',
                '/Css/less/search/filters/filters-summary.less',
                '/Css/less/search/sorting/sorting.less',
                '/Css/less/search/rhs/rhs.less',
                '/Css/less/search/lhs/lhs.less',
                '/Css/less/contact-modals/contact-modals.less',
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'events_result_page' => array(
            self::PREFIX_FILE => array(
	            '/Css/bootstrap.css',
	            '/Css/normalize.css',
	            '/Css/font-awesome.css',
	            '/Css/entypo.css',
	            '/Css/header-footer.css',
	            '/Css/bootstrap-datetimepicker.min.css',
	            '/Css/search.css',
	            '/Css/events.css',
	            '/Css/disable-debugger.css',
	            '/Css/less/main.less',
	            '/Css/less/search/rhs/rhs.less',
	            '/Css/less/search/lhs/lhs.less',
	            '/Css/less/search/listing2018/listing2018.less',
	            '/Css/less/search/filters/filters.less',
	            '/Css/less/search/filters/filters-summary.less',
                '/Css/less/auctions/auctions.less',
                '/Css/less/auctions/auctions_modals.less',
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'listing_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/kartik-v/bootstrap-star-rating/css/star-rating.min.css',
                '/Css/bootstrap-datetimepicker.min.css',
                '/Css/ekko-lightbox.min.css',
                '/Css/bootstrap-social.css',
	            '/Css/bootstrap-select.min.css',
                '/Css/listings.css',
                '/Css/less/main.less',
                '/Css/disable-debugger.css',
                '/Css/less/listings.less',
	            '/Css/less/components/slider/Slider.less',
	            '/Css/less/components/slider/DefaultSlider.less',
	            '/Css/less/components/SideAuctions/SideAuctions.less',
                '/Css/less/auctions/auctions_modals.less',
                '/Css/less/listing/listing-events.less',
	            '/Css/less/contact-modals/contact-modals.less',
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'corporate_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/bootstrap-social.css',
                '/Css/less/main.less',
                '/Css/disable-debugger.css',
                '/Css/less/corporate/corporate-page.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'listing_contact' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/visualcaptcha.css',
                '/Css/bootstrap-select.min.css',
                '/Css/form.css',
                '/kartik-v/bootstrap-star-rating/css/star-rating.min.css',
                '/Css/less/main.less',
                '/Css/disable-debugger.css',
                '/Css/less/form.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'static_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/visualcaptcha.css',
                '/Css/bootstrap-select.min.css',
                '/Css/form.css',
                '/kartik-v/bootstrap-star-rating/css/star-rating.min.css',
                '/Css/static_page.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less',
                '/Css/less/auctions/auctions_static_page.less',
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'event_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/listings.css',
                '/Css/events.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'event_contact' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/visualcaptcha.css',
                '/Css/events.css',
                '/Css/form.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'categories_index_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/static.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'blog_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/search.css',
                '/Css/blog.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'home_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap336.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/search.css',
                '/kartik-v/bootstrap-star-rating/css/star-rating.min.css',
                '/Css/header-footer.css',
                '/Css/home.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less',
                '/Css/less/components/slider/Slider.less',
                '/Css/less/components/slider/DefaultSlider.less',
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        ),
        'vma_page' => array(
            self::PREFIX_FILE => array(
                '/Css/bootstrap.css',
                '/Css/normalize.css',
                '/Css/font-awesome.css',
                '/Css/entypo.css',
                '/Css/header-footer.css',
                '/Css/visualcaptcha.css',
                '/Css/bootstrap-select.min.css',
                '/Css/form.css',
                '/kartik-v/bootstrap-star-rating/css/star-rating.min.css',
                '/Css/static_page.css',
                '/Css/vma.css',
                '/Css/disable-debugger.css',
                '/Css/less/main.less',
                '/Css/less/vma/vma.less',
                '/Css/less/vma/vma_requests_history.less',
                '/Css/less/vma/vma_mobile_menu.less'
            ),
            self::PREFIX_PATH => '/css/page/',
            self::PREFIX_FILTER => \Extension\Assetic\MinifyFilter::class,
        )
    );

    /**
     *
     *
     * @param \Slim\Slim $app
     */
    public function __construct(\Slim\Slim &$app) {
        parent::__construct($app, APP_PATH . '/Page/Resources/');
        $this->setAppendPath(SPECIFIC_PATH . '/Page/Resources/');
        $this->setCommonPath(APP_PATH . '/Resources/');
    }

    public function indexAction() {
        parent::indexAction();
        //Generate css for debuggbar
        $debugbar = SlimDebugBarMiddleware::getSlimDebugBar($this->app);
        $renderer = $debugbar->getJavascriptRenderer();
        $path = PUBLIC_PATH . '/css/_debugger/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $renderer->dumpCssAssets($path . 'dump.css');
    }

}
