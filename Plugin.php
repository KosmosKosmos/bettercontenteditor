<?php namespace KosmosKosmos\BetterContentEditor;

use System\Classes\PluginBase;

/**
 * BetterContentEditor Plugin Information File
 */
class Plugin extends PluginBase
{
    public $elevated = true;
#    public $require = ['ToughDeveloper.ImageResizer'];

    public function boot() {
    }

    public function pluginDetails()
    {
        return [
            'name' => 'Better Content Editor',
            'description' => 'Der etwas bessere Content Editor',
            'author' => 'KosmosKosmos',
            'icon' => 'icon-edit'
        ];
    }

    public function registerComponents()
    {
        return [
            'KosmosKosmos\BetterContentEditor\Components\ImageUploader' => 'imageuploader',
            'KosmosKosmos\BetterContentEditor\Components\ContentEditor' => 'contenteditor',
        ];
    }

    public function registerSettings()
    {
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

    public function registerPermissions()
    {
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
