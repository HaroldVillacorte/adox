<?php

namespace Music\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Music\Entity\Genre;
use Music\Form\GenreForm;

class GenreController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function indexAction()
    {
        $genres = $this->getEntityManager()->getRepository('Music\Entity\Genre')->findAll();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'genres' => $genres,
        ));
    }

    public function addAction()
    {
        $form = new GenreForm();

        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $genre = new Genre();
            $form->setInputFilter($genre->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $genre->populate($data);
                $this->getEntityManager()->persist($genre);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Genre was successfully added.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem adding the genre.');
                }

                // Redirect to list of albums
                return $this->redirect()->toRoute('genre');
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int)$this->params('id');
        $genre = $this->getEntityManager()->find('Music\Entity\Genre', $id);

        if (!$id || !$genre)
        {
            return $this->redirect()->toRoute('genre');
        }

        $form = new GenreForm();

        $form->setBindOnValidate(false);
        $form->setValidationGroup('csrf', 'id', 'name');
        $form->bind($genre);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $form->setData($request->getPost());
            if ($form->isValid())
            {
                $form->bindValues();
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Genre was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem saving the genre.');
                }
                // Redirect to list of albums
                return $this->redirect()->toRoute('genre');
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
        $id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('genre');
        }

        $genre = $this->getEntityManager()->find('Music\Entity\Genre',$id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->getEntityManager()->remove($genre);
                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Genre was successfully deleted.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a deleting the genre.');
                }
            }

            // Redirect to list of variables.
            return $this->redirect()->toRoute('genre');
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'genre' => $genre,
        ));
    }

    public function viewAction()
    {
        $id = (int) $this->params('id');
        $genre = $this->getEntityManager()->find('Music\Entity\Genre', $id);
        if (!$id || !$genre)
        {
            return $this->redirect()->toRoute('genre');
        }
        $artists = $genre->getArtists();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'genre' => $genre,
            'artists' => $artists,
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
