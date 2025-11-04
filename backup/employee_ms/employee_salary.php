<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


//載入公用函數
@include_once '/website/include/pub_function.php';

//連結資料
@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	$web_id				= trim($aFormValues['web_id']);
	$auto_seq			= trim($aFormValues['auto_seq']);
	$employee_name		= trim($aFormValues['employee_name']);
	$company_id			= trim($aFormValues['company_id']);
	$member_no			= trim($aFormValues['member_no']);
	
	
	if (trim($aFormValues['employee_name']) == "")	{
		$objResponse->script("jAlert('警示', '請輸入姓名', 'red', '', 2000);");
		return $objResponse;
		exit;
	}

	if (trim($aFormValues['company_id']) == "")	{
		$objResponse->script("jAlert('警示', '請選擇公司', 'red', '', 2000);");
		return $objResponse;
		exit;
	}

	if (trim($aFormValues['member_no']) != "")	{
		//檢查會員帳號是否正確
		$member_row = getkeyvalue2("memberinfo","member","member_no = '$member_no'","count(member_no) as m_count");
		$m_count = $member_row['m_count'];
		if ($m_count <= 0)	{
			$objResponse->script("jAlert('警示', '您輸入的會員帳號不存在', 'red', '', 2000);");
			return $objResponse;
			exit;
		}
	}

	
	SaveValue($aFormValues);
	
	$objResponse->script("setSave();");
	$objResponse->script("parent.myDraw();");

	$objResponse->script("art.dialog.tips('已存檔!',1);");
	$objResponse->script("parent.$.fancybox.close();");
	$objResponse->script("parent.eModal.close();");
		
	
	return $objResponse;
}


$xajax->registerFunction("SaveValue");
function SaveValue($aFormValues){

	$objResponse = new xajaxResponse();
	
		//進行存檔動作
		$site_db				= trim($aFormValues['site_db']);
		$web_id					= trim($aFormValues['web_id']);
		$auto_seq				= trim($aFormValues['auto_seq']);
		$employee_name 			= htmlspecialchars(trim($aFormValues['employee_name']), ENT_QUOTES, 'UTF-8');
		$old_employee_id 		= trim($aFormValues['old_employee_id']);
		$id_number 				= trim($aFormValues['id_number']);
		$gender					= trim($aFormValues['gender']);
		$birthday				= trim($aFormValues['birthday']);
		$blood_type				= trim($aFormValues['blood_type']);
		$mobile_no				= trim($aFormValues['mobile_no']);
		$emergency_contact 		= htmlspecialchars(trim($aFormValues['emergency_contact']), ENT_QUOTES, 'UTF-8');
		$emergency_mobile_no	= trim($aFormValues['emergency_mobile_no']);
		$start_date				= trim($aFormValues['start_date']);
		$resignation_date		= trim($aFormValues['resignation_date']);
		$seniority				= trim($aFormValues['seniority']);
		$county					= trim($aFormValues['county']);
		$town					= trim($aFormValues['town']);
		$zipcode				= trim($aFormValues['zipcode']);
		$address 				= htmlspecialchars(trim($aFormValues['address']), ENT_QUOTES, 'UTF-8');
		$employee_content		= trim($aFormValues['employee_content']);
		$member_no				= trim($aFormValues['member_no']);
		$employee_type			= trim($aFormValues['employee_type']);
		$company_id				= trim($aFormValues['company_id']);
		$team_id				= trim($aFormValues['team_id']);
		$construction_id		= trim($aFormValues['construction_id']);
		$labor_health_insurance	= trim($aFormValues['labor_health_insurance']);
		$labor_Pension			= trim($aFormValues['labor_Pension']);
		$actual_insurance		= trim($aFormValues['actual_insurance']);

		//存入info實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();

		$Qry="UPDATE employee set
				 employee_name		= '$employee_name'
				,old_employee_id	= '$old_employee_id'
				,id_number			= '$id_number'
				,gender				= '$gender'
				,birthday			= '$birthday'
				,blood_type			= '$blood_type'
				,mobile_no			= '$mobile_no'
				,emergency_contact	= '$emergency_contact'
				,emergency_mobile_no = '$emergency_mobile_no'
				,`start_date`		= '$start_date'
				,`resignation_date`	= '$resignation_date'
				,seniority			= '$seniority'
				,county				= '$county'
				,town				= '$town'
				,zipcode			= '$zipcode'
				,`address`			= '$address'
				,`employee_content`	= '$employee_content'
				,member_no			= '$member_no'
				,employee_type		= '$employee_type'
				,company_id			= '$company_id'
				,team_id			= '$team_id'
				,construction_id	= '$construction_id'
				,actual_insurance	= '$actual_insurance'
				,labor_health_insurance	= '$labor_health_insurance'
				,labor_Pension		= '$labor_Pension'
				,last_modify		= now()
				where auto_seq = '$auto_seq'";
				
		$mDB->query($Qry);
        $mDB->remove();

		
	return $objResponse;
}

$xajax->processRequest();



$auto_seq = $_GET['auto_seq'];
$fm = $_GET['fm'];

$mess_title = $title;

$pro_id = 'emp';


