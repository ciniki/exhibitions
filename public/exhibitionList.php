<?php
//
// Description
// -----------
// This method will return the list of exhibitions for a tenant.  The latest ones
// will be at the top of the list.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:     The ID of the tenant to get events for.
//
// Returns
// -------
//
function ciniki_exhibitions_exhibitionList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    //  
    // Check access to tnid as owner, or sys admin. 
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $ac = ciniki_exhibitions_checkAccess($ciniki, $args['tnid'], 'ciniki.exhibitions.exhibitionList');
    if( $ac['stat'] != 'ok' ) { 
        return $ac;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);
    
    //
    // Load the exhibitions
    //
    $strsql = "SELECT ciniki_exhibitions.id, ciniki_exhibitions.name, "
        . "IFNULL(DATE_FORMAT(ciniki_exhibitions.start_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "'), '') AS start_date, "
        . "IFNULL(DATE_FORMAT(ciniki_exhibitions.end_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "'), '') AS end_date "
        . "FROM ciniki_exhibitions "
        . "WHERE ciniki_exhibitions.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "ORDER BY ciniki_exhibitions.start_date DESC "
        . "";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbRspQuery');
    $rc = ciniki_core_dbRspQuery($ciniki, $strsql, 'ciniki.exhibitions', 'exhibitions', 'exhibition', array('stat'=>'ok', 'exhibitions'=>array()));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    return $rc;
}
?>
