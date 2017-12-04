<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:     The ID of the tenant to get events for.
// type:            The type of participants to get.  Refer to participantAdd for 
//                  more information on types.
//
// Returns
// -------
//
function ciniki_exhibitions_imageList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'exhibition_id'=>array('required'=>'yes', 'blankk'=>'yes', 'name'=>'Exhibition'),
        'category'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Category'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    //  
    // Check access to tnid as owner, or sys admin. 
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'checkAccess');
    $ac = ciniki_exhibitions_checkAccess($ciniki, $args['tnid'], 'ciniki.exhibitions.imageList');
    if( $ac['stat'] != 'ok' ) { 
        return $ac;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);

    if( !isset($args['category']) ) {
        //
        // Load the list of categories and image counts
        //
        $strsql = "SELECT IF(category='', 'Uncategorized', category) AS category, "
            . "COUNT(*) AS count "
            . "FROM ciniki_exhibition_images "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' "
            . "GROUP BY category "
            . "ORDER BY category "
            . "";
        $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
            array('container'=>'categories', 'fname'=>'category', 'name'=>'category',
                'fields'=>array('name'=>'category', 'count')), 
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( !isset($rc['categories']) ) {
            return array('stat'=>'ok', 'category'=>'', 'images'=>array());
        }
        if( count($rc['categories']) > 1 ) {
            return array('stat'=>'ok', 'categories'=>$rc['categories']);
        }
        if( count($rc['categories']) == 0 ) {
            return array('stat'=>'ok', 'categories'=>array());
        }

        // 
        // If there is only one category, go directly and list images
        //
        $args['category'] = $rc['categories'][0]['category']['name'];
    }

    //
    // Load the list of images for a category
    //
    $strsql = "SELECT ciniki_exhibition_images.id, "
        . "ciniki_exhibition_images.exhibition_id, "
        . "ciniki_exhibition_images.name, "
        . "ciniki_exhibition_images.webflags, "
        . "ciniki_exhibition_images.image_id, "
        . "ciniki_exhibition_images.description "
        . "FROM ciniki_exhibition_images "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND exhibition_id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_id']) . "' ";
    if( $args['category'] == 'Uncategorized' ) {
        $strsql .= "AND category = '' ";
    } else {
        $strsql .= "AND category = '" . ciniki_core_dbQuote($ciniki, $args['category']) . "' ";
    }
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
        array('container'=>'images', 'fname'=>'id', 'name'=>'image',
            'fields'=>array('id', 'name', 'webflags', 'image_id', 'description')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['images']) ) {
        return array('stat'=>'ok', 'category'=>$args['category'], 'images'=>array());
    }
    $images = $rc['images'];

    //
    // Add thumbnail information into list
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheThumbnail');
    foreach($images as $inum => $image) {
        if( isset($image['image']['image_id']) && $image['image']['image_id'] > 0 ) {
            $rc = ciniki_images_loadCacheThumbnail($ciniki, $args['tnid'], $image['image']['image_id'], 75);
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            $images[$inum]['image']['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
        }
    }

    return array('stat'=>'ok', 'category'=>$args['category'], 'images'=>$images);
}
?>
