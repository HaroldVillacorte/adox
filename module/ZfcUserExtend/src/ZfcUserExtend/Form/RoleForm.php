<?php

namespace ZfcUserExtend\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter;

class RoleForm extends Form
{

    function __construct($name = null, $options = array(), $radio_value = '')
    {
        parent::__construct($name);
        $this->setInputFilter($this->createInputFilter());
        $this->setAttribute('class', 'custom');

        // Csrf
        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        // Id
        $id = new Element\Hidden('id');
        $this->add($id);

        // RoleId
        $roleId = new Element\Text('roleId');
        $roleId->setLabel('Role Id');
        $this->add($roleId);

        // Parent
        $parent = new Element\Radio('parent_id');
        $parent->setValueOptions($options);
        $parent->setValue($radio_value);
        $parent->setLabel('Parent');
        $parent->setAttribute('class', 'multi-box');
        $this->add($parent);

        // Submit.
        $submit = new Element\Submit('submit');
        $submit->setValue('Add');
        $submit->setAttributes(array('class' => 'button secondary'));
        $this->add($submit);
    }

    function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // Csrf
        $csrf = new InputFilter\Input('csrf');
        $csrf->setRequired(true);
        $csrf->getValidatorChain()->addByName('Csrf');
        $csrf->getFilterChain()->attachByName('StripTags');
        $csrf->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($csrf);

        // Id
        $id = new InputFilter\Input('id');
        $id->setRequired(true);
        $id->getValidatorChain()->addByName('Digits');
        $inputFilter->add($id);

        // RoleId
        $roleId = new InputFilter\Input('roleId');
        $roleId->setRequired(true);
        $roleId->getValidatorChain()->addByName('Alnum', array('allowWhiteSpace' => true));
        $roleId->getFilterChain()->attachByName('StripTags');
        $roleId->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($roleId);

        // Parent.
        $parent = new InputFilter\Input('parent_id');
        $parent->setRequired(false);
        $parent->getValidatorChain()->addByName('Digits');
        $inputFilter->add($parent);

        return $inputFilter;
    }

}
