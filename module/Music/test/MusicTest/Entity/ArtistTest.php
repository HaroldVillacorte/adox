<?php

namespace MusicTest\Entity;

use Music\Entity\Artist;
use PHPUnit_Framework_TestCase;

class ArtistTest extends PHPUnit_Framework_TestCase
{

    public function testArtistInitailState()
    {
        $artist = new Artist();
        $this->assertEquals(NULL, $artist->getId('"id" should initailly be null'));
        $this->assertEquals(NULL, $artist->getName('"name" should initailly be null'));
        $this->assertEquals(NULL, $artist->getEmail('"name" should initailly be null'));
        $this->assertEquals(NULL, $artist->getWebsite('"name" should initailly be null'));
        $this->assertEquals(NULL, $artist->getGenres('"name" should initailly be null'));
        $this->assertEquals(NULL, $artist->getAlbums('"name" should initailly be null'));
        $this->assertEquals(NULL, $artist->getSongs('"name" should initailly be null'));

    }

    public function testArtistSetters()
    {
        $artist = new Artist();
        $artist->setName('test');
        $this->assertEquals('test', $artist->getName('"name" should initailly equal "test"'));

        $artist->addAlbum('test');
        $this->assertEquals('test', $artist->getName('"name" should initailly equal "test"'));

    }

}
