<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business to add the exhibition to.
// name:                The name of the exhibition.  
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_exhibitions_exhibitionUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'exhibition_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Exhibition'), 
        'name'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'no', 'name'=>'Name'),
        'type'=>array('required'=>'no', 'blank'=>'no', 'validlist'=>array('1'), 'name'=>'Exhibition Type'),
        'tagline'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'yes', 'name'=>'Tagline'),
        'permalink'=>array('required'=>'no', 'trimblanks'=>'yes', 'blank'=>'no', 'name'=>'Permalink'),
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'), 
        'start_date'=>array('required'=>'no', 'blank'=>'no', 'type'=>'date', 'name'=>'Start Date'), 
        'end_date'=>array('required'=>'no', 'blank'=>'no', 'type'=>'date', 'name'=>'End Date'), 
        // Details
        'use-exhibitors'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Exhibitors'),
        'use-sponsors'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Sponsors'),
        'use-tour'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Tour'),
        'exhibitor-label-singular'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Exhibitor Label'),
        'exhibitor-label-plural'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Exhibitor Label Plural'),
        // Sponsor levels
        'sponsor-level-10-name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sponsor Level 10'),
        'sponsor-level-20-name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sponsor Level 20'),
        'sponsor-level-30-name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sponsor Level 30'),
        'sponsor-level-40-name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sponsor Level 40'),
        'sponsor-level-50-name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sponsor Level 50'),
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    if( isset($args['name']) && (!isset($args['permalink']) || $args['permalink'] == '') ) {
        $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args[    'name'])));
    }

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['business_id'], 'ciniki.exhibitions.exhibitionUpdate', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Check the permalink doesn't already exist
    //
    if( isset($args['permalink']) ) {
        $strsql = "SELECT id, name, permalink FROM ciniki_exhibitions "
            . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.exhibitions', 'exhibition');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'128', 'msg'=>'You already have an exhibition with this name, please choose another name.'));
        }
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
    // NOTE: This does NOT use the generic objectUpdate function, it's more complicated with the details table
    //


    //
    // Add all the fields to the change log
    //
    $strsql = "UPDATE ciniki_exhibitions SET last_updated = UTC_TIMESTAMP()";

    $changelog_fields = array(
        'name',
        'permalink',
        'type',
        'description',
        'tagline',
        'start_date',
        'end_date',
        );
    foreach($changelog_fields as $field) {
        if( isset($args[$field]) && $args[$field] != '' ) {
            $strsql .= ", $field = '" . ciniki_core_dbQuote($ciniki, $args[$field]) . "' ";
            $rc = ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.exhibitions', 
                'ciniki_exhibition_history', $args['business_id'], 
                2, 'ciniki_exhibitions', $args['exhibition_id'], $field, $args[$field]);
        }
    }
    $strsql .= "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
        . "";
    $rc = ciniki_core_dbUpdate($ciniki, $strsql, 'ciniki.exhibitions');
    if( $rc['stat'] != 'ok' ) {
        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.exhibitions');
        return $rc;
    }
    if( !isset($rc['num_affected_rows']) || $rc['num_affected_rows'] != 1 ) {
        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.exhibitions');
        return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'129', 'msg'=>'Unable to update exhibition'));   
    }

    //
    // Check for any details
    //
    $detail_keys = array(
        'use-exhibitors',
        'use-sponsors',
        'use-tour',
        'exhibitor-label-singular',
        'exhibitor-label-plural',
        'sponsor-level-10-name',
        'sponsor-level-20-name',
        'sponsor-level-30-name',
        'sponsor-level-40-name',
        'sponsor-level-50-name',
        );
    foreach($detail_keys as $key_name) {
        if( isset($args[$key_name]) ) {
            $strsql = "INSERT INTO ciniki_exhibition_details (business_id, exhibition_id, "
                . "detail_key, detail_value, date_added, last_updated) "
                . "VALUES ('" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "', "
                . "'" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "', "
                . "'" . ciniki_core_dbQuote($ciniki, $key_name) . "', "
                . "'" . ciniki_core_dbQuote($ciniki, $args[$key_name]) . "', "
                . "UTC_TIMESTAMP(), UTC_TIMESTAMP()) "
                . "ON DUPLICATE KEY UPDATE detail_value = '" . ciniki_core_dbQuote($ciniki, $args[$key_name]) . "' "
                . ", last_updated = UTC_TIMESTAMP() "
                . "";
            $rc = ciniki_core_dbInsert($ciniki, $strsql, 'ciniki.exhibitions');
            if( $rc['stat'] != 'ok' ) {
                ciniki_core_dbTransactionRollback($ciniki, 'ciniki.exhibitions');
                return $rc;
            }
            $rc = ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.exhibitions', 
                'ciniki_exhibition_history', $args['business_id'], 
                2, 'ciniki_exhibition_details', $args['exhibition_id'], $key_name, $args[$key_name]);
        }
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

    $ciniki['syncqueue'][] = array('push'=>'ciniki.exhibitions.exhibition', 
        'args'=>array('id'=>$args['exhibition_id']));

    return array('stat'=>'ok');
}
?>
