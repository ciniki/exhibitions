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
// name:                The name of the exhibition.  
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_exhibitions_imageUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'exhibition_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Exhibition Image'), 
        'image_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Image'),
        'name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Title'), 
        'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
        'category'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Category'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Website Flags'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'), 
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
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['tnid'], 'ciniki.exhibitions.imageUpdate', 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Get the exhibition ID
    //
    $strsql = "SELECT id, exhibition_id "
        . "FROM ciniki_exhibition_images "
        . "WHERE id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_image_id']) . "' "
        . "AND tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.exhibitions', 'image');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['image']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.exhibitions.28', 'msg'=>'The image does not exist.'));
    }
    $exhibition_id = $rc['image']['exhibition_id'];

    //
    // Check if permalink should be changed
    //
    if( isset($args['name']) ) {
        if( $args['name'] != '' ) { 
            $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['name'])));
        } else {
            $strsql = "SELECT uuid FROM ciniki_exhibition_images "
                . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_image_id']) . "' "
                . "";
            $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.exhibitions', 'image');
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            if( !isset($rc['image']) ) {
                return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.exhibitions.22', 'msg'=>'Unable to update image'));
            }
            $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($rc['image']['uuid'])));
        }
        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id FROM ciniki_exhibition_images "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['exhibition_image_id']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.exhibitions', 'image');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.exhibitions.23', 'msg'=>'You already have an image with this name, please choose another name'));
        }
    } 

    //
    // Update the image
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    return ciniki_core_objectUpdate($ciniki, $args['tnid'], 'ciniki.exhibitions.image', $args['exhibition_image_id'], $args, 0x07);
}
?>
