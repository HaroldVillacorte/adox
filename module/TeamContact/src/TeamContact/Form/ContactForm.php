<?php

namespace TeamContact\Form;

use Zend\Form\Form;

class ContactForm extends Form
{

    function __construct($name = 'contact')
    {
        parent::__construct($name);

        $this->setAttribute('method', 'post');

        // CSRF
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                    'csrf_options' => array(
                            'timeout' => 600
                    )
            )
        ));

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'replyTo',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'five',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'name' => 'subject',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'five',
            ),
            'options' => array(
                'label' => 'Subject',
            ),
        ));

        $this->add(array(
            'name' => 'message',
            'attributes' => array(
                'type'  => 'textarea',
                'class' => 'five',
            ),
            'options' => array(
                'label' => 'Message',
            ),
        ));

        $this->add(array(
            'name' => 'teamMember',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'three',
            ),
            'options' => array(
                'label' => 'Team member:  ',
                'value_options' => array(
                    'haroldVillacorte' => 'Harold Villacorte',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'button',
            ),
        ));
    }

}
