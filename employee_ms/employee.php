<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = "0";
} else {
	$isMobile = "1";
}


@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("DeleteRow");
function DeleteRow($auto_seq,$site_db,$pro_id){

	$objResponse = new xajaxResponse();
	
	$mDB = "";
	$mDB = new MywebDB();
	
	$web_id = "sales.eshop";
		
	//取得檔案目錄
	$attach_path = "/website/webdata/".$site_db."/".$web_id."/employee/".$pro_id."/attach".$auto_seq."/";

	//刪除檔案資料庫
	//$Qry = "delete from file_caption where web_id = '$web_id' and file_id like '%$attach_path%'";
	$Qry = "delete from file_caption where web_id = '$web_id' and pro_id = '$pro_id' and ftype ='employee' and localpath = 'attach' and seq = '$auto_seq'";
	$mDB->query($Qry);
	
	//刪除關連附加檔案
	if (file_exists($attach_path)) {
		if (is_dir($attach_path)) {
			SureRemoveDir($attach_path,true);
		}
	}
		
	//刪除主資料
	$Qry="delete from employee where auto_seq = '$auto_seq'";
	$mDB->query($Qry);
	
	$mDB->remove();
	
    $objResponse->script("oTable = $('#db_table').dataTable();oTable.fnDraw(false)");
    $objResponse->script("art.dialog.tips('相關資料已全數刪除!',2)");

	return $objResponse;
	
}


$xajax->registerFunction("returnValue");
function returnValue($web_id,$auto_seq,$makeby,$site_db,$fm,$memberID,$pubweburl,$pro_id){
	$objResponse = new xajaxResponse();

	//取得圖片目錄
	$files_dir0 = "/webdata/".$site_db."/".$web_id."/employee/".$pro_id."/attach".$auto_seq;
	$files_dir1 = "/website".$files_dir0."/";
	
	//從資料庫中讀取圖片資料
	$mDB = "";
	$mDB = new MywebDB();
	//$Qry="select file_id,caption,orderby from file_caption where web_id = '$web_id' and file_id like '%$files_dir1%' order by orderby";
	$Qry="select file_id,caption,orderby from file_caption where web_id = '$web_id' and pro_id = '$pro_id' and ftype ='employee' and localpath = 'attach' and seq = '$auto_seq' order by orderby";
	$mDB->query($Qry);
	$files_list = "";
	$n = 0;
	$file_size_total = 0;
	
	if ($mDB->rowCount() > 0) {
		while ($row=$mDB->fetchRow(2)) {
			$o_file = $row['file_id'];
			$file_size = filesize($o_file);
			$file_size_total += $file_size;
			$n++;
		}
	}
	
	$mDB->remove();
	
	$show_file_size_total = "<span style=\"white-space: pre;\">(".byteConvert($file_size_total).")</span>";
	
	
	if ($n > 0)
		$show_files_total = "<i class=\"bi bi-file-earmark-medical blue01 me-1\" title=\"附加檔案\"></i><span class=\"badge text-bg-info me-1\">$n</span><span class=\"red weight me-2\">".$show_file_size_total."</span>";
	else 
		$show_files_total = "";
	
	
	$objResponse->assign("files_total".$auto_seq,"innerHTML",$show_files_total);
	
    return $objResponse;
}

$xajax->registerFunction("binding");
function binding(){
	$objResponse = new xajaxResponse();

	//@include_once("/website/class/memberinfo_class.php");

	$mDB = "";
	$mDB = new MywebDB();

	$mDB2 = "";
	$mDB2 = new MywebDB();

	$mDB3 = "";
	$mDB3 = new MywebDB();

	$Qry="SELECT * FROM employee ORDER BY auto_seq";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		while ($row=$mDB->fetchRow(2)) {
			$employee_id = $row['employee_id'];

			$Qry2="SELECT member_no FROM memberinfo.member WHERE employee_id = '$employee_id'";
			$mDB2->query($Qry2);
			if ($mDB2->rowCount() > 0) {
				$row2=$mDB2->fetchRow(2);
				$member_no = $row2['member_no'];
				
				$Qry3 = "UPDATE employee SET member_no = '$member_no' WHERE employee_id = '$employee_id'";
				$mDB3->query($Qry3);
			}
		}
	}
	
	$mDB3->remove();
	$mDB2->remove();
	$mDB->remove();
	
    $objResponse->script("myDraw();");
	$objResponse->script("autoclose('提示', '已完成會員帳號綁定及檢查！', 1500);");
	
    return $objResponse;
}


$xajax->processRequest();

$fm = $_GET['fm'];
$t = $_GET['t'];
$mc = $_GET['mc'];
$sc = $_GET['sc'];

