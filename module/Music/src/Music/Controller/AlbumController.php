<?php

namespace Music\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Music\Form\AlbumForm;
use Music\Entity\Album;

class AlbumController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $albums = $this->getEntityManager()->getRepository('Music\Entity\Album')->findAll();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'albums' => $albums,
        ));
    }

    public function addAction()
    {
        // Populate the artist selector.
        $artists = $this->getEntityManager()->getRepository('Music\Entity\Artist')->findAll();
        $artists_array = array();
        foreach ($artists as $artist)
        {
             $artists_array[$artist->getId()] = $artist->getName();
        }
        $form = new AlbumForm('album', $artists_array);
        $form->setValidationGroup('csrf', 'artist', 'name', 'price', 'album_art');

        if ($this->getRequest()->isPost()) {

            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);

            if ($form->isValid()) {
                $this->saveAlbum($data);
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
            'id' => $id = (!$this->params('id')) ? null : (int) $this->params('id'),
        ));
    }

    public function editAction()
    {
        $id = (int)$this->params('id');
        $album = $this->getEntityManager()->find('Music\Entity\Album', $id);
        if (!$id || !$album)
        {
            return $this->redirect()->toRoute('album');
        }

        // Populate the artist selector.
        $artists = $this->getEntityManager()->getRepository('Music\Entity\Artist')->findAll();
        $artists_array = array();
        foreach ($artists as $artist)
        {
             $artists_array[$artist->getId()] = $artist->getName();
        }

        $form = new AlbumForm('album',$artists_array, $album->getArtist()->getId(), false);
        $form->setBindOnValidate(false);
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);

            if ($form->isValid())
            {
                if (isset($data['album_art']) && $data['album_art']['name'] != '') {
                    $this->saveAlbum($data);
                }
                else {
                    $artist = $this->getEntityManager()->find('Music\Entity\Artist', $data['artist']);
                    $album->setName($data['name']);
                    $album->setArtist($artist);
                    $album->setPrice($data['price']);
                    $this->getEntityManager()->persist($album);
                    try {
                        $this->getEntityManager()->flush();
                        $this->flashMessenger()->setNamespace('success')->addMessage('Album was successfully saved.');
                    }
                    catch (Exception $e) {
                        $this->flashMessenger()->setNamespace('error')->addMessage('There was a problem saving the album.');
                    }
                    return $this->redirect()->toRoute('artist', array('action' => 'view', 'id' => $artist->getId()));
                }
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'form' => $form,
            'album' => $album,
        ));
    }

    public function deleteAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $album = $this->getEntityManager()->find('Music\Entity\Album',$id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {

                // Delete album's songs.
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
                // Delete album.
                if (file_exists($album->getArtPath()))
                {
                    unlink($album->getArtPath());
                }
                $this->getEntityManager()->remove($album);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Album was successfully deleted.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a deleting the album.');
                }
            }

            // Redirect to list of variables.
            return $this->redirect()
                ->toRoute('artist', array('action' => 'view', 'id' => $album->getArtist()->getId()));
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'album' => $album,
        ));
    }

    public function viewAction()
    {
        $id = (int) $this->params('id');
        $album = $this->getEntityManager()->find('Music\Entity\Album', $id);
        if (!$id || !$album)
        {
            return $this->redirect()->toRoute('album');
        }
        $songs = $album->getSongs();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'album' => $album,
            'songs' => $songs,
        ));
    }

    public function saveAlbum($data = array())
    {
        // Get album.
        $artist = $this->getEntityManager()->find('Music\Entity\Artist', $data['artist']);

        // Set artist/album directory path.
        $album_art_dir_name = './public_html/Music/' . $artist->getId() . '/AlbumArt/';

        // Set new file path.
        $album_art_new_file_path = $album_art_dir_name . $data['album_art']['name'];

        // Make Mp3 directory.
        if (!file_exists($album_art_dir_name))
        {
            if (!@mkdir($album_art_dir_name, 0755, true)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage('Album art directory is not writable by the server.');
                return $this->redirect()->toRoute('album');
            }
        }

        // Move file.
        if (@copy($data['album_art']['tmp_name'], $album_art_new_file_path))
        {
            $album = ($data['id'] != '') ? $this->getEntityManager()->find('Music\Entity\Album', $data['id']) : new Album();
            if ($data['id'] != '') {
                if ($album->getArtPath() != $album_art_new_file_path)
                {
                    unlink($album->getArtPath());
                }
            }
            $album->setName($data['name']);
            $album->setArtPath($album_art_new_file_path);
            $album->setArtUrl(str_replace('./public_html/', '', $album_art_new_file_path));
            $album->setArtist($artist);
            $album->setPrice($data['price']);
            $this->getEntityManager()->persist($album);
            try {
                $this->getEntityManager()->flush();
                $this->flashMessenger()->setNamespace('success')->addMessage('Album was successfully saved.');
            }
            catch (Exception $e) {
                $this->flashMessenger()->setNamespace('error')->addMessage('There was a problem saving the album.');
                unlink($album_art_new_file_path);
            }

        }
        else {
            $this->flashMessenger()->setNamespace('error')->addMessage('Files not uploaded');
        }

        return $this->redirect()->toRoute('artist', array('action' => 'view', 'id' => $artist->getId()));
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
