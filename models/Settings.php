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

    const CACHE_KEY = 'kosmoskosmos.bettercontenteditor.additional_styles';

    public function initSettingsData() {
        $this->additional_styles = File::get(plugins_path() . '/kosmoskosmos/bettercontenteditor/assets/additional-css.css');
    }

    public function getEnabledButtonsOptions() {
        return [
            'bold'           => 'Bold (b)',
            'italic'         => 'Italic (i)',
            'link'           => 'Link (a)',

            'align-left'     => 'Align left',
            'align-center'   => 'Align center',
            'align-right'    => 'Align right',

            'heading'        => 'Heading (h1)',
            'subheading'     => 'Subheading (h2)',

            'subheading3'    => 'Subheading3 (h3)',
            'subheading4'    => 'Subheading4 (h4)',
            'subheading5'    => 'Subheading5 (h5)',

            'paragraph'      => 'Paragraph (p)',
            'unordered-list' => 'Unordered list (ul)',
            'ordered-list'   => 'Ordered list (ol)',

            'table'          => 'Table',
            'indent'         => 'Indent',
            'unindent'       => 'Unindent',
            'line-break'     => 'Line-break (br)',

            'image'          => 'Image upload',
            'video'          => 'Video',
            'preformatted'   => 'Preformatted (pre)',
        ];
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
