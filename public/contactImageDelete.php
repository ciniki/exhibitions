<?php
//
// Description
// ===========
//
// Arguments
// ---------
// 
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_exhibitions_contactImageDelete(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'contact_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Image'),
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
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['tnid'], 'ciniki.exhibitions.contactImageDelete', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Get the existing image information
    //
    $strsql = "SELECT uuid FROM ciniki_exhibition_contact_images "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['contact_image_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.exhibitions', 'item');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['item']) ) {
        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.exhibitions');
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.exhibitions.9', 'msg'=>'Contact image does not exist'));
    }
    $uuid = $rc['item']['uuid'];

    //
    // Delete the object
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectDelete');
    return ciniki_core_objectDelete($ciniki, $args['tnid'], 'ciniki.exhibitions.contact_image', $args['contact_image_id'], $uuid, 0x07);
}
?>
