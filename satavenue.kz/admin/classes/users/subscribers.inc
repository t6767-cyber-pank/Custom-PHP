<?php
	if($GLOBAL->UserInfo->RightsUsers!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$Subscribers=$DataBase->Query("SELECT * FROM {$GLOBAL->Subscribes} ORDER BY `DateTime`;");

	error_reporting(E_ALL);

	include_once '../PHPExcel/PHPExcel.php';

	$PHPExcel = new PHPExcel();

	// Set document properties
	$PHPExcel->getProperties()->setCreator("iec-telecom.kz")
			->setLastModifiedBy("iec-telecom.kz")
			->setTitle("iec-telecom.kz subsribers")
			->setSubject("iec-telecom.kz subscribe list")
			->setDescription("iec-telecom.kz")
			->setKeywords("iec-telecom.kz")
			->setCategory("");

	$ThisColumn=1;
	$PHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0,$ThisColumn++)->setValue('E-mail');

	foreach($Subscribers as $Subscriber){
		$PHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0,$ThisColumn++,1)->setValue(urldecode($Subscriber->EMail));
	}

	$PHPExcel->getActiveSheet()->setTitle('EMails');
	$PHPExcel->setActiveSheetIndex(0);

	$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');

	header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
	header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
	header ( "Cache-Control: no-cache, must-revalidate" );
	header ( "Pragma: no-cache" );
	header ( "Content-type: application/vnd.ms-excel" );
	header ( "Content-Disposition: attachment; filename=iec-telecom-subscribers".date("dmy Hi").'.xls');

	$objWriter->save('php://output');