<?php

namespace Zf2menu\Table;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zf2menu\Entity\Menus;
use Zend\Db\Sql\Predicate\Expression;

class MenusTable extends AbstractTableGateway {

    protected $table = 'menus';

    public function __construct(Adapter $adapter) {
        $this->adapter            = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Menus());

        $this->initialize();

    }

    public function fetchAll(Select $select = null, $paginated = false) {
        $adapter = $this->adapter;
        if (null === $select)
            $select  = new Select();
        $select->from($this->table);
        if ($paginated) {
            $paginatorAdapter = new DbSelect($select, $adapter);
            $paginator        = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $sql       = new Sql($adapter);
            $statement = $sql->getSqlStringForSqlObject($select);
            $resultSet = $adapter->query($statement, $adapter::QUERY_MODE_EXECUTE);
            $resultSet->buffer();
            return $resultSet;
        }

    }

    public function getMenus($id) {
        $id     = (int) $id;
        $rowset = $this->select(array ('id' => $id));
        $row    = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;

    }

    public function getOrder($parent_id) {
        $select    = new Select();
        $select->from($this->table);
        $select->where->equalTo('parent_id', $parent_id);
        $select->order('order' . ' ' . 'ASC');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        $order     = 0;
        if (!empty($resultSet)) {
            foreach ($resultSet as $key => $row) {
                $data = array (
                    'order' => $order,
                );
                $this->update($data, array ('id' => $row->getId()));
                $order++;
            }
        }
        return $order;

    }

    public function saveMenus(Menus $menus) {
        $result = array (
            'status'  => 0,
            'message' => ''
        );
        $data   = array (
            'name'        => $menus->name,
            'icon'        => $menus->icon,
            'type'        => $menus->type,
            'resource_id' => $menus->resource_id,
            'url'         => $menus->url,
            'target'      => $menus->target,
            'parent_id'   => $menus->parent_id,
            'order'       => $menus->order,
            'status'      => $menus->status,
            'created'     => date('Y-m-d H:i:s'),
            'modified'    => date('Y-m-d H:i:s'),
        );

        $id = (int) $menus->id;
        if ($id == 0) {
            unset($data['modified_by']);
            unset($data['modified']);
            if (empty($data['order'])) {
                $data['order'] = $this->getOrder($data['parent_id']);
            }
            $result['status'] = $this->insert($data);
        } else {
            if ($this->getMenus($id)) {
                unset($data['created_by']);
                unset($data['created']);
                unset($data['order']);
                $result['status'] = $this->update($data, array ('id' => $id));
            } else {
                $result['message'] = 'ID does not exist';
            }
        }

        return $result;

    }

    public function reorderMenus($id, $direction) {
        $result   = array (
            'status'  => 0,
            'message' => ''
        );
        $previous = '';
        $current  = '';
        $next     = '';
        $matched  = false;
        $setPrev  = true;

        $id   = (int) $id;
        $data = $this->getMenus($id);
        if (!empty($data)) {
            $parent_id = $data->getParentId();

            $select    = new Select();
            $select->from($this->table);
            $select->where->equalTo('parent_id', $parent_id);
            $select->order('order' . ' ' . 'ASC');
            $resultSet = $this->selectWith($select);
            $resultSet->buffer();
            $order     = 0;
            if (!empty($resultSet)) {
                foreach ($resultSet as $key => $row) {
                    if ($setPrev) {
                        $previous = $current;
                    }

                    $current['id']    = $row->getId();
                    $current['order'] = $order;

                    if ($matched) {
                        $next    = $current;
                        $matched = false;
                    }

                    if ($current['id'] == $id) {
                        $matched = true;
                        $setPrev = false;
                    }

                    $data = array (
                        'order' => $order,
                    );
                    $this->update($data, array ('id' => $row->getId()));
                    $order++;
                }
            }
            $data = $this->getMenus($id);


            switch ($direction) {
                case 'up':
                    if (!empty($previous['id'])) {
                        $prev_data     = $this->getMenus((int) $previous['id']);
                        $current_data  = array (
                            'order' => $prev_data->getOrder(),
                        );
                        $new_prev_data = array (
                            'order' => $data->getOrder(),
                        );
                        $this->update($current_data, array ('id' => $id));
                        $this->update($new_prev_data, array ('id' => $previous['id']));
                    }
                    break;
                case 'down':
                    if (!empty($next['id'])) {
                        $next_data     = $this->getMenus((int) $next['id']);
                        $current_data  = array (
                            'order' => $next_data->getOrder(),
                        );
                        $new_prev_data = array (
                            'order' => $data->getOrder(),
                        );
                        $this->update($current_data, array ('id' => $id));
                        $this->update($new_prev_data, array ('id' => $next['id']));
                    }
                    break;
            }

//
//            unset($data['created_by']);
//            unset($data['created']);
//            unset($data['order']);
//            $result['status'] = $this->update($data, array ('id' => $id));
        } else {
            $result['message'] = 'ID does not exist';
        }


//        $data = array (
//            'name'        => $menus->name,
//            'type'        => $menus->type,
//            'resource_id' => $menus->resource_id,
//            'url'         => $menus->url,
//            'target'      => $menus->target,
//            'parent_id'   => $menus->parent_id,
//            'order'       => $menus->order,
//            'status'      => $menus->status,
//            'created'     => date('Y-m-d H:i:s'),
//            'modified'    => date('Y-m-d H:i:s'),
//        );
//
//        $id = (int) $menus->id;
//        if ($id == 0) {
//            unset($data['modified_by']);
//            unset($data['modified']);
//            if (empty($data['order'])) {
//                $data['order'] = $this->getOrder($data['parent_id']);
//            }
//            $result['status'] = $this->insert($data);
//        } else {
//            if ($this->getMenus($id)) {
//                unset($data['created_by']);
//                unset($data['created']);
//                unset($data['order']);
//                $result['status'] = $this->update($data, array ('id' => $id));
//            } else {
//                $result['message'] = 'ID does not exist';
//            }
//        }

        return $result;

    }

    public function deleteMenus($id) {
        $result = array (
            'status'  => 0,
            'message' => ''
        );
        try {
            $result['status'] = $this->delete(array ('id' => $id));
        } catch (\Exception $e) {
            $result['message'] = 'Information can not be deleted.';
        }
        return $result;

    }

    public function dropdownMenus(Select $select = null) {
        if (null === $select)
            $select    = new Select();
        $select->from($this->table);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();

        $options     = array ();
        $options[''] = '--- Please Select ---';
        if (count($resultSet) > 0) {
            foreach ($resultSet as $row)
                $options[$row->getId()] = $row->getName();
        }
        return $options;

    }

    public function getTreeMenus($parent_id = 0, $extraParam = array ()) {

        $adapter = $this->adapter;
        $result  = array ();
        $select  = new Select();
        $select->from($this->table);
        $select->join('resources', 'resources.id = menus.resource_id', array ('resource_name' => 'name'), 'left');
        $select->join('role_resources', 'menus.resource_id = role_resources.resource_id', array ('role_id' => 'role_id'), 'left');

        if (!empty($extraParam['role_id'])) {
            $role_id = (int) $extraParam['role_id'];
            if ($role_id != 1) {
                $select->where->equalTo('role_id', $role_id);
            }
        }


        $select->where->equalTo('parent_id', $parent_id);
        $select->where->equalTo('status', 1);
        $select->order('order' . ' ' . 'ASC');
        $sql       = new Sql($adapter);
        $statement = $sql->getSqlStringForSqlObject($select);
//echo $statement;
//        die();
        $resultSet = $adapter->query($statement, $adapter::QUERY_MODE_EXECUTE);
        $resultSet->buffer();

        if (!empty($resultSet)) {
            $exist_ids = array ();
            foreach ($resultSet as $key => $data) {
                if (!in_array($data->id, $exist_ids)) {
                    $exist_ids[]           = $data->id;
                    $result[$key]['data']  = $data;
                    $result[$key]['child'] = $this->getTreeMenus($data->id, $extraParam);
                }
            }
        }
        return $result;

    }

    public function getUserTreeMenus($role_id, $parent_id = 0) {
        $adapter = $this->adapter;
        $result  = array ();
        $select  = new Select();
        $select->from($this->table);
//        $select->columns(array(new Expression('DISTINCT(menus.id) as id'), 'name', 'type', 'url', 'target'));
        $select->join('resources', 'resources.id = menus.resource_id', array ('resource_name' => 'name'), 'left');
        $select->join('role_resources', 'menus.resource_id = role_resources.resource_id', array ('role_id' => 'role_id'), 'left');
        $where   = new \Zend\Db\Sql\Where();
        if ($role_id != 1) {
            $where->addPredicate(
                    new \Zend\Db\Sql\Predicate\Expression("(role_resources.role_id = {$role_id} OR menus.type = 1)")
            );
        }

        $where->equalTo('parent_id', $parent_id);
        $where->equalTo('status', 1);

        if (!empty($where)) {
            $select->where($where);
        }
        $select->order('order' . ' ' . 'ASC');
        $sql       = new Sql($adapter);
        $statement = $sql->getSqlStringForSqlObject($select);
//echo $statement;
//        die();
        $resultSet = $adapter->query($statement, $adapter::QUERY_MODE_EXECUTE);
        $resultSet->buffer();

        if (!empty($resultSet)) {
            $exist_ids = array ();
            foreach ($resultSet as $key => $data) {
                if (!in_array($data->id, $exist_ids)) {
                    $exist_ids[]           = $data->id;
                    $result[$key]['data']  = $data;
                    $result[$key]['child'] = $this->getUserTreeMenus($role_id, $data->id);
                }
            }
        }
        return $result;

    }

}
