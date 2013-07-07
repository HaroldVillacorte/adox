<?php

namespace Music\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;

class SongForm extends Form
{

    public function __construct($name = null, $options = array(), $value_album = '', $files_required = true)
    {
        parent::__construct($name);

        $this->setInputFilter($this->createInputFilter($files_required));
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
        $album = new Element\Select('album');
        $album->setValueOptions($options);
        $album->setValue($value_album);
        $album->setLabel('Album');
        $album->setAttributes(array(
            'id' => 'song-album-select',
        ));
        $this->add($album);

        // Name.
        $name = new Element\Text('name');
        $name->setLabel('Name');
        $this->add($name);

        // Price.
        $price = new Element\Text('price');
        $price->setLabel('Price');
        $this->add($price);

        // Mp3 File Input.
        $mp3 = new Element\File('mp3');
        $mp3->setLabel('Mp3 file: ');
        $this->add($mp3);

        // Mp3 File Input.
        $ogg = new Element\File('ogg');
        $ogg->setLabel('Ogg file: ');
        $this->add($ogg);

        // Submit.
        $submit = new Element\Submit('submit');
        $submit->setValue('Add');
        $submit->setAttributes(array('class' => 'button secondary'));
        $this->add($submit);
    }

    public function createInputFilter($files_required)
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

        // Album.
        $album = new InputFilter\Input('album');
        $album->getValidatorChain()->addByName('Digits');
        $inputFilter->add($album);

        // Name.
        $name = new InputFilter\Input('name');
        $name->setRequired(true);
        $name->getFilterChain()->attachByName('StripTags');
        $name->getFilterChain()->attachByName('StringTrim');
        $inputFilter->add($name);

        // Price.
        $price = new InputFilter\Input('price');
        $price->setRequired(true);
        $price->getValidatorChain()->addByName('Zend\I18n\Validator\Float');
        $inputFilter->add($price);

        // Mp3
        $mp3 = new InputFilter\FileInput('mp3');
        $mp3->setRequired($files_required);
        $mp3->getValidatorChain()
            ->addByName('fileextension', array('extension' => array('mp3')));
        $mp3->getValidatorChain()
            ->addByName('Zend\Validator\File\Size', array('min' => 1, 'max' => 20000000));
        $inputFilter->add($mp3);

        // Ogg
        $ogg = new InputFilter\FileInput('ogg');
        $ogg->setRequired($files_required);
        $ogg->getValidatorChain()
            ->addByName('fileextension', array('extension' => array('ogg')));
        $ogg->getValidatorChain()
            ->addByName('Zend\Validator\File\Size', array('min' => 1, 'max' => 20000000));
        $inputFilter->add($ogg);

        return $inputFilter;
    }
}