$tb = "employee";

$pro_id = "emp";

$m_t = urlencode($_GET['t']);

$mess_title = $t;


$today = date("Y-m-d");



$dataTable_de = getDataTable_de();
$Prompt = getlang("提示訊息");
$Confirm = getlang("確認");
$Cancel = getlang("取消");




$pubweburl = "//".$domainname;


//設定權限
$cando = "N";
if ($powerkey=="A") {
	$cando = "Y";
} else if ($super_admin=="Y") {
	if ($admin_readonly <> "Y") {
		$cando = "Y";
	}
} else if ($super_advanced=="Y") {
	if ($advanced_readonly <> "Y") {
		$cando = "Y";
	}
}


if ($cando=="Y") {


	
$importexcel=<<<EOT
	<button type="button" class="btn btn-primary text-nowrap" onclick="openfancybox_edit('/index.php?ch=importexcel&t=匯入員工Excel資料檔&fm=$fm',850,350,'');"><i class="bi bi-filetype-xls"></i>&nbsp;匯入Excel</button>
EOT;
$exportexcel=<<<EOT
	<a role="button" class="btn btn-primary text-nowrap" href="/index.php?ch=exportexcel&site_db=$site_db&web_id=$web_id&fm=$fm"><i class="bi bi-filetype-xls"></i>&nbsp;匯出Excel</a>
EOT;
	


$show_modify_btn=<<<EOT
<div class="text-center my-2">
	<div class="btn-group me-2 mb-2 text-nowrap" role="group">
		<button type="button" class="btn btn-danger text-nowrap" onclick="openfancybox_edit('/index.php?ch=add&t=$t&fm=$fm',1240,'96%','');"><i class="bi bi-plus-circle"></i>&nbsp;新增資料</button>
		<button type="button" class="btn btn-success text-nowrap" onclick="myDraw();"><i class="bi bi-arrow-repeat"></i>&nbsp;重整</button>
	</div>
	<div class="btn-group mb-2 text-nowrap" role="group">
		<button type="button" class="btn btn-warning text-nowrap" onclick="binding();"><i class="bi bi-person-fill-exclamation"></i>&nbsp;按此進行員工綁定會員帳號及檢查</button>
	</div>
	<div class="btn-group mb-2 text-nowrap" role="group">
		$importexcel
		$exportexcel
		<!--
		<button type="button" class="btn btn-warning text-nowrap" onclick="openfancybox_edit('/index.php?ch=report&crm_id=$crm_id&fm=$fm',1240,'96%','');"><i class="bi bi-file-text"></i>&nbsp;報表</button>
		-->
	</div>
</div>
EOT;

} else {
$show_modify_btn=<<<EOT
<div class="size14 red m-auto text-center my-2 px-2 py-1 border border-danger" style="width:100px;">唯讀</div>
EOT;
}


$list_view=<<<EOT
<div class="w-100 m-auto p-1 mb-5 bg-white" style="max-width:1640px;">
	<div class="size20 pt-3 text-center">員工管理</div>
	$show_modify_btn
	<table class="table table-bordered border-dark w-100" id="db_table" style="min-width:1640px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th scope="col" class="text-start text-nowrap" style="width:10%;">姓名/工號/舊工號</th>
				<th scope="col" class="text-start" style="width:15%;">身分證/出生日/血型/電話</th>
				<th scope="col" class="text-start" style="width:8%;">緊急連絡人</th>
				<th scope="col" class="text-start" style="width:22%;">地址/附檔</th>
				<th scope="col" class="text-center" style="width:15%;">入職-離職日期/年資</th>
				<th scope="col" class="text-start" style="width:26%;">公司/職務/團隊/工地</th>
				<th scope="col" class="text-center text-nowrap" style="width:5%;">處理</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="7" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
	</table>
</div>
EOT;

	
$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}

	
$show_center=<<<EOT
<style type="text/css">
#db_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}

</style>

$list_view

