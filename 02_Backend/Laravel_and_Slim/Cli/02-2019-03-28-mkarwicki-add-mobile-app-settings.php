<?php

chdir(__DIR__ . '/../');
include ('./cli-functions.php');



$settingsTitle='mobile_App';
$settings= [
    [
        'cp_title'=>'Mobile app',
        'slug'=> 'mobile_app_enabled',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Show mobile app modal (BMA)',
        'slug'=> 'mobile_app_modal_enabled',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Show mobile app modal only on BMA members area (BMA)',
        'slug'=> 'mobile_app_only_bma',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Show iOS Safari Smart App Banner (BMA)',
        'slug'=> 'mobile_app_ios_smart_banner',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Show iOS Safari Smart App Banner only on dashboard index (BMA)',
        'slug'=> 'mobile_app_ios_smart_banner_dashboard',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Show links to download app in top header (BMA)',
        'slug'=> 'mobile_app_show_in_bma_header',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Show links to download app in footer (PAGE)',
        'slug'=> 'mobile_app_show_in_page_footer',
        'value'=>false,
        'type'=>'checkbox'
    ],
    [
        'cp_title'=>'Apple Store App path',
        'slug'=> 'mobile_app_ios_link',
        'value'=>false,
        'type'=>'text'
    ],
    [
        'cp_title'=>'Apple Store App ID',
        'slug'=> 'mobile_app_ios_app_id',
        'value'=>false,
        'type'=>'text'
    ],
    [
        'cp_title'=>'Google Play App path',
        'slug'=> 'mobile_app_android_link',
        'value'=>false,
        'type'=>'text'
    ],
];
foreach($settings as $key=>$setting):
    add_settings($settingsTitle, $setting['slug'], $setting['value'], $setting['type']);
    set_phrase($setting['cp_title'], 'setting_'.$setting['slug'].'_title', 'settings');
endforeach;
add_settings('packages', 'packages_mobile_app_section', false, 'checkbox');
add_phrase('Show mobile app section after Featured Listings Results', 'setting_packages_mobile_app_section_title', 'settings');

