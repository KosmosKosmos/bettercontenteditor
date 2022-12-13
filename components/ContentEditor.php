<?php namespace KosmosKosmos\BetterContentEditor\Components;

use Log;
use App;
use Lang;
use File;
use Cache;
use BackendAuth;
use Cms\Classes\Content;
use Cms\Classes\ComponentBase;
use KosmosKosmos\BetterContentEditor\Models\Settings;

class ContentEditor extends ComponentBase {

    public $theme;
    public $content;
    public $defaultFile;
    public $file;
    public $fixture;
    public $tools;
    public $class;
    public $buttons;
    public $palettes;
    public $renderCount;

    public function componentDetails() {
        return [
            'name'        => 'Content Editor',
            'description' => 'Edit your front-end content in page.'
        ];
    }

    public function defineProperties() {
        return [
            'file' => [
                'title'       => 'Content file',
                'description' => 'Content block filename to edit, optional',
                'default'     => '',
                'type'        => 'dropdown'
            ],
            'remove' => [
                'title'       => 'Remove file if empty tag',
                'default'     => true,
                'type'        => 'checkbox'
            ],
            'fixture' => [
                'title'       => 'Content block tag with disabled toolbox',
                'description' => 'Fixed name for content block, useful for inline texts (headers, spans...)',
                'default'     => ''
            ],
            'tools' => [
                'title'       => 'List of enabled tools',
                'description' => 'List of enabled tools for selected content (for all use *)',
                'default'     => ''
            ],
            'class' => [
                'title'       => 'CSS classes',
                'description' => 'CSS class or classes that should be applied to the content block when rendered',
                'default'     => ''
            ],
        ];
    }

    public function getFileOptions() {
        return Content::sortBy('baseFileName')->lists('baseFileName', 'fileName');
    }

    public function onRun() {
        $this->renderCount = 0;
        $this->theme = $this->getTheme();
        if ($this->checkEditor()) {

            $this->buttons = Settings::get('enabled_buttons');
            $this->palettes = Settings::get('style_palettes');

            // put content tools js + css
            $this->addCss('assets/content-tools.min.css');
//            $this->addCss('assets/contenteditor.css');
        }
    }

    public function onRender() {
        $this->renderCount += 1;

        $fileName = str_ends_with(($file = $this->property('file')), '.htm') ? $file : ($file.'.htm');
        $this->defaultFile = $fileName;
        $this->file = $this->setFile($fileName);

        $content = $this->getFile();

        if ($this->checkEditor()) {
            $this->tools = $this->property('tools');
            $this->fixture = $this->property('fixture', false);
            $this->class = $this->property('class');
            $this->page['localisations'] = Lang::get('kosmoskosmos.bettercontenteditor::lang.translations');
            $this->page['lang'] = App::getLocale();
            if ($this->page['lang'] !== 'en') {
                $this->page['translations'] = file_get_contents(__DIR__ .'/contenteditor/translations/' . $this->page['lang'] . '.json', FALSE, NULL);
            }
            $this->content = $content;
        } else {
            return Cache::remember('bettercontenteditor::content-' . $this->file, now()->addHours(24), function () use ($content) {
                if (count($availableVars = $this->page->available_vars ?? [])) {
                    $search = [];
                    $replace = [];
                    foreach ($availableVars as $key => $value) {
                        $search[] = '{{'.$key.'}}';
                        $replace[] = $value;
                    }
                    $content = str_replace($search, $replace, $content);
                }
                return $this->renderPartial('@render.htm', ['content' => $content]);
            });
        }
    }

    public function onSave() {
        $this->theme = $this->getTheme();
        if ($this->checkEditor()) {
            $fileName = post('file');
            $contentToSave = post('content');
            if (trim(strip_tags($contentToSave))) {
                $fileContent = Content::load($this->theme, $fileName) ?? Content::inTheme($this->theme);
                $fileContent->fill(['fileName' => $fileName, 'markup' => $contentToSave]);
                $fileContent->save();

                Cache::forget('bettercontenteditor::content-' . $fileName);
            } elseif ($this->fileExists($fileName)) {
                unlink(base_path('themes/'.$this->theme->getDirName().'/content/'.$fileName));
            }
        }
    }

    public function getFile() {
        if (Content::load($this->theme, $this->file)) {
            return $this->renderContent($this->file);
        } else if (Content::load($this->theme, $this->defaultFile)) {
            return $this->renderContent($this->defaultFile);
        }
        return '';
    }

    public function setFile($file) {
        if ($this->translateExists()) {
            return $this->setTranslateFile($file);
        }
        return $file;
    }

    public function setTranslateFile($file) {
        $translate = \RainLab\Translate\Classes\Translator::instance();
        $defaultLocale = $translate->getDefaultLocale();
        $locale = $translate->getLocale();
        $multipleLang = count(\RainLab\Translate\Classes\Locale::listEnabled()) > 1;

        // Compatibility with Rainlab.StaticPage
        // StaticPages content does not append default locale to file.
        if ($this->fileExists($file) && $locale === $defaultLocale) return $file;

        $translateFile = str_replace('.htm', '.'. $locale. '.htm', $file);
        return !$this->fileExists($translateFile) && !$multipleLang ? $file : $translateFile;
    }

    public function checkEditor() {
        $backendUser = BackendAuth::getUser();
        return $backendUser && $backendUser->hasAccess('kosmoskosmos.bettercontenteditor.editor');
    }

    public function fileExists($file) {
        return File::exists((new Content)->getFilePath($file));
    }

    public function translateExists() {
        return class_exists('\RainLab\Translate\Classes\Locale');
    }
}
