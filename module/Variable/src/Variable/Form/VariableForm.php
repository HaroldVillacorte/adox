<?php
namespace Variable\Form;

//use Zend\Form\Element;
use Zend\Form\Form;

class VariableForm extends Form
{
    public function __construct($id = null)
    {
        // we want to ignore the name passed
        parent::__construct('variable');

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
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'category',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Category',
            ),
        ));

        $this->add(array(
            'name' => 'value',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Value',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
            ),
        ));

    }
}
