<?php

namespace Apps\Page\Controller\Listings;

use Apps\Page\Controller,
    Lib\Auth\AuthBusiness,
    Lib\Auth\AuthAdmin,
    Lib\Listings\Meta,
    Orm\Model\Listings\Listings as ListingsModel,
    Orm\Dao\ListingsImages,
    Orm\Dao\Fields\Fields as FieldsDao,
    Orm\Model\Fields\Groups as FieldsGroupsModel,
    Orm\Dao\Listings\Listings as ListingsDao,
    Orm\Dao\Events\Events as EventsDao,
    Orm\Dao\ListingsLogo,
    Orm\Dao\Listings\Reviews\Reviews as ReviewsDao,
    Orm\Dao\Listings\Reviews\Elastica as ReviewsElastica,
    Orm\Dto\Related\ElasticaSearchParams,
    Orm\Dto\Related\DistanceAlgoSettings,
    Orm\Dao\Events\Elastica as EventsElastica,
    Orm\Dao\Listings\Elastica as ListingsElastica,
    Orm\Dao\Listings\MostPopular as MostPopularDao,

    Orm\Dao\Articles\Elastica as ArticlesElastica,
    Orm\Dto\Related\ArticleAlgoSettings,
    Orm\Dto\Related\ElasticaSearchArticleParams,

    // v3 elastica
    Orm\Dao\Listings\SpecialOffers\Elastica as SpecialOffersElastica,
    Orm\Dto\Related\SpecialOffersAlgoSettings,
    //Orm\Dto\Related\ElasticaSearchSpecialOffersParams,

    // v1 simple and v2 elastica
    Orm\Dao\Listings\SpecialOffers,
    Orm\Dto\Listings\SpecialOffers\SearchParams as SpecialOffersSearchParams,

    Lib\Listings\MostPopular,
    Lib\Email\IntroductionMail\Cookie as IntroductionMailCookie,
    Lib\StringLib;

use Lib\Navigation\AdminBar\Adapter\ListingPreviewAdapter;
use Lib\Navigation\AdminBar\AdminBar;
use Orm\Dao\Listings\Related;
use Orm\Dto\Related\RelatedSearchParams;
use Orm\Model\Articles\Articles;
use Orm\Model\Listings\Events\Events;
use Orm\Model\Listings\Reviews\Reviews;
use Orm\Dao\Associations\Associations as AssociationsDao;
use Apps\Bma\Form\Listings\FormDecorator\ListingsTagsDecorator;


class Listings extends Controller {

    CONST REVIEWS_PER_PAGE = 10;
    CONST TEMPLATE_NAME_PENDING = 'Pending';
    CONST TEMPLATE_NAME_FREE = 'Free';
    CONST TEMPLATE_NAME_FEATURED = 'Featured';

    /**
     *
     * @var ListingsModel
     */
    private $_listing = null;

    public function indexAction($mode=false) {
        $id = $this->app->router()->getCurrentRoute()->getParam('id');

        $this->_listing = ListingsModel::findCachedById($id);

        $this->checkRedirect();
        $this->app->applyHook('before.render.gziped');
        $this->language('public_listing');
        $this->language('public_auctions');

        $templateName = $this->getTemplateName();

        $this->_listing->loadFromCache('category');
        $this->_listing->loadFromCache('location');
        // break and return template if listing is not active
        if ((self::TEMPLATE_NAME_PENDING == $templateName)) {
            return $this->render('Listings/Single/' . $templateName, $this->getViewData());
        }

        if ($this->_listing->category_limit > 0) {
            $this->_listing->loadFromCache('categories');
        }

        $this->_listing->loadFromCache('product');
        $this->_listing->loadFromCache('fields');

        $this->_listing->loadFromCache('images');
        $this->_listing->loadFromCache('logo');

        if ($this->_listing->documents_limit > 0) {
            $this->_listing->loadFromCache('documents');
        }

        if ($this->_listing->coverage_limit > 0) {
            $this->_listing->loadFromCache('locations');
        }

        if ($this->_listing->isMobileTypeFixed() === false) {
            $this->_listing->loadFromCache('companyAddress');
        }
        $this->_listing->loadFromCache('bookingSettings');

        /**
         *  If Listing is in preview mode
         *  and currently logged in user is Administrator
         *  and it is free, claimed, pending, not on steps listing
         *  we display
         *  it with limited sections and a admin menu bar
         */
        $step=\Orm\Dao\Listings\Steps::getUserSteps($this->_listing->user->id);
        if(
            AuthAdmin::getUser() !== null
            && $mode == 'adminPreview'
            && $templateName == 'Free'
            && $this->_listing->location_id !=0
            && $this->_listing->claimed == \Orm\Model\Listings\Listings::CLAIMED
            && $this->_listing->status == \Orm\Model\Listings\Listings::STATUS_PENDING
            && isset($step)
            && $step->date_end != '0000-00-00 00:00:00'
        ) {
            $listingPreviewAdminBar=new AdminBar(new ListingPreviewAdapter($this->_listing, $this->app));
            $this->render(
                'Listings/Single/FreeAdminPreview',
                array_merge(
                    $this->getViewData(),
                    ['listingPreviewAdminBarData'=>$listingPreviewAdminBar->getViewData()]
                )
            );
        }else{
            $this->render('Listings/Single/' . $templateName, $this->getViewData());
        }

    }

