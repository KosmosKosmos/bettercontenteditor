<?php namespace KosmosKosmos\BetterContentEditor;

use Log;
use Event;
use System\Classes\PluginBase;

class Plugin extends PluginBase {

    public $elevated = true;

    public function boot() {
        Event::listen('pages.object.fillObject', function ($staticPage, $object, &$objectData, $type) {
            if ($type != 'page') return;
            if (isset($objectData['settings']['viewBag']['sections'])) {
                foreach ($objectData['settings']['viewBag']['sections'] as $sectionId => $section) {
                    if (isset($section['elements'])) {
                        foreach ($section['elements'] as $elementId => $element) {
                            if ($element['id'] == '') {
                                $objectData['settings']['viewBag']['sections'][$sectionId]['elements'][$elementId]['id'] = uniqid();
                            }
                        }
                    }
                }
            }
        });
        Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {
            $controller->addCss('/plugins/kosmoskosmos/bettercontenteditor/assets/backend.css');
        });
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
