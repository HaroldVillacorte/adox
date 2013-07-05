<?php

namespace Music\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class SongRestController extends AbstractRestfulController
{
    protected $em;

    public function getSong($song_id = '')
    {
        $song = $this->getEntityManager()->find('Music\Entity\Song', $song_id);
        return file_get_contents($song->getMp3FilePath());
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

}
