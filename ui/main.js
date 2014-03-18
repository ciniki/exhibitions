//
// The exhibitions app
//
function ciniki_exhibitions_main() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		};
	this.init = function() {
		//
		// Setup the main panel to list the collection
		//
		this.menu = new M.panel('Exhibitions',
			'ciniki_exhibitions_main', 'menu',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.main.menu');
		this.menu.data = {};
		this.menu.sections = {
			'_':{'label':'', 'type':'simplegrid', 'num_cols':1,
				'headerValues':null,
				'cellClasses':['multiline'],
				'noData':'No exhibitions',
				'addTxt':'Add Exhibition',
				'addFn':'M.ciniki_exhibitions_main.showExhibitionEdit(\'M.ciniki_exhibitions_main.showMenu();\',0);',
				},
			};
		this.menu.sectionData = function(s) { return this.data; }
		this.menu.cellValue = function(s, i, j, d) {
			return '<span class="maintext">' + d.exhibition.name + '</span>'
				+ '<span class="subtext">' + d.exhibition.start_date + ' - ' + d.exhibition.end_date + '</span>';
		};
		this.menu.rowFn = function(s, i, d) { 
			return 'M.ciniki_exhibitions_main.showExhibition(\'M.ciniki_exhibitions_main.showMenu();\',\'' + d.exhibition.id + '\');'; 
		};
		this.menu.addButton('add', 'Add', 'M.ciniki_exhibitions_main.showExhibitionEdit(\'M.ciniki_exhibitions_main.showMenu();\',0');
		this.menu.addClose('Back');

		//
		// The exhibition information/menu panel
		//
		this.exhibition = new M.panel('Exhibition',
			'ciniki_exhibitions_main', 'exhibition',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.main.exhibition');
		this.exhibition.exhibition_id = 0;
		this.exhibition.sections = {
			'menu':{'label':'', 'type':'simplelist'},
			'info':{'label':'', 'list':{
				'name':{'label':'Name'},
				'tagline':{'label':'Tagline'},
				'start_date':{'label':'Start'},
				'end_date':{'label':'End'},
			}},
			'_description':{'label':'Description', 'type':'simpleform', 'fields':{
				'description':{'label':'', 'type':'noedit', 'hidelabel':'yes'},
			}},
			'_buttons':{'label':'', 'buttons':{
				'edit':{'label':'Edit', 'fn':'M.ciniki_exhibitions_main.showExhibitionEdit(\'M.ciniki_exhibitions_main.showExhibition();\',M.ciniki_exhibitions_main.exhibition.exhibition_id);'},
			}},
		};
		this.exhibition.listLabel = function(s, i, d) {
			switch (s) {
				case 'menu': return '';
				case 'info': return d.label;
			}
		};
		this.exhibition.listValue = function(s, i, d) {
			if( s == 'menu' ) { return d.label; }
			return this.data[s][i];
		};
		this.exhibition.fieldValue = function(s, i, d) {
			return this.data['info'][i];
		};
		this.exhibition.listFn = function(s, i, d) {
			if( s == 'menu' ) {
				return this.data[s][i].fn;
			}
			return '';
		};
		this.exhibition.sectionData = function(s) {
			if( s == 'description' ) { return {s:this.data[s]} }
			if( s == 'info' ) { return this.sections[s].list; }
			return this.data[s];
		};
		this.exhibition.addButton('edit', 'Edit', 'M.ciniki_exhibitions_main.showExhibitionEdit(\'M.ciniki_exhibitions_main.showExhibition();\',M.ciniki_exhibitions_main.exhibition.exhibition_id);');
		this.exhibition.addClose('Back');

		//
		// The exhibition edit panel
		//
		this.edit = new M.panel('Exhibition',
			'ciniki_exhibitions_main', 'edit',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.main.edit');
		this.edit.data = {};
		this.edit.sections = {
			'info':{'label':'', 'fields':{
				'name':{'label':'Name', 'type':'text'},
				'tagline':{'label':'Tagline', 'type':'text'},
				'start_date':{'label':'Start', 'type':'date'},
				'end_date':{'label':'End', 'type':'date'},
				}},
			'_description':{'label':'Description', 'fields':{
				'description':{'label':'', 'hidelabel':'yes', 'type':'textarea'},
				}},
//			'_location':{'label':'Location':, 'fields':{
//				'location-address':{'label':'Location', 'type':'text'},
//				}},
			'options':{'label':'Options', 'fields':{
//				'use-gallery':{'label':'Gallery', 'type':'toggle', 'toggles':{'no':'No', 'yes':'Yes'}},
				'use-exhibitors':{'label':'Exhibitors', 'type':'toggle', 'toggles':{'no':'No', 'yes':'Yes'}},
				'use-tour':{'label':'Tour', 'type':'toggle', 'toggles':{'no':'No', 'yes':'Yes'}},
				'use-sponsors':{'label':'Sponsors', 'type':'toggle', 'toggles':{'no':'No', 'yes':'Yes'}},
				'exhibitor-label-singular':{'label':'Exhibitor Label', 'type':'text'},
				'exhibitor-label-plural':{'label':'Exhibitor Label Plural', 'type':'text'},
				}},
			'sponsoroptions':{'label':'Sponsor Level Names', 'fields':{
				'sponsor-level-50-name':{'label':'Platnium', 'type':'text'},
				'sponsor-level-40-name':{'label':'Gold', 'type':'text'},
				'sponsor-level-30-name':{'label':'Silver', 'type':'text'},
				'sponsor-level-20-name':{'label':'Bronze', 'type':'text'},
				'sponsor-level-10-name':{'label':'Other', 'type':'text'},
				}},
			'_buttons':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_exhibitions_main.saveExhibition();'},
				}},
			};
		this.edit.fieldValue = function(s, i, d) {
			if( this.data[i] != null ) { return this.data[i]; }
			return '';
		};
		this.edit.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.exhibitions.exhibitionHistory', 'args':{'business_id':M.curBusinessID, 
				'exhibition_id':this.exhibition_id, 'field':i}};
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_exhibitions_main.saveExhibition();');
		this.edit.addClose('Cancel');
	}

	this.start = function(cb, appPrefix, aG) {
		args = {};
		if( aG != null ) {
			args = eval(aG);
		}

		//
		// Create container
		//
		var appContainer = M.createContainer(appPrefix, 'ciniki_exhibitions_main', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		}
	
		this.showMenu(cb);
	}

	this.showMenu = function(cb) {
		// Get the list of existing exhibitions
		var rsp = M.api.getJSONCb('ciniki.exhibitions.exhibitionList', 
			{'business_id':M.curBusinessID}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				var p = M.ciniki_exhibitions_main.menu;
				p.data = rsp.exhibitions;
				p.refresh();
				p.show(cb);
			});	
	};

	this.showExhibition = function(cb, eid) {
		if( eid != null ) {
			this.exhibition.exhibition_id = eid;
		}
		var rsp = M.api.getJSONCb('ciniki.exhibitions.exhibitionGet', 
			{'business_id':M.curBusinessID, 'exhibition_id':this.exhibition.exhibition_id}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				var p = M.ciniki_exhibitions_main.exhibition;
				p.data = {'menu':{
		//			'gallery':{'label':'Gallery', 'fn':'M.startApp(\'ciniki.exhibitions.images\',null,\'M.ciniki_exhibitions_main.showExhibition();\',\'mc\',{\'exhibition_id\':M.ciniki_exhibitions_main.exhibition.exhibition_id});'},
					}};
				if( rsp.exhibition['use-exhibitors'] != null && rsp.exhibition['use-exhibitors'] == 'yes') {
					p.data.menu.exhibitors = {'label':'Exhibitors',
						'fn':'M.startApp(\'ciniki.exhibitions.participants\',null,\'M.ciniki_exhibitions_main.showExhibition();\',\'mc\',{\'exhibition_id\':M.ciniki_exhibitions_main.exhibition.exhibition_id,\'exhibitors\':\'yes\'});'};
				}
				if( rsp.exhibition['use-tour'] != null && rsp.exhibition['use-tour'] == 'yes' ) {
					p.data.menu.tour = {'label':'Tour Exhibitors',
						'fn':'M.startApp(\'ciniki.exhibitions.participants\',null,\'M.ciniki_exhibitions_main.showExhibition();\',\'mc\',{\'exhibition_id\':M.ciniki_exhibitions_main.exhibition.exhibition_id,\'tour\':\'yes\'});'};
				}
				if( rsp.exhibition['use-sponsors'] != null && rsp.exhibition['use-sponsors'] == 'yes' ) {
					p.data.menu.sponsor = {'label':'Sponsors',
						'fn':'M.startApp(\'ciniki.exhibitions.participants\',null,\'M.ciniki_exhibitions_main.showExhibition();\',\'mc\',{\'exhibition_id\':M.ciniki_exhibitions_main.exhibition.exhibition_id,\'sponsors\':\'yes\'});'};
				}
		//		p.data.menu['contacts'] = {'label':'Contacts',
		//			'fn':'M.startApp(\'ciniki.exhibitions.participants\',null,\'M.ciniki_exhibitions_main.showExhibition();\',\'mc\',{\'exhibition_id\':M.ciniki_exhibitions_main.exhibition.exhibition_id,\'contacts\':\'yes\'});'};
				p.data.info = rsp.exhibition;
				p.refresh();
				p.show(cb);
			});
	};

	this.showExhibitionEdit = function(cb, eid) {
		if( eid != null ) {
			this.edit.exhibition_id = eid;
		}
		if( this.edit.exhibition_id > 0 ) {
			var rsp = M.api.getJSONCb('ciniki.exhibitions.exhibitionGet', 
				{'business_id':M.curBusinessID, 'exhibition_id':this.edit.exhibition_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					var p = M.ciniki_exhibitions_main.edit;
					p.data = rsp.exhibition;
					//
					// Setup the defaults if they don't exist
					//
					if( rsp.exhibition['use-gallery'] == null ) {
						p.data['use-gallery'] = 'no';
					}
					if( rsp.exhibition['use-exhibitors'] == null ) {
						p.data['use-exhibitors'] = 'no';
					}
					if( rsp.exhibition['use-sponsors'] == null ) {
						p.data['use-sponsors'] = 'no';
					}
					if( rsp.exhibition['use-tour'] == null ) {
						p.data['use-tour'] = 'no';
					}
					p.refresh();
					p.show(cb);
				});
		} else {
			this.edit.reset();
			this.edit.data = {
				'use-gallery':'no',
				'use-exhibitors':'yes',
				'use-sponsors':'yes',
				'use-tour':'yes',
				'exhibitor-label-singular':'Artist',
				'exhibitor-label-plural':'Artists',
			};
			this.edit.refresh();
			this.edit.show(cb);
		}
	};

	this.saveExhibition = function() {
		if( this.edit.exhibition_id > 0 ) {
			var c = this.edit.serializeForm('no');
			if( c != '' ) {
				var rsp = M.api.postJSONCb('ciniki.exhibitions.exhibitionUpdate', 
					{'business_id':M.curBusinessID, 'exhibition_id':this.edit.exhibition_id}, c, function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						}
						M.ciniki_exhibitions_main.edit.close();
					});
			} else {
				this.edit.close();
			}
		} else {
			var c = this.edit.serializeForm('yes');
			var rsp = M.api.postJSONCb('ciniki.exhibitions.exhibitionAdd', 
				{'business_id':M.curBusinessID}, c, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_exhibitions_main.edit.close();
				});
		}
	};
}
