<?php

namespace Variable\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Variable\Entity\Variable;
use Variable\Form\VariableForm;

class VariableController extends AbstractActionController
{
    /**
     * The data array.
     *
     * @var array
     */
    protected static $data = array();

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Variable\Model\VariableTable
     */
    public $variableTable;

    public function indexAction()
    {
        $variables = $this->getEntityManager()->getRepository('Variable\Entity\Variable')->findAll();

        return new ViewModel(array(
                'variables' => $variables,
        ));
    }

    public function addAction()
    {
        $form = new VariableForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $variable = new Variable();
            $form->setInputFilter($variable->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid())
            {
                $data = $form->getData();
                $data['value'] = serialize($data['value']);
                $variable->populate($data);
                $this->getEntityManager()->persist($variable);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('variable');
            }
        }

        self::$data['form'] = $form;
        return new ViewModel(self::$data);
    }

    public function editAction()
    {
        $id = (int)$this->params('id');
        $variable = $this->getEntityManager()->find('Variable\Entity\Variable', $id);

        if (!$id || !$variable)
        {
            return $this->redirect()->toRoute('variable');
        }

        // Repopulate the object with the unserialized 'value'.
        $variable_proxy = array(
            'id' => $variable->getId(),
            'name' => $variable->getName(),
            'category' => $variable->getCategory(),
            'value' => $variable->getValue(),
        );
        $variable->populate($variable_proxy);

        $form = new VariableForm();

        $form->setBindOnValidate(false);
        $form->setValidationGroup('csrf', 'id', 'name', 'category', 'value');
        $form->bind($variable);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $request->getPost()->value = serialize($request->getPost()->value);
            $form->setData($request->getPost());

            if ($form->isValid())
            {
                //var_dump($form);
                $form->bindValues();
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('variable');
            }
            else
            {
                $request->getPost()->value = unserialize($request->getPost()->value);
                $form->setData($request->getPost());
            }
        }

        self::$data['id'] = $id;
        self::$data['form'] = $form;
        return new ViewModel(self::$data);
    }

    public function deleteAction()
    {
        $id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('variable');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost()->get('id');
                $this->getVariableTable()->deleteVariable($id);
            }

            // Redirect to list of variables.
            return $this->redirect()->toRoute('variable');
        }

        self::$data['id'] = $id;
        self::$data['variable'] = $this->getVariableTable()->getVariable($id);
        return new ViewModel(self::$data);
    }

    public function getVariableTable()
    {
        if (!$this->variableTable)
        {
            $sm = $this->getServiceLocator();
            $this->variableTable = $sm->get('Variable\Model\VariableTable');
        }
        return $this->variableTable;
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
