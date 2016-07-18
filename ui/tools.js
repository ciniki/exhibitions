
//
// The exhibitions app
//
function ciniki_exhibitions_tools() {
    this.webFlags = {
        '1':{'name':'Hidden'},
        };
    this.init = function() {
        //
        // The tools available to work on customer records
        //
        this.tour= new M.panel('Tools',
            'ciniki_exhibitions_tools', 'tour',
            'mc', 'narrow', 'sectioned', 'ciniki.exhibitions.tools.tour');
        this.tour.data = {};
        this.tour.sections = {
            'tools':{'label':'Reports', 'list':{
                'missing':{'label':'Missing Information', 
                    'fn':'M.ciniki_exhibitions_tools.showTourMissing(\'M.ciniki_exhibitions_tools.showTour()\',M.ciniki_exhibitions_tools.tour.exhibition_id);'},
            }},
            'exports':{'label':'Exports', 'list':{
                'missing':{'label':'Download Excel Participant List', 
                    'fn':'M.ciniki_exhibitions_tools.downloadExcel(\'ciniki.exhibitions.participantListExcel\',M.ciniki_exhibitions_tools.tour.exhibition_id);'},
            }},
        };
        this.tour.addClose('Back');

        //
        // This panel display missing information for exhibitors or tourexhibitors
        //
        this.missing = new M.panel('Missing Information',
            'ciniki_exhibitions_tools', 'missing',
            'mc', 'medium', 'sectioned', 'ciniki.exhibitions.tools.missing');
        this.missing.data = {};
        this.missing.sections = {};
        this.missing.sectionData = function(s) { return this.data[s]; }
        this.missing.cellValue = function(s, i, j, d) {
            if( j == 0 ) { return d.label; }
            if( j == 1 ) { return d.value; }
            };
        this.missing.rowFn = function(s, i, d) {
            if( d.participant_id != null ) { 
                return 'M.startApp(\'ciniki.exhibitions.participants\',null,\'M.ciniki_exhibitions_tools.showMissing();\',\'mc\',{\'exhibition_id\':' + M.ciniki_exhibitions_tools.missing.exhibition_id + ',\'participant_id\':' + d.participant_id + ',\'type\':\'' + M.ciniki_exhibitions_tools.missing.participant_type_text + '\'});'; 
            } 
            return '';
        };
        this.missing.addClose('Back');
    }

    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) { args = eval(aG); }

        //
        // Create container
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_exhibitions_tools', 'yes');
        if( appContainer == null ) {
            alert('App Error');
            return false;
        }

        if( args.type == 0x10 ) {
            this.showExhibitors(cb);
        } else {
            this.showTour(cb, args.exhibition_id);
        }
    }

    this.showTour = function(cb, eid) {
        if( eid != null ) { this.tour.exhibition_id = eid; }
        this.tour.refresh();
        this.tour.show(cb);
    };

    this.showTourMissing = function(cb, eid) {
        this.showMissing(cb, eid, 0x40, 'tourexhibitor');
    };

    this.showMissing = function(cb, eid, type, typet) {
        if( type != null ) { this.missing.participant_type = 0x40; }
        if( typet != null ) { this.missing.participant_type_text = typet; }
        if( eid != null ) { this.missing.exhibition_id = eid; }
        this.missing.data = {};
        M.api.getJSONCb('ciniki.exhibitions.participantList', {'business_id':M.curBusinessID,
            'exhibition_id':this.missing.exhibition_id,
            'type':this.missing.participant_type, 'details':'yes'}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_exhibitions_tools.missing;
                p.sections = {};
                p.data = {};
                p.sections = {};
                for(i in rsp.participants) {
                    pt = rsp.participants[i].participant;
                    var missing = '';
                    if( pt.address1 == null || pt.address1 == '' ) {
                        missing += (missing!=''?'<br/>':'') + 'Address';
                    }
                    if( p.participant_type == 0x40 && (pt.latitude == null || pt.latitude == '') ) {
                        missing += (missing!=''?'<br/>':'') + 'Latitude/Longitude';
                    }
                    if( pt.primary_image_id == null || pt.primary_image_id == '' || pt.primary_image_id == '0' ) {
                        missing += (missing!=''?'<br/>':'') + 'Profile Image';
                    }
                    if( pt.short_description == null || pt.short_description == '' ) {
                        missing += (missing!=''?'<br/>':'') + 'Short Bio';
                    }
                    if( pt.description == null || pt.description == '' ) {
                        missing += (missing!=''?'<br/>':'') + 'Full Bio';
                    }
                    if( pt.images == null || pt.images == '' || pt.images == '0' ) {
                        missing += (missing!=''?'<br/>':'') + 'Additional Images';
                    }
                    if( missing == '' ) {
                        continue;
                    }

                    p.data[i] = {};
                    p.sections[i] = {'label':pt.first + ' ' + pt.last,
                        'type':'simplegrid', 'num_cols':2,
                        'headerValues':null,
                        'cellClasses':['label',''],
                        };
                    if( pt.phone_home != null && pt.phone_home != '' ) {
                        p.data[i]['0'] = {'label':'Home', 'value':pt.phone_home};
                    }
                    if( pt.phone_work != null && pt.phone_work != '' ) {
                        p.data[i]['1'] = {'label':'Work', 'value':pt.phone_work};
                    }
                    if( pt.phone_cell != null && pt.phone_cell != '' ) {
                        p.data[i]['2'] = {'label':'Cell', 'value':pt.phone_cell};
                    }
                    if( pt.phone_fax != null && pt.phone_fax != '' ) {
                        p.data[i]['3'] = {'label':'Fax', 'value':pt.phone_fax};
                    }
                    if( pt.email != null && pt.email != '' ) {
                        p.data[i]['4'] = {'label':'Email', 'value':pt.email};
                    }
                    p.data[i]['9'] = {'label':'Missing', 'participant_id':pt.id, 'value':missing};

                }
                p.refresh();
                p.show(cb);
            });
    };

    this.downloadExcel = function(api, tour_id) {
        M.api.openFile(api, {'business_id':M.curBusinessID, 'exhibition_id':tour_id, 'type':0x40});
    };
}