<script>
	var oTable;
	$(document).ready(function() {
		$('#db_table').dataTable( {
			"processing": true,
			"serverSide": true,
			"responsive":  {
				details: true
			},//RWD響應式
			"scrollX": '$scroll',
			"paging": true,
			"pageLength": -1,
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"pagingType": "full_numbers",  //分页样式： simple,simple_numbers,full,full_numbers
			"searching": true,  //禁用原生搜索
			"ordering": false,
			"ajaxSource": "/smarty/templates/$site_db/$templates/sub_modal/base/employee_ms/server_employee.php?site_db=$site_db&web_id=$web_id",
			"language": {
						"sUrl": "$dataTable_de"
					},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) { 


				//預覽連結
				var preview = "openfancybox_edit('/?ch=view&auto_seq="+aData[15]+"&fm=$fm',1240,'96%',false);";
				
				var show_btn = '';
				
				if ('$cando'=="Y") {

					var url1 = "openfancybox_edit('/index.php?ch=edit&auto_seq="+aData[15]+"&fm=$fm',1240,'96%','');";
					var url3 = "openfancybox_edit('/index.php?tb=$tb&pro_id=$pro_id&auto_seq="+aData[15]+"&fm=attach02','96%','96%','');";

					var mdel = "myDel("+aData[15]+",'$site_db','$pro_id');";
					show_btn = '<div class="btn-group text-nowrap">'
							+'<button type="button" class="btn btn-light" onclick="'+url1+'" title="修改"><i class="bi bi-pencil-square"></i></button>'
							+'<button type="button" class="btn btn-light" onclick="'+url3+'" title="上傳檔案"><i class="bi bi-file-arrow-up"></i></button>'
							+'<button type="button" class="btn btn-light" onclick="'+mdel+'" title="刪除"><i class="bi bi-trash"></i></button>'
							+'</div>';

				}
				
				var m_gender = "";
				if (aData[3] == "1")
					m_gender = "小姐";
				else if (aData[3] == "2")
					m_gender = "先生";


				//舊工號
				var old_employee_id = "";
				if (aData[24] != null && aData[24] != "")
					old_employee_id = '/'+aData[24];


				var m_employee = "";
				if (aData[1] != null && aData[1] != "") {
					//m_employee = '<div class="inline text-nowrap"><a href="javascript:void(0);" onclick="'+preview+'" class="blue02 size12 weight vbottom">'+aData[1]+'</a>'+' '+m_gender+'</div><div>'+aData[0]+'</div>';
					m_employee = '<div class="inline text-nowrap"><span class="blue02 size12 weight vbottom">'+aData[1]+'</span>'+' '+m_gender+'</div><div class="text-nowrap">'+aData[0]+old_employee_id+'</div>';
				}
			
				
				$('td:eq(0)', nRow).html( m_employee );

				//身分證字號
				var id_number = "";
				if (aData[2] != null && aData[2] != "")
					id_number = aData[2];

				//出生日期
				var birthday = "";
				if (aData[4] != null && aData[4] != "" && aData[4] != "0000-00-00")
					birthday = aData[4];

				//血型
				var blood_type = "";
				if (aData[5] != null && aData[5] != "")
					blood_type = aData[5]+'型';

				//連絡電話
				var mobile_no = "";
				if (aData[6] != null && aData[6] != "")
					mobile_no = aData[6];

				var base_info = '<div class="text-nowrap"><div class="inline size12 weight me-2 vbottom">'+id_number+'</div><div class="inline">'+birthday+'</div></div>'
							+'<div class="text-nowrap"><div class="inline me-2">'+blood_type+'</div><div class="inline">'+mobile_no+'</div></div>'

				$('td:eq(1)', nRow).html( base_info );


				//緊急連絡人
				var emergency_contact = "";
				if (aData[7] != null && aData[7] != "")
					emergency_contact = aData[7];

				var emergency_mobile_no = "";
				if (aData[8] != null && aData[8] != "")
					emergency_mobile_no = aData[8];

				var emergency_info = '<div class="size12 weight">'+emergency_contact+'</div>'
							+'<div>'+emergency_mobile_no+'</div>'

				$('td:eq(2)', nRow).html( emergency_info );


				var zipcode = "";
				if (aData[11] != null && aData[11] != "")
					zipcode = aData[11];
					
				var county = "";
				if (aData[12] != null && aData[12] != "")
					county = aData[12];

				var town = "";
				if (aData[13] != null && aData[13] != "")
					town = aData[13];

				var address = "";
				if (aData[14] != null && aData[14] != "")
					address = aData[14];
					
					
				var files_total = '<span class="text-nowrap" id="files_total'+aData[15]+'"></span>';
				xajax_returnValue('$web_id',aData[15],aData[3],'$site_db','$fm','$memberID','$pubweburl','$pro_id');
					
					
				var m_info = '<div class="weight"><span class="size12 text-nowrap me-1 vbottom">'+zipcode+'</span> <span class="text-nowrap size12 vbottom">'+county+'</span><span class="text-nowrap size12 vbottom">'+town+'</span><span>'+address+'</span></div>'
							+'<div class="inline">'+files_total+'</div>';

				$('td:eq(3)', nRow).html( m_info );

				//計算年資
				var seniority = '';
				if (aData[9] != null && aData[9] != "" && aData[9] != "0000-00-00") {
					var start_date = new Date(aData[9]);

					//檢查離職日期是否有設定
					if (aData[26] != null && aData[26] != "" && aData[26] != "0000-00-00") {
						var end_date = new Date(aData[26]);
						const difference = getDifferenceInYMD(start_date,end_date);
						seniority = difference.years+'年'+difference.months+'月'+difference.days+'天';
						$('td:eq(4)', nRow).html( '<div class="text-center size12 weight">'+aData[9]+' ～ '+aData[26]+'</div><div class="text-center"><div class="inline">'+seniority+'</div><div class="inline red ms-1">已離職</div></div>' );
					} else {
						const difference = getDifferenceInYMD(start_date);
						seniority = difference.years+'年'+difference.months+'月'+difference.days+'天';
						$('td:eq(4)', nRow).html( '<div class="text-center size12 weight">'+aData[9]+'</div><div class="text-center">'+seniority+'</div>' );
					}

				} else {
					$('td:eq(4)', nRow).html( '' );
				}

				var company_info = '';

				var company_name = '';
				if (aData[18] != null && aData[18] != "")
					company_name = '<div class="inline size12 weight me-3">'+aData[19]+'</div>';

				var employee_type = '';
				if (aData[17] != null && aData[17] != "")
					employee_type = '<div class="inline size12 me-3">'+aData[17]+'</div>';

				var department = '';
				if (aData[25] != null && aData[25] != "")
					department = '<div class="inline size12">'+aData[25]+'</div>';

				var team_name = '';
				if (aData[20] != null && aData[20] != ""){
					if (aData[21] != null && aData[21] != ""){
						team_name = '<div class="inline size12 weight me-3">'+aData[21]+'</div>';
					}
				}

				var construction_site = '';
				if (aData[22] != null && aData[22] != "")
					construction_site = '<div class="inline size12">'+aData[23]+'</div>';


				company_info = '<div>'+company_name+employee_type+department+'</div>'
							+'<div>'+team_name+construction_site+'</div>';

				$('td:eq(5)', nRow).html( company_info );

				/*
				var salary_url = "openfancybox_edit('/index.php?ch=salary&auto_seq="+aData[15]+"&fm=$fm',1240,'96%','');";

				var salary_btn = '<button type="button" class="btn btn-outline-secondary btn-sm text-nowrap" onclick="'+salary_url+'" title="薪資設定"><i class="bi bi bi-cash-coin"></i>&nbsp;薪資設定</button>';

				$('td:eq(6)', nRow).html( salary_btn );
				*/

				$('td:eq(6)', nRow).html( '<div class="text-center">'+show_btn+'</div>' );
				
				return nRow;
			
			}
			
		});
	
		/* Init the table */
		oTable = $('#db_table').dataTable();
		
	} );
	
