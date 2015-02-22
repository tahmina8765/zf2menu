<?php

namespace Zf2menu\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zf2menu\Entity\Menus;
use Zf2menu\Form\MenusForm;
use Zf2menu\Form\MenusSearchForm;
use Zend\Db\Sql\Select;

class MenusController extends Zf2menuAppController {

    public $vm;

    function __construct() {
        parent::__construct();
        $this->vm = new viewModel();

    }

    /**
     * Search Action
     * Receive the POST value from 'Search Form' and returns the GET params to the index action.
     * So when index get the search params, it will always receive as GET and same format either comes from the search form or the pagination link.
     * Author: Tahmina Khatoon
     */
    public function searchAction() {
        $request = $this->getRequest();

        $url = 'index';

        if ($request->isPost()) {
            $formdata    = (array) $request->getPost();
            $search_data = array ();
            foreach ($formdata as $key => $value) {
                if ($key != 'submit') {
                    if (!empty($value)) {
                        $search_data[$key] = trim($value);
                    }
                }
            }
            if (!empty($search_data)) {
                $search_by = json_encode($search_data);
                $url .= '/search_by/' . $search_by;
            }
        }
        $this->redirect()->toUrl($url);

    }

    /**
     * index Action
     * Receive the search params
     * Build the search query
     * Generate the search result as a list
     * @return type
     * Author: Tahmina Khatoon
     */
    public function indexAction() {
        $searchform = new MenusSearchForm();

        /**
         * Set Resource List
         */
        $options = $this->getResourcesTable()->dropdownResources();
        $searchform->get('resource_id')->setOptions(array ('value_options' => $options));


        /**
         * Set Parent Menu List
         */
        $menuoptions = $this->dropdownMenus();
        $searchform->get('parent_id')->setOptions(array ('value_options' => $menuoptions));

        $searchform->get('submit')->setValue('Search');

        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ?
                $this->params()->fromRoute('order_by') : 'order';
        $order    = $this->params()->fromRoute('order') ?
                $this->params()->fromRoute('order') : Select::ORDER_DESCENDING;

        $page          = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $item_per_page = $this->params()->fromRoute('item_per_page') ? (int) $this->params()->fromRoute('item_per_page') : 10;
        $page_range    = $this->params()->fromRoute('page_range') ? (int) $this->params()->fromRoute('page_range') : 7;

        $select->order($order_by . ' ' . $order);
        $search_by = $this->params()->fromRoute('search_by') ?
                $this->params()->fromRoute('search_by') : '';

        $select->join('resources', 'resources.id = menus.resource_id', array ('resource_name' => 'name'), 'left');
        $select->join(array('parents' => 'menus'), 'parents.id = menus.parent_id', array ('parent_name' => 'name'), 'left');

        $where    = new \Zend\Db\Sql\Where();
        $formdata = array ();
        if (!empty($search_by)) {
            $formdata = (array) json_decode($search_by);
            if (!empty($formdata['name'])) {
                $where->addPredicate(
                        new \Zend\Db\Sql\Predicate\Like('menus.name', '%' . $formdata['name'] . '%')
                );
            }
            if (!empty($formdata['type'])) {
                $where->equalTo('menus.type', $formdata['type']);
            }
            if (!empty($formdata['resource_id'])) {
                $where->equalTo('menus.resource_id', $formdata['resource_id']);
            }
            if (!empty($formdata['url'])) {
                $where->addPredicate(
                        new \Zend\Db\Sql\Predicate\Like('menus.url', '%' . $formdata['url'] . '%')
                );
            }
            if (!empty($formdata['target'])) {
                $where->equalTo('menus.target', $formdata['target']);
            }
            if (!empty($formdata['parent_id'])) {
                $where->equalTo('menus.parent_id', $formdata['parent_id']);
            }
            if (!empty($formdata['status'])) {
                $where->equalTo('menus.status', $formdata['status']);
            }
        }
        if (!empty($where)) {
            $select->where($where);
        }

        $paginator = $this->getMenusTable()->fetchAll($select, true);
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($item_per_page)
                ->setPageRange($page_range);

        $totalRecord = $paginator->getTotalItemCount();
        $currentPage = $paginator->getCurrentPageNumber();
        $totalPage   = $paginator->count();

        $searchform->setData($formdata);
        $this->vm->setVariables(array (
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'search_by'     => $search_by,
            'order_by'      => $order_by,
            'order'         => $order,
            'page'          => $page,
            'item_per_page' => $item_per_page,
            'paginator'     => $paginator,
            'pageAction'    => 'menus/index',
            'form'          => $searchform,
            'totalRecord'   => $totalRecord,
            'currentPage'   => $currentPage,
            'totalPage'     => $totalPage
        ));
        return $this->vm;

    }

