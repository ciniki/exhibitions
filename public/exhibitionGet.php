<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the exhibition to.
// exhibition_id:       The ID of the exhibition to get the information about.
//
// Returns
// -------
//
function ciniki_exhibitions_exhibitionGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'exhibition_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Exhibition'),
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['tnid'], 'ciniki.exhibitions.exhibitionGet', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);

    //
    // Get the main information
    //
    $strsql = "SELECT ciniki_exhibitions.id, "
        . "ciniki_exhibitions.name, "
        . "ciniki_exhibitions.tagline, "
        . "ciniki_exhibitions.description, "
        . "ciniki_exhibitions.name, "
        . "DATE_FORMAT(ciniki_exhibitions.start_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS start_date, "
        . "DATE_FORMAT(ciniki_exhibitions.end_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS end_date "
        . "FROM ciniki_exhibitions "
        . "WHERE ciniki_exhibitions.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND ciniki_exhibitions.id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.exhibitions', 'exhibition');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['exhibition']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.exhibitions.16', 'msg'=>'Unable to find exhibition'));
    }
    $exhibition = $rc['exhibition'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQuery');
    $rc = ciniki_core_dbDetailsQuery($ciniki, 'ciniki_exhibition_details', 
        'exhibition_id', $args['exhibition_id'], 'ciniki.exhibitions', 'details', '');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['details']) ) {
        $exhibition = array_merge($exhibition, $rc['details']);
    }
    
    return array('stat'=>'ok', 'exhibition'=>$exhibition);
}
?>
