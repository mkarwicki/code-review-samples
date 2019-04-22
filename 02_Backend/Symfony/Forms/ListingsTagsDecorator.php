<?php

namespace Apps\Bma\Form\Listings\FormDecorator;


use Symfony\Component\Form\FormBuilderInterface as Form;

use Symfony\Component\Form\Extension\Core\Type\TextType,
    Symfony\Component\Form\Extension\Core\Type\TextareaType,
    Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Orm\Model\Tags\TagsGroups as TagsGroups;
use Orm\Model\Listings\Listings as ListingsModel;
use Orm\Model\Tags\Tags;
use Orm\Model\Suggestions\Suggestions as SuggestionsModel;

/**
 * Class ListingsTagsDecorator
 * @package Apps\Bma\Form\Listings\FormDecorator
 */
class ListingsTagsDecorator extends FormDecorator {

    /**
     * @return Form
     */
    public function manipulateForm(){
        foreach(self::getListingTagsSettings() as $key=>$tag){
            $this->form->add($tag['slug'].'tag', TextType::class, array(
                'label' =>  false,
                'attr' => array(
                    'placeholder' => $tag['add_placeholder'] //bma_general_add_qualifications
                ),
                'required' => false,

            ));
            $this->form->add($tag['slug'].'tagValues', HiddenType::class, array(
                'label' =>  false,
                'attr' => array(
                    'id' => $tag['slug'].'tagValues'
                ),
                'required' => false,
            ));
            $this->form->add($tag['slug'].'tagMissing', TextareaType::class, array(
                'label' =>  false,
                'attr' => array(
                    'placeholder' =>$tag['missing_placeholder']
                ),
                'required' => false,
            ));
        }
        return $this->form;
    }

    /**
     * CONFIGURATION OF TAGS FOR LISTINGS (form, form controller and view)
     * To add new one - Just prepare phrases and group in tags groups.
     *
     * If listing added as parameter -> also gets its tags group data
     *
     * @param ListingsModel|null $listing
     * @return array
     */
    public static function getListingTagsSettings(ListingsModel $listing = null){
        $app = \Apps\Apps::getInstance();
        $tags=[];
        //QUALIFICATIONS
        $slug = TagsGroups::GROUP_QUALIFICATIONS_SLUG;
        $groupID = \Orm\Dao\Tags\TagsGroups::getTagGroupIdBySlug($slug);
        if($groupID){
            $tags[$slug]=[
                'title' => $app->lang->translate('bma_general_qualifications'),
                'publicTitle' => $app->lang->translate('public_qualifications_qualifications'),
                'slug' => $slug,
                'groupID' => $groupID,
                'suggestionType' => SuggestionsModel::TYPE_QUALIFICATION,
                'left_title' => $app->lang->translate('bma_general_left_qualifications'),
                'add_placeholder' => $app->lang->translate('bma_general_search_qualifications'),
                'missing_placeholder'=>$app->lang->translate('bma_general_missing_qualification'),
                'cant_find'=>$app->lang->translate('bma_general_cant_find_qualification'),
                'additional_information_optional'=>$app->lang->translate('bma_general_additional_information_optional'),
                'tag_limit'=> 20,
            ];
            if($listing){
                $tags[$slug]['list'] = \Orm\Dao\Tags\Tags::getTagsByGroupListingId($groupID, $listing->id);
            }
        }
        //CERTIFICATIONS
        $slug=TagsGroups::GROUP_CERTIFICATIONS_SLUG;
        $groupID = \Orm\Dao\Tags\TagsGroups::getTagGroupIdBySlug($slug);
        if($groupID){
            $tags[$slug]=[
                'title' => $app->lang->translate('bma_general_certifications'),
                'publicTitle' => $app->lang->translate('public_certifications_certifications'),
                'slug'=>$slug,
                'groupID'=>$groupID,
                'suggestionType' => SuggestionsModel::TYPE_CERTIFICATION,
                'left_title' => $app->lang->translate('bma_general_left_certifications'),
                'add_placeholder'=>$app->lang->translate('bma_general_search_certifications'),
                'missing_placeholder'=>$app->lang->translate('bma_general_missing_certifications'),
                'cant_find'=>$app->lang->translate('bma_general_cant_find_certification'),
                'additional_information_optional'=>$app->lang->translate('bma_general_additional_information_optional'),
                'tag_limit'=> 20,
            ];
            if($listing){
                $tags[$slug]['list'] = \Orm\Dao\Tags\Tags::getTagsByGroupListingId($groupID, $listing->id);
            }
        }
        return $tags;
    }

