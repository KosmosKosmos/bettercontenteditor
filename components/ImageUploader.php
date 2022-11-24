<?php namespace KosmosKosmos\BetterContentEditor\Components;

use Log;
use Str;
use Cache;
use Config;
use Resizer;
use BackendAuth;
use Cms\Classes\ComponentBase;
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
        $this->page['bce'] = Cache::rememberForever($key, fn() =>$this->getImage($file, $key));
        $this->renderCount += 1;
    }

    private function getImage($file, $key) {
        $basePath = base_path(ltrim(rtrim(Config::get('system.storage.media.path', '/storage/app/media'), '/'), '/'));
        $uploadPath = Settings::get('image_folder') ? Settings::get('image_folder') : 'contenteditor';
        $base = $basePath.'/'.$uploadPath;
        $path = $base.'/'.$file;
        $files = glob($path.'.*');
        $filePath = count($files) ? $files[0] : null;

        if ($filePath) {
            $width = $this->property('width') ?? ($this->property('size') ?? 1600);
            $height = $this->property('height');
            $mode = $this->property('mode') ?? 'auto';
            $quality = $this->property('quality') ?? 90;
            if (Str::endsWith($filePath, '.svg')) {
                $fileName = trrim($filePath, strrpos($filePath, '/') + 1);
            } else {
                $fileName = $this->property('fileName') ?? basename($filePath);
                $suffix = '.'.pathinfo($fileName)['extension'];
                $fileUrl = ltrim((Config::get('system.storage_path') ?? '/storage'), '/').'/temp/public/'.$file.$suffix;
                $imageResize = Resizer::open($filePath);
                $imageResize = $imageResize->resize($width, $height, ['mode' => $mode, 'quality' => $quality]);
                $pathWithoutFile = substr($fileUrl, 0, -strlen(basename($fileName)));
                if (!file_exists(base_path($pathWithoutFile))) {
                    mkdir(base_path($pathWithoutFile));
                }
                if (file_exists(base_path($fileUrl))) {
                    unlink(base_path($fileUrl));
                }

                $imageResize->save(base_path($fileUrl));
                $image = '/'.$fileUrl.'?timestamp='.time();
            }
        } else {
            $image = $this->property('default') ?? Settings::getDefaultImage();
            $fileName = 'Placeholder';
        }
        $result = [
            'key' => $key,
            'item' => $file,
            'image' => $image,
            'classes' => $this->property('class') ?? 'uk-background-cover uk-height-large uk-position-relative',
            'tag' => $this->property('tag') ?? 'img',
            'leaveOpen' => $this->property('leaveOpen') == true,
            'fileName' => $fileName,
            'attributes' => $this->property('attributes')
        ];
        return $result;
    }
}
