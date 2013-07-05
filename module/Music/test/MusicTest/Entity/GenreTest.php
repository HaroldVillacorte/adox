<?php

namespace MusicTest\Entity;

use Music\Entity\Genre;
use Music\Entity\Artist;
use PHPUnit_Framework_TestCase;

class GenreTest extends PHPUnit_Framework_TestCase
{

    public function testGenreInitialState()
    {
        $genre = new Genre();
        $this->assertEquals(NULL, $genre->getId('"id" should initially be null'));
        $this->assertEquals(NULL, $genre->getName('"name" should initially be null'));
        $this->assertEquals(NULL, $genre->getArtists('"artists" should initially be null'));
        $this->assertEquals(NULL, $genre->getAlbums('"albums" should initially be null'));
        $this->assertEquals(NULL, $genre->getSongs('"songs" should initially be null'));
    }

}
