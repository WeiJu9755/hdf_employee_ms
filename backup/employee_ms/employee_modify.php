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
		$department				= trim($aFormValues['department']);
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
				,department			= '$department'
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
$Qry="select * from employee where auto_seq = '$auto_seq'";
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
	$team_id = $row['team_id'];
	$construction_id = $row['construction_id'];
	$department = $row['department'];
	$labor_health_insurance = $row['labor_health_insurance'];
	$labor_Pension = $row['labor_Pension'];
	$actual_insurance = $row['actual_insurance'];

	$m_gender = "";
	$m_gender .=  "<option value='' ".mySelect('',$gender)."></option>";
	$m_gender .=  "<option value='1' ".mySelect('1',$gender).">小姐</option>";
	$m_gender .=  "<option value='2' ".mySelect('2',$gender).">先生</option>";	


	$m_blood_type = "";
	$m_blood_type .=  "<option value='' ".mySelect('',$blood_type)."></option>";
	$m_blood_type .=  "<option value='A' ".mySelect('A',$blood_type).">A</option>";
	$m_blood_type .=  "<option value='B' ".mySelect('B',$blood_type).">B</option>";	
	$m_blood_type .=  "<option value='AB' ".mySelect('AB',$blood_type).">AB</option>";	
	$m_blood_type .=  "<option value='O' ".mySelect('O',$blood_type).">O</option>";	

	/*
	$m_employee_type = "";
	$m_employee_type .=  "<option value='' ".mySelect('',$employee_type)."></option>";
	$m_employee_type .=  "<option value='工班' ".mySelect('工班',$employee_type).">工班</option>";
	$m_employee_type .=  "<option value='移工' ".mySelect('移工',$employee_type).">移工</option>";
	$m_employee_type .=  "<option value='老闆' ".mySelect('老闆',$employee_type).">老闆</option>";
	*/
	
	$tw_county = array('台北市','基隆市','新北市','宜蘭縣','新竹市','新竹縣','桃園市','苗栗縣','台中市','彰化縣','南投縣','嘉義市','嘉義縣','雲林縣','台南市','高雄市','屏東縣','台東縣','花蓮縣','澎湖縣','金門縣','連江縣');
	$m_county = "";
	$m_county .=  "<option value=''>請選擇</option>";
	$count_len = sizeof($tw_county);
	for ( $i = 0; $i <= $count_len-1; $i++ ) {
		$m_county .=  "<option value=\"$tw_county[$i]\" ".mySelect($tw_county[$i],$county).">$tw_county[$i]</option>";
	}
	
}


//載入所有公司
if  ((($super_advanced=="Y") && ($advanced_readonly <> "Y")) && ($super_admin <> "Y")) {
	$Qry="SELECT a.company_id,a.company_name FROM company a
	RIGHT JOIN group_company b ON b.company_id = a.company_id and b.member_no = '$memberID'
	ORDER BY a.company_id";
} else {
	$Qry="SELECT company_id,company_name FROM company ORDER BY company_id";
}
$mDB->query($Qry);
$select_company = "";
$select_company .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_company_id = $row['company_id'];
		$ch_company_name = $row['company_name'];
		$select_company .= "<option value=\"$ch_company_id\" ".mySelect($ch_company_id,$company_id).">$ch_company_name $ch_company_id</option>";
	}
}

//載入部門別
$Qry="SELECT caption FROM items where pro_id ='department' ORDER BY pro_id,orderby";
$mDB->query($Qry);
$select_department = "";
$select_department .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_department = $row['caption'];
		$select_department .= "<option value=\"$ch_department\" ".mySelect($ch_department,$department).">$ch_department</option>";
	}
}

//載入職務別
$Qry="SELECT caption FROM items where pro_id ='position' ORDER BY pro_id,orderby";
$mDB->query($Qry);
$select_employee_type = "";
$select_employee_type .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_employee_type = $row['caption'];
		$select_employee_type .= "<option value=\"$ch_employee_type\" ".mySelect($ch_employee_type,$employee_type).">$ch_employee_type</option>";
	}
}


