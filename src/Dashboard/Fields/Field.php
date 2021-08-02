<?php

/**
 * Chestnut Admin Resource Field.
 *
 */

namespace Chestnut\Dashboard\Fields;

use Illuminate\Http\Request;
use JsonSerializable;

/**
 * Chestnut Admin Resource Field abstract class
 *
 * @category Abstract_Field
 * @package  Chestnut\Dashboard
 * @author   Leon Zhang <33543015@qq.com>
 */
abstract class Field implements JsonSerializable
{
    public $prop;
    public $label;
    public $component;
    public $callback;
    public $attrs;
    public $hideFrom;

    /**
     * Field Constructor
     *
     * @param {String} $prop  property name
     * @param {String} $label property show label text
     * @param {String} $type  property type
     */
    public function __construct($prop, $label = null, $callback = null)
    {
        $this->prop      = $prop;
        $this->label     = $label;
        $this->callback = $callback;

        $this->attrs  = collect();
        $this->hideFrom = collect();
    }

    public function getProperty()
    {
        return $this->prop;
    }

    public function setAttribute($key, $value)
    {
        $this->attrs[$key] = $value;

        return $this;
    }

    public function hasAttribute($key)
    {
        return $this->attrs->has($key);
    }

    public function showOn(string $showOn)
    {
        return !$this->hideFrom->get($showOn, false);
    }

    public function hideFrom(string $hideFrom, $confirm = true)
    {
        $this->hideFrom[$hideFrom] = $confirm;

        return $this;
    }

    /**
     * Set property readonly
     *
     * @return self
     */
    public function readonly()
    {
        return $this->setAttribute('readonly', true);
    }

    /**
     * Set property sortable
     *
     * @return self
     */
    public function sortable()
    {
        return $this->setAttribute('sortable', 'custom');
    }

    public function showOnIndex()
    {
        return $this->hideFrom('index', false);
    }

    public function showOnDetail()
    {
        return $this->hideFrom('defailt', false);
    }

    public function showOnCreating()
    {
        return $this->hideFrom('creating', false);
    }

    public function showOnUpdating()
    {
        return $this->hideFrom('updating', false);
    }

    public function hideFromIndex()
    {
        return $this->hideFrom('index');
    }

    public function hideFromDetail()
    {
        return $this->hideFrom('detail');
    }

    public function hideWhenCreating()
    {
        return $this->hideFrom('creating');
    }

    public function hideWhenUpdating()
    {
        return $this->hideFrom('updating');
    }

    public function onlyOnIndex()
    {
        return $this->hideFromDetail()->hideWhenCreating()->hideWhenUpdating();
    }

    public function onlyOnDetail()
    {
        return $this->hideFromIndex()->hideWhenCreating()->hideWhenUpdating();
    }

    public function onlyOnForms()
    {
        return $this->hideFromIndex()->hideFromDetail();
    }

    public function exceptOnForms()
    {
        return $this->hideWhenCreating()->hideWhenUpdating();
    }

    public function rules()
    {
        $rules = func_get_args();

        return $this->setAttribute("rules", join("|", $rules));
    }

    /**
     * Set field validator
     *
     * @param String $validate
     *
     * @return self
     */
    public function validate(string $validate)
    {
        return $this->setAttribute('validate', $validate);
    }

    public function jsonSerialize()
    {
        return [
            "name"      => $this->prop,
            "label"     => $this->label,
            "component" => $this->component,
            "align"     => 'center',
            "attrs"     => $this->attrs->all(),
        ];
    }

    public function fillAttributeFromRequest(Request $request, $model)
    {
        if ($request->exists($this->getProperty())) {
            $model->{$this->getProperty()} = $request[$this->getProperty()];
        }
    }

    public function isReadonly()
    {
        return isset($this->attrs['readonly']) && $this->attrs['readonly'];
    }
}
