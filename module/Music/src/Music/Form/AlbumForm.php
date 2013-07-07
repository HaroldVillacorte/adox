<?php

namespace Music\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class AlbumForm extends Form
{
    public function __construct($name, $artists = array(), $selected = '', $file_required = true)
    {
        // we want to ignore the name passed
        parent::__construct($name);

        $this->setInputFilter($this->createInputFilter($file_required));
        $this->setAttributes(array(
            'class' => 'custom',
        ));

        // CSRF
        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        // Id.
        $id = new Element\Hidden('id');
        $this->add($id);

        // Artist.
        $artist = new Element\Select('artist');
        $artist->setValueOptions($artists);
        $artist->setValue($selected);
        $artist->setLabel('Artist');
        $artist->setAttributes(array(
            'id' => 'album-artist-select',
        ));
        $this->add($artist);

        // Name.
        $name = new Element\Text('name');
        $name->setLabel('Name');
        $this->add($name);

        // Price.
        $price = new Element\Text('price');
        $price->setLabel('Price');
        $this->add($price);

        // Album Art File Input.
        $album_art = new Element\File('album_art');
        $album_art->setLabel('Album art: ');
        $this->add($album_art);

        // Submit.
        $submit = new Element\Submit('submit');
        $submit->setValue('Add');
        $submit->setAttributes(array('class' => 'button secondary'));
        $this->add($submit);

    }

    public function createInputFilter($file_required)
    {
        $inputFilter = new InputFilter\InputFilter();

        // Csrf
        $csrf = new InputFilter\Input('csrf');
        $csrf->setRequired(true);
        $csrf->getValidatorChain()->addByName('Csrf');
        $csrf->getFilterChain()->attachByName('StripTags');
        $csrf->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($csrf);

        // Id.
        $id = new InputFilter\Input('id');
        $id->setRequired(true);
        $id->getValidatorChain()->addByName('Digits');
        $inputFilter->add($id);

        // Artist.
        $artist = new InputFilter\Input('artist');
        $artist->setRequired(true);
        $artist->getValidatorChain()->addByName('Digits');
        $inputFilter->add($artist);

        // Name.
        $name = new InputFilter\Input('name');
        $name->setRequired(true);
        $name->getValidatorChain()->addByName('Alnum', array('allowWhiteSpace' => true));
        $name->getFilterChain()->attachByName('StripTags');
        $name->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($name);

        // Price.
        $price = new InputFilter\Input('price');
        $price->setRequired(true);
        $price->getValidatorChain()->addByName('Zend\I18n\Validator\Float');
        $inputFilter->add($price);

        // Album Art
        $album_art = new InputFilter\FileInput('album_art');
        $album_art->setRequired($file_required);
        $album_art->getValidatorChain()
            ->addByName('fileextension', array('extension' => array('jpg')));
        $album_art->getValidatorChain()
            ->addByName('Zend\Validator\File\Size', array('min' => 1, 'max' => 20000000));
        $inputFilter->add($album_art);

        return $inputFilter;
    }
}
