<?php
//
// Description
// -----------
// This function will go through the history of the ciniki.customers module and add missing history elements.
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_exhibitions_dbIntegrityCheck($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'fix'=>array('required'=>'no', 'default'=>'no', 'name'=>'Fix Problems'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    //
    // Check access to tnid as owner, or sys admin
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $rc = ciniki_exhibitions_checkAccess($ciniki, $args['tnid'], 'ciniki.exhibitions.dbIntegrityCheck', 0);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbUpdate');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDelete');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbFixTableHistory');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectRefFix');

    if( $args['fix'] == 'yes' ) {
        //
        // Load objects file
        //
        ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'objects');
        $rc = ciniki_exhibitions_objects($ciniki);
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        $objects = $rc['objects'];

        //
        // Check any references for the objects
        //
        foreach($objects as $o => $obj) {
            $rc = ciniki_core_objectRefFix($ciniki, $args['tnid'], 'ciniki.exhibitions.'.$o, 0x04);
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
        }

        //
        // Update the history for ciniki_exhibitions
        //
        $rc = ciniki_core_dbFixTableHistory($ciniki, 'ciniki.exhibitions', $args['tnid'],
            'ciniki_exhibitions', 'ciniki_exhibition_history', 
            array('uuid', 'name', 'permalink', 'type', 'description', 'tagline', 
                'start_date', 'end_date'));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }

        //
        // Update the history for ciniki_exhibition_contacts
        //
        $rc = ciniki_core_dbFixTableHistory($ciniki, 'ciniki.exhibitions', $args['tnid'],
            'ciniki_exhibition_contacts', 'ciniki_exhibition_history', 
            array('uuid', 'first', 'last', 'company', 'permalink', 'email', 
                'passcode', 'phone_home', 'phone_work', 'phone_cell', 'phone_fax',
                'url', 'primary_image_id', 'short_description', 'description', 'notes'));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }

        //
        // Update the history for ciniki_exhibition_contact_images
        //
        $rc = ciniki_core_dbFixTableHistory($ciniki, 'ciniki.exhibitions', $args['tnid'],
            'ciniki_exhibition_contact_images', 'ciniki_exhibition_history', 
            array('uuid', 'contact_id', 'name', 'permalink', 'webflags', 'image_id', 
                'description', 'url'));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }

        //
        // Update the history for ciniki_exhibition_participants
        //
        $rc = ciniki_core_dbFixTableHistory($ciniki, 'ciniki.exhibitions', $args['tnid'],
            'ciniki_exhibition_participants', 'ciniki_exhibition_history', 
            array('uuid', 'exhibition_id', 'contact_id', 'category', 'type', 
                'status', 'webflags', 'title', 'location'));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }

        //
        // Update the history for ciniki_exhibition_images
        //
        $rc = ciniki_core_dbFixTableHistory($ciniki, 'ciniki.exhibitions', $args['tnid'],
            'ciniki_exhibition_images', 'ciniki_exhibition_history', 
            array('uuid', 'exhibition_id', 'name', 'permalink', 'category', 
                'webflags', 'image_id', 'description'));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }

        //
        // Check for items missing a UUID
        //
        $strsql = "UPDATE ciniki_exhibition_history SET uuid = UUID() WHERE uuid = ''";
        $rc = ciniki_core_dbUpdate($ciniki, $strsql, 'ciniki.exhibitions');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }

        //
        // Remote any entries with blank table_key, they are useless we don't know what they were attached to
        //
        $strsql = "DELETE FROM ciniki_exhibition_history WHERE table_key = ''";
        $rc = ciniki_core_dbDelete($ciniki, $strsql, 'ciniki.exhibitions');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
    }
    return array('stat'=>'ok');
}
?>
