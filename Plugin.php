<?php namespace KosmosKosmos\BetterContentEditor;

use Log;
use Event;
use Cms\Classes\Theme;
use System\Classes\PluginBase;
use RainLab\Pages\Classes\Page as StaticPage;

class Plugin extends PluginBase {

    public $elevated = true;

    public function boot() {
        Event::listen('pages.object.fillObject', function ($staticPage, $object, &$objectData, $type) {
            if ($type != 'page') return;
            $objectData['settings']['viewBag'] = $this->setIds($objectData['settings']['viewBag']);
        });
        Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {
            if (Theme::getActiveTheme()) {
                $themeBackendCss = '/themes/' . Theme::getActiveTheme()->getDirName() . '/assets/styles/backend.css';
                if (file_exists(base_path($themeBackendCss))) {
                    $controller->addCss($themeBackendCss);
                }
                $controller->addCss('/plugins/kosmoskosmos/bettercontenteditor/assets/backend.css');
            }
        });
    }

    protected function setIds(&$elements) {
        if (isset($elements['id']) && !$elements['id']) {
            $elements['id'] = uniqid();
        }
        foreach ($elements as $elementId => $element) {
            if (is_array($element)) {
                $elements[$elementId] = $this->setIds($element);
            }
        }
        return $elements;
    }

    public function pluginDetails() {
        return [
            'name' => 'Better Content Editor',
            'description' => 'Der etwas bessere Content Editor',
            'author' => 'KosmosKosmos',
            'icon' => 'icon-edit'
        ];
    }

    public function registerComponents() {
        return [
            'KosmosKosmos\BetterContentEditor\Components\ImageUploader' => 'imageuploader',
            'KosmosKosmos\BetterContentEditor\Components\ContentEditor' => 'contenteditor',
        ];
    }

    public function registerSettings() {
        return [
            'settings' => [
                'label' => 'Content Editor Settings',
                'description' => 'Manage main editor settings.',
                'icon' => 'icon-cog',
                'class' => 'KosmosKosmos\BetterContentEditor\Models\Settings',
                'order' => 500,
                'permissions' => ['kosmoskosmos.bettercontenteditor.access_settings']
            ]
        ];
    }

    public function registerPermissions() {
        return [
            'kosmoskosmos.bettercontenteditor.editor' => [
                'tab' => 'Content Editor',
                'label' => 'Allow to use content editor on frontend'
            ],
            'kosmoskosmos.bettercontenteditor.access_settings' => [
                'tab' => 'Content Editor',
                'label' => 'Access content editor settings'
            ],
        ];
    }
}