//載入所有團隊
if  ((($super_advanced=="Y") && ($advanced_readonly <> "Y")) && ($super_admin <> "Y")) {
	$Qry="SELECT a.team_id,a.team_name FROM team a
	RIGHT JOIN group_company b ON b.company_id = a.company_id and b.member_no = '$memberID'
	ORDER BY a.team_id";
} else {
	$Qry="SELECT team_id,team_name FROM team ORDER BY team_id";
}

$mDB->query($Qry);
$select_team = "";
$select_team .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_team_id = $row['team_id'];
		$ch_team_name = $row['team_name'];
		$select_team .= "<option value=\"$ch_team_id\" ".mySelect($ch_team_id,$team_id).">$ch_team_name $ch_team_id</option>";
	}
}

//載入所有工地
$Qry="SELECT construction_id,construction_site FROM construction ORDER BY construction_id";
$mDB->query($Qry);
$select_construction = "";
$select_construction .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_construction_id = $row['construction_id'];
		$ch_construction_site = $row['construction_site'];
		$select_construction .= "<option value=\"$ch_construction_id\" ".mySelect($ch_construction_id,$construction_id).">$ch_construction_site $ch_construction_id</option>";
	}
}

//載入實際投保公司
$Qry="select company_id,company_name from company ORDER BY company_id";
$mDB->query($Qry);
$select_actual_insurance = "";
$select_actual_insurance .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_company_id = $row['company_id'];
		$ch_company_name = $row['company_name'];
		$select_actual_insurance .= "<option value=\"$ch_company_id\" ".mySelect($ch_company_id,$actual_insurance).">$ch_company_name $ch_company_id</option>";
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

.code_class {
	width:150px;
	text-align:right;
	padding:0 10px 0 0;
}

.maxwidth {
    width: 100%;
    max-width: 300px;
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

.code_class {
	width:auto;
	text-align:left;
	padding:0 10px 0 0;
}

.maxwidth {
    width: 100%;
}

</style>
EOT;

}



$show_center=<<<EOT
<script src="/os/aj-address/js/aj-address.js" type="text/javascript"></script>

