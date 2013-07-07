<?php

namespace Music\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Music\Form\SongForm;
use Music\Entity\Song;

class SongController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $songs = $this->getEntityManager()->getRepository('Music\Entity\Song')->findAll();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'songs' => $songs,
        ));
    }

    public function addAction()
    {
        $albums = $this->getEntityManager()->getRepository('Music\Entity\Album')->findAll();
        $albums_array = array();
        foreach ($albums as $album)
        {
            $albums_array[$album->getId()] = $album->getName();
        }
        $form = new SongForm('song', $albums_array);
        $form->setValidationGroup('csrf', 'album', 'price', 'mp3', 'ogg');

        if ($this->getRequest()->isPost()) {

            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);

            if ($form->isValid()) {
                $this->saveMusic($data);
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
            'id' => (!$this->params('id')) ? null : (int) $this->params('id'),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params('id');
        $song = $this->getEntityManager()->find('Music\Entity\Song', $id);

        if (!$id || !$song)
        {
            return $this->redirect()->toRoute('song');
        }

        $albums = $this->getEntityManager()->getRepository('Music\Entity\Album')->findAll();
        $albums_array = array();
        foreach ($albums as $album)
        {
            $albums_array[$album->getId()] = $album->getName();
        }

        $form = new SongForm('song', $albums_array, $song->getAlbum()->getId(), false);
        $form->setBindOnValidate(false);
        $form->bind($song);
        $form->get('submit')->setAttribute('value', 'Save');

        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);

            if ($form->isValid()) {
                if ((isset($data['mp3']) && isset($data['ogg'])) && ($data['mp3']['name'] !='' && $data['ogg']['name'] != '')) {
                    $this->saveMusic($data);
                }
                else {
                    $album = $this->getEntityManager()->find('Music\Entity\Album', (int) $data['album']);
                    $song->setName(isset($data['mp3']['name']) ? str_replace('.mp3', '',$data['mp3']['name']) : $data['name']);
                    $song->setAlbum($album);
                    $song->setPrice($data['price']);
                    $this->getEntityManager()->persist($song);
                    try {
                        $this->getEntityManager()->flush();
                        $this->flashMessenger()->setNamespace('success')->addMessage('Song was successfully saved.');
                    }
                    catch (Exception $e) {
                        $this->flashMessenger()->setNamespace('error')->addMessage('There was a problem saving the song.');
                    }
                    return $this->redirect()
                        ->toRoute('album', array('action' => 'view', 'id' => $song->getAlbum()->getId()));
                }

            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
           'form' => $form,
           'id' => $id,
           'song' => $song,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params('id');
        $song = $this->getEntityManager()->find('Music\Entity\Song', $id);
        if (!$id || !$song) {
            return $this->redirect()->toRoute('song');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->deleteSong($song);
            }

            // Redirect to list of variables.
            return $this->redirect()
                        ->toRoute('album', array('action' => 'view', 'id' => $song->getAlbum()->getId()));
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'song' => $song,
        ));
    }

    public function deleteSong(Song $song)
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

    public function saveMusic($data = array())
    {
        // Get album.
        $album = $this->getEntityManager()->find('Music\Entity\Album', (int) $data['album']);

        // Set artist/album directory path.
        $mp3_dir_name = './public_html/Music/' . $album->getArtist()->getId() . '/' . $album->getId() . '/mp3/';
        $ogg_dir_name = './public_html/Music/' . $album->getArtist()->getId() . '/' . $album->getId() . '/ogg/';

        // Set new file path.
        $mp3_new_file_path = $mp3_dir_name . $data['mp3']['name'];
        $ogg_new_file_path = $ogg_dir_name . $data['ogg']['name'];

        // Make Mp3 directory.
        if (!file_exists($mp3_dir_name))
        {
            if (!@mkdir($mp3_dir_name, 0755, true)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage('Music directory is not writable by the server.');
                return $this->redirect()->toRoute('song');
            }
        }

        // Make Ogg directory.
        if (!file_exists($ogg_dir_name))
        {
            if (!@mkdir($ogg_dir_name, 0755, true)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage('Music directory is not writable by the server.');
                return $this->redirect()->toRoute('song');
            }
        }

        // Move file.
        if (@copy($data['mp3']['tmp_name'], $mp3_new_file_path) && @copy($data['ogg']['tmp_name'], $ogg_new_file_path))
        {
            $song = ($data['id'] != '') ? $this->getEntityManager()->find('Music\Entity\Song', (int) $data['id']) : new Song();
            if ($data['id'] != '') {
                if ($song->getMp3FilePath() != $mp3_new_file_path)
                {
                    unlink($song->getMp3FilePath());
                }
                if ($song->getOggFilePath() != $ogg_new_file_path)
                {
                    unlink($song->getOggFilePath());
                }
            }
            $song->setName(str_replace('.mp3', '',$data['mp3']['name']));
            $song->setMp3FilePath($mp3_new_file_path);
            $song->setMp3Url(str_replace('./public_html/', '', $mp3_new_file_path));
            $song->setOggFilePath($ogg_new_file_path);
            $song->setOggUrl(str_replace('./public_html/', '', $ogg_new_file_path));
            $song->setAlbum($album);
            $song->setPrice($data['price']);
            $this->getEntityManager()->persist($song);
            try {
                $this->getEntityManager()->flush();
                $this->flashMessenger()->setNamespace('success')->addMessage('Song was successfully saved.');
            }
            catch (Exception $e) {
                $this->flashMessenger()->setNamespace('error')->addMessage('There was a problem saving the song.');
                unlink($mp3_new_file_path);
                unlink($ogg_new_file_path);
            }

        }
        else {
            $this->flashMessenger()->setNamespace('error')->addMessage('Files not uploaded');
        }

        return $this->redirect()
                        ->toRoute('album', array('action' => 'view', 'id' => $song->getAlbum()->getId()));
    }

    public function viewAction()
    {
        $id = (int) $this->params('id');
        $song = $this->getEntityManager()->find('Music\Entity\Song', $id);
        if (!$id || !$song)
        {
            return $this->redirect()->toRoute('song');
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'song' => $song,
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
