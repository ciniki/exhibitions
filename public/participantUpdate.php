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
		'level'=>array('required'=>'no', 'blank'=>'yes', 'validlist'=>array('10','20','30','40','50'), 'name'=>'Level'),
		'sequence'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sequence'),
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
	// Update the participant
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
	return ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.exhibitions.participant', $args['participant_id'], $args, 0x07);
}
?>
