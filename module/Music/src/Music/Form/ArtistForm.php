<?php

namespace Music\Form;

use Zend\Form\Form;

class ArtistForm extends Form
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct($genres = array(), $checked = array())
    {
        // we want to ignore the name passed
        parent::__construct('artist');

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                    'csrf_options' => array(
                            'timeout' => 600
                    )
            )
        ));

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'name' => 'website',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Website',
            ),
        ));

        $this->add(array(
            'type' => 'MultiCheckbox',
            'name' => 'genre',
            'options' => array(
                'label' => 'Genres',
                'value_options' => $genres,
            ),
            'attributes' => array(
                'value' => $checked,
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'button secondary',
            ),
        ));

    }
}
