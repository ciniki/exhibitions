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
function ciniki_exhibitions_sync_objects(&$ciniki, &$sync, $tnid, $args) {
    ciniki_core_loadMethod($ciniki, 'ciniki', 'exhibitions', 'private', 'objects');
    return ciniki_exhibitions_objects($ciniki);
}
?>
