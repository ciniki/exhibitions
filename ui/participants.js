//
// The participants app to manage participants for an exhibition
//
function ciniki_exhibitions_participants() {
    this.statusToggles = {'1':'Applied', '10':'Accepted', '60':'Rejected'};
    this.webFlags = {'1':{'name':'Hidden'}};
    this.sponsorLevels = [];

    this.init = function() {
        //
        // Setup the main panel to list the participants 
        //
        this.exhibitors = new M.panel('Exhibitors',
            'ciniki_exhibitions_participants', 'exhibitors',
            'mc', 'medium', 'sectioned', 'ciniki.exhibitions.participants.exhibitors');
        this.exhibitors.data = {};
        this.exhibitors.exhibition_id = 0;
        this.exhibitors.sections = {
            '_':{'label':'', 'type':'simplegrid', 'sortable':'yes', 'num_cols':3,
                'headerValues':['Name/Company', 'Category', 'Status'],
                'sortTypes':['text', 'text', 'text'],
                'cellClasses':['multiline', 'multiline', 'multiline'],
                'noData':'No participants',
                'addTxt':'Add Exhibitor',
                'addFn':'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showExhibitors();\',M.ciniki_exhibitions_participants.exhibitors.exhibition_id,0,0,\'exhibitor\');',
                },
            };
        this.exhibitors.sectionData = function(s) { return this.data; }
        this.exhibitors.cellValue = function(s, i, j, d) {
            if( j == 0 ) {
                if( d.participant.company != null && d.participant.company != '' ) {
                    return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span><span class="subtext">' + d.participant.company + '</span>';
                } 
                return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span>';
            } else if( j == 1 ) {
                return '<span class="maintext">' + d.participant.category + '</span><span class="subtext">' + d.participant.location + '</span>';
            } else if( j == 2 ) {
                return '<span class="maintext">' + d.participant.status_text + '</span><span class="subtext">' + d.participant.webvisible + '</span>';
            }
        };
        this.exhibitors.rowFn = function(s, i, d) { 
            return 'M.ciniki_exhibitions_participants.showParticipant(\'M.ciniki_exhibitions_participants.showExhibitors();\',\'' + this.exhibition_id + '\',\'' + d.participant.id + '\',\'exhibitor\');'; 
        };
        this.exhibitors.addButton('add', 'Add', 'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showExhibitors();\',M.ciniki_exhibitions_participants.exhibitors.exhibition_id,0,0,\'exhibitor\');');
        this.exhibitors.addClose('Back');

        //
        // The panel to display the list of sponsors
        //
        this.sponsors = new M.panel('Sponsors',
            'ciniki_exhibitions_participants', 'sponsors',
            'mc', 'medium', 'sectioned', 'ciniki.exhibitions.participants.sponsors');
        this.sponsors.data = {};
        this.sponsors.exhibition_id = 0;
        this.sponsors.sections = {
            '_':{'label':'', 'type':'simplegrid', 'num_cols':1,
                'headerValues':['Name/Company'],
                'cellClasses':['multiline'],
                'noData':'No participants',
                'addTxt':'Add Sponsor',
                'addFn':'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showSponsors();\',M.ciniki_exhibitions_participants.sponsors.exhibition_id,0,0,\'sponsor\');',
                },
            };
        this.sponsors.sectionData = function(s) { return this.data; }
        this.sponsors.cellValue = function(s, i, j, d) {
            if( j == 0 ) {
                if( d.participant.company != null && d.participant.company != '' ) {
                    return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span><span class="subtext">' + d.participant.company + '</span>';
                } 
                return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span>';
            }
        };
        this.sponsors.rowFn = function(s, i, d) { 
            return 'M.ciniki_exhibitions_participants.showParticipant(\'M.ciniki_exhibitions_participants.showSponsors();\',\'' + this.exhibition_id + '\',\'' + d.participant.id + '\',\'sponsor\');'; 
        };
        this.sponsors.addButton('add', 'Add', 'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showSponsors();\',M.ciniki_exhibitions_participants.sponsors.exhibition_id,0,0,\'sponsor\'');
        this.sponsors.addClose('Back');

        //
        // The panel to display the list of tour exhibitors
        //
        this.tourexhibitors = new M.panel('Tour Exhibitors',
            'ciniki_exhibitions_participants', 'tourexhibitors',
            'mc', 'medium', 'sectioned', 'ciniki.exhibitions.participants.tourexhibitors');
        this.tourexhibitors.data = {};
        this.tourexhibitors.exhibition_id = 0;
        this.tourexhibitors.sections = {
            '_':{'label':'', 'type':'simplegrid', 'sortable':'yes', 'num_cols':3,
                'headerValues':['Name/Company', 'Category', 'Status'],
                'sortTypes':['text', 'text'],
                'cellClasses':['multiline', 'multiline', 'multiline'],
                'noData':'No participants',
                'addTxt':'Add Exhibitor',
                'addFn':'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showTourExhibitors();\',M.ciniki_exhibitions_participants.tourexhibitors.exhibition_id,0,0,\'tourexhibitor\');',
                },
            };
        this.tourexhibitors.sectionData = function(s) { return this.data; }
        this.tourexhibitors.cellValue = function(s, i, j, d) {
            if( j == 0 ) {
                if( d.participant.company != null && d.participant.company != '' ) {
                    return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span><span class="subtext">' + d.participant.company + '</span>';
                } 
                return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span>';
            } else if( j == 1 ) {
                return '<span class="maintext">' + d.participant.category + '</span>';
            } else if( j == 2 ) {
                return '<span class="maintext">' + d.participant.status_text + '</span><span class="subtext">' + d.participant.webvisible + '</span>';
            }
        };
        this.tourexhibitors.rowFn = function(s, i, d) { 
            return 'M.ciniki_exhibitions_participants.showParticipant(\'M.ciniki_exhibitions_participants.showTourExhibitors();\',\'' + this.exhibition_id + '\',\'' + d.participant.id + '\',\'tourexhibitor\');'; 
        };
        this.tourexhibitors.addButton('add', 'Add', 'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showTourExhibitors();\',M.ciniki_exhibitions_participants.tourexhibitors.exhibition_id,0,0,\'tour\'');
        this.tourexhibitors.addButton('tools', 'Tools', 'M.startApp(\'ciniki.exhibitions.tools\',null,\'M.ciniki_exhibitions_participants.showTourExhibitors();\',\'mc\',{\'type\':0x40,\'exhibition_id\':M.ciniki_exhibitions_participants.tourexhibitors.exhibition_id});');
        this.tourexhibitors.addClose('Back');

        //
        // The panel to display the list of contacts (organizers, exhibitors, sponsors, etc)
        //
        this.contacts = new M.panel('Contacts',
            'ciniki_exhibitions_participants', 'contacts',
            'mc', 'medium', 'sectioned', 'ciniki.exhibitions.participants.contacts');
        this.contacts.data = {};
        this.contacts.exhibition_id = 0;
        this.contacts.sections = {
            '_':{'label':'', 'type':'simplegrid', 'num_cols':1,
                'headerValues':null,
                'cellClasses':['multiline'],
                'noData':'No participants',
                'addTxt':'Add Contact',
                'addFn':'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showContacts();\',M.ciniki_exhibitions_participants.contacts.exhibition_id,0,0,\'contact\');',
                },
            };
        this.contacts.sectionData = function(s) { return this.data; }
        this.contacts.cellValue = function(s, i, j, d) {
            if( d.participant.company != null && d.participant.company != '' ) {
                return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span><span class="subtext">' + d.participant.company + '</span>';
            } 
            return '<span class="maintext">' + d.participant.first + ' ' + d.participant.last + '</span>';
        };
        this.contacts.rowFn = function(s, i, d) { 
            return 'M.ciniki_exhibitions_participants.showParticipant(\'M.ciniki_exhibitions_participants.showContacts();\',\'' + this.exhibition_id + '\',\'' + d.participant.id + '\',\'contact\');'; 
        };
        this.contacts.addButton('add', 'Add', 'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showContacts();\',M.ciniki_exhibitions_participants.contacts.exhibition_id,0,0,\'contact\');');
        this.contacts.addClose('Back');

        //
        // The participant panel will show the information for a exhibitor/sponsor/organizer
        //
        this.participant = new M.panel('Participant',
            'ciniki_exhibitions_participants', 'participant',
            'mc', 'medium mediumaside', 'sectioned', 'ciniki.exhibitions.participants.participant');
        this.participant.data = {};
        this.participant.participant_id = 0;
        this.participant.exhibition_id = 0;
        this.participant.sections = {
            '_image':{'label':'', 'aside':'yes', 'type':'imageform', 'fields':{
                'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'history':'no'},
                }},
            'info':{'label':'', 'aside':'yes', 'list':{
                'name':{'label':'Name'},
                'company':{'label':'Company', 'visible':'no'},
                'email':{'label':'Email', 'visible':'no'},
                'phone_home':{'label':'Home Phone', 'visible':'no'},
                'phone_work':{'label':'Work Phone', 'visible':'no'},
                'phone_cell':{'label':'Cell Phone', 'visible':'no'},
                'phone_fax':{'label':'Fax', 'visible':'no'},
                'url':{'label':'Website', 'visible':'no'},
                }},
            'address':{'label':'', 'aside':'yes', 'visible':'no', 'list':{
                'studio_name':{'label':'Studio Name'},
                'address1':{'label':'Display Address'},
                'mailing_address1':{'label':'Mailing Address'},
                'latlong':{'label':'Lat/Long'},
                }},
            'organizer':{'label':'Organization', 'aside':'yes', 'visible':'no', 'list':{
                'title':{'label':'Title'},
                }},
            'exhibitor':{'label':'Organization', 'aside':'yes', 'visible':'no', 'list':{
                'category':{'label':'Category'},
                'status_text':{'label':'Status'},
                'webvisible':{'label':'Website'},
                'location':{'label':'Location'},
                }},
            'sponsor':{'label':'Organization', 'aside':'yes', 'visible':'no', 'list':{
                'level':{'label':'Level'},
                'sequence':{'label':'Sequence'},
                'category':{'label':'Category'},
                'webvisible':{'label':'Website'},
                'location':{'label':'Location'},
                }},
            'tourexhibitor':{'label':'Organization', 'aside':'yes', 'visible':'no', 'list':{
                'category':{'label':'Category'},
                'status_text':{'label':'Status'},
                'webvisible':{'label':'Website'},
                }},
            'short_description':{'label':'Brief Description', 'type':'htmlcontent'},
            'description':{'label':'Bio', 'type':'htmlcontent'},
            'notes':{'label':'Notes', 'type':'htmlcontent'},
            'images':{'label':'Gallery', 'type':'simplethumbs'},
            '_images':{'label':'', 'hidelabel':'yes', 'type':'simplegrid', 'num_cols':1,
                'addTxt':'Add Image',
                'addFn':'M.startApp(\'ciniki.exhibitions.contactimages\',null,\'M.ciniki_exhibitions_participants.showParticipant();\',\'mc\',{\'contact_id\':M.ciniki_exhibitions_participants.participant.contact_id,\'add\':\'yes\'});',
                },
            '_buttons':{'label':'', 'buttons':{
                'edit':{'label':'Edit', 'fn':'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showParticipant();\',M.ciniki_exhibitions_participants.participant.exhibition_id,M.ciniki_exhibitions_participants.participant.participant_id,0,M.ciniki_exhibitions_participants.participant.participant_type);'},
//              'delete':{'label':'Delete', 'fn':'M.ciniki_artcatalog_main.deletePiece();'},
                }},
        };
        this.participant.sectionData = function(s) {
            if( s == 'images' ) { return this.data.images; }
            if( s == 'short_description' || s == 'description' || s == 'notes' ) { return this.data[s].replace(/\n/g, '<br/>'); }
                return this.sections[s].list;
            };
        this.participant.listLabel = function(s, i, d) {
            if( s == 'info' || s == 'organizer' || s == 'exhibitor' || s == 'sponsor' || s == 'tourexhibitor' || s == 'address' ) { 
                return d.label; 
            }
            return null;
        };
        this.participant.listValue = function(s, i, d) {
            if( s == 'address' && i == 'address1' ) {
                var address = this.data.address1;
                if( this.data.address2 != '' ) {
                    address += ', ' + this.data.address2;
                }
                if( this.data.city != null && this.data.city != '' ) {
                    address += ', ' + this.data.city;
                }
                if( this.data.province != null && this.data.province != '' ) {
                    address += ', ' + this.data.province;
                }
                if( this.data.postal != null && this.data.postal != '' ) {
                    address += '  ' + this.data.postal;
                }
                return address;
            }
            if( s == 'address' && i == 'mailing_address1' ) {
                var address = this.data.mailing_address1;
                if( this.data.mailing_address2 != '' ) {
                    address += ', ' + this.data.mailing_address2;
                }
                if( this.data.mailing_city != null && this.data.mailing_city != '' ) {
                    address += ', ' + this.data.mailing_city;
                }
                if( this.data.mailing_province != null && this.data.mailing_province != '' ) {
                    address += ', ' + this.data.mailing_province;
                }
                if( this.data.mailing_postal != null && this.data.mailing_postal != '' ) {
                    address += '  ' + this.data.mailing_postal;
                }
                return address;
            }
            if( s == 'address' && i == 'latlong' ) {
                return this.data.latitude + ', '  + this.data.longitude;
            }
            if( i == 'name' ) {
                return this.data.first + ' '  + this.data.last;
            }
            if( i == 'level' ) {
                return M.ciniki_exhibitions_participants.sponsorLevels[this.data[i]];
            }
            return this.data[i];
        };
        this.participant.fieldValue = function(s, i, d) {
            if( i == 'short_description' || i == 'description' || i == 'notes' ) { 
                return this.data[i].replace(/\n/g, '<br/>');
            }
            return this.data[i];
        };
//      this.participant.rowFn = function(s, i, d) {
//          if( s == 'images' ) {
//              return 'M.startApp(\'ciniki.exhibitions.contactimages\',null,\'M.ciniki_exhibitions_participants.showParticipant();\',\'mc\',{\'contact_image_id\':\'' + d.image.id + '\'});';
//          }
//      };
        this.participant.thumbFn = function(s, i, d) {
            return 'M.startApp(\'ciniki.exhibitions.contactimages\',null,\'M.ciniki_exhibitions_participants.showParticipant();\',\'mc\',{\'contact_image_id\':\'' + d.image.id + '\'});';
        };
        this.participant.addDropImage = function(iid) {
            var rsp = M.api.getJSON('ciniki.exhibitions.contactImageAdd',
                {'business_id':M.curBusinessID, 'image_id':iid,
                    'contact_id':M.ciniki_exhibitions_participants.participant.contact_id});
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            return true;
        };
        this.participant.addDropImageRefresh = function() {
            if( M.ciniki_exhibitions_participants.participant.contact_id > 0 ) {
                var rsp = M.api.getJSONCb('ciniki.exhibitions.participantGet', {'business_id':M.curBusinessID, 
                    'exhibition_id':M.ciniki_exhibitions_participants.participant.exhibition_id, 
                    'participant_id':M.ciniki_exhibitions_participants.participant.participant_id, 'images':'yes'}, function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        }
                        M.ciniki_exhibitions_participants.participant.data.images = rsp.participant.images;
                        M.ciniki_exhibitions_participants.participant.refreshSection('images');
                    });
            }
        };
        this.participant.addButton('edit', 'Edit', 'M.ciniki_exhibitions_participants.showEdit(\'M.ciniki_exhibitions_participants.showParticipant();\',M.ciniki_exhibitions_participants.participant.exhibition_id,M.ciniki_exhibitions_participants.participant.participant_id,0,M.ciniki_exhibitions_participants.participant.participant_type);');
        this.participant.addClose('Back');

        //
        // The edit panel for participant/contact
        //
        this.edit = new M.panel('Edit',
            'ciniki_exhibitions_participants', 'edit',
            'mc', 'medium mediumaside', 'sectioned', 'ciniki.exhibitions.participants.edit');
        this.edit.data = {};
        this.edit.exhibition_id = 0;
        this.edit.participant_id = 0;
        this.edit.contact_id = 0;
        this.edit.participant_type = 0;
        this.edit.sections = {
            '_image':{'label':'', 'aside':'yes', 'type':'imageform', 'fields':{
                'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
            }},
            'name':{'label':'', 'aside':'yes', 'fields':{
                'first':{'label':'First Name', 'type':'text', 'livesearch':'yes'},
                'last':{'label':'Last Name', 'type':'text', 'livesearch':'yes'},
                'company':{'label':'Business', 'type':'text', 'livesearch':'yes'},
                }},
            'organization':{'label':'Organization', 'aside':'yes', 'visible':'no', 'fields':{
                'level':{'label':'Level', 'active':'no', 'type':'multitoggle', 'options':{}},
                'sequence':{'label':'Order', 'active':'no', 'type':'text', 'hint':'255-1', 'size':'small'},
                'category':{'label':'Category', 'active':'no', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
                'status':{'label':'Status', 'active':'no', 'type':'toggle', 'toggles':this.statusToggles},
                'webflags':{'label':'Website', 'type':'flags', 'toggle':'no', 'join':'yes', 'flags':this.webFlags},
                'title':{'label':'Title', 'active':'no', 'type':'text'},
                'location':{'label':'Location', 'type':'text'},
                }},
            'contact':{'label':'Contact Info', 'aside':'yes', 'fields':{
                'email':{'label':'Email', 'type':'text'},
                'phone_home':{'label':'Home Phone', 'type':'text'},
                'phone_work':{'label':'Work Phone', 'type':'text'},
                'phone_cell':{'label':'Cell Phone', 'type':'text'},
                'phone_fax':{'label':'Fax Phone', 'type':'text'},
                'url':{'label':'Website', 'type':'text'},
                }},
            'address':{'label':'Gallery Address', 'aside':'yes', 'fields':{
                'studio_name':{'label':'Studio Name', 'type':'text'},
                'address1':{'label':'Address', 'type':'text'},
                'address2':{'label':'', 'type':'text'},
                'city':{'label':'City', 'type':'text'},
                'province':{'label':'Province', 'type':'text'},
                'postal':{'label':'Postal', 'type':'text'},
                'latitude':{'label':'Latitude', 'type':'text'},
                'longitude':{'label':'Longitude', 'type':'text'},
                }},
            '_address_buttons':{'label':'', 'aside':'yes', 'buttons':{
                'save':{'label':'Lookup Lat/Long', 'fn':'M.ciniki_exhibitions_participants.lookupLatLong();'},
                }},
            'mailing_address':{'label':'Mailing Address', 'aside':'yes', 'fields':{
                'mailing_address1':{'label':'Address', 'type':'text'},
                'mailing_address2':{'label':'', 'type':'text'},
                'mailing_city':{'label':'City', 'type':'text'},
                'mailing_province':{'label':'Province', 'type':'text'},
                'mailing_postal':{'label':'Postal', 'type':'text'},
                }},
            '_short_description':{'label':'Brief Description', 'fields':{
                'short_description':{'label':'', 'hidelabel':'yes', 'size':'medium', 'type':'textarea'},
                }},
            '_description':{'label':'Bio', 'fields':{
                'description':{'label':'', 'hidelabel':'yes', 'type':'textarea'},
                }},
            '_notes':{'label':'Notes', 'fields':{
                'notes':{'label':'', 'hidelabel':'yes', 'type':'textarea'},
                }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Add', 'fn':'M.ciniki_exhibitions_participants.saveContact();'},
                'delete':{'label':'Delete', 'fn':'M.ciniki_exhibitions_participants.deleteParticipant();'},
                }},
        };
        this.edit.fieldValue = function(s, i, d) {
            if( this.data[i] != null ) { return this.data[i]; }
            return '';
        };
        this.edit.liveSearchCb = function(s, i, value) {
            if( i == 'first' || i == 'last' || i == 'company' ) {
                var rsp = M.api.getJSONBgCb('ciniki.exhibitions.contactSearch', 
                    {'business_id':M.curBusinessID, 
                    'exhibition_id':M.ciniki_exhibitions_participants.edit.exhibition_id, 
                    'start_needle':value, 'limit':25},
                    function(rsp) { 
                        M.ciniki_exhibitions_participants.edit.liveSearchShow(s, i, M.gE(M.ciniki_exhibitions_participants.edit.panelUID + '_' + i), rsp.contacts); 
                    });
            }
            if( i == 'category' ) {
                var rsp = M.api.getJSONBgCb('ciniki.exhibitions.participantCategorySearch', 
                    {'business_id':M.curBusinessID, 
//                  'exhibition_id':M.ciniki_exhibitions_participants.edit.exhibition_id, 
                    'start_needle':value, 'limit':25},
                    function(rsp) { 
                        M.ciniki_exhibitions_participants.edit.liveSearchShow(s, i, M.gE(M.ciniki_exhibitions_participants.edit.panelUID + '_' + i), rsp.categories); 
                    });
            }
        };
        this.edit.liveSearchResultValue = function(s, f, i, j, d) {
            if( f == 'first' || f == 'last' || f == 'company' ) { 
                if( d.contact.company == '' ) {
                    return d.contact.first + ' ' + d.contact.last;
                } else if( d.contact.first == '' && d.contact.last == '' ) {
                    return d.contact.company;
                } else {
                    return d.contact.first + ' ' + d.contact.last + ' (' + d.contact.company + ')';
                }
            }
            if( f == 'category' ) {
                return d.category.name;
            }
            return '';
        };
        this.edit.liveSearchResultRowFn = function(s, f, i, j, d) { 
            if( f == 'first' || f == 'last' || f == 'company' ) {
                return 'M.ciniki_exhibitions_participants.edit.updateParticipant(\'' + s + '\',\'' + d.contact.id + '\',M.ciniki_exhibitions_participants.edit.exhibition_id);';
            }
            if( f == 'category' ) {
                return 'M.ciniki_exhibitions_participants.edit.updateField(\'' + s + '\',\'category\',\'' + escape(d.category.name) + '\');';
            }
        };
        this.edit.updateParticipant = function(s, cid, eid) {
            M.ciniki_exhibitions_participants.showEdit(null, eid, 0, cid, null);
        };
        this.edit.updateField = function(s, fid, result) {
            M.gE(this.panelUID + '_' + fid).value = unescape(result);
            this.removeLiveSearch(s, fid);
        };
        this.edit.fieldHistoryArgs = function(s, i) {
            if( s == 'organization' ) {
                return {'method':'ciniki.exhibitions.participantHistory', 'args':{'business_id':M.curBusinessID, 
                    'participant_id':this.participant_id, 'field':i}};
            } else {
                return {'method':'ciniki.exhibitions.contactHistory', 'args':{'business_id':M.curBusinessID, 
                    'contact_id':this.contact_id, 'field':i}};
            }
        };
        this.edit.addDropImage = function(iid) {
            M.ciniki_exhibitions_participants.edit.setFieldValue('primary_image_id', iid);
            return true;
        };
        this.edit.deleteImage = function(fid) {
            this.setFieldValue(fid, 0);
            return true;
        };
        this.edit.addButton('save', 'Save', 'M.ciniki_exhibitions_participants.saveContact();');
        this.edit.addClose('Cancel');

        //
        // The tour exhibitors tools
        //
        this.tourtools 
    }
    
    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) { args = eval(aG); }

        //
        // Create container
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_exhibitions_participants', 'yes');
        if( appContainer == null ) {
            alert('App Error');
            return false;
        }

        if( args.exhibition_id != null && args.exhibition_id > 0 ) {
            var rsp = M.api.getJSONCb('ciniki.exhibitions.exhibitionGet',
                {'business_id':M.curBusinessID, 'exhibition_id':args.exhibition_id}, function(rsp) {
                    if( rsp.stat == 'ok' ) {
                        var o = M.ciniki_exhibitions_participants;
                        o.sponsorLevels = [];
                        if( rsp.exhibition['sponsor-level-10-name'] != null && rsp.exhibition['sponsor-level-10-name'] != '' ) {
                            o.sponsorLevels[10] = rsp.exhibition['sponsor-level-10-name'];
                        }
                        if( rsp.exhibition['sponsor-level-20-name'] != null && rsp.exhibition['sponsor-level-20-name'] != '' ) {
                            o.sponsorLevels[20] = rsp.exhibition['sponsor-level-20-name'];
                        }
                        if( rsp.exhibition['sponsor-level-30-name'] != null && rsp.exhibition['sponsor-level-30-name'] != '' ) {
                            o.sponsorLevels[30] = rsp.exhibition['sponsor-level-30-name'];
                        }
                        if( rsp.exhibition['sponsor-level-40-name'] != null && rsp.exhibition['sponsor-level-40-name'] != '' ) {
                            o.sponsorLevels[40] = rsp.exhibition['sponsor-level-40-name'];
                        }
                        if( rsp.exhibition['sponsor-level-50-name'] != null && rsp.exhibition['sponsor-level-50-name'] != '' ) {
                            o.sponsorLevels[50] = rsp.exhibition['sponsor-level-50-name'];
                        }
                    }
                    o.startFinish(cb, args);
                });
        } else {
            this.startFinish(cb, args);
        }
    }

    this.startFinish = function(cb, args) {
        if( args.exhibition_id != null && args.participant_id != null 
            && args.edit != null && args.edit == 'yes' && args.type != null ) {
            this.showEdit(cb, args.exhibition_id, args.participant_id, null, args.type);
        } else if( args.exhibition_id != null && args.participant_id != null 
            && args.type != null ) {
            this.showParticipant(cb, args.exhibition_id, args.participant_id, args.type);
        } else if( args.exhibition_id != null 
            && args.exhibitors != null && args.exhibitors == 'yes' ) {
            this.showExhibitors(cb, args.exhibition_id);
        } else if( args.exhibition_id != null 
            && args.sponsors != null && args.sponsors == 'yes' ) {
            this.showSponsors(cb, args.exhibition_id);
        } else if( args.exhibition_id != null 
            && args.tour != null && args.tour == 'yes' ) {
            this.showTourExhibitors(cb, args.exhibition_id);
        } else if( args.exhibition_id != null 
            && args.contacts != null && args.contacts == 'yes' ) {
            this.showContacts(cb, args.exhibition_id);
        } else {
            alert('Sorry, no exhibition specified');
            return false;
        }
    }

    this.showExhibitors = function(cb, eid) {
        if( eid != null ) {
            this.exhibitors.exhibition_id = eid;
        }
        // Get the list of existing exhibitions
        var rsp = M.api.getJSONCb('ciniki.exhibitions.participantList', 
            {'business_id':M.curBusinessID, 'exhibition_id':this.exhibitors.exhibition_id, 
                'categorized':'yes', 'type':0x10}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_exhibitions_participants.exhibitors;
                    p.data = rsp.participants;
                    p.refresh();
                    p.show(cb);
                }); 
    };

    this.showSponsors = function(cb, eid) {
        if( eid != null ) {
            this.sponsors.exhibition_id = eid;
        }
        // Get the list of existing exhibitions
        var rsp = M.api.getJSONCb('ciniki.exhibitions.participantList', 
            {'business_id':M.curBusinessID, 'exhibition_id':this.sponsors.exhibition_id, 
                'categorized':'yes', 'type':0x20}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_exhibitions_participants.sponsors;
                    p.data = rsp.participants;
                    p.refresh();
                    p.show(cb);
                }); 
    };

    this.showTourExhibitors = function(cb, eid) {
        if( eid != null ) {
            this.tourexhibitors.exhibition_id = eid;
        }
        // Get the list of existing exhibitions
        var rsp = M.api.getJSONCb('ciniki.exhibitions.participantList', 
            {'business_id':M.curBusinessID, 'exhibition_id':this.tourexhibitors.exhibition_id, 
                'categorized':'yes', 'type':0x40}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_exhibitions_participants.tourexhibitors;
                    p.data = rsp.participants;
                    p.refresh();
                    p.show(cb);
                }); 
    };

    this.showContacts = function(cb, eid) {
        if( eid != null ) {
            this.contacts.exhibition_id = eid;
        }
        // Get the list of existing exhibitions
        var rsp = M.api.getJSONCb('ciniki.exhibitions.participantList', 
            {'business_id':M.curBusinessID, 'exhibition_id':this.contacts.exhibition_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_exhibitions_participants.tourexhibitors;
                p.data = rsp.participants;
                p.refresh();
                p.show(cb);
            }); 
    };

    //
    // The edit form takes care of editing existing, or add new.
    // It can also be used to add the same person to an exhibition
    // as an exhibitor and sponsor and organizer, etc.
    //
    this.showEdit = function(cb, eid, pid, cid, type) {
        if( eid != null ) {
            this.edit.exhibition_id = eid;
        }
        if( pid != null ) {
            this.edit.participant_id = pid;
        }
        if( cid != null ) {
            this.edit.contact_id = cid;
        }
        if( this.edit.participant_id > 0 ) {
            this.edit.sections._buttons.buttons.delete.visible = 'yes';
            var rsp = M.api.getJSONCb('ciniki.exhibitions.participantGet',
                {'business_id':M.curBusinessID, 'exhibition_id':this.edit.exhibition_id,
                'participant_id':this.edit.participant_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_exhibitions_participants.edit;
                    p.data = rsp.participant;
                    p.contact_id = rsp.participant.contact_id;
                    M.ciniki_exhibitions_participants.showEditFinish(cb, 'Edit', type);
                });
        } else if( this.edit.contact_id > 0 ) {
            this.edit.sections._buttons.buttons.delete.visible = 'no';
            var rsp = M.api.getJSONCb('ciniki.exhibitions.contactGet',
                {'business_id':M.curBusinessID, 'exhibition_id':this.edit.exhibition_id,
                'contact_id':this.edit.contact_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_exhibitions_participants.edit;
                    p.data = rsp.contact;
                    p.participant_id = rsp.contact.participant_id;
                    M.ciniki_exhibitions_participants.showEditFinish(cb, 'Add', type);
                });
        } else {
            this.edit.sections._buttons.buttons.delete.visible = 'no';
            this.edit.data = {};
            M.ciniki_exhibitions_participants.showEditFinish(cb, 'Add', type);
        }
    };

    this.showEditFinish = function(cb, formname, type) {
        if( this.edit.data.type == null ) {
            this.edit.data.type = 0;
        }
        if( type == 'organizer' ) {
            this.edit.title = formname + ' Organizer';
            this.edit.participant_type = this.edit.data.type | 0x1;
            this.edit.sections.organization.visible = 'yes';
            this.edit.sections._description.visible = 'yes';
            this.edit.sections.address.visible = 'no';
            this.edit.sections._address_buttons.visible = 'no';
            this.edit.sections.mailing_address.visible = 'yes';
            this.edit.sections.organization.fields.category.active = 'no';
            this.edit.sections.organization.fields.status.active = 'no';
            this.edit.sections.organization.fields.webflags.active = 'yes';
            this.edit.sections.organization.fields.level.active = 'no';
            this.edit.sections.organization.fields.title.active = 'yes';
            this.edit.sections.organization.fields.location.active = 'no';
            this.edit.sections._buttons.buttons.save.label = 'Save Organizer';
            this.edit.sections._buttons.buttons.delete.label = 'Remove Organizer';
        } else if( type == 'volunteer' ) {
            this.edit.title = formname + ' Volunteer';
            this.edit.participant_type = this.edit.data.type | 0x2;
            this.edit.sections.address.visible = 'no';
            this.edit.sections._address_buttons.visible = 'no';
            this.edit.sections.mailing_address.visible = 'yes';
            this.edit.sections.organization.visible = 'no';
            this.edit.sections._description.visible = 'no';
            this.edit.sections._buttons.buttons.save.label = 'Save Volunteer';
            this.edit.sections._buttons.buttons.delete.label = 'Remove Volunteer';
        } else if( type == 'exhibitor' ) {
            this.edit.title = formname + ' Exhibitor';
            this.edit.participant_type = this.edit.data.type | 0x10;
            this.edit.sections.organization.visible = 'yes';
            this.edit.sections._description.visible = 'yes';
            this.edit.sections.address.visible = 'no';
            this.edit.sections._address_buttons.visible = 'no';
            this.edit.sections.mailing_address.visible = 'yes';
            this.edit.sections.organization.fields.sequence.active = 'no';
            this.edit.sections.organization.fields.category.active = 'yes';
            this.edit.sections.organization.fields.status.active = 'yes';
            this.edit.sections.organization.fields.webflags.active = 'yes';
            this.edit.sections.organization.fields.level.active = 'no';
            this.edit.sections.organization.fields.title.active = 'no';
            this.edit.sections.organization.fields.location.active = 'yes';
            this.edit.sections._buttons.buttons.save.label = 'Save Exhibitor';
            this.edit.sections._buttons.buttons.delete.label = 'Remove Exhibitor';
        } else if( type == 'sponsor' ) {
            this.edit.title = formname + ' Sponsor';
            this.edit.participant_type = this.edit.data.type | 0x20;
            this.edit.sections.organization.visible = 'yes';
            this.edit.sections._description.visible = 'no';
            this.edit.sections.address.visible = 'no';
            this.edit.sections._address_buttons.visible = 'no';
            this.edit.sections.mailing_address.visible = 'yes';
            this.edit.sections.organization.fields.sequence.active = 'yes';
            this.edit.sections.organization.fields.category.active = 'yes';
            this.edit.sections.organization.fields.status.active = 'no';
            this.edit.sections.organization.fields.webflags.active = 'yes';
            this.edit.sections.organization.fields.title.active = 'no';
            this.edit.sections.organization.fields.location.active = 'yes';
            this.edit.sections._buttons.buttons.save.label = 'Save Sponsor';
            this.edit.sections._buttons.buttons.delete.label = 'Remove Sponsor';
            if( this.sponsorLevels.length > 0 ) {
                this.edit.sections.organization.fields.level.active = 'yes';
                this.edit.sections.organization.fields.level.toggles = this.sponsorLevels;
            } else {
                this.edit.sections.organization.fields.level.active = 'no';
            }
        } else if( type == 'tourexhibitor' ) {
            this.edit.title = formname + ' Exhibitor';
            this.edit.participant_type = this.edit.data.type | 0x40;
            this.edit.sections.organization.visible = 'yes';
            this.edit.sections._description.visible = 'yes';
            this.edit.sections.address.visible = 'yes';
            this.edit.sections._address_buttons.visible = 'yes';
            this.edit.sections.mailing_address.visible = 'yes';
            this.edit.sections.organization.fields.sequence.active = 'no';
            this.edit.sections.organization.fields.category.active = 'yes';
            this.edit.sections.organization.fields.status.active = 'yes';
            this.edit.sections.organization.fields.webflags.active = 'yes';
            this.edit.sections.organization.fields.level.active = 'no';
            this.edit.sections.organization.fields.title.active = 'no';
            this.edit.sections.organization.fields.location.active = 'no';
            this.edit.sections._buttons.buttons.save.label = 'Save Exhibitor';
            this.edit.sections._buttons.buttons.delete.label = 'Remove Exhibitor';
        } else if( type == 'contact' ) {
            this.edit.title = formname + ' Contact';
//          this.edit.participant_type = this.edit.data.type & 0;
            this.edit.sections.organization.visible = 'no';
            this.edit.sections._description.visible = 'no';
            this.edit.sections._buttons.buttons.save.label = 'Save Contact';
            this.edit.sections._buttons.buttons.delete.label = 'Remove Contact';
        }

        if( this.edit.data.description != null && this.edit.data.description != '' ) {
            this.edit.sections._description.visible = 'yes';
        }

        this.edit.refresh();
        this.edit.show(cb);
    };

    this.showParticipant = function(cb, eid, pid, type) {
        if( eid != null ) {
            this.participant.exhibition_id = eid;
        }
        if( pid != null ) {
            this.participant.participant_id = pid;
        }
        if( type != null ) {
            this.participant.participant_type = type;
        }
        var rsp = M.api.getJSONCb('ciniki.exhibitions.participantGet',
            {'business_id':M.curBusinessID, 'exhibition_id':this.participant.exhibition_id,
            'participant_id':this.participant.participant_id, 'images':'yes'}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_exhibitions_participants.participant;
                p.data = rsp.participant;
                p.contact_id = rsp.participant.contact_id;

                if( rsp.participant.notes != null && rsp.participant.notes != '' ) {
                    p.sections.notes.visible = 'yes';
                } else {
                    p.sections.notes.visible = 'no';
                }
                if( rsp.participant.description != null && rsp.participant.description != '' ) {
                    p.sections.description.visible = 'yes';
                } else {
                    p.sections.description.visible = 'no';
                }
                if( rsp.participant.short_description != null && rsp.participant.short_description != '' ) {
                    p.sections.short_description.visible = 'yes';
                } else {
                    p.sections.short_description.visible = 'no';
                }
                var fields = ['company','email','phone_home','phone_work','phone_cell','phone_fax','url'];
                for(i in fields) {
                    if( rsp.participant[fields[i]] != null && rsp.participant[fields[i]] != '' ) {
                        p.sections.info.list[fields[i]].visible = 'yes';
                    } else {
                        p.sections.info.list[fields[i]].visible = 'no';
                    }
                }
                p.sections.info.list.name.label = 'Name';
                if( type == 'organizer' ) {
                    p.sections.address.visible = 'no';
                    p.sections.organizer.visible = 'yes';
                    p.sections.exhibitor.visible = 'no';
                    p.sections.sponsor.visible = 'no';
                    p.sections.tourexhibitor.visible = 'no';
                    p.sections.images.visible = 'no';
                    p.sections._images.visible = 'no';
                } else if( type == 'exhibitor' ) {
                    p.sections.address.visible = 'no';
                    p.sections.organizer.visible = 'no';
                    p.sections.exhibitor.visible = 'yes';
                    p.sections.sponsor.visible = 'no';
                    p.sections.tourexhibitor.visible = 'no';
                    p.sections.images.visible = 'yes';
                    p.sections._images.visible = 'yes';
                } else if( type == 'sponsor' ) {
                    p.sections.info.list.name.label = 'Contact';
                    p.sections.address.visible = 'no';
                    p.sections.organizer.visible = 'no';
                    p.sections.exhibitor.visible = 'no';
                    p.sections.sponsor.visible = 'yes';
                    if( M.ciniki_exhibitions_participants.sponsorLevels.length > 0 && M.ciniki_exhibitions_participants.sponsorLevels[rsp.participant.level] != null ) {
                        p.sections.sponsor.list.level.visible = 'yes';
                    } else {
                        p.sections.sponsor.list.level.visible = 'no';
                    }
                    p.sections.tourexhibitor.visible = 'no';
                    p.sections.images.visible = 'no';
                    p.sections._images.visible = 'no';
                } else if( type == 'tourexhibitor' ) {
                    p.sections.address.visible = 'yes';
                    p.sections.organizer.visible = 'no';
                    p.sections.exhibitor.visible = 'no';
                    p.sections.sponsor.visible = 'no';
                    p.sections.tourexhibitor.visible = 'yes';
                    p.sections.images.visible = 'yes';
                    p.sections._images.visible = 'yes';
                }

                p.refresh();
                p.show(cb);
            });
    };

    this.saveContact = function() {
        //
        // Depending on if there was a contact loaded, or participant
        // loaded for editing, determine what should be sent back
        // to the server
        //
        if( this.edit.contact_id > 0 ) {
            // Update contact
            var c = this.edit.serializeFormSection('no', '_image')
                + this.edit.serializeFormSection('no', 'name')
                + this.edit.serializeFormSection('no', '_short_description')
                + this.edit.serializeFormSection('no', '_notes')
                + this.edit.serializeFormSection('no', 'contact');
            if( this.edit.sections._description.visible == 'yes' ) {
                c += this.edit.serializeFormSection('no', '_description');
            }
            if( this.edit.sections.address.visible == 'yes' ) {
                c += this.edit.serializeFormSection('no', 'address');
            }
            if( this.edit.sections.mailing_address.visible == 'yes' ) {
                c += this.edit.serializeFormSection('no', 'mailing_address');
            }
            if( c != '' ) {
                var rsp = M.api.postJSONCb('ciniki.exhibitions.contactUpdate', 
                    {'business_id':M.curBusinessID, 'contact_id':this.edit.contact_id}, c, function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } 
                        M.ciniki_exhibitions_participants.saveParticipant();
                    });
            } else {
                this.saveParticipant();
            }
        } else {
            // Add contact
            var c = this.edit.serializeFormSection('yes', '_image')
                + this.edit.serializeFormSection('yes', 'name')
                + this.edit.serializeFormSection('yes', '_short_description')
                + this.edit.serializeFormSection('yes', '_notes')
                + this.edit.serializeFormSection('yes', 'contact');
            if( this.edit.sections._description.visible == 'yes' ) {
                c += this.edit.serializeFormSection('yes', '_description');
            }
            if( this.edit.sections.address.visible == 'yes' ) {
                c += this.edit.serializeFormSection('yes', 'address');
            }
            if( this.edit.sections.mailing_address.visible == 'yes' ) {
                c += this.edit.serializeFormSection('yes', 'mailing_address');
            }
            var rsp = M.api.postJSONCb('ciniki.exhibitions.contactAdd', 
                {'business_id':M.curBusinessID}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_exhibitions_participants.edit.contact_id = rsp.id;
                    M.ciniki_exhibitions_participants.saveParticipant();
                });
        }
    };

    this.saveParticipant = function() {
        if( this.edit.participant_id > 0 ) {
            // Update participant details
            var c = this.edit.serializeFormSection('no', 'organization');
            if( this.edit.contact_id != this.edit.data.contact_id ) {
                c += '&contact_id=' + encodeURIComponent(this.edit.contact_id);
            }
            if( this.edit.participant_type != this.edit.data.type ) {
                c += '&type=' + encodeURIComponent(this.edit.participant_type);
            }
            if( c != '' ) {
                var rsp = M.api.postJSONCb('ciniki.exhibitions.participantUpdate', 
                    {'business_id':M.curBusinessID, 
                    'exhibition_id':this.edit.exhibition_id,
                    'participant_id':this.edit.participant_id}, c, function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } 
                        M.ciniki_exhibitions_participants.edit.close();
                    });
            } else {
                this.edit.close();
            }
        } else {
            // Add participant
            var c = this.edit.serializeFormSection('yes', 'organization');
            c += '&type=' + encodeURIComponent(this.edit.participant_type);
            c += '&contact_id=' + encodeURIComponent(this.edit.contact_id);
            var rsp = M.api.postJSONCb('ciniki.exhibitions.participantAdd', 
                {'business_id':M.curBusinessID,
                'exhibition_id':this.edit.exhibition_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_exhibitions_participants.edit.participant_id = rsp.id;
                    M.ciniki_exhibitions_participants.edit.close();
                });
        }
    };

    this.deleteParticipant = function() {
        if( confirm('Are you sure you want to ' + this.edit.sections._buttons.buttons.delete.label.toLowerCase() + '?') ) {
            var rsp = M.api.getJSONCb('ciniki.exhibitions.participantDelete', {'business_id':M.curBusinessID, 
                'participant_id':this.edit.participant_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_exhibitions_participants.participant.close();
                    M.ciniki_exhibitions_participants.edit.reset();
                });
        }
    };

    this.lookupLatLong = function() {
        M.startLoad();
        if( document.getElementById('googlemaps_js') == null) {
            var script = document.createElement("script");
            script.id = 'googlemaps_js';
            script.type = "text/javascript";
            script.src = "https://maps.googleapis.com/maps/api/js?key=" + M.curBusiness.settings['googlemapsapikey'] + "&sensor=false&callback=M.ciniki_exhibitions_participants.lookupGoogleLatLong";
            document.body.appendChild(script);
        } else {
            this.lookupGoogleLatLong();
        }
    };

    this.lookupGoogleLatLong = function() {
        var address = M.ciniki_exhibitions_participants.edit.formFieldValue(M.ciniki_exhibitions_participants.edit.sections.address.fields.address1, 'address1')
            + ', ' + M.ciniki_exhibitions_participants.edit.formFieldValue(M.ciniki_exhibitions_participants.edit.sections.address.fields.address2, 'address2')
            + ', ' + M.ciniki_exhibitions_participants.edit.formFieldValue(M.ciniki_exhibitions_participants.edit.sections.address.fields.city, 'city')
            + ', ' + M.ciniki_exhibitions_participants.edit.formFieldValue(M.ciniki_exhibitions_participants.edit.sections.address.fields.province, 'province')
            + ', ' + M.ciniki_exhibitions_participants.edit.formFieldValue(M.ciniki_exhibitions_participants.edit.sections.address.fields.postal, 'postal');
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                M.ciniki_exhibitions_participants.edit.setFieldValue('latitude', results[0].geometry.location.lat());
                M.ciniki_exhibitions_participants.edit.setFieldValue('longitude', results[0].geometry.location.lng());
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        }); 
        M.stopLoad();
    };
}
