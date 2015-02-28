<?php

/**
 *
 */
class SPVIDEOLITE_CLASS_EventHandler
{
    protected static $jsIncluded = false;
    
    public static function requireSpvideoJs() {
        if (!self::$jsIncluded) {
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/spvideo.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            self::$jsIncluded = true;
        }
    }

    public function integrateForum() {
        if (!SPVIDEOLITE_BOL_Service::isRoute('FORUM_CTRL_Topic') 
            && !SPVIDEOLITE_BOL_Service::isRoute('FORUM_CTRL_AddTopic')
            && !SPVIDEOLITE_BOL_Service::isRoute('FORUM_CTRL_EditPost')
            && !SPVIDEOLITE_BOL_Service::isRoute('FORUM_CTRL_EditTopic')
            ) return;

        self::requireSpvideoJs();

        OW::getDocument()->addStyleDeclaration(".floatbox_container .floatbox_body textarea[name=code] {display:none}");

        OW::getDocument()->addOnloadScript("
            SPVideo.baseUrl = '".OW::getRouter()->urlForRoute('spvideolite.base')."';
            SPVideo.loadingElement = $('#checkClip_loading');
            SPVideo.detailElement = $('#checkClip_resp_place_holder');
            SPVideo.checkClipUrl = SPVideo.baseUrl + '/ajax_get_clip';
            SPVideo.connectHtmlArea();
        ");

    }

    public function integrateBlog() {
        if (!SPVIDEOLITE_BOL_Service::isRoute('BLOGS_CTRL_Save') 
            && !SPVIDEOLITE_BOL_Service::isRoute('BLOGS_CTRL_Blog')
            ) return;

        self::requireSpvideoJs();

        OW::getDocument()->addStyleDeclaration(".floatbox_container .floatbox_body textarea[name=code] {display:none}");

        OW::getDocument()->addOnloadScript("
            SPVideo.baseUrl = '".OW::getRouter()->urlForRoute('spvideolite.base')."';
            SPVideo.loadingElement = $('#checkClip_loading');
            SPVideo.detailElement = $('#checkClip_resp_place_holder');
            SPVideo.checkClipUrl = SPVideo.baseUrl + '/ajax_get_clip';
            SPVideo.connectHtmlArea();
        ");
    }
    
    /**
     *
     */
    public function replaceVideoAddView($event) {
        if (!SPVIDEOLITE_BOL_Service::isRoute('VIDEO_CTRL_Add')) return;
        if (OW::getRequest()->isPost()) return;
        if (OW::getRequest()->isAjax()) return;
        if (!OW::getUser()->isAuthenticated()) return;
        
        $language = OW::getLanguage();
        $doc = OW::getDocument();
        
        // add scripts
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/base64.js');
        self::requireSpvideoJs();
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/jquery.easing.min.js');
        
        // add stylesheets
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticCssUrl() . 'spvideo.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
        
        // inject & modify the adding form
        $embedForm = $doc->getBody();
        $matches = array();
        preg_match_all("/<form.*<\/form>/is", $embedForm, $matches);
        if (count($matches[0]) > 0) $embedForm = $matches[0][0];
        $spVideoCtrl = new SPVIDEOLITE_CTRL_Add();
        $spVideoCtrl->setTemplate(OW::getPluginManager()->getPlugin('spvideolite')->getCtrlViewDir() . 'add_index.html');
        $spVideoCtrl->setEmbedForm($embedForm);

        $language->addKeyForJs('spvideolite', 'tab_from_link');
        $language->addKeyForJs('spvideolite', 'tab_embed_code');
        
        $spVideoCtrl->index();

        // commit body changes
        $doc->setBody($spVideoCtrl->render());
    }
    
    function addCategoriesList($event) {
        if (!SPVIDEOLITE_BOL_Service::isRoute('VIDEO_CTRL_Video', 'viewList') && !SPVIDEOLITE_BOL_Service::isRoute('VIDEO_CTRL_Video', 'viewTaggedList')) return;
        
        self::requireSpvideoJs();
        OW::getDocument()->addOnloadScript("SPVideo.addCategoriesList('" . OW::getRouter()->urlForRoute('spvideolite.categories') . "')");
    }
    
    /**
     *
     */
    function on_add_console_item(BASE_CLASS_EventCollector $event) {
        $event->add(array('label' => 'Video Manager', 'url' => OW_Router::getInstance()->urlForRoute('spvideolite.my_video')));
    }
    
    /**
     * ============= TWEAKS =============
     */
    
    /**
     *
     */
    public static function showLessVideoDescription(BASE_CLASS_EventCollector $event) {
        $language = OW::getLanguage();
        self::requireSpvideoJs();
        OW::getDocument()->addOnloadScript("SPVideo.showLessDescription();");
        $language->addKeyForJs('spvideolite', 'btn_show_more');
        $language->addKeyForJs('spvideolite', 'btn_show_less');
    }
    
    public function fixLongTitles() {
        if (!SPVIDEOLITE_BOL_Service::isRoute('VIDEO_CTRL_Video', 'viewList')) return;

        self::requireSpvideoJs();
        OW::getDocument()->addOnloadScript("SPVideo.fixLongTitles();");
    }
    
    /**
     *
     */
    public static function correctPlayerSize(BASE_CLASS_EventCollector $event) {
        self::requireSpvideoJs();
        OW::getDocument()->addOnloadScript("SPVideo.correctPlayerSize();");
    }
    
    /**
     *
     */
    public static function addLargerPlayerButton(BASE_CLASS_EventCollector $event) {
        self::requireSpvideoJs();
        $language = OW::getLanguage();

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticCssUrl() . 'spvideo_player.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
        OW::getDocument()->addOnloadScript("SPVideo.addEnlargeButton();");
        
        $language->addKeyForJs('spvideolite', 'btn_larger');
        $language->addKeyForJs('spvideolite', 'btn_smaller');
        
        $event->add(array('href' => 'javascript:;', 'id' => 'btn-resize-player', 'class' => 'btn-resize-player', 'label' => $language->text('spvideolite', 'btn_larger')));
    }
    
    function initServiceHooking() {
        SPVIDEOLITE_CLASS_ClipService::getInstance();
    }
}
