<?php

/**
 * Chestnut Admin Resource Field.
 *
 */

namespace Chestnut\Dashboard\Fields;

use Illuminate\Database\Eloquent\Model;
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
     * @param string $prop  property name
     * @param string $label property show label text
     * @param string $type  property type
     */
    public function __construct($prop, $label = null, $callback = null)
    {
        $this->prop      = $prop;
        $this->label     = $label;
        $this->callback = $callback;

        $this->attrs  = collect();
        $this->hideFrom = collect();
    }

    /**
     * Get field property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->prop;
    }

    /**
     * Set field attribute
     *
     * @param string $key attribute key
     * @param string $value attribute value
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->attrs[$key] = $value;

        return $this;
    }

    /**
     * Determind field has attribute
     *
     * @param string $key attribute key
     *
     * @return boolean
     */
    public function hasAttribute($key)
    {
        return $this->attrs->has($key);
    }

    /**
     * Determind field show on given view
     *
     * @param string $view view name
     *
     * @return boolean
     */
    public function showOn(string $view)
    {
        return !$this->hideFrom->get($view, false);
    }

    /**
     * Set field hide from given view
     *
     * @param string $view view name
     * @param boolean $confirm hide in given view, default to true
     *
     * @return self
     */
    public function hideFrom(string $view, bool $confirm = true)
    {
        $this->hideFrom[$view] = $confirm;

        return $this;
    }

    /**
     * Set field readonly
     *
     * @return self
     */
    public function readonly()
    {
        return $this->setAttribute('readonly', true);
    }

    /**
     * Set field sortable
     *
     * @return self
     */
    public function sortable()
    {
        return $this->setAttribute('sortable', 'custom');
    }

    /**
     * Set field show on Index view
     *
     * @return self
     */
    public function showOnIndex()
    {
        return $this->hideFrom('index', false);
    }

    /**
     * Set field show on Detail view
     *
     * @return self
     */
    public function showOnDetail()
    {
        return $this->hideFrom('defailt', false);
    }

    /**
     * Set field show on Create view
     *
     * @return self
     */
    public function showOnCreating()
    {
        return $this->hideFrom('creating', false);
    }

    /**
     * Set field show on Update view
     *
     * @return self
     */
    public function showOnUpdating()
    {
        return $this->hideFrom('updating', false);
    }

    /**
     * Set field hiden on Index view
     *
     * @return self
     */
    public function hideFromIndex()
    {
        return $this->hideFrom('index');
    }

    /**
     * Set field hiden on Detail view
     *
     * @return self
     */
    public function hideFromDetail()
    {
        return $this->hideFrom('detail');
    }

    /**
     * Set field hiden on Create view
     *
     * @return self
     */
    public function hideWhenCreating()
    {
        return $this->hideFrom('creating');
    }

    /**
     * Set field hiden on Update view
     *
     * @return self
     */
    public function hideWhenUpdating()
    {
        return $this->hideFrom('updating');
    }

    /**
     * Set field only show on Index view
     *
     * @return self
     */
    public function onlyOnIndex()
    {
        return $this->hideFromDetail()->hideWhenCreating()->hideWhenUpdating();
    }

    /**
     * Set field only show on Detail view
     *
     * @return self
     */
    public function onlyOnDetail()
    {
        return $this->hideFromIndex()->hideWhenCreating()->hideWhenUpdating();
    }

    /**
     * Set field only show on Forms view
     *
     * @return self
     */
    public function onlyOnForms()
    {
        return $this->hideFromIndex()->hideFromDetail();
    }

    /**
     * Set field only hide on Forms view
     *
     * @return self
     */
    public function exceptOnForms()
    {
        return $this->hideWhenCreating()->hideWhenUpdating();
    }

    /**
     * Set field rules
     *
     * @param string $rule  field rule
     * @param string ...$rules more field rules
     *
     * @return self
     */
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
            "property"      => $this->getProperty(),
            "label"     => $this->label,
            "component" => $this->component,
            "attrs"     => $this->attrs->all(),
        ];
    }

    /**
     * Fill data to model field from request
     *
     * @param Illuminate\Http\Request $request request
     * @param Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function fillAttributeFromRequest(Request $request, Model $model)
    {
        if ($request->exists($this->getProperty())) {
            $model->{$this->getProperty()} = $request[$this->getProperty()];
        }
    }

    /**
     * Determind field readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return isset($this->attrs['readonly']) && $this->attrs['readonly'];
    }
}
