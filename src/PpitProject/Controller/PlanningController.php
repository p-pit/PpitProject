<?php
namespace PpitProject\Controller;

use DateInterval;
use Date;
use DOMPDFModule\View\Model\PdfModel;
use PpitProject\Model\Planning;
use PpitProject\ViewHelper\PlanningViewHelper;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class PlanningController extends AbstractActionController
{
	public function getFilters($params)
	{
		// Retrieve the query parameters
		$filters = array();

		$project = ($params()->fromQuery('project', null));
		if ($project) $filters['project'] = $project;
		
		$account_id = ($params()->fromQuery('account_id', null));
		if ($account_id) $filters['account_id'] = $account_id;

		$phase = ($params()->fromQuery('phase', null));
		if ($phase) $filters['phase'] = $phase;

		$deliverable = ($params()->fromQuery('deliverable', null));
		if ($deliverable) $filters['deliverable'] = $deliverable;

		$responsible = ($params()->fromQuery('responsible', null));
		if ($responsible) $filters['responsible'] = $responsible;
		
		$status = ($params()->fromQuery('status', null));
		if ($status) $filters['status'] = $status;
		
		$min_initial_due_date = ($params()->fromQuery('min_initial_due_date', null));
		if ($min_initial_due_date) $filters['min_initial_due_date'] = $min_initial_due_date;

		$max_initial_due_date = ($params()->fromQuery('max_initial_due_date', null));
		if ($max_initial_due_date) $filters['max_initial_due_date'] = $max_initial_due_date;

		$min_new_due_date = ($params()->fromQuery('min_new_due_date', null));
		if ($min_new_due_date) $filters['min_new_due_date'] = $min_new_due_date;
		
		$max_new_due_date = ($params()->fromQuery('max_new_due_date', null));
		if ($max_new_due_date) $filters['max_new_due_date'] = $max_new_due_date;

		$min_actual_delivery_date = ($params()->fromQuery('min_actual_delivery_date', null));
		if ($min_actual_delivery_date) $filters['min_actual_delivery_date'] = $min_actual_delivery_date;
		
		$max_actual_delivery_date = ($params()->fromQuery('max_actual_delivery_date', null));
		if ($max_actual_delivery_date) $filters['max_actual_delivery_date'] = $max_actual_delivery_date;

		for ($i = 1; $i < 20; $i++) {
		
			$property = ($params()->fromQuery('property_'.$i, null));
			if ($property) $filters['property_'.$i] = $property;
			$min_property = ($params()->fromQuery('min_property_'.$i, null));
			if ($min_property) $filters['min_property_'.$i] = $min_property;
			$max_property = ($params()->fromQuery('max_property_'.$i, null));
			if ($max_property) $filters['max_property_'.$i] = $max_property;
		}
		
		return $filters;
	}
	
   	public function indexAction()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'config' => $context->getconfig(),
   				'accounts' => Account::getList('customer_name', 'ASC'),
   				'statuses' => $context->getInstance()->specifications['ppitProject']['statuses'],
   		));
		$view->setTerminal(true);
       	return $view;
   	}

   	public function getList()
   	{
		// Retrieve the context
		$context = Context::getCurrent();

		$params = $this->getFilters($this->params());
		
		$major = ($this->params()->fromQuery('major', 'new_due_date'));
		$dir = ($this->params()->fromQuery('dir', 'ASC'));

		if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

		// Retrieve the list
		$deliverables = Planning::getList($params, $major, $dir, $mode);

   		// Return the link list
   		$view = new ViewModel(array(
   				'context' => $context,
				'config' => $context->getconfig(),
   				'properties' => $context->getInstance()->specifications['ppitProject']['properties'],
   				'statuses' => $context->getInstance()->specifications['ppitProject']['statuses'],
   				'deliverables' => $deliverables,
   				'mode' => $mode,
   				'params' => $params,
   				'major' => $major,
   				'dir' => $dir,
   		));
		$view->setTerminal(true);
       	return $view;
   	}
   	
   	public function listAction()
   	{
   		return $this->getList();
   	}

   	public function exportAction()
   	{
   		$view = $this->getList();

   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new PlanningViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
//		header('Content-type: application/pdf');
//		header('Content-type: application/vnd.ms-excel');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=Fichier.xlsx ');
		$writer->save('php://output');
/*   		
   		
   		
   		$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
   		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
   		$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
   		
   		
   		// Rename worksheet
   		echo date('H:i:s') , " Rename worksheet" , EOL;
   		$objPHPExcel->getActiveSheet()->setTitle('Simple');
   		
   		
   		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
   		$objPHPExcel->setActiveSheetIndex(0);
   		 
   		// Save Excel 2007 file
   		echo date('H:i:s') , " Write to Excel2007 format" , EOL;
   		$callStartTime = microtime(true);
   		
   		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
   		$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
   		$callEndTime = microtime(true);
   		$callTime = $callEndTime - $callStartTime;
   		
   		echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
   		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
   		// Echo memory usage
   		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
   		
   		
   		// Save Excel 95 file
   		echo date('H:i:s') , " Write to Excel5 format" , EOL;
   		$callStartTime = microtime(true);
   		
   		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
   		$objWriter->save(str_replace('.php', '.xls', __FILE__));
   		$callEndTime = microtime(true);
   		$callTime = $callEndTime - $callStartTime;
   		
   		echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
   		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
   		// Echo memory usage
   		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
   		
   		
   		// Echo memory peak usage
   		echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
   		
   		// Echo done
   		echo date('H:i:s') , " Done writing files" , EOL;
   		echo 'Files have been created in ' , getcwd() , EOL;*/
   	}
}
