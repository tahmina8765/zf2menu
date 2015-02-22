<?php

namespace Zf2menu\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class Navigationhelper extends AbstractHelper {

    protected $serviceLocator;
    protected $authService;

    public function __invoke($role_id = 0) {
        $data = $this->serviceLocator->get('Zf2menu\Table\MenusTable')->getUserTreeMenus($role_id);
        return $this->getView()->render('partial/navigation_helper', array ('data' => $data, 'role_id' => $role_id));
    }

    public function setServiceLocator(ServiceManager $serviceLocator) {
        $this->serviceLocator = $serviceLocator;

    }

}
