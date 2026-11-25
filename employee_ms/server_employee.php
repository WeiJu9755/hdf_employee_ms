<?php

	session_start();

	$memberID = $_SESSION['memberID'];
	$powerkey = $_SESSION['powerkey'];

	//載入公用函數
	@include_once '/website/include/pub_function.php';

	//檢查是否為管理員及進階會員
	$super_admin = "N";
	$super_advanced = "N";
	$mem_row = getkeyvalue2('memberinfo','member',"member_no = '$memberID'",'admin,advanced,checked,luck,admin_readonly,advanced_readonly');
	$super_admin = $mem_row['admin'];
	$super_advanced = $mem_row['advanced'];


	$site_db = $_GET['site_db'];
	$web_id = $_GET['web_id'];
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables 詮能
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	
//	$aColumns = array( 'auto_seq','web_id','employee_id','employee_name','contact','gender','title','county','town','zipcode','address','tel','fax','email','mobile_no','owner','owner_gender','owner_title','short_name');
	
	$aColumns = array( 'a.employee_id','a.employee_name','a.id_number','a.gender','a.birthday','a.blood_type','a.mobile_no','a.emergency_contact','a.emergency_mobile_no','a.start_date','a.seniority'
			,'a.zipcode','a.county','a.town','a.address','a.auto_seq','a.member_no','a.employee_type','a.company_id','b.company_name','a.team_id'
			,'c.team_name','a.construction_id','d.construction_site','a.old_employee_id','a.department','a.resignation_date');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "auto_seq";
	
	/* DB table to use */
	$sTable = "employee";
	
//	include( $_SERVER['DOCUMENT_ROOT']."/class/products_db.php" );
	include( "/website/class/".$site_db."_db.php" );
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysqli_connect( $gaSql['server'], $gaSql['user'], $gaSql['password'], $gaSql['db'] ) or
		die( 'Could not open connection to server' );
	
	// 既有資料以 latin1 連線讀取時回傳 UTF-8 位元組；維持原清單的編碼行為。
	mysqli_set_charset( $gaSql['link'], 'latin1' );
	$memberID_sql = mysqli_real_escape_string( $gaSql['link'], $memberID );
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".max(0, intval($_GET['iDisplayStart'])).", ".
			max(1, intval($_GET['iDisplayLength']));
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "ORDER BY a.employee_id ";
	/*
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				".mysqli_real_escape_string( $gaSql['link'], $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	*/
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string( $gaSql['link'], $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i], $_GET['sSearch_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch_'.$i])."%' ";
		}
	}
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	 
	/* 
	$mc = $sc = "";

	if (isset($_GET['mc']))
		$mc = $_GET['mc'];
	if (isset($_GET['sc']))
		$sc = $_GET['sc'];	 
	 
	$filter_str = "";

	if (($mc<>"") && ($sc<>"")) {
		$filter_str = " and a.main_class = '$mc' and a.small_class = '$sc' ";
	} else if (($mc<>"") && ($sc=="")) {
		if ($mc=="其他未分類") {
			$filter_str = " and (a.main_class = '' or isnull(a.main_class)) and (a.small_class = '' or isnull(a.small_class))";
		} else {
			$filter_str = " and a.main_class = '$mc' ";
		}
	}
	 
	 
	if ($sWhere=="")
		$sWhere = "WHERE (a.web_id = '$web_id' ".$filter_str.") ";
	else
		$sWhere .= " and (a.web_id = '$web_id' ".$filter_str.") ";
	*/
	
	/*
	if ($sWhere=="")
		$sWhere = "WHERE (web_id = '$web_id') ";
	else
		$sWhere .= " and (web_id = '$web_id') ";
	*/
	
	if (($powerkey=="A") || ($super_admin=="Y")) {

		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable a
			LEFT JOIN company b ON b.company_id = a.company_id
			LEFT JOIN team c ON c.team_id = a.team_id
			LEFT JOIN construction d ON d.construction_id = a.construction_id
			$sWhere
			$sOrder
			$sLimit
		";

	} else {

		if ($sWhere=="")
			$sWhere = "WHERE (e.member_no = '$memberID_sql') ";
		else
			$sWhere .= " and (e.member_no = '$memberID_sql') ";


		$sQuery = "
			SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
			FROM   $sTable a
			LEFT JOIN company b ON b.company_id = a.company_id
			LEFT JOIN team c ON c.team_id = a.team_id
			LEFT JOIN construction d ON d.construction_id = a.construction_id
			RIGHT JOIN group_company e ON e.company_id = a.company_id and e.member_no = '$memberID_sql'
			$sWhere
			$sOrder
			$sLimit
		";

	}
	$rResult = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($gaSql['link']));
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($gaSql['link']));
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or die(mysqli_error($gaSql['link']));
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				//$row[] = $aRow[ $aColumns[$i] ];

				$field = $aColumns[$i];
				$field = str_replace("a.","",$field);
				$field = str_replace("b.","",$field);
				$field = str_replace("c.","",$field);
				$field = str_replace("d.","",$field);
				
				$row[] = $aRow[ $field ];

				
			}
		}
		foreach ($row as $i => $value) {
			if (is_string($value) && preg_match('//u', $value) !== 1) {
				error_log('server_employee.php: invalid UTF-8, employee auto_seq=' . $aRow['auto_seq'] . ', column=' . $aColumns[$i]);
			}
		}
		$output['aaData'][] = $row;
	}
	
	header('Content-Type: application/json; charset=utf-8');
	$json = json_encode( $output, JSON_INVALID_UTF8_SUBSTITUTE );
	if ($json === false) {
		error_log('server_employee.php: JSON encoding failed: ' . json_last_error_msg());
		http_response_code(500);
		exit;
	}
	echo $json;
?>