    public function previewAction() {
        $id = $this->app->router()->getCurrentRoute()->getParam('id');
        $this->_listing = ListingsModel::findCachedById($id);
        if (AuthAdmin::getUser() !== null) {
            $this->indexAction('adminPreview');
        } else if (AuthBusiness::getUser() !== null && AuthBusiness::getUser()->id == $this->_listing->user_id) {
            $this->indexAction();
        } else {
            $this->app->notFound();
        }
    }

    /**
     * Get related Listings by AJAX
     */
    public function getRelatedAction() {
        $id = $this->app->router()->getCurrentRoute()->getParam('id');
        $this->_listing = ListingsModel::findCachedById($id);
        if (empty($this->_listing)) {
            return;
        }

        $models = $this->getRelatedItemsModels($this->request()->get('items'));

        $this->language('public_listing');

        $searchParams = new RelatedSearchParams(1, 100);
        $searchParams->setParamsFromListing($this->_listing);

        $this->render('Blocks/Listings/Free/RelatedListingsList2018.twig', [
            'results' => $models,
            'searchParams' => $searchParams,
        ]);
    }

    public function getRelatedItems($listing) {
        if (empty($listing) || $listing->featured || !$listing->location || !$listing->category || !$listing->latitude || !$listing->longitude) {
            return [];
        }
        $return = [];

        $relatedService = new Related($this->app);
        $relatedAll = [];

        // Special Offers
        $retSo = $relatedService->searchRelatedSpecialOffers($listing);
        $return['special_offers'] = $retSo->getIds();

        // Related Events
        $retEvents = $relatedService->searchRelatedEvents($listing);
        $return['events'] = $retEvents->getIds();

        // Reviews
        $retReviews = $relatedService->searchRelatedReviews($listing);
        $uniqueListings = [];
        $reviewIds = [];
        $reviewsForRelatedAll = [];
        foreach ($retReviews->getResults() as $review) {
            $listingId = $review->getHit()['_source']['listing_id'];
            if (isset($uniqueListings[$listingId])) {
                continue;
            }
            $uniqueListings[$listingId] = true;
            $reviewIds[] = $review->getHit()['_id'];
            $reviewsForRelatedAll[] = [
                'id' => (int) $review->getHit()['_id'],
                'score' => $review->getHit()['_score'],
                'type' => 'review',
                'listing_id' => $listingId,
            ];
        }
        $return['reviews'] = array_slice($reviewIds, 0, 10);

        // Closest Featured Listings
        $retListings = $relatedService->searchRelatedListings($listing);
        $return['closest_featured'] = $retListings->getIds();

        // Articles
        $retArticles = $relatedService->searchRelatedArticles($listing);
        $return['articles'] = $retArticles->getIds();

        $relatedAll = array_merge($relatedAll, $retSo->getIdsWithScores('special_offer'));
        $relatedAll = array_merge($relatedAll, $retEvents->getIdsWithScores('event'));
        $relatedAll = array_merge($relatedAll, $reviewsForRelatedAll);
        $relatedAll = array_merge($relatedAll, $retListings->getIdsWithScores('listing'));
        $relatedAll = array_merge($relatedAll, $retArticles->getIdsWithScores('article'));


        usort($relatedAll, function($a, $b) {
            if ($a['score'] == $b['score']) {
                return 0;
            } elseif ($a['score'] < $b['score']) {
                return 1;
            } else {
                return -1;
            }
        });

        $return['all'] = $relatedAll;

        return $return;
    }

