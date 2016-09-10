<?php
//
// Description
// -----------
// This function will return a list of user interface settings for the module.
//
// Arguments
// ---------
// ciniki:
// business_id:     The ID of the business to get events for.
//
// Returns
// -------
//
function ciniki_exhibitions_hooks_uiSettings($ciniki, $business_id, $args) {

    //
    // Setup the default response
    //
    $rsp = array('stat'=>'ok', 'settings'=>array(), 'menu_items'=>array());  

    //
    // Check permissions for what menu items should be available
    //
    if( isset($ciniki['business']['modules']['ciniki.exhibitions'])
        && (isset($args['permissions']['owners'])
            || isset($args['permissions']['employees'])
            || isset($args['permissions']['resellers'])
            || ($ciniki['session']['user']['perms']&0x01) == 0x01
            )
        ) {
        //
        // Load the two most current exhibitions
        //
        $strsql = "SELECT ciniki_exhibitions.id, ciniki_exhibitions.name, "
            . "ciniki_exhibition_details.detail_key, "
            . "ciniki_exhibition_details.detail_value "
            . "FROM ciniki_exhibitions "
            . "LEFT JOIN ciniki_exhibition_details ON (ciniki_exhibitions.id = ciniki_exhibition_details.exhibition_id "
                . "AND ciniki_exhibition_details.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
                . ") "
            . "WHERE ciniki_exhibitions.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "ORDER BY ciniki_exhibitions.start_date DESC "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
        $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.exhibitions', array(
            array('container'=>'exhibitions', 'fname'=>'id', 'name'=>'exhibition',
                'fields'=>array('id', 'name'),
                'details'=>array('detail_key'=>'detail_value')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
      
        $priority = 8121;
        if( isset($rc['exhibitions']) ) {   
            foreach($rc['exhibitions'] as $exhibition) {
                $exhibition = $exhibition['exhibition'];
                $menu_item = array(
                    'priority'=>$priority,
                    'label'=>$exhibition['name'],
                    'subitems'=>array(),
                    );
                if( isset($exhibition['use-exhibitors']) && $exhibition['use-exhibitors'] == 'yes' ) {
                    $menu_item['subitems'][] = array(
                        'label'=>'Exhibitors',
                        'edit'=>array('app'=>'ciniki.exhibitions.participants', 'args'=>array('exhibition_id'=>'"' . $exhibition['id'] . '"', 'exhibitors'=>'\'"yes"\'')),
                        );
                }
                if( isset($exhibition['use-tour']) && $exhibition['use-tour'] == 'yes' ) {
                    $menu_item['subitems'][] = array(
                        'label'=>'Tour Exhibitors',
                        'edit'=>array('app'=>'ciniki.exhibitions.participants', 'args'=>array('exhibition_id'=>'"' . $exhibition['id'] . '"', 'tour'=>'\'"yes"\'')),
                        );
                }
                if( isset($exhibition['use-sponsors']) && $exhibition['use-sponsors'] == 'yes' ) {
                    $menu_item['subitems'][] = array(
                        'label'=>'Sponsors',
                        'edit'=>array('app'=>'ciniki.exhibitions.participants', 'args'=>array('exhibition_id'=>'"' . $exhibition['id'] . '"', 'sponsors'=>'\'"yes"\'')),
                        );
                }
                $rsp['menu_items'][] = $menu_item;
                $priority--;
                //
                // Limit menu to 2 items
                //
                if( $priority < 8120 ) { 
                    break;
                }
            }
        }

        //
        // Add the default menu item for exhibitions
        //
        $menu_item = array(
            'priority'=>3500,
            'label'=>'Exhibitions', 
            'edit'=>array('app'=>'ciniki.exhibitions.main'),
            );
        $rsp['menu_items'][] = $menu_item;
    } 

    return $rsp;
}
?>