var myDel = function(auto_seq,site_db,pro_id){				

	Swal.fire({
	title: '您確定要刪除此筆資料嗎?',
	text: "",
	icon: "question",
	showCancelButton: true,
	confirmButtonColor: "#3085d6",
	cancelButtonColor: "#d33",
	cancelButtonText: "取消",
	confirmButtonText: "刪除"
	}).then((result) => {
		if (result.isConfirmed) {
			xajax_DeleteRow(auto_seq,site_db,pro_id);
		}
	});

};

var binding = function(){				

	Swal.fire({
	title: '您確定要進行員工綁定會員帳號及檢查嗎?',
	text: "",
	icon: "question",
	showCancelButton: true,
	confirmButtonColor: "#3085d6",
	cancelButtonColor: "#d33",
	cancelButtonText: "取消",
	confirmButtonText: "確定"
	}).then((result) => {
		if (result.isConfirmed) {
			xajax_binding();
		}
	});

};


var myDraw = function(){
	var oTable;
	oTable = $('#db_table').dataTable();
	oTable.fnDraw(false);
}


// 計算兩個日期之間的年月日差異 (endDate 預設為今天，可不輸入)
function getDifferenceInYMD(startDate, endDate = new Date()) {

    // 取得今天的日期並格式化
    const endYear = endDate.getFullYear();
    const endMonth = endDate.getMonth() + 1; // 月份從0開始，因此要+1
    const endDay = endDate.getDate();

    const end = new Date(endYear, endMonth - 1, endDay);
	
    let years = end.getFullYear() - startDate.getFullYear();
    let months = end.getMonth() - startDate.getMonth();
    let days = end.getDate() - startDate.getDate();

    // 調整月份和年份
    if (days < 0) {
        months--;
        // 獲得前一個月的天數，並調整天數
        const prevMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0);
        days += prevMonth.getDate();
    }

    if (months < 0) {
        years--;
        months += 12;
    }

    return { years, months, days };
}


</script>

EOT;



?>