    /**
     * Get closest Featured by AJAX
     */
    public function getClosestFeaturedAction() {
        $id = $this->app->router()->getCurrentRoute()->getParam('id');

        $this->_listing = ListingsModel::findCachedById($id);
        if (empty($this->_listing)) {
            return;
        }
        $this->language('public_listing');

        $items = $this->app->config('related_algo_closest_featured_listings_slp_items');
        if ($items < 2) {
            return;
        }
        $searchParams = new ElasticaSearchParams(1, $items);
        $searchAlgo = DistanceAlgoSettings::algoForClosestFeaturedLisingsSLP();
        $searchAlgo->setSearchInCategoryPath(false);
        $searchParams->setParamsFromListing($this->_listing);
        $searchParams->setAlgoSettings($searchAlgo);
        $searchParams->setCategory($this->_listing->category);
        $ret = ListingsElastica::searchRelatedFeaturedListings($searchParams, true);
        if ($ret->getCount() < 2) {
            return;
        }
        $this->render('Blocks/Listings/Free/ClosestFeaturedList.twig', [
            'paginatedResults' => $ret,
            'searchParams' => $searchParams,
        ]);
    }

    public function getRelatedArticlesAction() {
        $this->language('public_listing');
        $id = $this->app->router()->getCurrentRoute()->getParam('id');
        $this->_listing = ListingsModel::findCachedById($id);
        if (empty($this->_listing)) {
            return;
        }
        $items = $this->app->config('related_algo_articles_slp_items');
        if ($items < 2) {
            return;
        }
        $searchParams = new ElasticaSearchArticleParams(1, $items);
        $searchParams->setAlgoSettings(new ArticleAlgoSettings());
        $searchParams->setCategory($this->_listing->category);
        $searchParams->setLocation($this->_listing->location);
        $searchParams->setRandomizeResults(true);
        $ret = ArticlesElastica::searchRelatedArticles($searchParams);

        if ($ret->getCount() < 2) {
            return;
        }
        $this->render('Blocks/Listings/Free/RelatedArticlesList.twig', [
            'paginatedResults' => $ret,
            'searchParams' => $searchParams,
        ]);
    }

    public function getRelatedSpecialOffersAction() {
        $this->language('public_listing');
        $id = $this->app->router()->getCurrentRoute()->getParam('id');
        $this->_listing = ListingsModel::findCachedById($id);
        if (empty($this->_listing)) {
            return;
        }

        $items = $this->app->config('related_algo_special_offers_slp_items');
        if ($items < 2) {
            return;
        }

        $searchParams = new ElasticaSearchParams(1, $items);//ElasticaSearchParams or ElasticaSearchSpecialOffersParams
        $searchAlgo = DistanceAlgoSettings::algoForClosestFeaturedLisingsSLP();
        $searchParams->setAlgoSettings($searchAlgo);// newest version
        $searchParams->setParamsFromListing($this->_listing);
        $searchParams->setCategory($this->_listing->category);
        $searchParams->setRandomizeResults(true);
        $ret = EventsElastica::searchRelatedEvents($searchParams, Events::TYPE_SPECIAL_OFFER);// should be searchRelatedSpecialOffers temporary i use searchTop...Vistors

        if ($ret->getCount() < 2) {
            return;
        }
        $this->render('Blocks/Listings/Free/RelatedSpecialOffersList.twig', [
            'paginatedResults' => $ret,
            'searchParams' => $searchParams,
        ]);
    }

    public function getRelatedEventsAction() {
        $this->language('public_listing');
        if ($this->app->config('events_enabled') != 1) {
            return;
        }
        $id = $this->app->router()->getCurrentRoute()->getParam('id');
        $this->_listing = ListingsModel::findCachedById($id);
        if (empty($this->_listing)) {
            return;
        }

        $items = $this->app->config('related_algo_events_slp_items');
        if ($items < 2) {
            return;
        }
        $searchParams = new ElasticaSearchParams(1, $items);
        $searchAlgo = DistanceAlgoSettings::algoForRelatedEventsSLP();

        $searchParams->setParamsFromListing($this->_listing);
        $searchParams->setAlgoSettings($searchAlgo);
        $ret = EventsElastica::searchRelatedEvents($searchParams);
        if ($ret->getCount() < 2) {
            return;
        }
        $this->render('Blocks/Listings/Free/RelatedEventsList.twig', [
            'paginatedResults' => $ret,
            'searchParams' => $searchParams,
        ]);
    }

