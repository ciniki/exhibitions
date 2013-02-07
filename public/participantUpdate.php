<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:			The ID of the business to add the exhibition to.
// name:				The name of the exhibition.  
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_exhibitions_participantUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'exhibition_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Exhibition'), 
		'participant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Participant'),
        'contact_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Contact'), 
		'category'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'yes', 'name'=>'Category'),
		'type'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'no', 'name'=>'Type'),
		'status'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'yes', 'name'=>'status'),
		'webflags'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'yes', 'name'=>'Web Flags'),
		'title'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'yes', 'name'=>'Title'),
		'location'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'yes', 'name'=>'Location'),
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
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['business_id'], 'ciniki.exhibitions.participantUpdate', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

	//  
	// Turn off autocommit
	//  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbInsert');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbUpdate');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
	$rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.exhibitions');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}   

	//
	// Add all the fields to the change log
	//
	$strsql = "UPDATE ciniki_exhibition_participants SET last_updated = UTC_TIMESTAMP()";

	$changelog_fields = array(
		'contact_id',
		'category',
		'type',
		'status',
		'webflags',
		'title',
		'location',
		);
	foreach($changelog_fields as $field) {
		if( isset($args[$field]) && $args[$field] != '' ) {
			$strsql .= ", $field = '" . ciniki_core_dbQuote($ciniki, $args[$field]) . "' ";
			$rc = ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.exhibitions', 
				'ciniki_exhibition_history', $args['business_id'], 
				2, 'ciniki_exhibition_participants', $args['participant_id'], $field, $args[$field]);
		}
	}
	$strsql .= "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND id = '" . ciniki_core_dbQuote($ciniki, $args['participant_id']) . "' "
		. "AND exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
		. "";
	$rc = ciniki_core_dbUpdate($ciniki, $strsql, 'ciniki.exhibitions');
	if( $rc['stat'] != 'ok' ) {
		ciniki_core_dbTransactionRollback($ciniki, 'ciniki.exhibitions');
		return $rc;
	}
	if( !isset($rc['num_affected_rows']) || $rc['num_affected_rows'] != 1 ) {
		ciniki_core_dbTransactionRollback($ciniki, 'ciniki.exhibitions');
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'176', 'msg'=>'Unable to update participant'));	
	}

	//
	// Commit the database changes
	//
    $rc = ciniki_core_dbTransactionCommit($ciniki, 'ciniki.exhibitions');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	//
	// Update the last_change date in the business modules
	// Ignore the result, as we don't want to stop user updates if this fails.
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'updateModuleChangeDate');
	ciniki_businesses_updateModuleChangeDate($ciniki, $args['business_id'], 'ciniki', 'exhibitions');

	$ciniki['syncqueue'][] = array('push'=>'ciniki.exhibitions.participant', 
		'args'=>array('id'=>$args['participant_id']));

	return array('stat'=>'ok');
}
?>
