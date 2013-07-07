<?php

namespace Music\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Site variables.
*
* @ORM\Entity
* @ORM\Table(name="album")
* @property string $name
* @property int $id
* @property float $price
* @property object $artist
*/
class Album
{

    /**
    * @ORM\Id
    * @ORM\Column(type="integer");
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
    protected $artPath;

    /**
    * @ORM\Column(type="string")
    */
    protected $artUrl;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

    /**
     * @ORM\ManyToOne(targetEntity="Artist", inversedBy="albums")
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="id")
     **/
    protected $artist;

    /**
     * @ORM\OneToMany(targetEntity="Song", mappedBy="album")
     */
    protected $songs;

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getArtPath()
    {
        return $this->artPath;
    }

    public function getArtUrl()
    {
        return $this->artUrl;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getArtist()
    {
        return $this->artist;
    }

    public function getSongs()
    {
        return $this->songs;
    }

    // SETTERS

    public function setName($name = '')
    {
        $this->name = (string) $name;
    }

    public function setArtPath($path = '')
    {
        $this->artPath = (string) $path;
    }

    public function setArtUrl($url = '')
    {
        $this->artUrl = (string) $url;
    }

    public function setPrice($price = '')
    {
        $this->price = (double) $price;
    }

    public function setArtist(Artist $artist)
    {
        $this->artist = $artist;
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
