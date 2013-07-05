<?php

namespace DoctrineORMModule\Proxy\__CG__\Music\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Album extends \Music\Entity\Album implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function getArtPath()
    {
        $this->__load();
        return parent::getArtPath();
    }

    public function getArtUrl()
    {
        $this->__load();
        return parent::getArtUrl();
    }

    public function getPrice()
    {
        $this->__load();
        return parent::getPrice();
    }

    public function getArtist()
    {
        $this->__load();
        return parent::getArtist();
    }

    public function getSongs()
    {
        $this->__load();
        return parent::getSongs();
    }

    public function setName($name = '')
    {
        $this->__load();
        return parent::setName($name);
    }

    public function setArtPath($path = '')
    {
        $this->__load();
        return parent::setArtPath($path);
    }

    public function setArtUrl($url = '')
    {
        $this->__load();
        return parent::setArtUrl($url);
    }

    public function setPrice($price = '')
    {
        $this->__load();
        return parent::setPrice($price);
    }

    public function setArtist(\Music\Entity\Artist $artist)
    {
        $this->__load();
        return parent::setArtist($artist);
    }

    public function getArrayCopy()
    {
        $this->__load();
        return parent::getArrayCopy();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'artPath', 'artUrl', 'price', 'artist', 'songs');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}