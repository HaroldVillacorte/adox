<?php

namespace ZfcUserExtend\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter;

class UserAdminForm extends Form
{

    function __construct($name = null, $roles_array = array(), $state_value = 1, $user_roles_values = array(), $password_required = true)
    {
        parent::__construct($name);
        $this->setInputFilter($this->createInputFilter($password_required));
        $this->setAttribute('class', 'custom');

        // Csrf
        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        // Id
        $id = new Element\Hidden('id');
        $this->add($id);

        // Username
        $username = new Element\Text('username');
        $username->setLabel('Username');
        $this->add($username);

        // Password
        $password = new Element\Password('password');
        $password->setLabel('Password');
        $this->add($password);

        // Passconf
        $passconf = new Element\Password('passconf');
        $passconf->setLabel('Confirm Password');
        $this->add($passconf);

        // Display name
        $displayName = new Element\Text('displayName');
        $displayName->setLabel('Display Name');
        $this->add($displayName);

        // Email
        $email = new Element\Text('email');
        $email->setLabel('Email');
        $this->add($email);

        // State
        $state = new Element\Radio('state');
        $state->setValueOptions(array(1 => '1', 2 => '2'));
        $state->setValue($state_value);
        $state->setLabel('State');
        $this->add($state);

        // User roles
        $user_roles = new Element\MultiCheckbox('user_roles');
        $user_roles->setValueOptions($roles_array);
        $user_roles->setValue($user_roles_values);
        $user_roles->setLabel('Roles');
        $this->add($user_roles);

        // Submit.
        $submit = new Element\Submit('submit');
        $submit->setValue('Add');
        $submit->setAttributes(array('class' => 'button secondary'));
        $this->add($submit);
    }

    function createInputFilter($password_required)
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

        // Username
        $username = new InputFilter\Input('username');
        $username->setRequired(true);
        $username->getValidatorChain()->addByName('Alnum');
        $username->getFilterChain()->attachByName('StripTags');
        $username->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($username);

        // Password
        $password = new InputFilter\Input('password');
        $password->setRequired($password_required);
        $password->getFilterChain()->attachByName('StripTags');
        $password->getFilterChain()->attachByName('StringTrim');
        $password->getValidatorChain()->addByName('Identical', array('token' => 'passconf'));
        $inputFilter->add($password);

        // Passconf
        $passconf = new InputFilter\Input('passconf');
        $passconf->setRequired($password_required);
        $passconf->getFilterChain()->attachByName('StripTags');
        $passconf->getFilterChain()->attachByName('StringTrim');
        $passconf->getValidatorChain()->addByName('Identical', array('token' => 'password'));
        $inputFilter->add($passconf);

        // Display Name
        $displayName = new InputFilter\Input('displayName');
        $displayName->setRequired(true);
        $displayName->getValidatorChain()->addByName('Alnum', array('allowWhiteSpace' => true));
        $displayName->getFilterChain()->attachByName('StripTags');
        $displayName->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($displayName);

        // Email
        $email = new InputFilter\Input('email');
        $email->setRequired(true);
        $email->getValidatorChain()->addByName('EmailAddress');
        $email->getFilterChain()->attachByName('StripTags');
        $email->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($email);

        // State
        $state = new InputFilter\Input('state');
        $state->setRequired(true);
        $state->getValidatorChain()->addByName('Int');
        $inputFilter->add($state);

        // User Roles
        $user_roles = new InputFilter\Input('user_roles');
        $user_roles->setRequired(true);
        $inputFilter->add($user_roles);

        return $inputFilter;
    }

}
