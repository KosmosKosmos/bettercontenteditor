<?php namespace KosmosKosmos\BetterContentEditor\Components;

use Log;
use Str;
use Input;
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
        $file = $this->property('file');
        $key = implode('-', $this->getProperties());
        $this->page['bce'] = Cache::rememberForever($key, fn() =>$this->getItem($file, $key));
        $this->renderCount += 1;
    }

    private function getItem($file, $key) {
        $basePath = base_path(ltrim(rtrim(Config::get('system.storage.media.path', '/storage/app/media'), '/'), '/'));
        $uploadPath = Settings::get('image_folder', 'contenteditor');
        $base = $basePath.'/'.$uploadPath;
        $path = $base.'/'.$file;
        $files = glob($path.'*');
        $filePath = count($files) ? $files[0] : null;

        $defaultImage = $this->property('default') ?? '/plugins/kosmoskosmos/bettercontenteditor/assets/images/placeholder.jpg';
        $fileName = 'Placeholder';
        if ($filePath) {
            $width = $this->property('width') ?? ($this->property('size') ?? 1300);
            $height = $this->property('height');
            $mode = $this->property('mode') ?? 'auto';
            $quality = $this->property('quality') ?? 79;
            if (Str::endsWith($filePath, '.svg')) {
                $fileName = substr($filePath, strrpos($filePath, '/') + 1);
            } else {
                $fileName = basename($filePath);
                $fileUrl = ltrim((Config::get('system.storage_path') ?? '/storage'), '/').'/temp/public/'.$fileName;
                $imageResize = Resizer::open($filePath);
                $imageResize = $imageResize->resize($width, $height, ['mode' => $mode, 'quality' => $quality]);
                $imageResize->save(base_path($fileUrl));
                $image = '/'.$fileUrl;
            }
        }
        $result = [
            'key' => $key,
            'item' => $file,
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
