<?php

session_start();
$memberID = $_SESSION['memberID'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");

function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	if (trim($aFormValues['company_id']) == "") {
		$objResponse->script("jAlert('警示', '請選擇公司', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	if (trim($aFormValues['employee_id']) == "") {
		$objResponse->script("jAlert('警示', '請輸入員工代號', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	if (trim($aFormValues['employee_name']) == "") {
		$objResponse->script("jAlert('警示', '請輸入員工姓名', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	
	$fm					= trim($aFormValues['fm']);
	$site_db			= trim($aFormValues['site_db']);
	$templates			= trim($aFormValues['templates']);
	$web_id				= trim($aFormValues['web_id']);
	$company_id			= trim($aFormValues['company_id']);
	$employee_id		= trim($aFormValues['employee_id']);
	$employee_name 		= htmlspecialchars(trim($aFormValues['employee_name']), ENT_QUOTES, 'utf8');
	$memberID			= trim($aFormValues['memberID']);
	

	
	//存入實體資料庫中
	$mDB = "";
	$mDB = new MywebDB();
	
	//檢查帳號是否重複
	$Qry="select employee_id from employee where employee_id = '$employee_id'";
	$mDB->query($Qry);
	$total = $mDB->rowCount();
	if ($total > 0) {
		$mDB->remove();
		$objResponse->script("jAlert('警示', '您輸入的公司代號已重複，請重新輸入新的', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	
	$Qry="insert into employee (company_id,employee_id,employee_name,create_date,last_modify) values ('$company_id','$employee_id','$employee_name',now(),now())";
	$mDB->query($Qry);
	//再取出auto_seq
	$Qry="select auto_seq from employee where employee_id = '$employee_id' order by auto_seq desc limit 0,1";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		//已找到符合資料
		$row=$mDB->fetchRow(2);
		$auto_seq = $row['auto_seq'];
	}
	$mDB->remove();
	if (!empty($auto_seq)) {
		$objResponse->script("myDraw();");
		$objResponse->script("art.dialog.tips('已新增，請繼續輸入其他資料...',2);");
		$objResponse->script("window.location='/?ch=edit&auto_seq=$auto_seq&fm=$fm';");
	} else {
		$objResponse->script("jAlert('警示', '發生不明原因的錯誤，資料未新增，請再試一次!', 'red', '', 2000);");
		$objResponse->script("parent.$.fancybox.close();");
	}
	
	return $objResponse;	
}

$xajax->processRequest();

$fm = $_GET['fm'];
$t = $_GET['t'];

$mess_title = $title;


$mDB = "";
$mDB = new MywebDB();

//載入公司
if  ((($super_advanced=="Y") && ($advanced_readonly <> "Y")) && ($super_admin <> "Y")) {
	$Qry="SELECT a.company_id,a.company_name FROM company a
	RIGHT JOIN group_company b ON b.company_id = a.company_id and b.member_no = '$memberID'
	WHERE a.company_id <> ''
	ORDER BY a.company_id";
} else {
	$Qry="SELECT company_id,company_name FROM company ORDER BY company_id";
}

$mDB->query($Qry);


$select_company = "";
$select_company  = "<select class=\"form-select\" name=\"company_id\" id=\"company_id\" style=\"width:auto;\">";
$select_company .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_company_id = $row['company_id'];
		$ch_company_name = $row['company_name'];
		$select_company .= "<option value='$ch_company_id' ".mySelect($ch_company_id,$company_id).">$ch_company_name $ch_company_id</option>";
	}
}
$select_company .= "</select>";

$mDB->remove();




if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;

$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	max-width: 450px; !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;max-width:100px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:300px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

</style>
EOT;

} else {
	$isMobile = 1;
$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

</style>
EOT;

}
	


$show_center=<<<EOT
$style_css
<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start">
			$mess_title
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="addForm" name="addForm" enctype="multipart/form-data" action="javascript:void(null);">
				<div class="field_container3">
					<div>
						<div class="field_div1">公司:</div> 
						<div class="field_div2">
							$select_company
						</div> 
					</div>
					<div>
						<div class="field_div1">員工代號:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext" id="employee_id" name="employee_id" placeholder="請輸入員工代號" size="50" maxlength="50" style="width:100%;max-width:250px;"/>
						</div> 
					</div>
					<div>
						<div class="field_div1">姓名:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext" id="employee_name" name="employee_name" placeholder="請輸入姓名" size="20" maxlength="20" style="width:100%;max-width:250px;"/>
						</div> 
					</div>
				</div>
				<div class="form_btn_div mt-5">
					<input type="hidden" name="fm" value="$fm" />
					<input type="hidden" name="site_db" value="$site_db" />
					<input type="hidden" name="templates" value="$templates" />
					<input type="hidden" name="web_id" value="$web_id" />
					<input type="hidden" name="memberID" value="$memberID" />
					<button class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 10px;margin-right: 10px;"><i class="bi bi-check-lg green"></i>&nbsp;確定新增</button>
					<button class="btn btn-danger" type="button" onclick="parent.myDraw();parent.$.fancybox.close();" style="padding: 10px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('addForm'));
	thisform.submit();
}

var myDraw = function(){
	var oTable;
	oTable = parent.$('#db_table').dataTable();
	oTable.fnDraw(false);
}

$(document).ready(async function() {
	//等待其他資源載入完成，此方式適用大部份瀏覽器
	await new Promise(resolve => setTimeout(resolve, 100));
	$('#employee_id').focus();
});

</script>
EOT;

?>