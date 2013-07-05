<?php

namespace TeamContact\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use TeamContact\Form\ContactForm;
use TeamContact\Model\ContactFormModel;

class TeamContactController extends AbstractActionController
{

    public function indexAction()
    {
        $this->checkAjax();
        $result = '';

        $form = new ContactForm();
        $form->get('submit')->setAttribute('value', 'Send');

        if ($this->getRequest()->isPost())
        {
            $contactModel = new ContactFormModel();
            $form->setInputFilter($contactModel->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $message = $this->getRequest()->getPost();
                $contactModel->populate($message);
                $result = $contactModel->send();
                switch ($result)
                {
                    case TRUE:
                        $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Email was sent successfully.');
                        break;
                    case FALSE:
                        $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was a problem sending the email.');
                        break;
                }
                return $this->redirect()->toRoute('team-contact');
            }
        }


        $viewModel = new ViewModel(array(
            'form' => $form,
        ));

        if ($this->getRequest()->isXmlHttpRequest())
        {
            $viewModel->setTemplate('team-contact/team-contact/index-ajax');
        }

        return $viewModel;


    }

    public function checkAjax() {

        if ($this->getRequest()->isXmlHttpRequest())
        {
            $this->layout('layout/ajax-layout');
        };

    }

}
