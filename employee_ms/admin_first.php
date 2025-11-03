<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');


session_start();

$memberEmail = $_SESSION['memberEmail'];
$memberID = $_SESSION['memberID'];
$memberNickname = $_SESSION['memberNickname'];
$powerkey = $_SESSION['powerkey'];


@include_once("/website/class/".$site_db."_info_class.php");

//載入公用函數
@include_once '/website/include/pub_function.php';


$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
$m_pub_modal	= "/website/smarty/templates/".$site_db."/pub_modal";


$sid = "";
if (isset($_GET['sid']))
	$sid = $_GET['sid'];

	//程式分類
	$ch = empty($_GET['ch']) ? 'default' : $_GET['ch'];
	switch($ch) {
		case 'add':
			$title = "新增資料";
			$sid = "view01";
			$modal = $m_location."/sub_modal/base/employee_ms/employee_add.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
			break;
		case 'edit':
			$title = "資料編輯";
			$sid = "view01";
			$modal = $m_location."/sub_modal/base/employee_ms/employee_modify.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
			break;
			/*
		case 'mview':
		case 'view':
			$title = "資料瀏覽";
			if (empty($sid))
				$sid = "view01";
			$modal = $m_location."/sub_modal/base/employee_ms/employee_view.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			//$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
			break;
			*/
		case 'importexcel':
			$title = "匯入員工Excel資料檔".$mt;
			$sid = "view01";
			$modal = $m_location."/sub_modal/base/employee_ms/importexcel.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			break;
		case 'exportexcel':
			$title = "匯出員工Excel資料檔".$mt;
			$sid = "view01";
			$modal = $m_location."/sub_modal/base/employee_ms/exportexcel.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			break;
			/*
		case 'report':
			if (empty($sid))
				$sid = "view01";
			$modal = $m_location."/sub_modal/base/employee_ms/employee_report.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			break;
			*/
		default:
			if (empty($sid))
				$sid = "admin01";
			$modal = $m_location."/sub_modal/base/employee_ms/employee.php";
			include $modal;
			$smarty->assign('show_center',$show_center);
			$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
			break;
	};

?>