<?php

namespace Variable\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class VariableTable extends AbstractTableGateway
{
    protected $table = 'variable';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Variable());

        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getVariable($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row)
        {
            throw new \Exception("Could not find row $id");
        }

        $row->value = unserialize($row->value);

        return $row;
    }

    public function saveVariable(Variable $variable)
    {
        $data = array(
            'name'      => $variable->name,
            'category'  => $variable->category,
            'value'     => serialize($variable->value),
        );

        $id = (int)$variable->id;
        if ($id == 0)
        {
            $this->insert($data);
        }
        else
        {
            if ($this->getVariable($id))
            {
                $this->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteVariable($id)
    {
        $this->delete(array('id' => $id));
    }

}