$mDB = "";
$mDB = new MywebDB();
$Qry="SELECT a.*,b.company_name,c.team_name FROM employee a 
LEFT JOIN company b ON b.company_id = a.company_id
LEFT JOIN team c ON c.team_id = a.team_id
WHERE a.auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$employee_id = $row['employee_id'];
	$employee_name = $row['employee_name'];
	$old_employee_id = $row['old_employee_id'];
	$id_number = $row['id_number'];
	$gender = $row['gender'];
	$birthday = $row['birthday'];
	$blood_type = $row['blood_type'];
	$mobile_no = $row['mobile_no'];
	$emergency_contact = $row['emergency_contact'];
	$emergency_mobile_no = $row['emergency_mobile_no'];
	$start_date = $row['start_date'];
	$resignation_date = $row['resignation_date'];
	$seniority = $row['seniority'];
	$county = $row['county'];
	$town = $row['town'];
	$zipcode = $row['zipcode'];
	$address = $row['address'];
	$employee_content = $row['employee_content'];
	$create_date = $row['create_date'];
	$last_modify = $row['last_modify'];
	$member_no = $row['member_no'];
	$employee_type = $row['employee_type'];
	$company_id = $row['company_id'];
	$company_name = $row['company_name'];
	$team_id = $row['team_id'];
	$team_name = $row['team_name'];
	$construction_id = $row['construction_id'];
	$labor_health_insurance = $row['labor_health_insurance'];
	$labor_Pension = $row['labor_Pension'];
	$actual_insurance = $row['actual_insurance'];

	if ($gender == "1") {
		$m_gender = "小姐";
	} else {
		$m_gender = "先生";
	}



}


$mDB->remove();


$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="save" class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-check-circle"></i>&nbsp;存檔</button>
	<button id="cancel" class="btn btn-secondary display_none" type="button" onclick="setCancel();" style="padding: 5px 15px;"><i class="bi bi-x-circle"></i>&nbsp;取消</button>
	<button id="close" class="btn btn-danger" type="button" onclick="parent.myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;
	
$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	max-width: 1240px; !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:150px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:400px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div3 {width:100%;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.code_class {
	width:120px;
	text-align:right;
	padding:0 5px 0 0;
}

.maxwidth {
    width: 100%;
    max-width: 250px;
}

</style>

EOT;

} else {
	$isMobile = 1;

$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100%;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}
.field_div3 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.code_class {
	width:auto;
	text-align:left;
	padding:0 5px 0 0;
}

.maxwidth {
    width: 100%;
}

</style>
EOT;

}



$show_center=<<<EOT

$style_css

<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start" style="margin-top: 5px;">
			$mess_title
		</div>
		<div class="float-end" style="margin-top: -5px;">
			$show_savebtn
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="modifyForm" name="modifyForm" enctype="multipart/form-data" action="javascript:void(null);">
			<div class="w-100 mb-5">
				<div class="field_container3">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div2">
									<div class="inline code_class">員工代號:</div>
									<div class="inline" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$employee_id</div>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div3">
									<div class="inline me-1 text-nowrap">
										<div class="inline code_class">姓名:</div>
										<div class="inline me-1" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$employee_name</div>
										<div class="inline" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$m_gender</div>
									</div>
									<div class="inline me-1 text-nowrap">
										<div class="inline code_class">職務:</div>
										<div class="inline" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$employee_type</div>
									</div>
									<div class="inline me-1 text-nowrap">
										<div class="inline code_class">公司:</div>
										<div class="inline" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$company_name</div>
									</div>
									<div class="inline me-1 text-nowrap">
										<div class="inline code_class">團隊:</div>
										<div class="inline" style="padding:8px 0;font-size:18px;color:blue;text-align:left;font-weight:700;">$team_name</div>
									</div>
								</div> 
							</div> 
						</div>
					</div>
					<hr class="style_b">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">工地日薪:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="labor_health_insurance" name="labor_health_insurance" size="50" maxlength="120" value="$labor_health_insurance" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">辦公室日薪:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="labor_Pension" name="labor_Pension" size="50" maxlength="120" value="$labor_Pension" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">調薪記錄:</div> 
								<div class="inline w-auto mt-2">
									<button type="button" class="btn btn-success me-2">調薪作業</button>
									<div class="inline size14 weight">最新調薪：2024-xx-xx</div>
								</div> 
							</div> 
						</div>
					</div>
					<hr class="style_b">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">勞健保:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="labor_health_insurance" name="labor_health_insurance" size="50" maxlength="120" value="$labor_health_insurance" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">勞退:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="labor_Pension" name="labor_Pension" size="50" maxlength="120" value="$labor_Pension" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div>
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="web_id" value="$web_id" />
						<input type="hidden" name="auto_seq" value="$auto_seq" />
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function SaveValue(thisform) {
	xajax_SaveValue(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function setEdit() {
	$('#close', window.document).addClass("display_none");
	$('#cancel', window.document).removeClass("display_none");
}

function setCancel() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
	document.forms[0].reset();
}

function setSave() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
}
	

$(document).ready(async function() {
	//等待其他資源載入完成，此方式適用大部份瀏覽器
	await new Promise(resolve => setTimeout(resolve, 100));
	$('#employee_name').focus();
});

</script>

EOT;

?>