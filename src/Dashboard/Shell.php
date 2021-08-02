<?php

namespace Chestnut\Dashboard;

use BadMethodCallException;
use Chestnut\Dashboard\RepositoryRegistrar;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use JsonSerializable;
use ReflectionClass;

/**
 * Chestnut resource manager
 *
 * @method static Chestnut\Dashboard\Fields\Avatar Avatar($prop, $label)
 * @method static Chestnut\Dashboard\Fields\Checkbox Checkbox($prop, $label)
 * @method static Chestnut\Dashboard\Fields\CreatedAt CreatedAt($label)
 * @method static Chestnut\Dashboard\Fields\Datetime Datetime($prop, $label)
 * @method static Chestnut\Dashboard\Fields\Editor Editor($prop, $label)
 * @method static Chestnut\Dashboard\Fields\ID ID($label)
 * @method static Chestnut\Dashboard\Fields\Image Image($prop, $label)
 * @method static Chestnut\Dashboard\Fields\Password Password($prop, $label)
 * @method static Chestnut\Dashboard\Fields\Select Select($prop, $label)
 * @method static Chestnut\Dashboard\Fields\SoftDelete SoftDelete($label)
 * @method static Chestnut\Dashboard\Fields\Text Text($prop, $label)
 */
class Shell implements Jsonable, JsonSerializable
{
    public $app;
    protected $repositoryRegistrar;

    /**
     * Laravel admin constructor
     *
     * @param {Illuminate\Foundation\Application} $app Laravel Application
     */
    public function __construct(Application $app)
    {
        $this->app       = $app;
        $this->repositoryRegistrar = new RepositoryRegistrar($app->router);

        $this->boot();
    }

    /**
     * Laravel chestnut shell bootstrap
     *
     * @return void
     */
    public function boot()
    {
    }

    public function registerRepositories($directory, $package)
    {
        $this->repositoryRegistrar->register($directory, $package);
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            "data" => $this->repositoryRegistrar->getViews()
        ];
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function __call($name, $arguments)
    {
        if (class_exists("Chestnut\\Dashboard\\Fields\\" . $name)) {
            $class = new ReflectionClass("Chestnut\\Dashboard\\Fields\\" . $name);

            return $class->newInstanceArgs($arguments);
        }

        if (class_exists("Chestnut\\Dashboard\\Fields\\Relations\\" . $name)) {
            $class = new ReflectionClass("Chestnut\\Dashboard\\Fields\\Relations\\" . $name);

            return $class->newInstanceArgs($arguments);
        }

        throw new BadMethodCallException("Method [$name] not found in [" . get_class($this) . "]");
    }

    public static function __callStatic($name, $arguments)
    {
        if (class_exists("Chestnut\\Dashboard\\Fields\\" . $name)) {
            $class = new ReflectionClass("Chestnut\\Dashboard\\Fields\\" . $name);

            return $class->newInstanceArgs($arguments);
        }

        if (class_exists("Chestnut\\Dashboard\\Fields\\Relations\\" . $name)) {
            $class = new ReflectionClass("Chestnut\\Dashboard\\Fields\\Relations\\" . $name);

            return $class->newInstanceArgs($arguments);
        }

        throw new BadMethodCallException("Method [$name] not found in [" . static::class . "]");
    }
}
