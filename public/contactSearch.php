<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:			The ID of the business to search for the exhibition contacts.
// start_needle:		The search string to use.
// limit:				(optional) The maximum number of results to return.  If not
//						specified, the maximum results will be 25.
// 
// Returns
// -------
//
function ciniki_exhibitions_contactSearch($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'exhibition_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Exhibition'), 
        'start_needle'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Search String'), 
        'limit'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Limit'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['business_id'], 'ciniki.exhibitions.contactSearch', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Get the number of customers in each status for the business, 
	// if no rows found, then return empty array
	//
	$strsql = "SELECT ciniki_exhibition_contacts.id, "
		. "ciniki_exhibition_contacts.first, "
		. "ciniki_exhibition_contacts.last, "
		. "ciniki_exhibition_contacts.company "
		. "FROM ciniki_exhibition_contacts ";
	if( isset($args['exhibition_id']) && $args['exhibition_id'] > 0 ) {
		$strsql .= "LEFT JOIN ciniki_exhibition_participants ON ("
			. "ciniki_exhibition_contacts.id = ciniki_exhibition_participants.contact_id "
			. "AND ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
			. ") ";
	}
	$strsql .= "WHERE ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND (ciniki_exhibition_contacts.first LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. "OR ciniki_exhibition_contacts.last LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. "OR ciniki_exhibition_contacts.company LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. ") "
		. "";
	if( isset($args['limit']) && is_numeric($args['limit']) && $args['limit'] > 0 ) {
		$strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";	// is_numeric verified
	} else {
		$strsql .= "LIMIT 25 ";
	}

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbRspQuery');
	return ciniki_core_dbRspQuery($ciniki, $strsql, 'ciniki.exhibitions', 'contacts', 'contact', array('stat'=>'ok', 'contacts'=>array()));
}
?>
