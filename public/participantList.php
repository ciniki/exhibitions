<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:     The ID of the business to get events for.
// type:            The type of participants to get.  Refer to participantAdd for 
//                  more information on types.
//
// Returns
// -------
//
function ciniki_exhibitions_participantList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'exhibition_id'=>array('required'=>'yes', 'blankk'=>'yes', 'name'=>'Exhibition'),
        'type'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Participant Type'),
        'details'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Details'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    //  
    // Check access to business_id as owner, or sys admin. 
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $ac = ciniki_exhibitions_checkAccess($ciniki, $args['business_id'], 'ciniki.exhibitions.participantList');
    if( $ac['stat'] != 'ok' ) { 
        return $ac;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);
    
    //
    // Load the list of participants for an exhibition
    //
    $strsql = "SELECT ciniki_exhibition_participants.category, "
        . "ciniki_exhibition_participants.id, "
        . "ciniki_exhibition_participants.type, "
        . "ciniki_exhibition_participants.title, "
        . "ciniki_exhibition_participants.status, "
        . "ciniki_exhibition_participants.status AS status_text, "
        . "ciniki_exhibition_participants.location, "
        . "IF((ciniki_exhibition_participants.webflags&0x01)=1,'Hidden','Visible') as webvisible, "
        . "ciniki_exhibition_contacts.first, "
        . "ciniki_exhibition_contacts.last, "
        . "ciniki_exhibition_contacts.company, "
        . "ciniki_exhibition_contacts.email, "
        . "ciniki_exhibition_contacts.phone_home, "
        . "ciniki_exhibition_contacts.phone_work, "
        . "ciniki_exhibition_contacts.phone_cell, "
        . "ciniki_exhibition_contacts.phone_fax, "
        . "ciniki_exhibition_contacts.address1, "
        . "ciniki_exhibition_contacts.address2, "
        . "ciniki_exhibition_contacts.city, "
        . "ciniki_exhibition_contacts.province, "
        . "ciniki_exhibition_contacts.postal "
        . "";
    if( isset($args['details']) && $args['details'] == 'yes' ) {
        $strsql .= ", "
            . "ciniki_exhibition_contacts.studio_name, "
            . "ciniki_exhibition_contacts.latitude, "
            . "ciniki_exhibition_contacts.longitude, "
            . "ciniki_exhibition_contacts.primary_image_id, "
            . "ciniki_exhibition_contacts.short_description, "
            . "ciniki_exhibition_contacts.description, "
            . "ciniki_exhibition_contact_images.image_id AS images "
            . "";
    }
    $strsql .= "FROM ciniki_exhibition_participants "
        . "LEFT JOIN ciniki_exhibition_contacts ON ("
            . "ciniki_exhibition_participants.contact_id = ciniki_exhibition_contacts.id "
            . "AND ciniki_exhibition_contacts.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
            . ") ";
    if( isset($args['details']) && $args['details'] == 'yes' ) {
        $strsql .= "LEFT JOIN ciniki_exhibition_contact_images ON ("
            . "ciniki_exhibition_participants.contact_id = ciniki_exhibition_contact_images.contact_id "
            . "AND ciniki_exhibition_contact_images.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
            . ") ";
    }

    $strsql .= "WHERE ciniki_exhibition_participants.exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
        . "AND ciniki_exhibition_participants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "";
    if( isset($args['type']) && $args['type'] != '' ) {
        $strsql .= "AND (ciniki_exhibition_participants.type&'" . ciniki_core_dbQuote($ciniki, $args['type']) . "') > 0 ";
    }
    if( isset($args['categorized']) && $args['categorized'] == 'yes' ) {
        $strsql .= "ORDER BY ciniki_exhibition_participants.category, ";
    } else {
        $strsql .= "ORDER BY ";
    }
    $strsql .= "ciniki_exhibition_contacts.first, ciniki_exhibition_contacts.last ";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $statuses = array('0'=>'Unknown', '1'=>'Applied', '10'=>'Accepted', '60'=>'Rejected');
    if( isset($args['categorized']) && $args['categorized'] == 'yes' ) {
        if( isset($args['details']) && $args['details'] == 'yes' ) {
            $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
                array('container'=>'categories', 'fname'=>'category', 'name'=>'category',
                    'fields'=>array('name'=>'category')),
                array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
                    'fields'=>array('id', 'first', 'last', 'company', 'title', 'status', 'status_text',
                        'category', 'location', 
                        'address1', 'address2', 'city', 'province', 'postal',
                        'phone_home', 'phone_work', 'phone_cell', 'phone_fax', 'email',
                        'webvisible', 'latitude', 'longitude',
                        'primary_image_id', 'short_description', 'description', 'images'),
                    'lists'=>array('images'),
                    'maps'=>array('status_text'=>$statuses),
                    ),
                ));
        } else {
            $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
                array('container'=>'categories', 'fname'=>'category', 'name'=>'category',
                    'fields'=>array('name'=>'category')),
                array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
                    'fields'=>array('id', 'first', 'last', 'company', 'title', 'status', 'status_text',
                        'address1', 'address2', 'city', 'province', 'postal',
                        'phone_home', 'phone_work', 'phone_cell', 'phone_fax', 'email',
                        'webvisible'),
                    'maps'=>array('status_text'=>$statuses),
                    ),
                ));
        }
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['categories']) ) {
            return array('stat'=>'ok', 'categories'=>$rc['categories']);
        }
        return array('stat'=>'ok', 'categories'=>array());
    } else {
        if( isset($args['details']) && $args['details'] == 'yes' ) {
            $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
                array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
                    'fields'=>array('id', 'first', 'last', 'company', 'title', 'status', 'status_text',
                        'category', 'location',
                        'address1', 'address2', 'city', 'province', 'postal',
                        'phone_home', 'phone_work', 'phone_cell', 'phone_fax', 'email',
                        'webvisible', 'latitude', 'longitude',
                        'primary_image_id', 'short_description', 'description', 'images'),
                    'lists'=>array('images'),
                    'maps'=>array('status_text'=>$statuses),
                    ),
                ));
        } else {
            $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
                array('container'=>'participants', 'fname'=>'id', 'name'=>'participant',
                    'fields'=>array('id', 'first', 'last', 'company', 'title', 'status', 'status_text',
                        'category', 'location', 
                        'address1', 'address2', 'city', 'province', 'postal',
                        'phone_home', 'phone_work', 'phone_cell', 'phone_fax', 'email',
                        'webvisible'),
                    'maps'=>array('status_text'=>$statuses),
                    ),
                ));
        }
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['participants']) ) {
            return array('stat'=>'ok', 'participants'=>$rc['participants']);
        } 
        return array('stat'=>'ok', 'participants'=>array());
    }
}
?>
