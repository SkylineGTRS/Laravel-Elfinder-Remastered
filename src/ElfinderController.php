<?php namespace SkylineGTRS\Elfinder;

use SkylineGTRS\Elfinder\Session\LaravelSession;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;

class ElfinderController extends Controller
{
    protected $package = 'elfinder';

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function showIndex()
    {
        return $this->app['view']
            ->make($this->package . '::elfinder')
            ->with($this->getViewVars());
    }

    public function showTinyMCE()
    {
        return $this->app['view']
            ->make($this->package . '::tinymce')
            ->with($this->getViewVars());
    }

    public function showTinyMCE4()
    {
        return $this->app['view']
            ->make($this->package . '::tinymce4')
            ->with($this->getViewVars());
    }

    public function showCKeditor4()
    {
        return $this->app['view']
            ->make($this->package . '::ckeditor4')
            ->with($this->getViewVars());
    }

    public function showPopup($input_id = NULL ,$max_files = NULL ,$route = NULL)
    {
        $type = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
        $imgs = ['png', 'jpg', 'jpeg'];
        return $this->app['view']
            ->make($this->package . '::standalonepopup')
            ->with($this->getViewVars())
            ->with(compact('input_id','type','imgs','max_files','route'));
    }

    public function showFilePicker($input_id)
    {
        $type = Request::input('type');
        $mimeTypes = implode(',',array_map(function($t){return "'".$t."'";}, explode(',',$type)));
        return $this->app['view']
            ->make($this->package . '::filepicker')
            ->with($this->getViewVars())
            ->with(compact('input_id','type','mimeTypes'));
    }

    public function showConnector()
    {
        $roots = $this->app->config->get('elfinder.roots', []);
        if (empty($roots)) {
            $dirs = (array) $this->app['config']->get('elfinder.dir', []);
            foreach ($dirs as $dir) {
                $roots[] = [
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => public_path($dir), // path to files (REQUIRED)
                    'URL' => url($dir), // URL to files (REQUIRED)
                    'accessControl' => $this->app->config->get('elfinder.access') // filter callback (OPTIONAL)
                ];
            }

            $disks = (array) $this->app['config']->get('elfinder.disks', []);
            foreach ($disks as $key => $root) {
                if (is_string($root)) {
                    $key = $root;
                    $root = [];
                }
                $disk = app('filesystem')->disk($key);
                if ($disk instanceof FilesystemAdapter) {
                    $defaults = [
                        'driver' => 'Flysystem',
                        'filesystem' => $disk->getDriver(),
                        'alias' => $key,
                    ];
                    $roots[] = array_merge($defaults, $root);
                }
            }
        }

        if (app()->bound('session.store')) {
            $sessionStore = app('session.store');
            $session = new LaravelSession($sessionStore);
        } else {
            $session = null;
        }

        $rootOptions = $this->app->config->get('elfinder.root_options', array());
        foreach ($roots as $key => $root) {
            $roots[$key] = array_merge($rootOptions, $root);
        }

        $opts = $this->app->config->get('elfinder.options', array());
        $opts = array_merge($opts, ['roots' => $roots, 'session' => $session]);

        // run elFinder
        $connector = new Connector(new \elFinder($opts));
        $connector->run();
        return $connector->getResponse();
    }

    protected function getViewVars()
    {
        $dir = 'packages/skylinegtrs/' . $this->package;
        $locale = str_replace("-",  "_", $this->app->config->get('app.locale'));
        if (!file_exists($this->app['path.public'] . "/$dir/js/i18n/elfinder.$locale.js")) {
            $locale = false;
        }
        $csrf = true;
        return compact('dir', 'locale', 'csrf');
    }
}