    /**
     * add Action
     * Insert row in 'menus'
     * @return type
     * Author: Tahmina Khatoon
     */
    public function addAction() {
        $form = new MenusForm();

        /**
         * Set Resource List
         */
        $options = $this->getResourcesTable()->dropdownResources();
        $form->get('resource_id')->setOptions(array ('value_options' => $options));


        /**
         * Set Parent Menu List
         */
        $menuoptions = $this->dropdownMenus();
        $form->get('parent_id')->setOptions(array ('value_options' => $menuoptions));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $menus = new Menus();
            $form->setInputFilter($menus->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $menus->exchangeArray($form->getData());
                $confirm  = $this->getMenusTable()->saveMenus($menus);
                $redirect = false;
                if (!empty($confirm['status'])) {
                    switch ($confirm['status']) {
                        case '1':
                            $redirect = true;
                            $this->flashMessenger()->addMessage(array ('success' => $this->message->success));
                            break;
                        default:
                            $this->flashMessenger()->addMessage(array ('error' => $this->message->error));
                            break;
                    }
                }

                if ($redirect) {
                    // Redirect to list of menuss
                    return $this->redirect()->toRoute('menus');
                }
            }
        }
        $this->vm->setVariables(array (
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'form'          => $form
        ));

        return $this->vm;

    }

    /**
     * Edit Action
     * Edit row in 'menus'
     * @return type
     * Author: Tahmina Khatoon
     */
    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('menus', array (
                        'action' => 'add'
            ));
        }
        $menus = $this->getMenusTable()->getMenus($id);

        $form = new MenusForm();

        /**
         * Set Resource List
         */
        $options = $this->getResourcesTable()->dropdownResources();
        $form->get('resource_id')->setOptions(array ('value_options' => $options));


        /**
         * Set Parent Menu List
         */
        $menuoptions = $this->dropdownMenus($id);
        $form->get('parent_id')->setOptions(array ('value_options' => $menuoptions));



        $form->bind($menus);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($menus->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $confirm = $this->getMenusTable()->saveMenus($form->getData());

                $redirect = false;
                if (!empty($confirm['status'])) {
                    switch ($confirm['status']) {
                        case '1':
                            $redirect = true;
                            $this->flashMessenger()->addMessage(array ('success' => $this->message->success));
                            break;
                        default:
                            $this->flashMessenger()->addMessage(array ('error' => $this->message->error));
                            break;
                    }
                }

                if ($redirect) {
                    // Redirect to list of menuss
                    return $this->redirect()->toRoute('menus');
                }
            }
        }
        $this->vm->setVariables(array (
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'id'            => $id,
            'form'          => $form,
        ));

        return $this->vm;

    }

    /**
     * Delete Action
     * Delete row from 'menus'
     * @return type
     * Author: Tahmina Khatoon
     */
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('menus');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $id      = (int) $request->getPost('id');
            $confirm = $this->getMenusTable()->deleteMenus($id);


            if (!empty($confirm['status'])) {
                switch ($confirm['status']) {
                    case '1':
                        $this->flashMessenger()->addMessage(array ('success' => $this->message->success));
                        break;
                    default:
                        $this->flashMessenger()->addMessage(array ('error' => $this->message->error));
                        break;
                }
            }

            // Redirect to list of menuss
            return $this->redirect()->toRoute('menus');
        }
        $this->vm->setVariables(array (
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'id'            => $id,
            'menus'         => $this->getMenusTable()->getMenus($id)
        ));

        return $this->vm;

    }
    /**
     * Reorder Action
     * Reorder 'menus'
     * @return type
     * Author: Tahmina Khatoon
     */
    public function reorderAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $direction = $this->params()->fromRoute('direction', '');

        if(!empty($id) && !empty($direction)){
            $confirm = $this->getMenusTable()->reorderMenus($id, $direction);
            if (!empty($confirm['status'])) {
                switch ($confirm['status']) {
                    case '1':
                        $this->flashMessenger()->addMessage(array ('success' => $this->message->success));
                        break;
                    default:
                        $this->flashMessenger()->addMessage(array ('error' => $this->message->error));
                        break;
                }
            }
            // Redirect to list of menuss
            return $this->redirect()->toRoute('menus/reorder');
        }

        $this->vm->setVariables(array (
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'data'         => $this->getMenusTable()->getTreeMenus()
        ));

        return $this->vm;

    }

    private function dropdownMenus($id = 0) {
        $options         = array ();
        if (empty($id)) {
            $select  = new Select();
            $where   = new \Zend\Db\Sql\Where();
            $where->notEqualTo('id', (int) $id);
            $select->where($where);
            $options = $this->getMenusTable()->dropdownMenus($select);
        } else {
            $options = $this->getMenusTable()->dropdownMenus();
        }
        return $options;

    }

}