    public function getMessageBoxAction() {
        $id = $this->app->router()->getCurrentRoute()->getParam('id');

        $this->_listing = ListingsModel::findCachedById($id);
        $this->_listing->loadFromCache('product');

        $view = array(
            'listing' => $this->_listing,
            'upgradeUrl' => $this->app->urlFor('bma.packages', array('set_domain' => true)),
            'editUrl' => $this->app->urlFor('bma.base_url', array('set_domain' => true))
        );

        $this->render('Blocks/Listings/Default/MessageBox.twig', $view);
    }

    private function checkRedirect() {
        if (is_null($this->_listing) && empty($this->app->router()->getCurrentRoute()->getParam('id', null))) {
            $this->app->notFound();
        }

        if (is_null($this->_listing) && !empty($this->app->router()->getCurrentRoute()->getParam('id', null))) {
            $this->app->redirect("/", 301);
        }

        $friendly_url = $this->app->router()->getCurrentRoute()->getParam('friendly_url');
        if ($this->_listing->friendly_url != $friendly_url) {
            $this->app->redirect($this->app->urlFor('page.listing_page', array('friendly_url' => $this->_listing->friendly_url, 'id' => $this->_listing->id)), 301);
        }

        if ($this->_listing->status == ListingsModel::STATUS_DELETED) {
            $this->app->redirect("/", 301);
        }

        if ($this->_listing->status == ListingsModel::STATUS_ACTIVE and $this->_listing->locations()->count()==0 and $this->_listing->location()->count()==0) {
            $sentry = $this->app->sentry;
            $sentry->captureMessage('Listing has no location');
            $this->app->notFound();
        }
    }

    /**
     * @return string
     */
    private function getTemplateName() {
        //check preview page.listing_page_preview
        if ($this->app->router()->getCurrentRoute()->getName() == 'page.listing_page') {
            if ($this->_listing->status == ListingsModel::STATUS_PENDING || $this->_listing->status_visible == ListingsModel::NOT_VISIBLE) {
                return self::TEMPLATE_NAME_PENDING;
            }
        }

        if ($this->_listing->featured == ListingsModel::FEATURED) {
            return self::TEMPLATE_NAME_FEATURED;
        } else {
            return self::TEMPLATE_NAME_FREE;
        }
    }

