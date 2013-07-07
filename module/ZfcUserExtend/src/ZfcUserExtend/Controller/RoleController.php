<?php

namespace ZfcUserExtend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUserExtend\Form\RoleForm;
use ZfcUserExtend\Entity\Role;

class RoleController extends AbstractActionController
{
    protected $em;

    public function indexAction()
    {
        $roles = $this->getEntityManager()->getRepository('ZfcUserExtend\Entity\Role')->findAll();
        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'roles' =>$roles,
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
        if (count($roles_array) == 0)
        {
            $roles_array['0'] = 'Parent roles are not available.';
        }
        $form = new RoleForm('role', $roles_array);
        $form->setValidationGroup('csrf', 'roleId', 'parent');
        if (array_key_exists('0', $roles_array))
        {
            $form->get('parent')->setAttribute('disabled', 'disabled');
        }

        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $data = $form->getData();
                $this->saveRole($data);
                return $this->redirect()->toRoute('role');
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
        $role = $this->getEntityManager()->find('ZfcUserExtend\Entity\Role', $id);
        if (!$id || !$role)
        {
            return $this->redirect()->toRoute('role');
        }

        $roles = $this->getEntityManager()
            ->getRepository('ZfcUserExtend\Entity\Role')->findAll();
        $roles_array = array();
        foreach ($roles as $_role)
        {
            if ($_role->getId() != $id)
            {
                $roles_array[$_role->getId()] = $_role->getRoleId();
            }
        }
        if (count($roles_array) == 0)
        {
            $roles_array['0'] = 'Parent roles are not available.';
        }
        $parent_id  = ($role->getParent()) ? $role->getParent()->getId() : '';
        $form = new RoleForm('role', $roles_array, $parent_id);
        $form->setBindOnValidate(false);
        $form->bind($role);
        $form->get('submit')->setAttribute('value', 'Save');
        if (array_key_exists('0', $roles_array))
        {
            $form->get('parent')->setAttribute('disabled', 'disabled');
        }

        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $data = $this->getRequest()->getPost();
                $this->saveRole($data);
                return $this->redirect()->toRoute('role');
            }
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params('id');
        $role = $this->getEntityManager()->find('ZfcUserExtend\Entity\Role', $id);
        if (!$id || !$role) {
            return $this->redirect()->toRoute('role');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->deleteRole($role);
            }

            return $this->redirect()
                        ->toRoute('role');
        }

        $this->layout('layout/admin-layout');
        return new ViewModel(array(
            'id' => $id,
            'role' => $role,
        ));
    }

    public function saveRole($data = array())
    {
        $role = (isset($data['id']) && $data['id'] != '') ? $this->getEntityManager()
            ->find('ZfcUserExtend\Entity\Role', $data['id']) : new Role();
        if ($data['parent_id'] && $data['parent_id'] != '0')
        {
            $parent = $this->getEntityManager()->find('ZfcUserExtend\Entity\Role', (int) $data['parent_id']);
            $role->setParent($parent);
        }
        $role->setRoleId($data['roleId']);
        $this->getEntityManager()->persist($role);
        try {
            $this->getEntityManager()->flush();
            $this->flashMessenger()->setNamespace('success')
                ->addMessage('Role was successfully saved.');
        }
        catch (Exception $e) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage('There was a problem adding the role.');
        }
    }

    public function deleteRole(Role $role)
    {
        $this->getEntityManager()->remove($role);
        try {
            $this->getEntityManager()->flush();
            $this->flashMessenger()->setNamespace('success')
                ->addMessage('Role was successfully deleted.');
        }
        catch (Exception $e) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage('There was a deleting the role');
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
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

}
