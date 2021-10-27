<?php

namespace Chestnut\Dashboard;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class RepositoryRegistrar
{
    protected $router;

    protected $repositoryDefaults = ['index', 'create', 'store', 'detail', 'edit', 'update', 'destroy'];

    protected $views;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->views = collect();
    }

    public function register($directory, $package)
    {
        $repositories = $this->getRepositories($directory, $package);

        foreach ($repositories as $repository) {
            $this->resolveRepository($repository);
        }
    }

    public function getViews()
    {
        return array_values($this->views->filter(function ($value) {
            return $value->showOnMenu();
        })->all());
    }

    public function resolveRepository($repository)
    {
        $prefix = Str::plural(strtolower(class_basename($repository)));

        $callback = function () use ($repository) {
            $this->registerRepository($repository);
        };

        $callback = $callback->bindTo($this);

        $this->router->group(['prefix' => "chestnut/" . $prefix, 'middleware' => ['web', 'auth:sanctum']], $callback);

        $this->views->add(new RepositoryView($repository));
    }

    public function registerRepository($repository)
    {
        $defaults = $this->repositoryDefaults;

        foreach ($defaults as $method) {
            $this->{'addRepository' . ucfirst($method)}($repository);
        }

        $this->router->get("options", $this->getRepositoryAction($repository, 'getOptions'));

        $this->router->post('action', $this->getRepositoryAction($repository, 'doAction'));

        $this->router->post('statistic', $this->getRepositoryAction($repository, 'calculateStatistic'));
    }

    public function addRepositoryIndex($repository)
    {
        $this->router->get("", $this->getRepositoryAction($repository, "index"));
    }

    public function addRepositoryDetail($repository)
    {
        $this->router->get("{id}", $this->getRepositoryAction($repository, "detail"))->where("id", "[0-9]+");
    }

    public function addRepositoryEdit($repository)
    {
        $this->router->get("{id}/edit", $this->getRepositoryAction($repository, "edit"))->where("id", "[0-9]+");
    }

    public function addRepositoryCreate($repository)
    {
        $this->router->get("create", $this->getRepositoryAction($repository, "create"));
    }

    public function addRepositoryStore($repository)
    {
        $this->router->post("store", $this->getRepositoryAction($repository, "store"));
    }

    public function addRepositoryUpdate($repository)
    {
        $this->router->put("{id}", $this->getRepositoryAction($repository, "update"))->where("id", "[0-9]+");
    }

    public function addRepositoryDestroy($repository)
    {
        $this->router->delete("{id}", $this->getRepositoryAction($repository, "destroy"))->where("id", "[0-9]+");
    }

    public function getRepositoryAction($repository, $action)
    {
        return $repository . '@' . $action;
    }

    public function getRepositoriesInDirectory($directory)
    {
        $files = File::glob($directory . "/*.php");

        $nuts = [];
        foreach ($files as $file) {
            $fp    = fopen($file, 'r');
            $class = $namespace = $buffer = '';
            $i     = 0;
            while (!$class) {
                if (feof($fp)) {
                    break;
                }

                $buffer .= fread($fp, 512);
                $tokens = token_get_all($buffer);

                if (strpos($buffer, '{') === false) {
                    continue;
                }

                for (; $i < count($tokens); $i++) {
                    if ($tokens[$i][0] === T_NAMESPACE) {
                        for ($j = $i + 1; $j < count($tokens); $j++) {
                            if ($tokens[$j][0] === T_STRING) {
                                $namespace .= '\\' . $tokens[$j][1];
                            } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                                break;
                            }
                        }
                    }

                    if ($tokens[$i][0] === T_CLASS) {
                        for ($j = $i + 1; $j < count($tokens); $j++) {
                            if ($tokens[$j] === '{') {
                                $class = $tokens[$i + 2][1];
                            }
                        }
                    }
                }
            }

            array_push($nuts, $namespace . "\\" . $class);
        }

        return $nuts;
    }
    /**
     * Register Nut resources in given directory
     *
     * @param String $directory Nuts directory
     *
     * @return void
     */
    public function getRepositories($directory, $package): array
    {
        $repositories = $this->getRepositoriesInDirectory($directory);

        return $repositories;
    }
}
