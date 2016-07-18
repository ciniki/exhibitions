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
function ciniki_exhibitions_web_participantDetails($ciniki, $settings, $business_id, $exhibition_id, $permalink) {

    $strsql = "SELECT ciniki_exhibition_participants.id, "
        . "CONCAT_WS(' ', ciniki_exhibition_contacts.first, ciniki_exhibition_contacts.last) AS contact, "
        . "ciniki_exhibition_contacts.company, "
        . "ciniki_exhibition_contacts.permalink, "
        . "ciniki_exhibition_contacts.url, "
        . "ciniki_exhibition_contacts.studio_name, "
        . "ciniki_exhibition_contacts.address1, "
        . "ciniki_exhibition_contacts.address2, "
        . "ciniki_exhibition_contacts.city, "
        . "ciniki_exhibition_contacts.province, "
        . "ciniki_exhibition_contacts.postal, "
        . "ciniki_exhibition_contacts.latitude, "
        . "ciniki_exhibition_contacts.longitude, "
        . "ciniki_exhibition_contacts.description, "
        . "ciniki_exhibition_contacts.primary_image_id, "
        . "ciniki_exhibition_contact_images.image_id, "
        . "ciniki_exhibition_contact_images.name AS image_name, "
        . "ciniki_exhibition_contact_images.permalink AS image_permalink, "
        . "ciniki_exhibition_contact_images.description AS image_description, "
        . "ciniki_exhibition_contact_images.url AS image_url, "
        . "UNIX_TIMESTAMP(ciniki_exhibition_contact_images.last_updated) AS image_last_updated "
        . "FROM ciniki_exhibition_contacts "
        . "LEFT JOIN ciniki_exhibition_participants ON ("
            . "ciniki_exhibition_contacts.id = ciniki_exhibition_participants.contact_id "
            . "AND ciniki_exhibition_participants.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $exhibition_id) . "' "
            . ") "
        . "LEFT JOIN ciniki_exhibition_contact_images ON ("
            . "ciniki_exhibition_contacts.id = ciniki_exhibition_contact_images.contact_id "
            . "AND ciniki_exhibition_contact_images.image_id > 0 "
            . "AND (ciniki_exhibition_contact_images.webflags&0x01) = 0 "
            . ") "
        . "WHERE ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND ciniki_exhibition_contacts.permalink = '" . ciniki_core_dbQuote($ciniki, $permalink) . "' "
        // Check the participant is visible on the website
        . "AND (ciniki_exhibition_participants.webflags&0x01) = 0 "
        // Check the participant is an exhibitor and accepted, or a sponsor
        . "AND ("
            . "((type&0x10) = 0x10 AND status = 10) "
            . "OR ((type&0x20) = 0x20) "
            . "OR ((type&0x40) = 0x40 AND status = 10) "
            . ") "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.exhibitions', array(
        array('container'=>'participants', 'fname'=>'id', 
            'fields'=>array('id', 'permalink', 'contact', 'company', 'image_id'=>'primary_image_id', 
                'studio_name', 'address1', 'address2', 'city', 'province', 'postal', 'latitude', 'longitude', 
                'url', 'description')),
        array('container'=>'images', 'fname'=>'image_id', 
            'fields'=>array('image_id', 'title'=>'image_name', 'permalink'=>'image_permalink',
                'description'=>'image_description', 'url'=>'image_url',
                'last_updated'=>'image_last_updated')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['participants']) || count($rc['participants']) < 1 ) {
        return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'278', 'msg'=>'Unable to find participant'));
    }
    $participant = array_pop($rc['participants']);

    if( isset($participant['company']) && $participant['company'] != '' ) {
        $participant['name'] = $participant['company'];
    } else {
        $participant['name'] = $participant['contact'];
    }

    return array('stat'=>'ok', 'participant'=>$participant);
}
?>
