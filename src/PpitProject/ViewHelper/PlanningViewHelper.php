<?php
namespace PpitProject\ViewHelper;

use PpitCore\Model\Context;
use PpitProject\Model\Planning;

class PlanningViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');
		
		// Set document properties
		$workbook->getProperties()->setCreator("P-PIT")
			->setLastModifiedBy("P-PIT")
			->setTitle("Planning")
			->setSubject("Planning")
			->setDescription("Planning")
			->setKeywords("planning")
			->setCategory("Planning");

		$sheet = $workbook->getActiveSheet();
		
		$sheet->setCellValue('A1', $translator->translate('Project', 'ppit-project', $context->getLocale()));
		$sheet->setCellValue('B1', $translator->translate('Phase', 'ppit-project', $context->getLocale()));
		$sheet->setCellValue('C1', $translator->translate('Deliverable', 'ppit-project', $context->getLocale()));
		$sheet->setCellValue('D1', $translator->translate('Responsible', 'ppit-project', $context->getLocale()));
		$sheet->setCellValue('E1', $translator->translate('Status', 'ppit-core', $context->getLocale()));
		$sheet->setCellValue('F1', $translator->translate('Initial due date', 'ppit-project', $context->getLocale()));
		$sheet->setCellValue('G1', $translator->translate('New due date', 'ppit-project', $context->getLocale()));
		$sheet->setCellValue('H1', $translator->translate('Actual delivery date', 'ppit-project', $context->getLocale()));		

		$i = 1;
		foreach ($view->deliverables as $deliverable) {
			$i++;
			$sheet->setCellValue('A'.$i, $deliverable->project);
			$sheet->setCellValue('B'.$i, $deliverable->phase);
			$sheet->setCellValue('C'.$i, $deliverable->deliverable);
			$sheet->setCellValue('D'.$i, $deliverable->responsible);
			$sheet->setCellValue('E'.$i, $deliverable->status);
			$sheet->setCellValue('F'.$i, $deliverable->initial_due_date, 'Date');
			$sheet->setCellValue('G'.$i, $deliverable->new_due_date, 'Date');
			$sheet->setCellValue('H'.$i, $deliverable->actual_delivery_date, 'Date');
		}
   		$sheet->getColumnDimension('A')->setAutoSize(true);
   		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
	}
}