    /**
     * Do not return group data if there are't any
     * tags in group
     *
     * @param ListingsModel $listing
     * @return array
     */
    public static function getListingTagsForFrontend(ListingsModel $listing){
        $tagGroups = self::getListingTagsSettings($listing);
        $data = [];
        foreach ($tagGroups as $tagGroup) {
            if (count($tagGroup['list']) > 0) {
                $tagGroup['title'] = $tagGroup['publicTitle'];
                $data[] = $tagGroup;
            }
        }
       return $data;
    }

    /**
     * Updates listing tags
     * and adds suggestion for tag
     * if user filed textarea with suggestion
     *
     * @param ListingsModel $listing
     * @param $data
     */
    public static function updateListingTags(ListingsModel $listing, $data){
        $app = \Apps\Apps::getInstance();
        $tagGroups=self::getListingTagsSettings();
        $tags=[];
        foreach($tagGroups as $key=>$group){
            if(isset($data[$group['slug'].'tagValues'])){
                $currentGroupTagValues = json_decode($data[$group['slug'].'tagValues']);
                $tags=array_merge($tags,$currentGroupTagValues);
            }
            $currentGroupTagMissing = $data[$group['slug'].'tagMissing'];
            /** ADD SUGGESTION  */
            if(strlen($currentGroupTagMissing)>0){
                $msg=htmlentities($currentGroupTagMissing);
                SuggestionsModel::create([
                    'listing_id' => $listing->id,
                    'user_id' => $listing->user->id,
                    'content' => $msg,
                    'type' => $group['suggestionType'],
                    'status' => 0,
                    'updated_at' => \Lib\Dates::nowDBTime(),
                    'created_at' => \Lib\Dates::nowDBTime()
                ]);
            }
        }

         /* ADD ADDITIONAL INFORMATION TO TAG AND PREPARE DATA */
        $data = [];
        foreach($tags as $key=>$tagID){
            $data[] = [
                'tag_id'=>$tagID,
                'details'=> htmlentities($app->request()->post('tag-' . $tagID))
            ];
        }
        /*SNC LISTING*/
        $listing->tags()->sync($data);

        // clear cache
        $groups = [
            \Orm\Dao\Tags\TagsGroups::getBySlug(\Orm\Model\Tags\TagsGroups::GROUP_QUALIFICATIONS_SLUG)->id,
            \Orm\Dao\Tags\TagsGroups::getBySlug(\Orm\Model\Tags\TagsGroups::GROUP_CERTIFICATIONS_SLUG)->id,
        ];

        foreach($groups as $key=>$groupId) {
            $listing->getMemcache()->remove(\Orm\Dao\Tags\Tags::getTagsByGroupListingCacheKey($groupId, $listing->id));
        }
    }

    /**
     * Checks if Tags group id is
     * linked with listings tags groups.
     *
     * @param $groupID
     * @return bool
     */
    public static function isTagGroupInListingsTagsGroup($groupID){
        $tagGroups=self::getListingTagsSettings();
        foreach($tagGroups as $key=>$group){
            if($group['groupID'] == $groupID){
                return true;
            }
        }
        return false;
    }


    /**
     * Gets listing tags for a specific group
     *
     * @param ListingsModel $listing
     * @param $groupID
     * @return \Illuminate\Support\Collection
     */
    public static function getListingTagGroupTags(ListingsModel $listing, $groupID){
        $listing->loadFromCache('tagsRelations');
        $data = [];

        $tagsRelations = $listing->tagsRelations;

        foreach($tagsRelations as $key=>$tagRelation){
            $tagRelation->loadFromCache('tag');
            $tag = $tagRelation->tag;
            $tag->details = $tagRelation->details;

            if($tag->group_id ==$groupID){
                $data[]=$tag;
            }
        }
        $collection=collect($data);
        return $collection->sortBy('name');
    }
}