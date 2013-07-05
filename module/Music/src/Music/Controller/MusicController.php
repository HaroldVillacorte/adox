<?php

namespace Music\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MusicController extends AbstractActionController
{
    public function indexAction()
    {
        $data = array(
            'title' => 'Music',
        );

        return new ViewModel($data);
    }
}
