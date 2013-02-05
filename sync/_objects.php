<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_exhibitions_sync_objects(&$ciniki, &$sync, $business_id, $args) {

	$objects = array();
	$objects['exhibition'] = array(
		'name'=>'Exhibition',
		'table'=>'ciniki_exhibitions',
		'fields'=>array(
			'name'=>array(),
			'type'=>array(),
			'description'=>array(),
			'tagline'=>array(),
			'start_date'=>array(),
			'end_date'=>array(),
			),
		'details'=>array('key'=>'exhibition_id'),
		'history_table'=>'ciniki_exhibition_history',
		);
	$objects['image'] = array(
		'name'=>'Image',
		'table'=>'ciniki_exhibition_images',
		'fields'=>array(
			'exhibition_id'=>array('ref'=>'ciniki.exhibitions.exhibition'),
			'name'=>array(),
			'permalink'=>array(),
			'category'=>array(),
			'webflags'=>array(),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			),
		'history_table'=>'ciniki_exhibition_history',
		);
	$objects['contact'] = array(
		'name'=>'Contact',
		'table'=>'ciniki_exhibition_contacts',
		'fields'=>array(
			'first'=>array(),
			'last'=>array(),
			'company'=>array(),
			'email'=>array(),
			'passcode'=>array(),
			'phone_home'=>array(),
			'phone_cell'=>array(),
			'phone_fax'=>array(),
			'url'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			'notes'=>array(),
			),
		'history_table'=>'ciniki_exhibition_history',
		);
	$objects['contact_image'] = array(
		'name'=>'Contact Image',
		'table'=>'ciniki_exhibition_contact_images',
		'fields'=>array(
			'contact_id'=>array('ref'=>'ciniki.exhibitions.contact'),
			'name'=>array(),
			'permalink'=>array(),
			'webflags'=>array(),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			'url'=>array(),
			),
		'history_table'=>'ciniki_exhibition_history',
		);
	$objects['participants'] = array(
		'name'=>'Participant',
		'table'=>'ciniki_exhibition_participants',
		'fields'=>array(
			'exhibition_id'=>array('ref'=>'ciniki.exhibitions.exhibition'),
			'contact_id'=>array('ref'=>'ciniki.exhibitions.contact'),
			'category'=>array(),
			'type'=>array(),
			'status'=>array(),
			'webflags'=>array(),
			'title'=>array(),
			'location'=>array(),
			),
		'history_table'=>'ciniki_exhibition_history',
		);

	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
