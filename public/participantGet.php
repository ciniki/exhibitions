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
// exhibition_id:		The ID of the exhibition to get the participant from.
// participant_id:		The ID of the participant to get.
// images:				Specify if the method should return the image thumbnails.
//
// Returns
// -------
//
function ciniki_exhibitions_participantGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'exhibition_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Exhibition'),
		'participant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Participant'),
		'images'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Images'),
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
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['business_id'], 'ciniki.exhibitions.participantGet', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
	$date_format = ciniki_users_dateFormat($ciniki);

	//
	// Get the main information
	//
	$strsql = "SELECT ciniki_exhibition_participants.id, "
		. "ciniki_exhibition_participants.exhibition_id, "
		. "ciniki_exhibition_participants.category, "
		. "ciniki_exhibition_participants.type, "
		. "ciniki_exhibition_participants.status, "
		. "ciniki_exhibition_participants.status AS status_text, "
		. "ciniki_exhibition_participants.webflags, "
		. "if((ciniki_exhibition_participants.webflags&0x01)=1,'Hidden','Visible') AS webvisible, "
		. "ciniki_exhibition_participants.title, "
		. "ciniki_exhibition_participants.location, "
		. "ciniki_exhibition_contacts.id AS contact_id, "
		. "ciniki_exhibition_contacts.first, "
		. "ciniki_exhibition_contacts.last, "
		. "ciniki_exhibition_contacts.company, "
		. "ciniki_exhibition_contacts.email, "
		. "ciniki_exhibition_contacts.phone_home, "
		. "ciniki_exhibition_contacts.phone_work, "
		. "ciniki_exhibition_contacts.phone_cell, "
		. "ciniki_exhibition_contacts.phone_fax, "
		. "ciniki_exhibition_contacts.url, "
		. "ciniki_exhibition_contacts.primary_image_id, "
		. "ciniki_exhibition_contacts.description, "
		. "ciniki_exhibition_contacts.notes ";
	if( isset($args['images']) && $args['images'] == 'yes' ) {
		$strsql .= ", "
			. "ciniki_exhibition_contact_images.id AS img_id, "
			. "ciniki_exhibition_contact_images.name AS image_name, "
			. "ciniki_exhibition_contact_images.webflags AS image_webflags, "
			. "ciniki_exhibition_contact_images.image_id, "
			. "ciniki_exhibition_contact_images.description AS image_description, "
			. "ciniki_exhibition_contact_images.url AS image_url "
			. "";
	}
	$strsql .= "FROM ciniki_exhibition_participants "
		. "LEFT JOIN ciniki_exhibition_contacts ON (ciniki_exhibition_participants.contact_id = ciniki_exhibition_contacts.id "
			. "AND ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. ") ";
	if( isset($args['images']) && $args['images'] == 'yes' ) {
		$strsql .= "LEFT JOIN ciniki_exhibition_contact_images ON (ciniki_exhibition_contacts.id = ciniki_exhibition_contact_images.contact_id "
			. "AND ciniki_exhibition_contact_images.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. ") ";
	}
	$strsql .= "WHERE ciniki_exhibition_participants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
		. "AND ciniki_exhibition_participants.id = '" . ciniki_core_dbQuote($ciniki, $args['participant_id']) . "' "
		. "";

	//
	// Check if we need to include thumbnail images
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$statuses = array('0'=>'Unknown', '1'=>'Applied', '10'=>'Accepted', '60'=>'Rejected');
	if( isset($args['images']) && $args['images'] == 'yes' ) {
		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
			array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
				'fields'=>array('id', 'exhibition_id', 'category', 'type', 'status',
					'status_text', 'webflags', 'webvisible', 'title', 'location', 
					'contact_id', 'first', 'last', 'company', 'email', 'phone_home',
					'phone_work', 'phone_cell', 'phone_fax', 'url', 'primary_image_id',
					'description', 'notes'),
				'maps'=>array('status_text'=>$statuses)),
			array('container'=>'images', 'fname'=>'img_id', 'name'=>'image',
				'fields'=>array('id'=>'img_id', 'name'=>'image_name', 'webflags'=>'image_webflags',
					'image_id', 'description'=>'image_description', 'url'=>'image_url')),
		));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( !isset($rc['participants']) || !isset($rc['participants'][0]) ) {
			return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'197', 'msg'=>'Unable to find participant'));
		}
		$participant = $rc['participants'][0]['participant'];
		if( !isset($participant['images']) ) {
			$participant['images'] = array();
		}
		ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheThumbnail');
		foreach($participant['images'] as $img_id => $img) {
			if( isset($img['image']['image_id']) && $img['image']['image_id'] > 0 ) {
				$rc = ciniki_images_loadCacheThumbnail($ciniki, $img['image']['image_id'], 75);
				if( $rc['stat'] != 'ok' ) {
					return $rc;
				}
				$participant['images'][$img_id]['image']['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
			}
		}
	} else {
		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
			array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
				'fields'=>array('id', 'exhibition_id', 'category', 'type', 'status',
					'status_text', 'webflags', 'webvisible', 'title', 'location', 
					'contact_id', 'first', 'last', 'company', 'email', 'phone_home',
					'phone_work', 'phone_cell', 'phone_fax', 'url', 'primary_image_id',
					'description', 'notes'),
				'maps'=>array('status_text'=>$statuses)),
		));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( !isset($rc['participants']) || !isset($rc['participants'][0]) ) {
			return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'165', 'msg'=>'Unable to find participant'));
		}
		$participant = $rc['participants'][0]['participant'];
	}
	
	return array('stat'=>'ok', 'participant'=>$participant);
}
?>
