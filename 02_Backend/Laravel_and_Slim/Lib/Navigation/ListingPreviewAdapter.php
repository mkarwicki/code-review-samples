<?php

namespace Lib\Navigation\AdminBar\Adapter;


use Orm\Model\Listings\Listings;

/**
 * Created by PhpStorm.
 * User: michal
 * Date: 07/11/2018
 * Time: 08:49
 */

use Apps\Apps;
use Orm\Model\Users\Users;
use Orm\Dao\Listings\Duplicates\Results;
use Orm\Dao\Listings\Duplicates\Groups as GroupsDao;
use Orm\Model\Listings\Duplicates\GroupsResults as GroupsResultsModel;
use Orm\Model\Listings\Duplicates\Groups as GroupsModel;

/**
 * Class ListingPreviewAdapter
 * @package Lib\Navigation\AdminBar\Adapter
 */
class ListingPreviewAdapter  implements AdapterInterface
{
    private $listing;
    private $app;
    private $redirectStatus;

    /**
     * ListingPreviewAdapter constructor.
     * @param $listing
     * @param $app
     */
    public function __construct(Listings $listing, Apps $app)
    {
        $this->listing = $listing;
        $this->app = $app;
        $this->redirectStatus=$this->getRedirectStatus();

    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function getViewData()
    {
        $data=collect(
            [
                'mainActions' => $this->getMainActions(),
                'infoTable' => $this->getListingInfoTable(),
                'rightActions'=> $this->getRightActions(),
                'redirectStatus' => $this->redirectStatus
            ]
        );
        return $data;
    }


    /**
     * Main action buttons on left side of the bar
     */
    function getMainActions(){
        $step = $this->getStep($this->listing->user_id);
        $data=[];

        /**
         * Hide buttons when listing is not on steps
         */
        if(isset($step) && $step->date_end != '0000-00-00 00:00:00')
        {
            /**
             * APPROVE AND GO TO NEXT MATCHING LISTING IF EXISTS
             */
            if($this->listing->order->status != \Orm\Model\Orders\Orders::STATUS_ACTIVE || $this->listing->status != \Orm\Model\Listings\Listings::STATUS_ACTIVE)
            {
                $data[]=[
                    /** Approve and NEXT */
                    'title'     =>   $this->app->lang->translate('admin_bar_approve_and_next'),
                    'cplink'    =>   $this->app->urlFor('page.orders_express', ['id' => $this->listing->order->id, 'status' => \Orm\Model\Orders\Orders::STATUS_ACTIVE,'set_domain'=>true]),
                    'class'     =>   'btn btn-default btn-approve action-btn',
                ];
            }
            /**
             * REJECT AND GO TO NEXT MATCHING LISTING IF EXISTS
             */
            if($this->listing->featured == \Orm\Model\Listings\Listings::NOT_FEATURED){
                if($this->listing->order->status != \Orm\Model\Orders\Orders::STATUS_CANCELED || $this->listing->status != \Orm\Model\Listings\Listings::STATUS_DELETED)
                {
                    $data[]=[
                        /** REJECT and NEXT */
                        'title'     => $this->app->lang->translate('admin_bar_reject_and_next'),
                        'cplink'    =>  $this->app->urlFor('page.orders_express', ['id' => $this->listing->order->id, 'status' => \Orm\Model\Orders\Orders::STATUS_CANCELED,'set_domain'=>true]),
                        'class'     =>  'btn btn-default btn-reject action-btn',
                    ];
                }
            }
            return $data;
        }
    }



    /**
     * Returns table with listing info
     *
     * @return array
     */
    public function getListingInfoTable(){
        return [
            /** STATUS **/
            [
                'title' => $this->app->lang->translate('admin_bar_status'),
                'value' => $this->listing->status,
                'link'  => false
            ],
            /** Duplicates **/
            [
                'title' => $this->app->lang->translate('admin_bar_duplicates'),
                'value' => $this->getDuplicatesInfo()['title'],
                'link'  => $this->getDuplicatesInfo()['link']
            ],
            /** Last updated **/
            [
                'title' => $this->app->lang->translate('admin_bar_last_updated'),
                'value' => \Lib\Dates::formatToTpl($this->listing->date_update),
                'link'  => $this->app->urlFor('cp.listings_changelog', ['set_domain'=>true]).'?filter[listing_id]='.$this->listing->getId(),
                /** CP changelog of this listing **/
                'linkTitle'=>$this->app->lang->translate('admin_bar_cp_changelog'),
                'target'=>'_blank'
            ],
            /** Claimed **/
            [
                'title' => $this->app->lang->translate('admin_bar_claimed'),
                'value' => $this->alternateValue($this->listing->claimed),
                'link'  => false
            ],
            /** User **/
            [
                'title' => $this->app->lang->translate('admin_bar_user'),
                'value' => $this->listing->user->user_email.' ('.$this->listing->user_id.')',
                'link'  => $this->app->urlFor('cp.users_edit', ['id' => $this->listing->user_id, 'set_domain'=>true]),
                /** CP Edit User Page **/
                'linkTitle'=>$this->app->lang->translate('admin_bar_user_cp_edit_user_page'),
                'target'=>'_blank'
            ],
            /** User Country **/
            [
                'title' => $this->app->lang->translate('admin_bar_user_country'),
                'value' => $this->getUserCountryInfo($this->listing->user),
                'link'  => false
            ],
            /** Remaining listings **/
            [
                'title' => $this->app->lang->translate('admin_bar_remaining_listings'),
                'value' => $this->redirectStatus['count'],
                'link'  => false
            ],

        ];
    }


    /**
     * Right side of the admin bar
     *
     * @return array
     */
    public function getRightActions(){
        return [
            /** EDIT IN CP **/
            [
                'title'  => $this->app->lang->translate('admin_bar_edit_in_cp'),
                'link'   => $this->app->urlFor('cp.listings_edit', ['id' => $this->listing->getId(), 'set_domain'=>true]),
                'class'  => 'btn btn-default btn-edit',
                'target' => '_blank'
            ]
        ];
    }


    /**
     * getNextPendingFreeSubmitedCompletedListingOrderByIdDescOrToControlPanel
     *
     * Get next pending free submited completed listing
     * order by id descending or if such listing does not
     * exists go to control panel with specific search
     *
     * @return array
     */
    private function getRedirectStatus()
    {
        $queryBuilder =
            Listings::orderBy('id', 'desc')
                ->where('id', '!=', $this->listing->getId())
                ->where('product_id', '=', 1)
                ->where('location_id', '!=', 0)
                ->where('claimed', '=', Listings::CLAIMED)
                ->where('status', '=', Listings::STATUS_PENDING)
                ->whereHas('user.step', function($subQuery) {
                    $subQuery->where(T_LISTINGS_STEPS . '.date_end', '!=', '0000-00-00 00:00:00');
                });
        ;
        $count = $queryBuilder->count();
        /* @var $nextListing \Orm\Model\Listings\Listings */
        $nextListing=$queryBuilder->first();
        if($count>0){
            return
                [
                    'count'=>$count+1, // AS WE EXCLUDE CURRENT LISTING FROM LIST WE ADD +1
                    'redirectLink'=>$nextListing->getPreviewUrl()
                ];
        }
        return
            [
                'count'=>1,
                'redirectLink'=>    $this->app->urlFor('cp.listings_search',
                        ['set_domain' => true])
                    . '?formListingsFilter[product][product]=1&formListingsFilter[claimed]=1&formListingsFilter[listing_status]=pending&formListingsFilter[submit_completed]=1&formListingsFilter[mobile_type][]=fixed&formListingsFilter[mobile_type][]=mobile&formListingsFilter[mobile_type][]=online'
            ]
            ;
    }




    /**
     *
     * @param int $userId
     * @return \Orm\Model\Listings\Steps
     */
    private function getStep($userId) {
        return \Orm\Dao\Listings\Steps::getUserSteps($userId);
    }



    /**
     * @param $val
     * @return string
     */
    public function alternateValue($val){
        if($val){
            return '<span class="yes">'.$this->app->lang->translate('admin_bar_yes').'</span>'; /** YES */
        }else{
            return '<span class="no">'.$this->app->lang->translate('admin_bar_no').'</span>'; /** NO */
        }
    }


    /**
     * @param Users $user
     * @return string
     */
    public function getUserCountryInfo(Users $user){
        $out='';
        if($this->listing->user->logged_country){
            $out.=\Iso3166\Codes::map(strtoupper($user->logged_country));
            if($user->getCountryFlag()){
                $out.='<span class="country-flag" style="background-image:url('.$user->getCountryFlag().')"></span>';
            }
            return $out;
        }
        return '-';
    }


    /**
     * @return array
     */
    public function getDuplicatesInfo(){
        $model=$this->listing;
        $result = Results::getListingCurrentDuplicatesResult($model);
        if (!$result) {
            $dateUpdate = new \DateTime($model->date_update);
            $dateFull = GroupsDao::getLastRunDate();
            $dateFull = !empty($dateFull) ? new \DateTime($dateFull) : null;
            $dateImmediate = !empty($model->date_duplicate_finder) ? new \DateTime($model->date_duplicate_finder) : null;
            $dateRun = null;

            if ($dateFull) {
                if ($dateImmediate &&
                    $dateImmediate > $dateFull
                ) {
                    $dateRun = $dateImmediate;
                } else {
                    $dateRun = $dateFull;
                }
            } elseif ($dateImmediate) {
                $dateRun = $dateImmediate;
            }

            if (!$dateRun) {
                /** In progress */
               return ['title'=>$this->app->lang->translate('admin_bar_in_progress'),'link'=>false];
            }
            if ($dateRun < $dateUpdate) {
                /** In progress */
               return ['title'=>$this->app->lang->translate('admin_bar_in_progress'),'link'=>false];
            }
            return [
                /** Checked */
                'title'=>$this->app->lang->translate('admin_bar_checked'),'link'=>false
            ];
        }

        switch ($result->status) {
            case GroupsResultsModel::STATUS_PENDING:
                $resolveRoute = $result->group->search_type === GroupsModel::SEARCH_TYPE_FULL
                    ? 'cp.listings_duplicates_resolve_full'
                    : 'cp.listings_duplicates_resolve_immediate';
                /** Just number */
                return [
                    'title' => count($result->group->results),
                    'link' => $this->app->urlFor($resolveRoute, ['group_id' => $result->group->id, 'set_domain'=>true]),
                ];
                break;
            case GroupsResultsModel::STATUS_RESOLVED_NOT_DUPLICATE:
                /** Checked */
                return
                    [
                        'title'=>$this->app->lang->translate('admin_bar_checked'),'link'=>false
                    ] ;
                break;
            case GroupsResultsModel::STATUS_RESOLVED_DUPLICATE:
                /** Duplicate */
                return ['title'=>$this->app->lang->translate('admin_bar_duplicate'),'link'=>false];
                break;
        }

        return  [
            'title'=>'-','link'=>false
        ] ;

    }







}