<?php

namespace Zf2menu\Form;

use Zend\Form\Form;
use \Zend\Form\Element;

class MenusForm extends Form {

    public function __construct($name = null) {
        parent::__construct('menus');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('method', 'post');
        $formValue = $this->formValue();

        $id = new Element\Hidden('id');
        $id->setAttribute('class', 'primarykey');

        $csrf         = new Element\Csrf('csrf');
        $csrf_options = array (
            'csrf_options' => array (
                'timeout' => 1000
            )
        );
        $csrf->setOptions($csrf_options);

        $name = new Element\Text('name');
        $name->setLabel('Name')
                ->setAttribute('class', 'required form-control')
                ->setAttribute('id', 'name')
                ->setAttribute('placeholder', 'Name');

        $icon = new Element\Text('icon');
        $icon->setLabel('Icon')
                ->setAttribute('class', 'form-control')
                ->setAttribute('id', 'icon')
                ->setAttribute('placeholder', 'Fa icon name');


        $type = new Element\Select('type');
        $type->setLabel('Type')
                ->setAttribute('class', 'required form-control')
                ->setValueOptions($formValue['type'])
                ->setDisableInArrayValidator(true)
                ->setAttribute('id', 'type')
                ->setAttribute('placeholder', 'Type');


        $resource_id = new Element\Select('resource_id');
        $resource_id->setLabel('Resource')
                ->setAttribute('class', 'required form-control')
                ->setOptions(array ())
                ->setDisableInArrayValidator(true)
                ->setAttribute('id', 'resource_id')
                ->setAttribute('placeholder', 'Resource');


        $url = new Element\Text('url');
        $url->setLabel('Url')
                ->setAttribute('class', 'required form-control')
                ->setAttribute('id', 'url')
                ->setAttribute('placeholder', 'Url');


        $target = new Element\Select('target');
        $target->setLabel('Target')
                ->setAttribute('class', 'required form-control')
                ->setValueOptions($formValue['target'])
                ->setDisableInArrayValidator(true)
                ->setAttribute('id', 'target')
                ->setAttribute('placeholder', 'Target');


        $parent_id = new Element\Select('parent_id');
        $parent_id->setLabel('Parent')
                ->setAttribute('class', 'required form-control')
                ->setOptions(array ())
                ->setDisableInArrayValidator(true)
                ->setAttribute('id', 'parent_id')
                ->setAttribute('placeholder', 'Parent Id');


        $status = new Element\Select('status');
        $status->setLabel('Status')
                ->setAttribute('class', 'required form-control')
                ->setValueOptions($formValue['status'])
                ->setDisableInArrayValidator(true)
                ->setAttribute('id', 'status')
                ->setAttribute('placeholder', 'Status');


        $submit = new Element\Submit('submit');
        $submit->setValue('Submit')
                ->setAttribute('class', 'btn btn-primary');

        $this->add($id);
        $this->add($csrf);
        $this->add($name);
        $this->add($icon);
        $this->add($type);
        $this->add($resource_id);
        $this->add($url);
        $this->add($target);
        $this->add($parent_id);
        $this->add($status);

    }

    public function formValue() {
        $formValue['type'] = array (
            '0' => "Internal",
            '1' => "External"
        );
        $formValue['status'] = array (
            '1' => "Enable",
            '0' => "Disable"
        );
        $formValue['target'] = array (
            '_self' => "_self",
            '_blank' => "_blank",
            '_parent' => "_parent",
            '_top' => "_top",
        );
        return $formValue;

    }

}
