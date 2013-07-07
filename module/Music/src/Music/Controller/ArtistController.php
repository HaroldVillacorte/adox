<?php

namespace Music\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Music\Form\ArtistForm;
use Music\Entity\Artist;

class ArtistController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $artists = $this->getEntityManager()->getRepository('Music\Entity\Artist')->findAll();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'artists' => $artists,
        ));
    }

    public function addAction()
    {
        // Populate the genres checkboxes.
        $genres = $this->getEntityManager()->getRepository('Music\Entity\Genre')->findAll();
        $genres_array = array();
        foreach ($genres as $genre)
        {
             $genres_array[$genre->getId()] = $genre->getName();
        }

        $form = new ArtistForm($genres_array);

        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $artist = new Artist();
            $form->setInputFilter($artist->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                foreach($data['genre'] as $genre)
                {
                    $add_genre = $this->getEntityManager()
                        ->find('Music\Entity\Genre', (int) $genre);
                    $artist->addGenre($add_genre);
                }
                $artist->populate($data);
                $this->getEntityManager()->persist($artist);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Artist was successfully added.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem adding the artist.');
                }

                // Redirect to list of albums
                return $this->redirect()->toRoute('artist');
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
            'title' => 'Add Artist',
        ));
    }

    public function editAction()
    {
        $id = (int)$this->params('id');
        $artist = $this->getEntityManager()->find('Music\Entity\Artist', $id);
        if (!$id || !$artist)
        {
            return $this->redirect()->toRoute('artist');
        }

        // Populate the genres checkboxes.
        $genres = $this->getEntityManager()->getRepository('Music\Entity\Genre')->findAll();
        $genres_array = array();
        foreach ($genres as $genre)
        {
             $genres_array[$genre->getId()] = $genre->getName();
        }

        // Set checked genres.
        $artist_genres = $artist->getGenres();
        $checked = array();
        foreach ($artist_genres as $genre)
        {
            $checked[] = $genre->getId();
        }

        $form = new ArtistForm($genres_array, $checked);

        $form->setBindOnValidate(false);
        $form->setValidationGroup('csrf', 'id', 'name', 'genre', 'email', 'website');
        $form->bind($artist);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $form->setData($request->getPost());
            if ($form->isValid())
            {
                $form->bindValues();

                // Set the genres.
                $data = $request->getPost();
                $new_genres_array = array();
                foreach ($data->genre as $genre) {
                    $new_genre = $this->getEntityManager()->find('Music\Entity\Genre', (int) $genre);
                    $new_genres_array[] = $new_genre;
                }
                $artist->setGenres($new_genres_array);
                $this->getEntityManager()->persist($artist);

                // Presist.
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Artist was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem saving the artist.');
                }

                // Redirect to list of albums
                return $this->redirect()->toRoute('artist');
            }
            else {
                $form->setData($request->getPost());
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'form' => $form,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('artist');
        }

        $artist = $this->getEntityManager()->find('Music\Entity\Artist', $id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {

                $albums = $artist->getAlbums();
                foreach ($albums as $album)
                {
                    $songs = $album->getSongs();
                    foreach ($songs as $song)
                    {
                        if (file_exists($song->getMp3FilePath()))
                        {
                            unlink($song->getMp3FilePath());
                        }
                        if (file_exists($song->getOggFilePath()))
                        {
                            unlink($song->getOggFilePath());
                        }
                        $this->getEntityManager()->remove($song);
                    }
                    if (file_exists($album->getArtPath()))
                    {
                        unlink($album->getArtPath());
                    }
                    $this->getEntityManager()->remove($album);
                }

                $this->getEntityManager()->remove($artist);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Artist was successfully deleted.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a deleting the artist.');
                }
            }

            // Redirect to list of variables.
            return $this->redirect()->toRoute('artist');
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'artist' => $artist,
        ));
    }

    public function viewAction()
    {
        $id = (int) $this->params('id');
        $artist = $this->getEntityManager()->find('Music\Entity\Artist', $id);
        if (!$id || !$artist)
        {
            return $this->redirect()->toRoute('artist');
        }
        $albums = $artist->getAlbums();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'artist' => $artist,
            'albums' => $albums,
        ));
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
