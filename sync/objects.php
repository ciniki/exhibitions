<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_exhibitions_sync_objects(&$ciniki, &$sync, $business_id, $args) {
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'objects');
    return ciniki_exhibitions_objects($ciniki);
}
?>
