<?php
namespace PpitProject\Model;

use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

class Planning implements InputFilterAwareInterface
{
    public $id;
    public $project;
    public $account_id;
    public $phase;
    public $deliverable;
    public $responsible;
    public $status;
    public $initial_due_date;
    public $new_due_date;
    public $actual_delivery_date;
    public $property_1;
    public $property_2;
    public $property_3;
    public $property_4;
    public $property_5;
    public $update_time;

    // Joined properties
    public $customer_name;

    // Transient properties
    public $properties;
	
    protected $inputFilter;

    // Static fields
    private static $table;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->project = (isset($data['project'])) ? $data['project'] : null;
        $this->account_id = (isset($data['account_id'])) ? $data['account_id'] : null;
        $this->phase = (isset($data['phase'])) ? $data['phase'] : null;
        $this->deliverable = (isset($data['deliverable'])) ? $data['deliverable'] : null;
        $this->responsible = (isset($data['responsible'])) ? $data['responsible'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->initial_due_date = (isset($data['initial_due_date'])) ? $data['initial_due_date'] : null;
        $this->new_due_date = (isset($data['new_due_date'])) ? $data['new_due_date'] : null;
        $this->actual_delivery_date = (isset($data['actual_delivery_date'])) ? $data['actual_delivery_date'] : null;
        $this->property_1 = (isset($data['property_1'])) ? $data['property_1'] : null;
        $this->property_2 = (isset($data['property_2'])) ? $data['property_2'] : null;
        $this->property_3 = (isset($data['property_3'])) ? $data['property_3'] : null;
        $this->property_4 = (isset($data['property_4'])) ? $data['property_4'] : null;
        $this->property_5 = (isset($data['property_5'])) ? $data['property_5'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->customer_name = (isset($data['customer_name'])) ? $data['customer_name'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['project'] = $this->project;
    	$data['account_id'] = $this->account_id;
    	$data['phase'] = $this->phase;
    	$data['deliverable'] = $this->deliverable;
    	$data['responsible'] = $this->responsible;
    	$data['status'] = $this->status;
    	$data['initial_due_date'] = (int) $this->initial_due_date;
    	$data['new_due_date'] = (int) $this->new_due_date;
    	$data['actual_delivery_date'] = (int) $this->actual_delivery_date;
    	$data['property_1'] =  ($this->property_1) ? $this->property_1 : null;
    	$data['property_2'] =  ($this->property_2) ? $this->property_2 : null;
    	$data['property_3'] =  ($this->property_3) ? $this->property_3 : null;
    	$data['property_4'] =  ($this->property_4) ? $this->property_4 : null;
    	$data['property_5'] =  ($this->property_5) ? $this->property_5 : null;
    	return $data;
    }

    public static function getList($params, $major, $dir, $mode)
    {
    	$context = Context::getCurrent();
    	$select = Planning::getTable()->getSelect()
	    	->join('commitment_account', 'project_planning.account_id = commitment_account.id', array(), 'left')
    		->join('contact_community', 'commitment_account.customer_community_id = contact_community.id', array('customer_name' => 'name'), 'left');
    	 
    	$where = new Where();
    
    	// Todo list vs search modes
    	if ($mode == 'todo') {

			$where->equalTo('status', 'current');
    	}
    	else {
    			
    		// Set the filters
    		if (isset($params['project'])) $where->like('project', '%'.$params['project'].'%');
    		if (isset($params['account_id'])) $where->equalTo('account_id', $params['account_id']);
    		if (isset($params['phase'])) $where->like('phase', '%'.$params['phase'].'%');
    		if (isset($params['responsible'])) $where->like('responsible', '%'.$params['responsible'].'%');
    		if (isset($params['deliverable'])) $where->like('deliverable', '%'.$params['deliverable'].'%');
    		if (isset($params['responsible'])) $where->like('responsible', '%'.$params['responsible'].'%');
    		if (isset($params['status'])) $where->like('status', '%'.$params['status'].'%');
    		if (isset($params['min_initial_due_date'])) $where->greaterThanOrEqualTo('initial_due_date', $params['min_initial_due_date']);
    		if (isset($params['max_initial_due_date'])) $where->lessThanOrEqualTo('initial_due_date', $params['max_initial_due_date']);
    		if (isset($params['min_new_due_date'])) $where->greaterThanOrEqualTo('new_due_date', $params['min_new_due_date']);
    		if (isset($params['max_new_due_date'])) $where->lessThanOrEqualTo('new_due_date', $params['max_new_due_date']);
    		if (isset($params['min_actual_delivery_date'])) $where->greaterThanOrEqualTo('actual_delivery_date', $params['min_actual_delivery_date']);
    		if (isset($params['max_actual_delivery_date'])) $where->lessThanOrEqualTo('actual_delivery_date', $params['max_actual_delivery_date']);
    
    		for ($i = 1; $i < 5; $i++) {
    			if (isset($params['property_'.$i])) $where->like('property_'.$i, '%'.$params['property_'.$i].'%');
    			if (isset($params['min_property_'.$i])) $where->greaterThanOrEqualTo('property_'.$i, $params['min_property_'.$i]);
    			if (isset($params['max_property_'.$i])) $where->lessThanOrEqualTo('property_'.$i, $params['max_property_'.$i]);
    		}
    	}
    
    	// Sort the list
    	$select->where($where)->order(array($major.' '.$dir, 'new_due_date'));
    
    	$cursor = Planning::getTable()->selectWith($select);
    	$planning = array();
    	foreach ($cursor as $deliverable) $planning[] = $deliverable;
    
    	return $planning;
    }
        
    public static function get($id, $column = 'id')
    {
    	$deliverable = Planning::getTable()->get($id, $column);
    	if (!$deliverable) return null;
    	 
    	$deliverable->properties = $deliverable->toArray();
    	return $deliverable;
    }

    public static function instanciate($type)
    {
    	$deliverable = new Planning;
    	$deliverable->status = 'planned';
    	$delivrable->properties = $deliverable->toArray();
    	return $deliverable;
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!Planning::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Planning::$table = $sm->get('PpitProject\Model\PlanningTable');
    	}
    	return Planning::$table;
    }
}