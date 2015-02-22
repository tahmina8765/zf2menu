<?php

namespace Zf2menu\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class Zf2menuAppController extends AbstractActionController
{

    protected $menusTable;
    protected $resourcesTable;

    public function __construct()
    {
//        parent::__construct();

    }

    protected function getMenusTable()
    {
        if (!$this->menusTable) {
            $sm               = $this->getServiceLocator();
            $this->menusTable = $sm->get('Zf2menu\Table\MenusTable');
        }
        return $this->menusTable;
    }
    
    protected function getResourcesTable()
    {
        if (!$this->resourcesTable) {
            $sm = $this->getServiceLocator();
            $this->resourcesTable = $sm->get('Zf2auth\Table\ResourcesTable');
        }
        return $this->resourcesTable;
    }




}
