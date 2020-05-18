//
// The app to add/edit exhibition contact images
//
function ciniki_exhibitions_contactimages() {
    this.webFlags = {
        '1':{'name':'Hidden'},
        };
    this.init = function() {
        //
        // The panel to display the edit form
        //
        this.edit = new M.panel('Edit Image',
            'ciniki_exhibitions_contactimages', 'edit',
            'mc', 'medium', 'sectioned', 'ciniki.exhibitions.contactimages.edit');
        this.edit.default_data = {};
        this.edit.data = {};
        this.edit.contact_id = 0;
        this.edit.sections = {
            '_image':{'label':'Photo', 'type':'imageform', 'fields':{
                'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
            }},
            'info':{'label':'Information', 'type':'simpleform', 'fields':{
                'name':{'label':'Title', 'type':'text'},
                'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
//              'url':{'label':'Link', 'type':'text'},
            }},
            '_description':{'label':'Description', 'type':'simpleform', 'fields':{
                'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
            }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_exhibitions_contactimages.saveImage();'},
                'delete':{'label':'Delete', 'fn':'M.ciniki_exhibitions_contactimages.deleteImage();'},
            }},
        };
        this.edit.fieldValue = function(s, i, d) { 
            if( this.data[i] != null ) {
                return this.data[i]; 
            } 
            return ''; 
        };
        this.edit.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.exhibitions.contactImageHistory', 'args':{'tnid':M.curTenantID, 
                'contact_image_id':this.contact_image_id, 'field':i}};
        };
        this.edit.addDropImage = function(iid) {
            M.ciniki_exhibitions_contactimages.edit.setFieldValue('image_id', iid);
            return true;
        }
        this.edit.addButton('save', 'Save', 'M.ciniki_exhibitions_contactimages.saveImage();');
        this.edit.addClose('Cancel');
    };

    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) {
            args = eval(aG);
        }

        //
        // Create container
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_exhibitions_contactimages', 'yes');
        if( appContainer == null ) {
            M.alert('App Error');
            return false;
        }

        if( args.add != null && args.add == 'yes' ) {
            this.showEdit(cb, 0, args.contact_id);
        } else if( args.contact_image_id != null && args.contact_image_id > 0 ) {
            this.showEdit(cb, args.contact_image_id, 0);
        }
        return false;
    }

    this.showEdit = function(cb, iid, cid) {
        if( iid != null ) {
            this.edit.contact_image_id = iid;
        }
        if( cid != null ) {
            this.edit.contact_id = cid;
        }
        if( this.edit.contact_image_id > 0 ) {
            this.edit.sections._buttons.buttons.delete.visible = 'yes';
            var rsp = M.api.getJSONCb('ciniki.exhibitions.contactImageGet', 
                {'tnid':M.curTenantID, 'contact_image_id':this.edit.contact_image_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_exhibitions_contactimages.edit;
                    p.data = rsp.image;
                    p.refresh();
                    p.show(cb);
                });
        } else {
            this.edit.reset();
            this.edit.data = {};
            this.edit.sections._buttons.buttons.delete.visible = 'no';
            this.edit.refresh();
            this.edit.show(cb);
        }
    };

    this.saveImage = function() {
        if( this.edit.contact_image_id > 0 ) {
            var c = this.edit.serializeFormData('no');
            if( c != '' ) {
                var rsp = M.api.postJSONFormData('ciniki.exhibitions.contactImageUpdate', 
                    {'tnid':M.curTenantID, 
                    'contact_image_id':this.edit.contact_image_id}, c,
                        function(rsp) {
                            if( rsp.stat != 'ok' ) {
                                M.api.err(rsp);
                                return false;
                            } else {
                                M.ciniki_exhibitions_contactimages.edit.close();
                            }
                        });
            } else {
                M.ciniki_exhibitions_contactimages.edit.close();
            }
        } else {
            var c = this.edit.serializeForm('yes');
            c += '&contact_id=' + encodeURIComponent(this.edit.contact_id);
            var rsp = M.api.postJSONFormData('ciniki.exhibitions.contactImageAdd', {'tnid':M.curTenantID}, c,
                function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } else {
                        M.ciniki_exhibitions_contactimages.edit.close();
                    }
                });
        }
    };

    this.deleteImage = function() {
        M.confirm('Are you sure you want to delete this image?',null,function() {
            var rsp = M.api.getJSONCb('ciniki.exhibitions.contactImageDelete', {'tnid':M.curTenantID, 
                'contact_image_id':M.ciniki_exhibitions_contactimages.edit.contact_image_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_exhibitions_contactimages.edit.close();
                });
        });
    };
}
