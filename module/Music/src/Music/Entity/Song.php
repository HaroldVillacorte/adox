<?php

namespace Music\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site Variables.
 *
 * @ORM\Entity
 * @ORM\Table(name="song")
 * @property string $name
 * @property string $price
 * @property int $id
 */
class Song
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $mp3FilePath;

    /**
     * @ORM\Column(type="string")
     */
    protected $mp3Url;

    /**
     * @ORM\Column(type="string")
     */
    protected $oggFilePath;

    /**
     * @ORM\Column(type="string")
     */
    protected $oggUrl;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="songs")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    protected $album;

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMp3FilePath()
    {
        return $this->mp3FilePath;
    }

    public function getMp3Url()
    {
        return $this->mp3Url;
    }

    public function getOggFilePath()
    {
        return $this->oggFilePath;
    }

    public function getOggUrl()
    {
        return $this->oggUrl;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getAlbum()
    {
        return $this->album;
    }

    // SETTERS

    public function setName($name = '')
    {
        $this->name = (string) $name;
    }

    public function setMp3FilePath($filepath = '')
    {
        $this->mp3FilePath = (string) $filepath;
    }

    public function setMp3Url($url = '')
    {
        $this->mp3Url = (string) $url;
    }

    public function setOggFilePath($filepath = '')
    {
        $this->oggFilePath = (string) $filepath;
    }

    public function setOggUrl($url = '')
    {
        $this->oggUrl = (string) $url;
    }

    public function setPrice($price = '')
    {
        $this->price = (double) $price;
    }

    public function setAlbum(Album $album)
    {
        $this->album = $album;
    }

    /**
    * Convert the object to an array.
    *
    * @return array
    */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
