<?php namespace KosmosKosmos\BetterContentEditor\Controllers;

use Log;
use File;
use Input;
use Cache;
use Config;
use Resizer;
use Response;
use Exception;
use BackendAuth;
use ApplicationException;
use Media\Classes\MediaLibrary;
use Illuminate\Routing\Controller;
use Cms\Helpers\File as FileHelper;
use October\Rain\Support\Facades\Str;
use KosmosKosmos\BetterContentEditor\Models\Settings;

class ImageController extends Controller {

    public $requiredPermissions = ['kosmoskosmos.bettercontenteditor.editor'];

    public function __construct() {
        $this->middleware('web');
    }

    public function imageUploaderUpload() {
        if (BackendAuth::getUser()) {
            try {
                if (!Input::hasFile('image')) {
                    throw new ApplicationException('File missing from request');
                }
                $uploadedFile = Input::file('image')[0];
                if (!$uploadedFile->isValid()) {
                    throw new ApplicationException($uploadedFile->getErrorMessage());
                }
                $extension = strtolower($uploadedFile->getClientOriginalExtension());

                $file = Input::get('file');
                $key = Input::get('key');

                $basePath = ltrim(rtrim(Config::get('system.storage.media.path', '/storage/app/media'), '/'), '/');
                $uploadPath = Settings::get('image_folder') ? Settings::get('image_folder') : 'contenteditor';
                $base = $basePath.'/'.$uploadPath;
                $path = $base.'/'.$file;
                $pathWithoutFile = substr($path, 0, -strlen(basename($file)));
                if (!file_exists(base_path($pathWithoutFile))) {
                    mkdir(base_path($pathWithoutFile));
                }
                $url = $path.'.'.$extension;

                $files = glob($path.'*');
                $filePath = count($files) ? $files[0] : null;
                if ($filePath) {
                    unlink($filePath);
                }
                file_put_contents(base_path($url), File::get($uploadedFile->getRealPath()));

                list($width, $height) = getimagesize($uploadedFile);
                Cache::forget($key);
                return Response::json([
                    'url'      => '/'.$url,
                    'filename' => 'sdfds',
                    'size'     => [
                        $width,
                        $height
                    ]
                ]);
            }
            catch (Exception $ex) {
                return $ex;
            }
        }
    }

    public function uploadNew()
    {
        try {
            if (!Input::hasFile('image')) {
                throw new ApplicationException('File missing from request');
            }

            $uploadedFile = Input::file('image');
            $fileName = $uploadedFile->getClientOriginalName();

            // Convert uppcare case file extensions to lower case
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $fileName = File::name($fileName).'.'.$extension;

            // File name contains non-latin characters, attempt to slug the value
            if (!FileHelper::validateName($fileName)) {
                $fileNameSlug = Str::slug(File::name($fileName), '-');
                $fileName = $fileNameSlug.'.'.$extension;
            }
            if (!$uploadedFile->isValid()) {
                throw new ApplicationException($uploadedFile->getErrorMessage());
            }

            $path = Settings::get('image_folder', 'contenteditor');
            $path = MediaLibrary::validatePath($path);

            $realPath = empty(trim($uploadedFile->getRealPath()))
               ? $uploadedFile->getPath() . DIRECTORY_SEPARATOR . $uploadedFile->getFileName()
               : $uploadedFile->getRealPath();

            MediaLibrary::instance()->put(
                $path.'/'.$fileName,
                File::get($realPath)
            );

            list($width, $height) = getimagesize($uploadedFile);

            return Response::json([
                'url'      => MediaLibrary::instance()->getPathUrl($path.'/'.$fileName),
                'filePath' => $path.'/'.$fileName,
                'filename' => $fileName,
                'size'     => [
                   $width,
                   $height
               ]
            ]);
        } catch (Exception $ex) {
            throw new ApplicationException($ex);
        }

    }

    public function save()
    {
        $url = post('url');
        $crop = post('crop');
        $width = post('width');
        $height = post('height');
        $filePath = post('filePath');
        $relativeFilePath = config('cms.storage.media.path').$filePath;

        if ($crop && $crop != '0,0,1,1') {
            $crop = explode(',', $crop);

            $file = MediaLibrary::instance()->get(post('filePath'));
            $tempDirectory = temp_path().'/contenteditor';
            $tempFilePath = temp_path().post('filePath');
            File::makeDirectory($tempDirectory, 0777, true, true);

            if (!File::put($tempFilePath, $file)) {
                throw new SystemException('Error saving remote file to a temporary location.');
            }

            $width = floor(post('width') * $crop[3] - post('width') * $crop[1]);
            $height = floor(post('height') * $crop[2] - post('height') * $crop[0]);

            Resizer::open($tempFilePath)
                ->crop(
                    floor(post('width') * $crop[1]),
                    floor(post('height') * $crop[0]),
                    $width,
                    $height
                )
                ->save($tempFilePath, 100);

            $pathParts = pathinfo(post('filePath'));
            $newFilePath = $pathParts['dirname'] . '/' . $pathParts['filename'] . '-c.' . $pathParts['extension'];

            MediaLibrary::instance()->put($newFilePath, file_get_contents($tempFilePath));

            $url = MediaLibrary::instance()->getPathUrl($newFilePath);
        }

        return Response::json([
            'url'       => $url,
            'filePath'  => $relativeFilePath,
            'width'     => $width,
            'crop'      => post('crop'),
            'alt'       => post('alt'),
            'size'      => [
                $width,
                $height
            ]
        ]);
    }
}
