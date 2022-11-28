<?php namespace KosmosKosmos\BetterContentEditor\Models;

use File;
use Lang;
use Model;
use Cache;
use Less_Parser;
use System\Models\File as DbFile;
use System\Behaviors\SettingsModel;

class Settings extends Model {

    public $implement = [SettingsModel::class];
    public $settingsCode = 'kosmoskosmos_bettercontenteditor_settings';
    public $settingsFields = 'fields.yaml';

    public $attachOne = [
        'default_image' => DbFile::class
    ];

    public static function getDefaultImage() {
        $file = DbFile::where('attachment_type', self::class)->where('field', 'default_image')->first();
        return $file ? $file->getPath() : '/plugins/kosmoskosmos/bettercontenteditor/assets/images/placeholder.jpg';
    }

    public function beforeSave() {
        Cache::flush();
    }

    const CACHE_KEY = 'kosmoskosmos.bettercontenteditor.additional_styles';

    public function initSettingsData() {
        $this->additional_styles = file_get_contents(plugins_path() . '/kosmoskosmos/bettercontenteditor/assets/additional-css.css');
    }

    public function getEnabledButtonsOptions() {
        return trans('kosmoskosmos.bettercontenteditor::lang.styles');
    }

    // list of allowed tags
    public function getAllowedTagsOptions() {
        return [
            'p',
            'img',
            'div',
            'table',
            'span',
            'small',

            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',

            'b',
            'i',
            'strong',
        ];
    }

    public function afterSave() {
        Cache::forget(self::CACHE_KEY);
    }

    public static function renderCss() {
        if (Cache::has(self::CACHE_KEY)) {
            return Cache::get(self::CACHE_KEY);
        }
        try {
            $customCss = self::compileCss();
            Cache::forever(self::CACHE_KEY, $customCss);
        } catch (Exception $ex) {
            $customCss = '/* ' . $ex->getMessage() . ' */';
        }
        return $customCss;
    }

    public static function compileCss() {
        $parser = new Less_Parser(['compress' => true]);
        $customStyles = self::get('additional_styles');
        $parser->parse($customStyles);
        $css = $parser->getCss();
        return $css;
    }
}