<script src="/os/Autogrow-Textarea/jquery.autogrowtextarea.min.js"></script>

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
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">姓名:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="employee_name" name="employee_name" size="20" maxlength="20" value="$employee_name" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">身分證字號:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="id_number" name="id_number" size="50" maxlength="50" value="$id_number" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">性別:</div> 
								<div class="field_div2">
									<select name="gender" id="gender" class="input_button mb-2 me-3" placeholder="請選擇性別" onchange="setEdit();">
										$m_gender
									</select>
									血型:
									<select name="blood_type" id="blood_type" class="input_button mb-2"  placeholder="請選擇血型" onchange="setEdit();">
										$m_blood_type
									</select>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">出生日期:</div> 
								<div class="field_div2">
									<div class="input-group maxwidth" id="birthday" >
										<input type="text" class="form-control" name="birthday" placeholder="請輸入出生日期" aria-describedby="birthday" value="$birthday">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#birthday" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#birthday').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div>
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">連絡電話:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="mobile_no" name="mobile_no" size="50" maxlength="50" value="$mobile_no" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">舊工號:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="old_employee_id" name="old_employee_id" size="50" maxlength="50" value="$old_employee_id" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">緊急連絡人:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="emergency_contact" name="emergency_contact" size="50" maxlength="50" value="$emergency_contact" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">緊急連絡人電話:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="emergency_mobile_no" name="emergency_mobile_no" size="50" maxlength="50" value="$emergency_mobile_no" onchange="setEdit();"/>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">入職日期:</div> 
								<div class="field_div2">
									<div class="input-group maxwidth" id="start_date" >
										<input type="text" class="form-control" name="start_date" placeholder="請輸入入職日期" aria-describedby="start_date" value="$start_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#start_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#start_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div>
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">離職日期:</div> 
								<div class="field_div2">
									<div class="input-group maxwidth" id="resignation_date" >
										<input type="text" class="form-control" name="resignation_date" placeholder="請輸入離職日期" aria-describedby="resignation_date" value="$resignation_date">
										<button class="btn btn-outline-secondary input-group-append input-group-addon" type="button" data-target="#resignation_date" data-toggle="datetimepicker"><i class="bi bi-calendar"></i></button>
									</div>
									<script type="text/javascript">
										$(function () {
											$('#resignation_date').datetimepicker({
												locale: 'zh-tw'
												,format:"YYYY-MM-DD"
												,allowInputToggle: true
											});
										});
									</script>
								</div>
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">地址:</div> 
								<div class="inline mt-2">
									<div class="inline me-2 mb-2">
										<select class="input_button" id="county" name="county">$m_county</select>
										<select class="input_button" id="town" name="town"></select>
										<input readonly type="text" class="inputtext" id="zipcode" name="zipcode" style="width:100%;max-width: 80px;" value="$zipcode"/>
									</div>
									<div class="inline">
										<input type="text" class="inputtext " id="address" name="address" size="80" maxlength="240" style="width:100%;max-width:600px;" value="$address" onchange="setEdit();"/>
									</div>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">公司:</div> 
								<div class="field_div2">
									<select id="company_id" name="company_id" placeholder="請選擇公司" class="maxwidth">
										$select_company
									</select>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">職務:</div> 
								<div class="field_div2">
									<select name="employee_type" id="employee_type" class="input_button mb-2" placeholder="請選擇職務" onchange="setEdit();">
										$select_employee_type
									</select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">團隊:</div> 
								<div class="field_div2">
									<select id="team_id" name="team_id" placeholder="請選擇團隊" class="maxwidth">
										$select_team
									</select>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">主要工地:</div> 
								<div class="field_div2">
									<select id="construction_id" name="construction_id" placeholder="請選擇主要工地" class="maxwidth">
										$select_construction
									</select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">會員帳號:</div> 
								<div class="field_div2">
									<input type="text" class="inputtext maxwidth" id="member_no" name="member_no" size="50" maxlength="50" value="$member_no" onchange="setEdit();"/>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">實際投保公司:</div> 
								<div class="field_div2">
									<select id="actual_insurance" name="actual_insurance" placeholder="請選擇公司" class="maxwidth">
										$select_actual_insurance
									</select>
								</div> 
							</div> 
						</div>
					</div>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1">部門別:</div> 
								<div class="field_div2">
									<select id="department" name="department" placeholder="請選擇部門" class="maxwidth">
										$select_department
									</select>
								</div> 
							</div> 
							<div class="col-lg-6 col-sm-12 col-md-12">
								<div class="field_div1"></div> 
								<div class="field_div2">
								</div> 
							</div> 
						</div>
					</div>
					<!--
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
					-->
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-md-12">
								<div class="field_div1">備註:</div> 
								<div class="inline mt-2">
									<textarea class="inputtext w-100 p-3" id="employee_content" name="employee_content" cols="80" rows="2" style="max-width: 100%;">$employee_content</textarea>
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
//	$('#full_content').val(CKEDITOR.instances.full_content.getData());
	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function SaveValue(thisform) {
//	$('#full_content').val(CKEDITOR.instances.full_content.getData());
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
	
$(document).ready(function() {
	$("#employee_content").autoGrow({
		extraLine: true // Adds an extra line at the end of the textarea. Try both and see what works best for you.
	});
});


init_address();
set_address('$county','$town');

$(document).ready(async function() {
	//等待其他資源載入完成，此方式適用大部份瀏覽器
	await new Promise(resolve => setTimeout(resolve, 100));
	$('#employee_name').focus();
});

</script>

EOT;

?>