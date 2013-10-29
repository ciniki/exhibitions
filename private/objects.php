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
function ciniki_exhibitions_objects(&$ciniki) {
	$objects = array();
	$objects['exhibition'] = array(
		'name'=>'Exhibition',
		'table'=>'ciniki_exhibitions',
		'fields'=>array(
			'name'=>array(),
			'permalink'=>array(),
			'type'=>array(),
			'description'=>array(),
			'tagline'=>array(),
			'start_date'=>array(),
			'end_date'=>array(),
			),
		'details'=>array('key'=>'exhibition_id', 'table'=>'ciniki_exhibition_details'),
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
			'permalink'=>array(),
			'email'=>array(),
			'passcode'=>array(),
			'phone_home'=>array(),
			'phone_work'=>array(),
			'phone_cell'=>array(),
			'phone_fax'=>array(),
			'url'=>array(),
			'address1'=>array(),
			'address2'=>array(),
			'city'=>array(),
			'province'=>array(),
			'postal'=>array(),
			'latitude'=>array(),
			'longitude'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'short_description'=>array(),
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
	$objects['participant'] = array(
		'name'=>'Participant',
		'table'=>'ciniki_exhibition_participants',
		'fields'=>array(
			'exhibition_id'=>array('ref'=>'ciniki.exhibitions.exhibition'),
			'contact_id'=>array('ref'=>'ciniki.exhibitions.contact'),
			'category'=>array(),
			'type'=>array(),
			'status'=>array(),
			'webflags'=>array(),
			'level'=>array(),
			'title'=>array(),
			'location'=>array(),
			),
		'history_table'=>'ciniki_exhibition_history',
		);

	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