    /**
     * @author Mbojkow
     * @param $listing
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    private function getAuctionsCategories($listing, $limit=3)
    {
        $tmp = new \Illuminate\Support\Collection;
        $added_categories = array();

        if ($listing->category->hasAuctionContact() or $listing->category->hasAuctionBooking())
        {
                $bodyText = new \Lib\ResultPages\BodyText\BodyText();
                if ($listing->category)
                    $bodyText->setCategory($listing->category->id);
                if ($listing->location)
                    $bodyText->setLocation($listing->location->id);
                $listing->category['auctionContactHeader'] = $bodyText->getHeaderAuctions(\Lib\ResultPages\BodyText\BodyText::HEADER_AUCTIONS_TYPE_CONTACT);
                $listing->category['auctionBookingHeader'] = $bodyText->getHeaderAuctions(\Lib\ResultPages\BodyText\BodyText::HEADER_AUCTIONS_TYPE_BOOKING);

                $tmp[] = $listing->category;
                $added_categories[] = $listing->category->id;
        }

        foreach ($listing->categories as $category) {
            if (($category->hasAuctionContact() or $category->hasAuctionBooking()) and !in_array($category->id, $added_categories))
            {
                $bodyText = new \Lib\ResultPages\BodyText\BodyText();
                $bodyText->setCategory($category->id);
                if ($listing->location)
                    $bodyText->setLocation($listing->location->id);
                $category['auctionContactHeader'] = $bodyText->getHeaderAuctions(\Lib\ResultPages\BodyText\BodyText::HEADER_AUCTIONS_TYPE_CONTACT);
                $category['auctionBookingHeader'] = $bodyText->getHeaderAuctions(\Lib\ResultPages\BodyText\BodyText::HEADER_AUCTIONS_TYPE_BOOKING);

                $tmp[] = $category;
                $added_categories[] = $category->id;
            }
        }

        return $tmp->take($limit);
    }

    /**
     * @author Mbojkow
     * @param $listing
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    private function getPopularSearches($listing, $limit=5)
    {
        if ($this->app->config('enable_popular_searches') == 0
            || $listing->featured
            || empty($listing->categories) || empty($listing->location) || empty($listing->location->latitude) || empty($listing->location->longitude)
        ) {
            return null;
        }

        $results = \Orm\Dao\Listings\PopularSearches::getPopularSearchesFromListing($listing, 0, $limit, $this->app->config('popular_searches_distance'));
        return $results;
    }

    private function getViewData() {
        $listingsLogoDao = new ListingsLogo();
        $config = $this->app->getConfig();
        if ($this->app->config('hide_category_ideal_for') == 1) {
            $virtualCategories = [];
        } else {
            $categoriesIds = $this->_listing->categories->lists('id')->all();
            $virtualCategories = \Orm\Dao\Categories\Categories::getTopVirtualCategoriesConnectedFromCache($categoriesIds);
        }

        $bodyText = new \Lib\ResultPages\BodyText\BodyText();


        $associations = AssociationsDao::getAssociationChildrenFromListingID($this->_listing->id);
        $associationsMemberships = AssociationsDao::getAssociationsForListingFromCache($this->_listing->id);
        $viewData = array(
            'meta_title' => Meta::getTitle($this->_listing),
            'meta_description' => Meta::getDescription($this->_listing),
            'currentPage' => array('ga_segment' => 'listing_free'),
            'breadcrumb' => $this->getBreadcrumb(),
            'listing' => $this->_listing,
            'auctionsCategories' => $this->getAuctionsCategories($this->_listing),
            'popularSearches' => $this->getPopularSearches($this->_listing),
            'related_items' => $this->getRelatedItems($this->_listing),
            'eventsLive' => $this->getEventsLive(),
            'facebookFeed' => ListingsDao::getFacebookFeed($this->_listing),
            'twitterFeed' => ListingsDao::getTwitterFeed($this->_listing),
            'customFields' => FieldsDao::getFieldsByGroup(FieldsGroupsModel::GROUP_LISTINGS),
            'customFieldsEvents' => (true == $config::getDisableEvents()) ? array() : FieldsDao::getFieldsByGroup(FieldsGroupsModel::GROUP_EVENTS),
            'facebookId' => $this->app->config('facebook_api_app_id'),
            'reviewsPerPage' => self::REVIEWS_PER_PAGE,
            'reviewsPage' => 2,//in JS we must show page > 1
            'reviewsTotalPages' => '',
            'reviewsLoadMoreUrl' => '',
            'activationCookieStorage' => '',
            'wideImage' => false,
            'websiteScreenshot' => $listingsLogoDao->getWebsiteScreenshotUrl($this->_listing->id),
            'specialOffersLive' => $this->getSpecialOffersLive(),
            // OLD ? 'specialOffersLive' => \Orm\Dao\Listings\SpecialOffers\SpecialOffers::getLiveSpecialOffersForListingFromCache($this->_listing->id),
            'virtualCategories' => $virtualCategories,
            'bodyTextBottom' => $bodyText->getListingBottomSection(),
            'locationId' => $this->_listing->location_id,
            'categoryId' => $this->_listing->primary_category_id,
            'auctionsSource' => \Orm\Model\Auctions\Auctions::SOURCE_TYPE_LISTING_PAGE,
            'auctionsBookingEnabled' => $this->_listing->hasAuctionEnabledForType(\Orm\Model\Fields\Categories::TYPE_BOOKING),
            'auctionsContactEnabled' => $this->_listing->hasAuctionEnabledForType(\Orm\Model\Fields\Categories::TYPE_CONTACT),
            'contractorsEnabled' => $this->app->config('show_contractors'),
            'isListingOwner' => (AuthBusiness::getUser() !== null && AuthBusiness::getUser()->id == $this->_listing->user_id), // mb - as KF
            'associations'=> $associations,
            'associationsMemberships'=> $associationsMemberships,
            'tagGroups' => ListingsTagsDecorator::getListingTagsForFrontend($this->_listing)
        );

        if ($this->_listing->claimed == ListingsModel::NOT_CLAIMED) {
            $viewData['meta_noindex'] = true;
        }

        if ($this->_listing->reviews_count > 0) {
            $viewData['reviewsTotalPages'] = ceil($this->_listing->reviews_count / self::REVIEWS_PER_PAGE);
            $viewData['reviewsLoadMoreUrl'] = $this->_listing->getLoadMoreReviewUrl();
            $viewData['reviews'] = ReviewsDao::getLast10ActiveReviewsFromCache($this->_listing, self::REVIEWS_PER_PAGE);
        }

        if ($this->_listing->featured == ListingsModel::FEATURED) {
            if ($this->_listing->pictures_count > 0) {
                $wideImage = ListingsImages::getWideImage($this->_listing);
                $viewData['wideImage'] = $wideImage;
            }
            $viewData['currentPage'] = array('ga_segment' => 'listing_featured');
        } else {
            $viewData['activationCookieStorage'] = IntroductionMailCookie::getCookieName();
        }
        $viewData['mostPopularRankings'] = [];
        $years = MostPopular::getMostPopularYearsOnlyOnMode();

        foreach ($years as $year) {
            $yearRanks = MostPopularDao::getBestRankingsForListingFromCache($this->_listing, $year, 15);
            if (true === $yearRanks->isEmpty()) {
                continue;
            }
            $viewData['mostPopularRankings'][$year] = $yearRanks->each(function($rank) {
                $rank->loadFromCache('location');
            });
            $viewData['mostPopularRankings'][$year] = $yearRanks;
        }

        if (count($years) >= 3) {
            $viewData['mostPopularRankingsRhs'] = array_slice($viewData['mostPopularRankings'], 0, 3, true);
        } else {
            $viewData['mostPopularRankingsRhs'] = $viewData['mostPopularRankings'];
        }

        $viewData['articles'] = [];
        if ($this->_listing->articles_limit > 0) {
            $this->_listing->loadFromCache('articles');
            $viewData['articles'] = $this->_listing->articles->filter(function($article) {
                    if ($article->active == \Orm\Model\Articles\Articles::STATUS_ACTIVE) {
                        return true;
                    }
                    return false;
                })->sortByDesc('id');
        }

        return $viewData;
    }

    private function getEventsLive() {

        $eventsLive = array();

        if ($this->app->config('events_enabled') && $this->_listing->events_count > 0) {
            $eventsLive = EventsDao::getLiveEventsForListingFromCache($this->_listing->id, Events::TYPE_DEFAULT);
            $eventsLive->each(function($event) {
                $event->loadFromCache('images');
                $event->loadFromCache('fields');
            });
        }
        return $eventsLive;
    }

    private function getSpecialOffersLive() {
        $eventsLive = array();
        $config = $this->app->getConfig();

        if ($this->app->config('events_so_enabled')) {
            $eventsLive = EventsDao::getLiveEventsForListingFromCache($this->_listing->id, Events::TYPE_SPECIAL_OFFER);
            $eventsLive->each(function($event) {
                $event->loadFromCache('images');
                $event->loadFromCache('fields');
            });
        }
        return $eventsLive;
    }

    /**
     * Get breadcrumb
     * @return array
     */
    private function getBreadcrumb() {
        $breadCrumb = array();
        $breadCrumb[] = array('link' => $this->app->urlFor('page.search_category_empty'), 'text' => 'Browse Categories');

        if (!empty($this->_listing->category)) {
            $pathCategory = $this->_listing->category->getPath();
            foreach ($pathCategory as $category) {
                $breadCrumb[] = array('link' => $category->getUrlRp(), 'text' => $category->getNavigationName());
            }
        } else {
            $category = null;
        }
        if (!empty($this->_listing->location)) {
            $pathLocation = $this->_listing->location->getPath();

            foreach ($pathLocation as $location) {
                $breadCrumb[] = array('link' => $location->getUrlRp($category), 'text' => $location->getNavigationName());
            }
        }
        $breadCrumb[] = array('text' => $this->_listing->title);
        return $breadCrumb;
    }

    protected function getRelatedItemsModels($getQuery) {
        $items = [];
        $itemsById = [];
        $return = [];
        $parts = explode(',', $getQuery);
        $orderedParts = array_filter($parts);
        foreach ($orderedParts as $part) {
            list($type, $id) = explode(':', $part);
            if (!isset($items[$type])) {
                $items[$type] = [];
            }
            $items[$type][] = $id;
        }

        foreach ($items as $type => $ids) {
            $results = [];
            switch ($type) {
                case "listing":
                    $results = ListingsModel::findCachedByIds($ids);
                    break;
                case "article":
                    $results = Articles::findCachedByIds($ids);
                    break;
                case "special_offer":
                case "event":
                    $results = Events::findCachedByIds($ids);
                    break;
                case "review":
                    $results = Reviews::findCachedByIds($ids);
                    break;
            }
            $resultsByIds = [];
            foreach ($results as $result) {
                $resultsByIds[$result->id] = $result;
            }
            $itemsById[$type] = $resultsByIds;
        }

        foreach ($orderedParts as $part) {
            list($type, $id) = explode(':', $part);

            if (isset($itemsById[$type][$id])) {
                $return[] = $itemsById[$type][$id];
            }
        }

        return $return;
    }

}
