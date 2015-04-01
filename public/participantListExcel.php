<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to get events for.
// type:			The type of participants to get.  Refer to participantAdd for 
//					more information on types.
//
// Returns
// -------
//
function ciniki_exhibitions_participantListExcel($ciniki) {
	//
	// Find all the required and optional arguments
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
	$rc = ciniki_core_prepareArgs($ciniki, 'no', array(
		'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'exhibition_id'=>array('required'=>'yes', 'blankk'=>'yes', 'name'=>'Exhibition'),
		'type'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Participant Type'),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$args = $rc['args'];
	
    //  
    // Check access to business_id as owner, or sys admin. 
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $ac = ciniki_exhibitions_checkAccess($ciniki, $args['business_id'], 'ciniki.exhibitions.participantList');
    if( $ac['stat'] != 'ok' ) { 
        return $ac;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
	$date_format = ciniki_users_dateFormat($ciniki);
	
	//
	// Load the list of participants for an exhibition
	//
	$strsql = "SELECT ciniki_exhibition_participants.category, "
		. "ciniki_exhibition_participants.id, "
		. "ciniki_exhibition_participants.type, "
		. "ciniki_exhibition_participants.title, "
		. "ciniki_exhibition_participants.status, "
		. "ciniki_exhibition_participants.status AS status_text, "
		. "ciniki_exhibition_participants.location, "
		. "IF((ciniki_exhibition_participants.webflags&0x01)=1,'Hidden','Visible') as webvisible, "
		. "ciniki_exhibition_contacts.first, "
		. "ciniki_exhibition_contacts.last, "
		. "ciniki_exhibition_contacts.company, "
		. "ciniki_exhibition_contacts.email, "
		. "ciniki_exhibition_contacts.phone_home, "
		. "ciniki_exhibition_contacts.phone_work, "
		. "ciniki_exhibition_contacts.phone_cell, "
		. "ciniki_exhibition_contacts.phone_fax, "
		. "ciniki_exhibition_contacts.url, "
		. "ciniki_exhibition_contacts.studio_name, "
		. "ciniki_exhibition_contacts.address1, "
		. "ciniki_exhibition_contacts.address2, "
		. "ciniki_exhibition_contacts.city, "
		. "ciniki_exhibition_contacts.province, "
		. "ciniki_exhibition_contacts.postal, "
		. "ciniki_exhibition_contacts.latitude, "
		. "ciniki_exhibition_contacts.longitude, "
		. "ciniki_exhibition_contacts.mailing_address1, "
		. "ciniki_exhibition_contacts.mailing_address2, "
		. "ciniki_exhibition_contacts.mailing_city, "
		. "ciniki_exhibition_contacts.mailing_province, "
		. "ciniki_exhibition_contacts.mailing_postal, "
		. "ciniki_exhibition_contacts.short_description, "
		. "ciniki_exhibition_contacts.description, "
		. "ciniki_exhibition_contacts.notes "
		. "FROM ciniki_exhibition_participants "
		. "LEFT JOIN ciniki_exhibition_contacts ON ("
			. "ciniki_exhibition_participants.contact_id = ciniki_exhibition_contacts.id "
			. "AND ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. ") "
		. "WHERE ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
		. "AND ciniki_exhibition_participants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ciniki_exhibition_participants.status = 10 "
		. "";
	if( isset($args['type']) && $args['type'] != '' ) {
		$strsql .= "AND (ciniki_exhibition_participants.type&'" . ciniki_core_dbQuote($ciniki, $args['type']) . "') > 0 ";
	}
	if( isset($args['categorized']) && $args['categorized'] == 'yes' ) {
		$strsql .= "ORDER BY ciniki_exhibition_participants.category, ";
	} else {
		$strsql .= "ORDER BY ";
	}
	$strsql .= "ciniki_exhibition_contacts.first, ciniki_exhibition_contacts.last ";

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$statuses = array('0'=>'Unknown', '1'=>'Applied', '10'=>'Accepted', '60'=>'Rejected');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
		array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
			'fields'=>array('id', 'type', 'first', 'last', 'company', 'title', 'status', 'status_text',
				'category', 'location', 
				'address1', 'address2', 'city', 'province', 'postal',
				'phone_home', 'phone_work', 'phone_cell', 'phone_fax', 'email', 'url',
				'webvisible', 'latitude', 'longitude',
				'studio_name', 'address1', 'address2', 'city', 'province', 'postal',
				'mailing_address1', 'mailing_address2', 'mailing_city',
				'mailing_province', 'mailing_postal',
				'short_description', 'description', 'notes'),
			'maps'=>array('status_text'=>$statuses),
			),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( isset($rc['participants']) ) {
		$participants = $rc['participants'];
	} else {
		$participants = array();
	}

	//
	// Export to excel
	//
	ini_set('memory_limit', '4192M');
	require($ciniki['config']['core']['lib_dir'] . '/PHPExcel/PHPExcel.php');
	$objPHPExcel = new PHPExcel();
	$title = "Participants";
	$sheet_title = "Participants"; 	// Will be overwritten, which is fine
	$sheet = $objPHPExcel->setActiveSheetIndex(0);
	$sheet->setTitle($sheet_title);

	//
	// Headers
	//
	$i = 0;
	$sheet->setCellValueByColumnAndRow($i++, 1, 'First', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Last', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Company', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Home', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Work', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Cell', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Fax', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Email', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'URL', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Studio Name', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Show Address', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Mailing', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Synopsis', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Description', false);
	$sheet->setCellValueByColumnAndRow($i++, 1, 'Notes', false);
	$sheet->getStyle('A1:O1')->getFont()->setBold(true);

	//
	// Output the invoice list
	//
	$row = 2;
	foreach($participants as $pid => $participant) {
		$participant = $participant['participant'];
		$i = 0;
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['first'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['last'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['company'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['phone_home'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['phone_work'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['phone_cell'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['phone_fax'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['email'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['url'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['studio_name'], false);
		$addr = '';
		if( $participant['address1'] != '' ) { $addr .= $participant['address1']; }
		if( $participant['address2'] != '' ) { $addr .= ($addr!=''?', ':'') . $participant['address2']; }
		if( $participant['city'] != '' ) { $addr .= ($addr!=''?', ':'') . $participant['city']; }
		if( $participant['province'] != '' ) { $addr .= ($addr!=''?', ':'') . $participant['province']; }
		if( $participant['postal'] != '' ) { $addr .= ($addr!=''?'  ':'') . $participant['postal']; }
		$sheet->setCellValueByColumnAndRow($i++, $row, $addr, false);
		$addr = '';
		if( $participant['mailing_address1'] != '' ) { $addr .= $participant['mailing_address1']; }
		if( $participant['mailing_address2'] != '' ) { $addr .= ($addr!=''?', ':'') . $participant['mailing_address2']; }
		if( $participant['mailing_city'] != '' ) { $addr .= ($addr!=''?', ':'') . $participant['mailing_city']; }
		if( $participant['mailing_province'] != '' ) { $addr .= ($addr!=''?', ':'') . $participant['mailing_province']; }
		if( $participant['mailing_postal'] != '' ) { $addr .= ($addr!=''?'  ':'') . $participant['mailing_postal']; }
		$sheet->setCellValueByColumnAndRow($i++, $row, $addr, false);

		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['short_description'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['description'], false);
		$sheet->setCellValueByColumnAndRow($i++, $row, $participant['notes'], false);
		$row++;
	}
	$sheet->getColumnDimension('A')->setAutoSize(true);
	$sheet->getColumnDimension('B')->setAutoSize(true);
	$sheet->getColumnDimension('C')->setAutoSize(true);
	$sheet->getColumnDimension('D')->setAutoSize(true);
	$sheet->getColumnDimension('E')->setAutoSize(true);
	$sheet->getColumnDimension('F')->setAutoSize(true);
	$sheet->getColumnDimension('G')->setAutoSize(true);
	$sheet->getColumnDimension('H')->setAutoSize(true);
	$sheet->getColumnDimension('I')->setAutoSize(true);
	$sheet->getColumnDimension('J')->setAutoSize(true);
	$sheet->getColumnDimension('K')->setAutoSize(true);
	$sheet->getColumnDimension('L')->setAutoSize(true);
	$sheet->getColumnDimension('M')->setAutoSize(true);
	$sheet->getColumnDimension('N')->setAutoSize(true);
	$sheet->getColumnDimension('O')->setAutoSize(true);

	//
	// Output the excel
	//
	header('Content-Type: application/vnd.ms-excel');
	$filename = preg_replace('/[^a-zA-Z0-9\-]/', '', $title);
	header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	return array('stat'=>'exit');
}
?>
