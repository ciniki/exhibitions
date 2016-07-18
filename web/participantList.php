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
function ciniki_exhibitions_web_participantList($ciniki, $settings, $business_id, $exhibition_id, $type) {

    $strsql = "SELECT ciniki_exhibition_participants.id, "
        . "ciniki_exhibition_participants.category AS name, "
        . "IF(ciniki_exhibition_contacts.company='', CONCAT_WS(' ', ciniki_exhibition_contacts.first, ciniki_exhibition_contacts.last), ciniki_exhibition_contacts.company) AS title, "
        . "ciniki_exhibition_contacts.permalink, "
        . "ciniki_exhibition_contacts.short_description, "
        . "ciniki_exhibition_contacts.studio_name, "
        . "ciniki_exhibition_contacts.address1, "
        . "ciniki_exhibition_contacts.address2, "
        . "ciniki_exhibition_contacts.city, "
        . "ciniki_exhibition_contacts.province, "
        . "ciniki_exhibition_contacts.postal, "
        . "ciniki_exhibition_contacts.latitude, "
        . "ciniki_exhibition_contacts.longitude, "
        . "ciniki_exhibition_contacts.primary_image_id, "
        . "ciniki_exhibition_contacts.url, "
        . "'yes' AS is_details "
        . "FROM ciniki_exhibition_contacts "
        . "LEFT JOIN ciniki_exhibition_participants ON ("
            . "ciniki_exhibition_contacts.id = ciniki_exhibition_participants.contact_id "
            . "AND ciniki_exhibition_participants.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $exhibition_id) . "' "
            . ") "
        . "WHERE ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        // Check the participant is visible on the website
        . "AND (ciniki_exhibition_participants.webflags&0x01) = 0 ";
    if( $type == 'exhibitor' ) {
        $strsql .= "AND ((type&0x10) = 0x10 AND status = 10) ";
    } elseif( $type == 'sponsor' ) {
        $strsql .= "AND ((type&0x20) = 0x20) ";
    } elseif( $type == 'tourexhibitor' ) {
        $strsql .= "AND ((type&0x40) = 0x40 AND status = 10) ";
    } else {
        return array('stat'=>'ok', 'participants'=>array());
    }
    $strsql .= "ORDER BY category, title ";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.exhibitions', array(
        array('container'=>'categories', 'fname'=>'name',
            'fields'=>array('name')),
        array('container'=>'list', 'fname'=>'id',
            'fields'=>array('id', 'title', 'image_id'=>'primary_image_id', 'studio_name',
                'address1', 'address2', 'city', 'province', 'postal', 'latitude', 'longitude', 
                'permalink', 'description'=>'short_description', 'url', 'is_details')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['categories']) ) {
        return array('stat'=>'ok', 'categories'=>array());
    }
    return array('stat'=>'ok', 'categories'=>$rc['categories']);
}
?>
