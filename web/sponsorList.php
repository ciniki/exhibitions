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
function ciniki_exhibitions_web_sponsorList($ciniki, $settings, $business_id) {

    $exhibition_id = $settings['page-exhibitions-exhibition'];
    $type = 'sponsor';

    $strsql = "SELECT ciniki_exhibition_participants.id, "
        . "ciniki_exhibition_participants.category, "
        . "IF(ciniki_exhibition_contacts.company='', CONCAT_WS(' ', ciniki_exhibition_contacts.first, ciniki_exhibition_contacts.last), ciniki_exhibition_contacts.company) AS name, "
        . "ciniki_exhibition_contacts.permalink, "
        . "ciniki_exhibition_participants.level, "
        . "ciniki_exhibition_contacts.short_description, "
        . "ciniki_exhibition_contacts.primary_image_id, "
        . "ciniki_exhibition_contacts.url "
        . "FROM ciniki_exhibition_contacts "
        . "LEFT JOIN ciniki_exhibition_participants ON ("
            . "ciniki_exhibition_contacts.id = ciniki_exhibition_participants.contact_id "
            . "AND ciniki_exhibition_participants.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $exhibition_id) . "' "
            . ") "
        . "WHERE ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        // Check the participant is visible on the website
        . "AND (ciniki_exhibition_participants.webflags&0x01) = 0 "
        // Only get sponsors
        . "AND ((type&0x20) = 0x20) "
        . "ORDER BY ciniki_exhibition_participants.level DESC, ciniki_exhibition_participants.sequence DESC, category, name ";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
        array('container'=>'levels', 'fname'=>'level', 'name'=>'level',
            'fields'=>array('number'=>'level')),
        array('container'=>'categories', 'fname'=>'category', 'name'=>'category',
            'fields'=>array('name'=>'category')),
        array('container'=>'sponsors', 'fname'=>'id', 'name'=>'sponsor',
            'fields'=>array('id', 'name', 'image_id'=>'primary_image_id', 
                'permalink', 'description'=>'short_description', 'url')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['levels']) ) {
        return array('stat'=>'ok', 'levels'=>array());
    }
    $levels = $rc['levels'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQueryDash');
    $rc = ciniki_core_dbDetailsQueryDash($ciniki, 'ciniki_exhibition_details', 
        'exhibition_id', $exhibition_id, 'ciniki.exhibitions', 'details', 'sponsor');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['details']) ) {
        foreach($levels as $lnum => $level) {
            if( isset($rc['details']['sponsor-level-' . $level['level']['number'] . '-name']) ) {
                $levels[$lnum]['level']['name'] = $rc['details']['sponsor-level-' . $level['level']['number'] . '-name'];
            }
        }
    }

    return array('stat'=>'ok', 'levels'=>$levels);
}
?>
