//
// The app to add/edit exhibition images
//
function ciniki_exhibitions_images() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		};
	this.init = function() {
		//
		// The panel to list the images by category
		//
		this.list = new M.panel('Exhibition Album',
			'ciniki_exhibitions_images', 'list',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.images.list');
		this.list.data = {};
		this.list.sections = {
			'images':{'label':'', 'type':'simplegrid', 'num_cols':2,
				'headerValues':null,
				'cellClasses':['thumbnail','multiline'],
				'noData':'No images found',
				'addTxt':'Add Image',
				'addFn':'M.ciniki_exhibitions_images.showAdd(\'M.ciniki_exhibitions_images.showList();\',M.ciniki_exhibitions_images.list.exhibition_id,escape(M.ciniki_exhibitions_images.list.category));',
				},
			};
		this.list.noData = function(s) {
			return this.sections[s].noData;
		};
		this.list.sectionData = function(s) {
			return this.data;
		};
		this.list.cellValue = function(s, i, j, d) {
			if( j == 0 ) {
				if( d.image.image_id > 0 ) {
					if( d.image.image_data != null && d.image.image_data != '' ) {
						return '<img width="75px" height="75px" src=\'' + d.image.image_data + '\' />'; 
					} else {
						return '<img width="75px" height="75px" src=\'' + M.api.getBinaryURL('ciniki.artcatalog.getImage', {'business_id':M.curBusinessID, 'image_id':d.image.image_id, 'version':'thumbnail', 'maxwidth':'75'}) + '\' />'; 
					}
				} else {
					return '<img width="75px" height="75px" src=\'/ciniki-manage-themes/default/img/noimage_75.jpg\' />';
				}
			}
			if( j == 1 ) {
				return '<span class="maintext">' + d.image.name + '</span><span class="subtext">' + d.image.description + '</span>'; 
			}
		};
		this.list.rowFn = function(s, i, d) {
			return 'M.ciniki_exhibitions_images.showEdit(\'M.ciniki_exhibitions_images.showList();\',\'' + d.image.id + '\');';
		};
		this.list.addButton('add', 'Add', 'M.ciniki_exhibitions_images.showAdd(\'M.ciniki_exhibitions_images.showList();\',M.ciniki_exhibitions_images.list.exhibition_id,escape(M.ciniki_exhibitions_images.list.category));');
		this.list.addClose('Back');

		//
		// The panel to display the list of categories/albums
		//
		this.categories = new M.panel('Exhibition Albums',
			'ciniki_exhibitions_images', 'categories',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.images.categories');
		this.categories.sections = {
			'categories':{'label':'', 'type':'simplelist'},
		};
		this.categories.sectionData = function(s) {
			return this.data;
		};
		this.categories.listValue = function(s, i, d) {
			return d.category.name;
		};
		this.categories.listCount = function(s, i, d) {
			return d.category.count;
		};
		this.categories.listFn = function(s, i, d) {
			return 'M.ciniki_exhibitions_images.showList(\'M.ciniki_exhibitions_images.showCategories();\',M.ciniki_exhibitions_images.categories.exhibition_id,\'' + escape(d.category.name) + '\');';
		};
		this.categories.addButton('add', 'Add', 'M.ciniki_exhibitions_images.showAdd(\'M.ciniki_exhibitions_images.showList();\',M.ciniki_exhibitions_images.categories.exhibition_id,\'\');');
		this.categories.addClose('Back');

		//
		// The panel to display the add form
		//
		this.add = new M.panel('Add Image',
			'ciniki_exhibitions_images', 'add',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.images.edit');
		this.add.default_data = {'image_id':0};
		this.add.data = {'image_id':0};
		this.add.rotateImage = this.rotateImage;
		this.add.uploadImage = function(i) { return 'M.ciniki_exhibitions_images.uploadDropImagesAdd(\'' + i + '\');' };
		this.add.uploadDropFn = function() { return M.ciniki_exhibitions_images.uploadDropImagesAdd; };
		this.add.sections = {
			'_image':{'label':'Photo', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes'},
			}},
			'info':{'label':'Information', 'type':'simpleform', 'fields':{
				'name':{'label':'Title', 'type':'text'},
				'category':{'label':'Album', 'type':'text', 'livesearch':'yes'},
				'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
			}},
			'_description':{'label':'Description', 'type':'simpleform', 'fields':{
				'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_exhibitions_images.addImage();'},
			}},
		};
		this.add.fieldValue = function(s, i, d) { 
			if( this.data != null && this.data[i] != null ) {
				return this.data[i]; 
			} 
			return ''; 
		};
		this.add.liveSearchCb = function(s, i, value) {
			if( i == 'category' ) {
				var rsp = M.api.getJSONBgCb('ciniki.exhibitions.imageCategorySearch', 
					{'business_id':M.curBusinessID, 
					'exhibition_id':M.ciniki_exhibitions_images.add.exhibition_id, 
					'start_needle':value, 'limit':25},
					function(rsp) { 
						M.ciniki_exhibitions_images.add.liveSearchShow(s, i, M.gE(M.ciniki_exhibitions_images.add.panelUID + '_' + i), rsp.categories); 
					});
			}
		};
		this.add.liveSearchResultValue = function(s, f, i, j, d) {
			if( f == 'category' ) { return d.category.name; }
			return '';
		};
		this.add.liveSearchResultRowFn = function(s, f, i, j, d) { 
			if( f == 'category' ) {
				return 'M.ciniki_exhibitions_images.add.updateField(\'' + s + '\',\'category\',\'' + escape(d.category.name) + '\');';
			}
		};
		this.add.updateField = function(s, fid, result) {
			M.gE(this.panelUID + '_' + fid).value = unescape(result);
			this.removeLiveSearch(s, fid);
		};
		this.add.addButton('save', 'Save', 'M.ciniki_exhibitions_images.addImage();');
		this.add.addClose('Cancel');

		//
		// The panel to display the edit form
		//
		this.edit = new M.panel('Edit Image',
			'ciniki_exhibitions_images', 'edit',
			'mc', 'medium', 'sectioned', 'ciniki.exhibitions.images.edit');
		this.edit.default_data = {};
		this.edit.data = {};
		this.edit.exhibition_id = 0;
		this.edit.rotateImage = this.rotateImage;
		this.edit.uploadImage = function(i) { return 'M.ciniki_exhibitions_images.uploadDropImagesEdit(\'' + i + '\');' };
		this.edit.uploadDropFn = function() { return M.ciniki_exhibitions_images.uploadDropImagesEdit; };
		this.edit.sections = {
			'_image':{'label':'Photo', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes'},
			}},
			'info':{'label':'Information', 'type':'simpleform', 'fields':{
				'name':{'label':'Title', 'type':'text'},
				'category':{'label':'Album', 'type':'text', 'livesearch':'yes'},
				'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
			}},
			'_description':{'label':'Description', 'type':'simpleform', 'fields':{
				'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_exhibitions_images.saveImage();'},
			}},
		};
		this.edit.fieldValue = function(s, i, d) { 
			if( this.data[i] != null ) {
				return this.data[i]; 
			} 
			return ''; 
		};
		this.edit.liveSearchCb = function(s, i, value) {
			if( i == 'category' ) {
				var rsp = M.api.getJSONBgCb('ciniki.exhibitions.imageCategorySearch', 
					{'business_id':M.curBusinessID, 
					'exhibition_id':M.ciniki_exhibitions_images.edit.exhibition_id, 
					'start_needle':value, 'limit':25},
					function(rsp) { 
						M.ciniki_exhibitions_images.edit.liveSearchShow(s, i, M.gE(M.ciniki_exhibitions_images.edit.panelUID + '_' + i), rsp.categories); 
					});
			}
		};
		this.edit.liveSearchResultValue = function(s, f, i, j, d) {
			if( f == 'category' ) { return d.category.name; }
			return '';
		};
		this.edit.liveSearchResultRowFn = function(s, f, i, j, d) { 
			if( f == 'category' ) {
				return 'M.ciniki_exhibitions_images.edit.updateField(\'' + s + '\',\'category\',\'' + escape(d.category.name) + '\');';
			}
		};
		this.edit.updateField = function(s, fid, result) {
			M.gE(this.panelUID + '_' + fid).value = unescape(result);
			this.removeLiveSearch(s, fid);
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_exhibitions_images.saveImage();');
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
		var appContainer = M.createContainer(appPrefix, 'ciniki_exhibitions_images', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		}
	
		if( args.add != null && args.add == 'yes' ) {
			this.showAdd(cb, args.exhibition_id, args.category);
		} else if( args.img_id != null && args.img_id > 0 ) {
			this.showEdit(cb, args.img_id);
		} else {
			this.list.album = null;
			this.showList(cb, args.exhibition_id, null);
		}
		return true;
	};

	this.showList = function(cb, eid, category) {
		if( category != null ) {
			this.list.category = category;
		} 
		if( this.list.category != null ) {
			if( eid != null ) { this.list.exhibition_id = eid; }
			var rsp = M.api.getJSON('ciniki.exhibitions.imageList', 
				{'business_id':M.curBusinessID, 'exhibition_id':this.list.exhibition_id,
					'category':this.list.category});
		} else {
			if( eid != null ) { this.categories.exhibition_id = eid; }
			var rsp = M.api.getJSON('ciniki.exhibitions.imageList', 
				{'business_id':M.curBusinessID, 'exhibition_id':this.categories.exhibition_id});
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
		}
		if( rsp.stat != 'ok' ) {
			M.api.err(rsp);
			return false;
		}
		if( rsp.images != null ) {
			if( eid != null ) { this.list.exhibition_id = eid; }
			this.list.category = rsp.category;
			this.list.data = rsp.images;
			this.list.sections.images.label = rsp.category;
			this.list.refresh();
			this.list.show(cb);
		} else if( rsp.categories != null ) {
			if( eid != null ) { this.categories.exhibition_id = eid; }
			this.categories.data = rsp.categories;
			this.categories.refresh();
			this.categories.show(cb);
		} else {
			alert('Sorry, no images found');
		}
	};

	this.showCategories = function() {
		this.list.category = null;
		this.showList();
	};

	this.showAdd = function(cb, eid, category) {
		this.add.reset();
		this.add.data = {};
		if( eid != null ) {
			this.add.exhibition_id = eid;
		}
		if( category != null ) {
			this.add.data['category'] = unescape(category);
		}
		this.add.refresh();
		this.add.show(cb);
	};

	this.addImage = function() {
		var c = this.add.serializeForm('yes');
		c += '&exhibition_id=' + encodeURIComponent(this.add.exhibition_id);
		if( c != null ) {
			var rsp = M.api.postJSONFormData('ciniki.exhibitions.imageAdd', {'business_id':M.curBusinessID}, c,
				function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					} else {
						M.ciniki_exhibitions_images.add.close();
					}
				});
		}
	};

	this.showEdit = function(cb, iid) {
		if( iid != null ) {
			this.edit.exhibition_image_id = iid;
		}
		var rsp = M.api.getJSON('ciniki.exhibitions.imageGet', 
			{'business_id':M.curBusinessID, 'exhibition_image_id':this.edit.exhibition_image_id});
		if( rsp.stat != 'ok' ) {
			M.api.err(rsp);
			return false;
		}
		this.edit.data = rsp.image;
		this.edit.refresh();
		this.edit.show(cb);
	};

	this.saveImage = function() {
		var c = this.edit.serializeFormData('no');
		if( c != '' ) {
			var rsp = M.api.postJSONFormData('ciniki.exhibitions.imageUpdate', 
				{'business_id':M.curBusinessID, 
				'exhibition_image_id':this.edit.exhibition_image_id}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} else {
							M.ciniki_exhibitions_images.edit.close();
						}
					});
		}
	};

	//
	// This function handles the submit when an image is dropped onto
	// the add media element
	//
	this.uploadDropImagesAdd = function(e) {
		M.ciniki_exhibitions_images.uploadDropImages(e, M.ciniki_exhibitions_images.add, '_image',
			function(rsp) { 
				if( rsp['stat'] == 'ok' ) {
					M.ciniki_exhibitions_images.add.setFieldValue('image_id', rsp['id'], null, null);
				} else {
					M.api.err(rsp);
					return false;
				}
			});
	}

	this.uploadDropImagesEdit = function(e) {
		M.ciniki_exhibitions_images.uploadDropImages(e, M.ciniki_exhibitions_images.edit, '_image', 
			function(rsp) { 
				if( rsp['stat'] == 'ok' ) {	
					M.ciniki_exhibitions_images.edit.setFieldValue('image_id', rsp['id'], null, null);
				} else {
					M.api.err(rsp);
					return false;
				}
			});
	}

	this.uploadDropImages = function(e, p, s, c) {
		M.startLoad();

		//
		// Check for external files dropped
		//
		var files = null;
		if( typeof(e) == 'string' ) {
			var f = M.gE(p.panelUID + '_' + e + '_upload');
			files = f.files;
		} else if( e.dataTransfer != null ) {
			files = e.dataTransfer.files;
		}
		
		if( files != null ) {
			for(i in files) {
				if( files[i].type == null ) { continue; }
				if( files[i].type != 'image/jpeg' 
					) {
					alert("I'm sorry, we only allow jpeg images to be uploaded.");
					M.stopLoad();
					return false;
				}
				var rsp = M.api.postJSONFile('ciniki.images.add', {'business_id':M.curBusinessID}, files[i], c);
				if( rsp == null ) {
					alert('Unknown error occured, please try again');
					M.stopLoad();
					return false;
				}
				if( rsp['stat'] != 'ok' ) {
					M.api.err(rsp);
					M.stopLoad();
					return false;
				}
			}
		}

		M.stopLoad();
	}

	this.rotateImage = function(fid) {
		var image_id = this.formFieldValue(this.sections['_image'].fields[fid], fid);
		var rsp = M.api.getJSON('ciniki.images.rotate', {'business_id':M.curBusinessID, 'image_id':image_id});
		if( rsp['stat'] != 'ok' ) {
			M.api.err(rsp);
			return false;
		}
		this.updateImgPreview('image_id', image_id);
		return true;
	}
}
