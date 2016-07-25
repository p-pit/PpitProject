<?php
namespace PpitProject;

use PpitCore\Model\GenericTable;
use PpitProject\Model\Planning;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\EventManager\EventInterface;
use Zend\Validator\AbstractValidator;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
 	          	'PpitProject\Model\PlanningTable' =>  function($sm) {
                    $tableGateway = $sm->get('PlanningTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'PlanningTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Planning());
                    return new TableGateway('project_planning', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
