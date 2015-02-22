<?php

namespace Zf2menu\Entity;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Menus implements InputFilterAwareInterface
{

    public $name;
    public $icon;
    public $type;
    public $resource_id;
    public $url;
    public $target;
    public $parent_id;
    public $order;
    public $status;
    public $created;
    public $modified;
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)
    {
        $this->id          = (isset($data['id'])) ? $data['id'] : null;
        $this->name        = (isset($data['name'])) ? $data['name'] : null;
        $this->icon        = (isset($data['icon'])) ? $data['icon'] : null;
        $this->type        = (isset($data['type'])) ? $data['type'] : null;
        $this->resource_id = (isset($data['resource_id'])) ? $data['resource_id'] : null;
        $this->url         = (isset($data['url'])) ? $data['url'] : null;
        $this->target      = (isset($data['target'])) ? $data['target'] : null;
        $this->parent_id   = (isset($data['parent_id'])) ? $data['parent_id'] : null;
        $this->order       = (isset($data['order'])) ? $data['order'] : null;
        $this->status      = (isset($data['status'])) ? $data['status'] : null;
        $this->created     = (isset($data['created'])) ? $data['created'] : null;
        $this->modified    = (isset($data['modified'])) ? $data['modified'] : null;

    }

    public function getArrayCopy()
    {
        return get_object_vars($this);

    }

    public function setId($id)
    {
        $this->id = $id;

    }

    public function getId()
    {
        return $this->id;

    }

    public function setName($name)
    {
        $this->name = $name;

    }

    public function getName()
    {
        return $this->name;

    }

    public function setIcon($icon)
    {
        $this->icon = $icon;

    }

    public function getIcon()
    {
        return $this->icon;

    }

    public function setType($type)
    {
        $this->type = $type;

    }

    public function getType()
    {
        return $this->type;

    }

    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;

    }

    public function getResourceId()
    {
        return $this->resource_id;

    }

    public function setUrl($url)
    {
        $this->url = $url;

    }

    public function getUrl()
    {
        return $this->url;

    }

    public function setTarget($target)
    {
        $this->target = $target;

    }

    public function getTarget()
    {
        return $this->target;

    }

    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;

    }

    public function getParentId()
    {
        return $this->parent_id;

    }

    public function setOrder($order)
    {
        $this->order = $order;

    }

    public function getOrder()
    {
        return $this->order;

    }

    public function setStatus($status)
    {
        $this->status = $status;

    }

    public function getStatus()
    {
        return $this->status;

    }

    public function setCreated($created)
    {
        $this->created = $created;

    }

    public function getCreated()
    {
        return $this->created;

    }

    public function setModified($modified)
    {
        $this->modified = $modified;

    }

    public function getModified()
    {
        return $this->modified;

    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");

    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'id',
                        'required' => true,
                        'filters'  => array (
                            array ('name' => 'Int'),
                        ),
            )));


            $inputFilter->add($factory->createInput(array (
                        'name'     => 'name',
                        'required' => true,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'icon',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'type',
                        'required' => true,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'resource_id',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'url',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'target',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'parent_id',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'status',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'created',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array (
                        'name'     => 'modified',
                        'required' => false,
            )));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;

    }

}
