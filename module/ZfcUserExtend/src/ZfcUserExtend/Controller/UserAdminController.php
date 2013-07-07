<?php

namespace ZfcUserExtend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUserExtend\Form\UserAdminForm;
use ZfcUserExtend\Entity\User;
use Zend\Crypt\Password\Bcrypt;

class UserAdminController extends AbstractActionController
{
    protected $em;

    public function indexAction()
    {
        $users = $this->getEntityManager()->getRepository('ZfcUserExtend\Entity\User')->findAll();

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'users' => $users,
        ));
    }

    public function addAction()
    {
        $roles = $this->getEntityManager()
            ->getRepository('ZfcUserExtend\Entity\Role')->findAll();
        $roles_array = array();
        foreach ($roles as $role)
        {
            $roles_array[$role->getId()] = $role->getRoleId();
        }

        $form = new UserAdminForm('user', $roles_array);
        $form->setValidationGroup('csrf', 'username', 'password', 'passconf','displayName', 'email', 'state', 'user_roles');

        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $data = $form->getData();
                $this->saveUser($data);
                return $this->redirect()->toRoute('useradmin');
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params('id');
        $user = $this->getEntityManager()->find('ZfcUserExtend\Entity\User', $id);
        if (!$id || !$user)
        {
            return $this->redirect()->toRoute('useradmin');
        }

        $roles = $this->getEntityManager()
            ->getRepository('ZfcUserExtend\Entity\Role')->findAll();
        $roles_array = array();
        foreach ($roles as $_role)
        {
            $roles_array[$_role->getId()] = $_role->getRoleId();
        }
        $user_roles_values = array();
        foreach ($user->getRoles() as $user_role) {
            $user_roles_values[] = $user_role->getId();
        }
        $form = new UserAdminForm('user', $roles_array, $user->getState(), $user_roles_values, false);
        $form->setValidationGroup('csrf', 'id','username', 'password', 'passconf','displayName', 'email', 'state', 'user_roles');
        $form->setBindOnValidate(false);
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');

        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $data = $this->getRequest()->getPost();
                $this->saveUser($data);
                return $this->redirect()->toRoute('useradmin');
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
        ));
    }

    public function saveUser($data = array())
    {
        $user = (isset($data['id']) && $data['id'] != '') ? $this->getEntityManager()
            ->find('ZfcUserExtend\Entity\User', (int) $data['id']) : new User();
        $user->setUsername($data['username']);

        // Hash the password.
        if (isset($data['password']) && $data['password'] != '')
        {
            $crypt = new Bcrypt();
            $password = $crypt->create($data['password']);
            $user->setPassword($password);
        }

        $user->setDisplayName($data['displayName']);
        $user->setEmail($data['email']);
        $user->setState($data['state']);
        foreach ($data['user_roles'] as $user_role)
        {
            $role = $this->getEntityManager()
                ->find('ZfcUserExtend\Entity\Role', (int) $user_role);
            $user->addRole($role);
        }
        $this->getEntityManager()->persist($user);
        try {
            $this->getEntityManager()->flush();
            $this->flashMessenger()->setNamespace('success')
                ->addMessage('User was successfully saved.');
        }
        catch (Exception $e) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage('There was problem saving the user.');
        }
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        if ($this->em === null)
        {
            $this->em = $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

}
