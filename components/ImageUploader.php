<?php namespace KosmosKosmos\BetterContentEditor\Components;

use Log;
use Str;
use Cache;
use Config;
use Resizer;
use BackendAuth;
use Cms\Classes\ComponentBase;
use KosmosKosmos\BetterContentEditor\Models\Images;
use KosmosKosmos\BetterContentEditor\Models\Settings;

class ImageUploader extends ComponentBase {

    public $renderCount;
    protected $themeID;

    public function componentDetails() {
        return [
            'name'        => 'ImageUploader',
            'description' => 'Lade Bilder wie beim ContentEditor im Frontend hoch'
        ];
    }

    public function onRun() {
        $this->renderCount = 0;
        $this->page['backendUser'] = BackendAuth::getUser() && BackendAuth::getUser()->hasAccess('kosmoskosmos.bettercontenteditor.editor');
        $this->page['theme'] = $this->getTheme()->getId();
        $this->themeID = $this->getTheme()->getId() . '.';
        $this->addCss('/plugins/kosmoskosmos/bettercontenteditor/assets/imageuploader.css');
    }

    public function onRender() {
//        Cache::forget($this->themeID.$this->property('item'));
        $this->page['bce'] = Config::get('app.debug')
            ? $this->getItem()
            : Cache::rememberForever($this->themeID.$this->property('item'), function() {
                return $this->getItem();
            });

        $this->renderCount += 1;
    }

    private function getItem() {
        $item = $this->themeID.$this->property('item');
        $urls = Images::where('item', $item)->pluck('url');
        $image = $urls->count() > 0 ? $urls[0] : null;
        $size = $this->property('size') ?? 1300;
        $defaultImage = $this->property('default') ?? '/plugins/kosmoskosmos/bettercontenteditor/assets/images/placeholder.jpg';
        $fileName = 'Placeholder';
        if ($image) {
            if (Str::endsWith($image, '.svg')) {
                $fileName = substr($image, strrpos($image, '/') + 1);
            } else {
                $path = Settings::get('image_folder', 'contenteditor');
                $basePath = rtrim(Config::get('system.storage.media.path', '/storage/app/media'), '/');
                $imageResize = Resizer::open(base_path($image));
                $imageResize = $imageResize->resize(null, $size, ['mode' => 'auto']);
                $fileName = str_replace($basePath . '/' . $path . '/', '', $image);
                $fileName = substr($fileName, 0, strrpos($fileName, '.'));
                $image = $imageResize->__toString();
            }
        }
        $result = [
            'item' => $item,
            'image' => $image ?? $defaultImage,
            'classes' => $this->property('class') ?? 'uk-background-cover uk-height-large uk-position-relative',
            'tag' => $this->property('tag') ?? 'img',
            'leaveOpen' => $this->property('leaveOpen') == true,
            'fileName' => $fileName,
            'attributes' => $this->property('attributes')
        ];
        return $result;
    }
}
