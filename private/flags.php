<?php
//
// Description
// -----------
// The module flags
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_exhibitions_flags($ciniki, $modules) {
    $flags = array(
        array('flag'=>array('bit'=>'1', 'name'=>'Exhibitors')),
        array('flag'=>array('bit'=>'2', 'name'=>'Sponsors')),
        array('flag'=>array('bit'=>'3', 'name'=>'Tour')),
        );

    return array('stat'=>'ok', 'flags'=>$flags);
}
?>
