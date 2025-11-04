<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

//載入公用函數
@include_once '/website/include/pub_function.php';

$web_id = $_POST['web_id'];
$site_db = $_POST['site_db'];


/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '/website/os/PHPExcel-1.8.1/Classes/');

/** PHPExcel_IOFactory */
include '/website/os/PHPExcel-1.8.1/Classes/PHPExcel/IOFactory.php';


// /website/webdata/eshop/mybrand.eshop/emulation/excel

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	
	$sfile = $_FILES['Filedata']['name'];

	$filetype = strtolower(pathinfo($sfile, PATHINFO_EXTENSION));

    //檔名處理
	$sfilename = validfilename($sfile);
	
	//產生唯一值
	$tfile = strtoupper(uuid());
	
	$targetFile =  str_replace('//','/',$targetPath).$tfile.".".$filetype;
	
	if (move_uploaded_file($tempFile,$targetFile)) {
//		echo "檔案：".str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile)."已上傳並完成匯入!";
		
//		echo $targetFile;

		//匯入資料庫中
		include_once("/website/class/".$site_db."_info_class.php");
		$mDB = "";
		$mDB = new MyWebDB();

		$retval = "";
		

		$inputFileName = $targetFile;
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

		//先選擇Excel第1頁工作表「主要資料表」存入至 employee
		$objPHPExcel->setActiveSheetIndex(0);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

		
		foreach($sheetData as $sheetIndex => $data) {
			if ($sheetIndex > 1 ) {
			
				$employee_id = htmlentities(trim($data["A"]), ENT_QUOTES, 'UTF-8');
				$employee_name = htmlentities(trim($data["B"]), ENT_QUOTES, 'UTF-8');
				$id_number = htmlentities(trim($data["C"]), ENT_QUOTES, 'UTF-8');
				$gender = htmlentities(trim($data["D"]), ENT_QUOTES, 'UTF-8');
				$blood_type = htmlentities(trim($data["E"]), ENT_QUOTES, 'UTF-8');
				$birthday = htmlentities(trim($data["F"]), ENT_QUOTES, 'UTF-8');
				$mobile_no = htmlentities(trim($data["G"]), ENT_QUOTES, 'UTF-8');
				$old_employee_id = htmlentities(trim($data["H"]), ENT_QUOTES, 'UTF-8');
				$emergency_contact = htmlentities(trim($data["I"]), ENT_QUOTES, 'UTF-8');
				$emergency_mobile_no = htmlentities(trim($data["J"]), ENT_QUOTES, 'UTF-8');
				$start_date = htmlentities(trim($data["K"]), ENT_QUOTES, 'UTF-8');
				$seniority = htmlentities(trim($data["L"]), ENT_QUOTES, 'UTF-8');
				$county = htmlentities(trim($data["M"]), ENT_QUOTES, 'UTF-8');
				$town = htmlentities(trim($data["N"]), ENT_QUOTES, 'UTF-8');
				$zipcode = htmlentities(trim($data["O"]), ENT_QUOTES, 'UTF-8');
				$address = htmlentities(trim($data["P"]), ENT_QUOTES, 'UTF-8');
				$company_id = htmlentities(trim($data["Q"]), ENT_QUOTES, 'UTF-8');
				$employee_type = htmlentities(trim($data["R"]), ENT_QUOTES, 'UTF-8');
				$team_id = htmlentities(trim($data["S"]), ENT_QUOTES, 'UTF-8');
				$construction_id = htmlentities(trim($data["T"]), ENT_QUOTES, 'UTF-8');
				$member_no = htmlentities(trim($data["U"]), ENT_QUOTES, 'UTF-8');
				$actual_insurance = htmlentities(trim($data["V"]), ENT_QUOTES, 'UTF-8');
				$labor_health_insurance = htmlentities(trim($data["W"]), ENT_QUOTES, 'UTF-8');
				$labor_Pension = htmlentities(trim($data["X"]), ENT_QUOTES, 'UTF-8');
				$employee_content = htmlentities(trim($data["Y"]), ENT_QUOTES, 'UTF-8');
				
				//檢查是否重複
				$employee_row = getkeyvalue2($site_db."_info","employee","employee_id = '$employee_id'","count(*) as e_count");
				$e_count = $employee_row['e_count'];
				if ($e_count <= 0 ) {
					$Qry = "insert into employee (employee_id,create_date,last_modify) values ('$employee_id',now(),now())";
					$mDB->query($Qry);
				}
				
				//更新 employee
				$Qry="UPDATE employee SET
					 employee_name = '$employee_name'
					,id_number = '$id_number'
					,gender = '$gender'
					,blood_type = '$blood_type'
					,birthday  = '$birthday'
					,mobile_no  = '$mobile_no'
					,old_employee_id  = '$old_employee_id'
					,emergency_contact  = '$emergency_contact'
					,emergency_mobile_no  = '$emergency_mobile_no'
					,`start_date`  = '$start_date'
					,seniority  = '$seniority'
					,county  = '$county'
					,town  = '$town'
					,zipcode  = '$zipcode'
					,`address`  = '$address'
					,company_id  = '$company_id'
					,employee_type  = '$employee_type'
					,team_id  = '$team_id'
					,construction_id  = '$construction_id'
					,member_no  = '$member_no'
					,actual_insurance  = '$actual_insurance'
					,labor_health_insurance  = '$labor_health_insurance'
					,labor_Pension 	= '$labor_Pension'
					,employee_content  = '$employee_content'
					,last_modify  = now()
					where employee_id = '$employee_id'";
				$mDB->query($Qry);

				
				$retval .= "<div>已建立更新 員工代號：".$employee_id." &nbsp; 員工名稱：".$employee_name."</div>";
				
			}

		}
		
		
		$mDB->remove();
		
		
		if ($retval <> "")
			echo $retval;
		
		//結束程序，刪除上傳的檔案
		if (file_exists($targetFile)) {
			if (is_file($targetFile)) {
				recursiveDelete($targetFile);
			}
		}
		
		
	}
	
//	sleep(10);
		
}

?>