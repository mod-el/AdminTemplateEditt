var sId = null;
var currentAdminPage = false;
var menuResizing = false;
var columnResizing = false;
var menuIsOpen = true;
var sortedBy = [];
var currentPage = 1;
var selectedRows = [];
var searchCounter = 0;
var pageLoadingHash = '';

var saving = false;

/* Form history monitoring */
var changedValues = {};
var changeHistory = [];
var canceledChanges = [];

window.addEventListener('DOMContentLoaded', function() {
	currentAdminPage = document.location.pathname.substr(adminPrefix.length);
	var request = currentAdminPage.split('/');

	if(history.replaceState)
		history.replaceState({'request':request}, '', document.location);

	if(_('results-table'))
		tableEvents();

	if(_('sId'))
		sId = _('sId').getValue();

	if(currentAdminPage){
		_('main-loading').style.display = 'none';
		openMenuToRequest(request);
		loadPageAids(request);

		if(_('adminForm') && _('adminForm').dataset.filled==='0'){
			checkSubPages().then(function(){
				if(request[2]){
					loadElementData(request[0], request[2]).then(fillAdminForm).then(monitorFields).catch(alert);
				}else{
					initalizeEmptyForm();
					monitorFields();
				}
			});
		}
	}
});

window.addEventListener('load', function(){
	resize();
	window.addEventListener('resize', function () {
		resize();
	});
});

window.onpopstate = function(event){
	var s = event.state;
	if(typeof s['request']!=='undefined'){
		if(s['request'].join('/')===currentAdminPage && typeof s['p']!=='undefined'){
			goToPage(s['p'], false);
		}else{
			var get = '';
			if(typeof s['sId']!=='undefined')
				get = changeGetParameter(get, 'sId', s['sId']);

			if(s['request'][1]==='edit'){
				loadElement(s['request'][0], s['request'][2], false);
			}else{
				if(typeof s['p']!=='undefined')
					get = changeGetParameter(get, 'p', s['p']);
				loadAdminPage(s['request'], get, false, false);
			}
		}
	}
};

window.addEventListener('keydown', function(event){
	switch(event.keyCode){
		case 90: // CTRL+Z
			if(event.ctrlKey){
				historyStepBack();
				event.preventDefault();
			}
			break;
		case 89: // CTRL+Y
			if(event.ctrlKey){
				historyStepForward();
				event.preventDefault();
			}
			break;

	}
});

/*
 Opens or close a menu group
 */
function switchMenuGroup(id){
	var tasto = _('menu-group-'+id);
	var cont = _('menu-group-'+id+'-cont');
	if(tasto.hasClass('selected')) {
		closeMenuGroup(tasto, cont);
	}else{
		openMenuGroup(tasto, cont);
	}
}

/*
 Opens a menu group
 */
function openMenuGroup(tasto, cont){
	tasto.addClass('selected');
	if(cont){
		cont.style.height = cont.firstElementChild.offsetHeight+'px';
		setTimeout(function(){
			cont.style.height = 'auto';
		}, 500);
	}
}

/*
 Closes a menu group
 */
function closeMenuGroup(tasto, cont){
	tasto.removeClass('selected');
	if(cont){
		if(cont.style.height=='0px')
			return;
		cont.addClass('no-transition');
		cont.style.height = cont.firstElementChild.offsetHeight+'px';
		cont.offsetHeight; // Reflow
		cont.removeClass('no-transition');
		cont.style.height = '0px';
		cont.offsetHeight; // Reflow
	}
}

/*
 Closes all menu groups except for the ones provided in the first argument
 */
function closeAllMenuGroups(except){
	if(typeof except=='undefined')
		except = [];
	document.querySelectorAll('.main-menu-sub, .main-menu-tasto').forEach(function(tasto){
		if(!in_array(tasto.getAttribute('data-menu-id'), except)){
			var cont = _('.main-menu-cont[data-menu-id="'+tasto.getAttribute('data-menu-id')+'"]');
			closeMenuGroup(tasto, cont);
		}
	});
}

/*
 Open the menù pages selecting a specific link
 */
function openMenuTo(id){
	var tasto = _('menu-group-'+id);
	if(!tasto)
		return false;

	var toOpen = [];
	var div = tasto;
	while(div){
		if(typeof div.getAttribute!=='undefined' && div.getAttribute('data-menu-id')!==null)
			toOpen.push(div.getAttribute('data-menu-id'));
		div = div.parentNode;
	}

	closeAllMenuGroups(toOpen);
	toOpen.forEach(function(id){
		var tasto = _('menu-group-'+id);
		var cont = _('menu-group-'+id+'-cont');
		openMenuGroup(tasto, cont);
	});
}

/*
 Given a specific request, opens the left menu to the appropriate button
 */
function openMenuToRequest(request){
	var button = document.querySelector('.main-menu-tasto[href="'+adminPrefix+request[0]+'"], .main-menu-sub[href="'+adminPrefix+request[0]+'"]');
	if(button)
		openMenuTo(button.getAttribute('data-menu-id'));
}

/*
 Resizes page dynamic components, called on page open and at every resize
 */
function resize(menu){
	var hHeight = _('header').offsetHeight;
	_('main-grid').style.height = 'calc(100% - '+(hHeight+4)+'px)';
	var tHeight = _('toolbar').offsetHeight;
	_('main-page').style.height = 'calc(100% - '+tHeight+'px)';

	if(typeof menu=='undefined')
		menu = true;

	if(menu){
		var hideMenu = _('main-menu').getAttribute('data-hide');
		switch(hideMenu){
			case 'always':
				if((lastPosition = localStorage.getItem('sidenav-open-menu'))!==null){
					if(lastPosition==="0")
						closeMenu();
					else if(lastPosition==="1")
						openMenu();
				}
				break;
			case 'mobile':
				if(window.innerWidth<800)
					closeMenu();
				break;
			case 'never':
				if(!menuIsOpen)
					openMenu();
				break;
		}
	}

	var table = _('results-table');
	if(table){
		var sub_h = _('breadcrumbs').offsetHeight+_('#main-content > div:first-of-type').offsetHeight+_('table-headings').offsetHeight+10;
		table.style.height = (_('main-page').offsetHeight-sub_h)+'px';
	}

	if(form = _('topForm')){
		var w = _('toolbar').clientWidth-10;
		_('toolbar').querySelectorAll('.toolbar-button').forEach(function(button){
			w -= button.offsetWidth;
		});
		form.style.width = w+'px';
	}
}

function switchMenu(){
	if(menuIsOpen)
		closeMenu();
	else
		openMenu();
}

function openMenu(){
	_('main-menu').style.width = '40%';
	_('main-menu').style.maxWidth = maxMenuWidth+'px';
	_('main-page-cont').style.width = 'calc(100% - '+maxMenuWidth+'px)';

	var hideMenu = _('main-menu').getAttribute('data-hide');
	if(window.innerWidth>=800 && hideMenu!='always'){
		_('img-open-menu').style.opacity = 0;
		_('header').style.paddingLeft = '0';
	}

	menuIsOpen = true;
	localStorage.setItem('sidenav-open-menu', "1");
	setTimeout(function(){
		resize(false);
	}, 500);
}

function closeMenu(){
	_('main-menu').style.width = '0%';
	_('main-page-cont').style.width = '100%';
	_('img-open-menu').style.opacity = 1;
	_('header').style.paddingLeft = '40px';
	menuIsOpen = false;
	localStorage.setItem('sidenav-open-menu', "0");
	setTimeout(function(){
		resize(false);
	}, 500);
}

function startMenuResize(){
	var coords = getMouseCoords(event);
	menuResizing = {'startX':coords.x, 'startW':maxMenuWidth, 'endW':false};
}

/*
 Loads a page using fetch; fills the main div with the content when the response comes, and additionally returns a Promise
 */
function loadPage(url, get, post, deleteContent){
	if(typeof get==='undefined')
		get = '';
	if(typeof post==='undefined')
		post = false;
	if(typeof deleteContent==='undefined')
		deleteContent = true;

	get = changeGetParameter(get, 'ajax', '');

	if(deleteContent) {
		_('main-loading').style.display = 'block';
		_('main-content').innerHTML = '';
	}

	pageLoadingHash = url+get+post;

	return ajax(false, url, get, post).then((function(hash) {
		return function (response) {
			if(hash!==pageLoadingHash)
				return false;

			_('main-loading').style.display = 'none';
			jsFill(response, _('main-content'));
			resize();
			if (_('results-table'))
				tableEvents();
			return response;
		}
	})(pageLoadingHash));
}

/*
 Moves between admin pages, moving the left menù and taking care of the browser history
 */
function loadAdminPage(request, get, post, history_push){
	if(request.length===0)
		return false;
	if(typeof get==='undefined')
		get = '';
	if(typeof history_push=='undefined')
		history_push = true;

	openMenuToRequest(request);

	var full_url = request.join('/');

	var state = {'request':request};
	if(get.match(/sId=[0-9]+/)){
		sId = get.replace(/.*sId=([0-9]+).*/, '$1');
		state['sId'] = sId;
	}

	if(get.match(/p=[0-9]+/)){
		currentPage = parseInt(get.replace(/.*p=([0-9]+).*/, '$1'));
	}else{
		currentPage = 1;
	}

	state['p'] = currentPage;
	var forcePage = currentPage;

	if(history.pushState && history_push){
		history.pushState(state, '', adminPrefix+full_url+'?'+get);
	}

	if(currentAdminPage!=full_url) {
		if(typeof request[1]=='undefined' || request[1]==''){ // Table page
			var promise = loadPageAids(request, get).then((function(forcePage){
				return function(){
					return search(forcePage);
				};
			})(forcePage));
		}else{
			var promise = Promise.all([loadPage(adminPrefix+full_url, get, post), loadPageAids(request, get)]);
		}
	}else{
		var promise = loadPage(adminPrefix+full_url, get, post);
	}

	if(window.innerWidth<800)
		closeMenu();

	currentAdminPage = full_url;

	historyWipe();

	return promise;
}

/*
 Loads the page aids, like breadcrumbs and toolbar buttons
 */
function loadPageAids(request, get){
	if(typeof get=='undefined')
		get = '';

	if(sId!==null)
		get = changeGetParameter(get, 'sId', sId);
	if(typeof request[1]!=='undefined')
		get = changeGetParameter(get, 'action', request[1]);
	if(typeof request[2]!=='undefined')
		get = changeGetParameter(get, 'id', request[2]);

	_('toolbar').innerHTML = '';
	_('breadcrumbs').innerHTML = '';
	if(form = _('filtersFormCont'))
		form.innerHTML = '';

	return ajax(false, adminPrefix+request[0]+'/pageAids', get).then(function(aids){
		if(typeof aids!='object')
			return false;

		sId = aids.sId;

		if(history.replaceState){
			var url = document.location.href.replace(document.location.search, '');
			if(url.substr(-1)=='?')
				url = url.substr(0, -1);
			var queryString = changeGetParameter(document.location.search.substr(1), 'sId', sId);
			history.replaceState({'request':currentAdminPage.split('/'), 'sId':sId, 'p':currentPage}, '', url+'?'+queryString);
		}

		var toolbar = _('toolbar');

		if(aids.actions.length==0){
			toolbar.style.display = 'none';
		}else{
			toolbar.style.display = 'block';

			aids.actions.forEach(function(act){
				var button = document.createElement('a');
				button.className = 'toolbar-button';
				button.id = 'toolbar-button-'+act.id;
				button.href = act.url;
				button.setAttribute('onclick', act.action);
				if(act.icon)
					button.innerHTML = '<img src="'+act.icon+'" alt="" onload="resize()" /> ';
				button.innerHTML += act.text;
				toolbar.appendChild(button);
			});
		}

		if(aids.breadcrumbs){
			_('breadcrumbs').style.display = 'block';
			_('breadcrumbs').innerHTML = aids.breadcrumbs;
		}else{
			_('breadcrumbs').style.display = 'none';
		}

		if(lightboxForm = _('filtersFormCont'))
			lightboxForm.innerHTML = '';

		if(typeof aids.topForm!=='undefined'){
			if(window.innerWidth<800 && lightboxForm){
				lightboxForm.innerHTML += aids.topForm;
			}else{
				var form = document.createElement('div');
				form.id = 'topForm';
				form.innerHTML = aids.topForm;
				toolbar.appendChild(form);

				resize();
			}
		}

		if(typeof aids.filtersForm!=='undefined'){
			if(lightboxForm)
				lightboxForm.innerHTML += aids.filtersForm;
		}

		document.querySelectorAll('[data-filter]').forEach(function(el){
			switch(el.nodeName.toLowerCase()){
				case 'input':
					switch(el.type.toLowerCase()){
						case 'checkbox':
						case 'radio':
							el.addEventListener('change', search);
							break;
						default:
							el.addEventListener('keyup', function(event){
								if((event.keyCode<=40 && event.keyCode!=8 && event.keyCode!=13 && event.keyCode!=32))
									return false;

								searchCounter++;
								setTimeout((function(c){
									return function(){
										if(c===searchCounter)
											search();
									}
								})(searchCounter), 400);
							});
							break;
					}
					break;
				default:
					el.addEventListener('change', search);
					break;
			}
		});

		return aids;
	});
}

function startColumnResize(event, k){
	var coords = getMouseCoords(event);
	columnResizing = {'k':k, 'startX':coords.x, 'startW':parseInt(_('column-'+k).style.width), 'endW':false};
}

document.onmousemove = function(event){
	var coords = getMouseCoords(event);
	if(menuResizing!==false){
		var diff = coords.x-menuResizing.startX;
		var newW = menuResizing.startW+diff;
		if(newW>window.innerWidth*0.4)
			newW = Math.floor(window.innerWidth*0.4);

		_('main-menu').style.maxWidth = newW+'px';

		menuResizing.endW = newW;
	}
	if(columnResizing!==false){
		var diff = coords.x-columnResizing.startX;
		var newW = columnResizing.startW+diff;
		if(newW<20)
			newW = 20;

		var celle = document.querySelectorAll('[data-column="'+columnResizing.k+'"]');
		celle.forEach(function(cella){
			cella.style.width = newW+'px';
		});

		columnResizing.endW = newW;
	}
};

document.onmouseup = function(event){
	if(menuResizing!==false){
		if(menuResizing.endW<10)
			menuResizing.endW = 10;

		maxMenuWidth = menuResizing.endW;
		openMenu();
		menuResizing = false;

		setCookie('menu-width', maxMenuWidth, 365*10);
	}
	if(columnResizing!==false){
		if(columnResizing.endW!==false)
			saveColumnWidth(columnResizing.k, columnResizing.endW);
		columnResizing = false;
	}
};

function autoResize(label){
	if(label!==false){
		var startW = parseInt(_('column-'+label).style.width);
		var maxW = 0;

		var celle = document.querySelectorAll('[data-column="'+label+'"]');
		celle.forEach(function(cella){
			cella.lastElementChild.addClass('just-for-calculation');
			var w = cella.lastElementChild.scrollWidth;
			cella.lastElementChild.removeClass('just-for-calculation');

			if(w>maxW)
				maxW = w;
		});

		if(maxW){
			maxW += 20;
			celle.forEach(function(cella) {
				cella.style.width = maxW+'px';
			});
		}

		if(startW!=maxW)
			saveColumnWidth(label, maxW);
	}else{
		var celle = document.querySelectorAll('#table-headings div[data-column]');
		celle.forEach(function(cella) {
			autoResize(cella.dataset.column);
		});
	}
}

function saveColumnWidth(k, w){
	var request = currentAdminPage.split('/');
	return ajax(false, adminPrefix+request[0]+'/saveWidth', 'k='+encodeURIComponent(k), 'w='+encodeURIComponent(w)+'&c_id='+c_id);
}

function tableEvents(){
	var table = _('results-table');

	table.addEventListener('scroll', function(){
		var intest = _('table-headings');
		if(this.scrollLeft>intest.scrollLeftMax)
			this.scrollLeft = intest.scrollLeftMax;
		intest.scrollLeft = this.scrollLeft;
	});

	table.querySelectorAll('[id^="row-checkbox-"]').forEach(function(checkbox){
		if(selectedRows.indexOf(checkbox.dataset.id)!==-1)
			checkbox.setValue(1, false);
	});

	table.querySelectorAll('.results-table-row').forEach(function(row){
		row.addEventListener('click', function(event){
			if(event.button===0){
				if(row.dataset.clickable==='1')
					loadElement(currentAdminPage.split('/')[0], row.dataset.id);
			}
		});
	});

	sortedBy = JSON.parse(_('sortedBy').getValue());
	currentPage = _('currentPage').getValue();
}

function changeSorting(event, column){
	if(event.altKey){
		sortedBy.some(function(s, idx){
			if(s[0]===column){
				sortedBy.splice(idx, 1);
				return true;
			}
			return false;
		});
	}else if(event.ctrlKey){
		if(!sortedBy.some(function(s, idx){
				if(s[0]===column){
					sortedBy[idx][1] = sortedBy[idx][1]==='ASC' ? 'DESC' : 'ASC';
					return true;
				}
				return false;
			})){
			sortedBy.push([
				column,
				'ASC'
			]);
		}
	}else{
		if(sortedBy.length===1 && sortedBy[0][0]===column){
			sortedBy[0][1] = sortedBy[0][1]==='ASC' ? 'DESC' : 'ASC';
		}else{
			sortedBy = [
				[
					column,
					'ASC'
				]
			];
		}
	}
	reloadResultsTable();
}

function reloadResultsTable(get, post){
	if(typeof get=='undefined')
		get = document.location.search.substr(1);
	get = changeGetParameter(get, 'sId', sId);
	if(sortedBy)
		get += '&sortBy='+encodeURIComponent(JSON.stringify(sortedBy));
	loadPage(adminPrefix+(currentAdminPage.split('/')[0]), get, post)
}

function changeGetParameter(queryString, k, v){
	if(queryString===''){
		queryString = k+'='+encodeURIComponent(v);
	}else{
		if(queryString.indexOf(k+'=')===-1){
			queryString += '&'+k+'='+encodeURIComponent(v);
		}else{
			var regexp = new RegExp(k+'=[^&]*(&|$)');
			queryString = queryString.replace(regexp, k+'='+encodeURIComponent(v)+'$1');
		}
	}
	return queryString;
}

function goToPage(p, history_push){
	if(typeof history_push=='undefined')
		history_push = true;

	var mainContentDiv = _('main-content');

	var moveBy = mainContentDiv.offsetWidth+50;
	if(p>currentPage)
		moveBy *= -1;

	get = changeGetParameter(document.location.search.substr(1), 'sId', sId);
	get = changeGetParameter(get, 'p', p);

	if(history_push && history.pushState)
		history.pushState({'request':currentAdminPage.split('/'), 'sId':sId, 'p':p}, '', adminPrefix+currentAdminPage+'?'+get);

	var pageMove = new Promise(function(resolve){
		if(p!==currentPage){
			mainContentDiv.style.left = moveBy+'px';

			setTimeout(resolve, 300);
		}else{
			resolve();
		}
	}).then(function(){
		_('main-content').style.display = 'none';
		_('main-loading').style.display = 'block';
		return true;
	});
	var pageLoad = loadPage(adminPrefix+currentAdminPage, get, false, false);

	return Promise.all([pageMove, pageLoad]).then(function(){
		_('main-content').style.display = 'block';
		_('main-loading').style.display = 'none';

		mainContentDiv.className = 'no-transition';
		mainContentDiv.style.left = (moveBy*-1)+'px';
		mainContentDiv.offsetWidth;
		mainContentDiv.className = '';
		mainContentDiv.style.left = '0px';

		return new Promise(function(resolve, reject){
			setTimeout(resolve, 300);
		});
	});
}

function selectRow(id, enable){
	var k = selectedRows.indexOf(id);
	if(k!==-1){
		if(!enable){
			selectedRows.splice(k, 1);
		}
	}else{
		if(enable){
			selectedRows.push(id);
		}
	}
}

function selectAllRows(enable){
	_('results-table').querySelectorAll('[id^="row-checkbox-"]').forEach(function(checkbox){
		checkbox.setValue(enable);
	});
}

function deleteRows(ids){
	var usingChecks = false;
	if(typeof ids==='undefined'){
		ids = selectedRows;
		usingChecks = true;
	}
	if(ids.length===0){
		alert('Nessuna riga selezionata');
		return false;
	}

	if(!confirm('Sicuro di voler eliminare?'))
		return false;

	if(usingChecks){
		var nChecked = 0;
		_('results-table').querySelectorAll('[id^="row-checkbox-"]').forEach(function(checkbox){
			if(checkbox.checked)
				nChecked++;
		});

		if(ids.length>nChecked){
			if(!confirm('ATTENZIONE: ci sono righe selezionate anche in altre pagine, saranno eliminate anche quelle. Continuare?'))
				return false;
		}
	}

	_('#toolbar-button-delete img').src = absolute_path+'model/Output/files/loading.gif';

	var request = currentAdminPage.split('/');
	return ajax(false, adminPrefix+request[0]+'/delete', 'id='+encodeURIComponent(ids.join(',')), 'c_id='+c_id).then(function(r){
		if(typeof r!='object')
			r = {'err':r};

		_('#toolbar-button-delete img').src = absolute_path+'model/AdminTemplateEditt/files/img/toolbar/delete.png';

		if(typeof r.err!=='undefined'){
			alert(r.err);
		}else{
			selectedRows = [];
			if(request.length===1)
				reloadResultsTable();
			else
				loadAdminPage([request[0]]);
		}

		return r;
	});
}

var lightboxOldParent = false;
function toolsLightbox(id, options){
	if(lightbox = _('tools-lightbox')){
		while(lightbox.childNodes.length){
			lightboxOldParent.appendChild(lightbox.firstChild);
		}
		lightboxOldParent = false;
		_('main-page').removeChild(lightbox);
		return;
	}

	options = array_merge({
		'origin': false,
		'width': false,
		'height': false,
		'left': false,
		'offset-x': 0,
		'offset-y': 0
	}, options);
	var lightbox = document.createElement('div');
	lightbox.className = 'tools-lightbox';
	lightbox.id = 'tools-lightbox';
	if(options['width']!==false)
		lightbox.style.width = options['width'];
	if(options['height']!==false)
		lightbox.style.height = options['height'];
	lightbox.style.transform = 'scale(1,0)';

	var contentDiv = _(id);
	lightboxOldParent = contentDiv;
	while(contentDiv.childNodes.length){
		lightbox.appendChild(contentDiv.firstChild);
	}

	_('main-page').appendChild(lightbox);

	var coords = getElementCoords(options['origin']);

	coords.y += options['origin'].offsetHeight;
	coords.y -= window.pageYOffset;
	lightbox.style.top = (coords.y+options['offset-y'])+'px';

	if(options['left']!==false){
		lightbox.style.left = options['left'];
	}else{
		if(coords.x+options['offset-x']+lightbox.offsetWidth>window.innerWidth-10){
			lightbox.style.right = (window.innerWidth-coords.x-options['origin'].offsetWidth-options['offset-x'])+'px';
		}else{
			lightbox.style.left = (coords.x+options['offset-x'])+'px';
		}
	}

	lightbox.style.transform = 'scale(1,1)';
}

function switchFiltersForm(origin) {
	if(_('filtersForm')){
		if(window.innerWidth<800){
			toolsLightbox('filtersForm', {'origin': origin, 'width': 'calc(100% - 20px)', 'left': '10px', 'offset-y': 10});
		}else{
			toolsLightbox('filtersForm', {'origin': origin, 'width': '60%', 'left': maxMenuWidth+'px', 'offset-y': 10});
		}
	}
}

function search(forcePage){
	if(typeof forcePage=='undefined')
		forcePage = 1;

	var filters = [];
	document.querySelectorAll('[data-filter]').forEach(function(el){
		var v = el.getValue();
		if(v==='')
			return;

		switch(el.dataset.filter){
			case 'custom':
				var f = [el.name, v];
				break;
			default:
				var f = [el.name, el.dataset.filter, v];
				break;
		}
		filters.push(f);
	});

	get = changeGetParameter(document.location.search.substr(1), 'sId', sId);
	get = changeGetParameter(get, 'p', forcePage);
	get = changeGetParameter(get, 'filters', JSON.stringify(filters));

	return loadPage(adminPrefix+currentAdminPage, get);
}

function filtersReset(){
	document.querySelectorAll('[data-filter]').forEach(function(el){
		el.setValue(el.dataset.default, false);
	});
	search();
}

function manageFilters(){
	var request = currentAdminPage.split('/');
	zkPopup({'url':adminPrefix+request[0]+'/pickFilters'});
}

function saveFilters(){
	var request = currentAdminPage.split('/');

	var filters = {};
	document.querySelectorAll('[data-managefilters]').forEach(function(radio){
		if(radio.checked && radio.value!='0'){
			filters[radio.getAttribute('data-managefilters')] = radio.value;
		}
	});

	loading(_('popup-real'));
	return ajax(false, adminPrefix+request[0]+'/pickFilters', '', 'c_id='+c_id+'&filters='+encodeURIComponent(JSON.stringify(filters))).then(function(r){
		if(r!='ok'){
			alert(r);
			return false;
		}else{
			return loadPageAids(currentAdminPage.split('/'));
		}
	}).then(function(){
		zkPopupClose();
		return search();
	});
}

function manageSearchFields(){
	var request = currentAdminPage.split('/');
	zkPopup({'url':adminPrefix+request[0]+'/pickSearchFields'});
}

function saveSearchFields(){
	var request = currentAdminPage.split('/');

	var fields = [];
	document.querySelectorAll('[data-managesearchfields]').forEach(function(check){
		if(check.checked)
			fields.push(check.getAttribute('data-managesearchfields'));
	});
	var post = 'c_id='+c_id+'&fields='+encodeURIComponent(fields.join(','));

	loading(_('popup-real'));
	return ajax(false, adminPrefix+request[0]+'/pickSearchFields', '', post).then(function(r){
		zkPopupClose();
		if(r!='ok'){
			alert(r);
		}else{
			return search();
		}
	});
}

function loadElement(page, id, history_push){
	if(typeof history_push=='undefined')
		history_push = true;

	if(id){
		var formTemplate = loadAdminPage([page, 'edit', id], '', false, history_push);
		var formData = loadElementData(page, id);

		return Promise.all([formTemplate, formData]).then(function(data){
			return checkSubPages().then(function(){
				return fillAdminForm(data[1]).then(monitorFields);
			});
		}).catch(alert);
	}else{
		return loadAdminPage([page, 'edit'], '', false, history_push).then(checkSubPages).then(monitorFields).catch(alert);
	}
}

function loadElementData(page, id){
	return ajax(false, adminPrefix+page+'/edit/'+id, 'getData=1', false).then(function(r){
		if(typeof r!='object')
			throw r;
		return r;
	});
}

function fillAdminForm(data){
	return new Promise(function(resolve, reject){
		if(!(form = _('adminForm'))){
			throw 'Error in loading element';
		}

		form.fill(data.data);

		for(var name in data.children){
			if(!data.children.hasOwnProperty(name))
				continue;

			var list = data.children[name];

			name = name.split('-');

			for(var id in list){
				if(!list.hasOwnProperty(id))
					continue;

				sublistAddRow(name[0], name[1], id, false);

				for(var k in list[id]){
					if(!list[id].hasOwnProperty(k))
						continue;

					var form_k = 'ch-'+k+'-'+name[0]+'-'+id;
					if(typeof form[form_k]!=='undefined')
						form[form_k].setValue(list[id][k], false);
				}
			}
		}

		form.dataset.filled = '1';

		resolve();
	});
}

function initalizeEmptyForm(){
	var form = _('adminForm');
	if(!form)
		return false;

	for(var i = 0, f; f = form.elements[i++];) {
		f.setValue(null);
	}

	return true;
}

function monitorFields(){
	var form = _('adminForm');
	for(var i in form.elements){
		if(!form.elements.hasOwnProperty(i)) continue;
		var f = form.elements[i];
		if(!f.name || f.name==='fakeusernameremembered' || f.name==='fakepasswordremembered')
			continue;

		if(f.getAttribute('data-monitored'))
			continue;

		var isInSublistTemplate = false;
		var check = f;
		while(check){
			if(check.hasClass && check.hasClass('sublist-template')){
				isInSublistTemplate = true;
				break;
			}
			check = check.parentNode;
		}
		if(isInSublistTemplate)
			continue;

		f.setAttribute('data-monitored', '1');

		f.addEventListener('change', function(e){
			changedMonitoredField(this);
		});

		var v = f.getValue();
		if(typeof v==='object') // Probably file inputs, not handled in history
			continue;

		f.setAttribute('data-default-value', v);
	}
	return true;
}

function changedMonitoredField(f){
	var old = null;
	if(typeof changedValues[f.name]==='undefined'){
		old = f.getAttribute('data-default-value');
	}else{
		old = changedValues[f.name];
	}

	var v = f.getValue();

	if(typeof v==='object') { // Probably file inputs, not handled in history (I just store the new file value)
		v.then(function(file){
			changedValues[f.name] = file;
		});
		return;
	}

	changedValues[f.name] = v;

	changeHistory.push({
		'field': f.name,
		'old': old,
		'new': v
	});

	canceledChanges = [];

	rebuildHistoryBox();
}

function rebuildHistoryBox(){
	_('links-history').innerHTML = '<a href="#" onclick="historyGoToStep(\'reset\'); return false" class="link-history">Situazione iniziale</a>';

	changeHistory.forEach(function(i, idx){
		var a = document.createElement('a');
		a.href = '#';
		a.setAttribute('onclick', 'historyGoToStep(\'back\', '+idx+'); return false');
		a.className = 'link-history';
		if(typeof i.sublist!=='undefined'){
			switch(i.action){
				case 'new':
					a.textContent = 'new sublist row in "'+i.sublist+'"';
					break;
				case 'delete':
					a.textContent = 'deleted row in "'+i.sublist+'"';
					break;
			}
		}else{
			a.textContent = 'edited "'+i.field+'"';
		}
		_('links-history').appendChild(a);
	});

	canceledChanges.forEach(function(i, idx){
		var a = document.createElement('a');
		a.href = '#';
		a.setAttribute('onclick', 'historyGoToStep(\'forward\', '+idx+'); return false');
		a.className = 'link-history disabled';
		if(typeof i.sublist!=='undefined'){
			switch(i.action){
				case 'new':
					a.textContent = 'new sublist row in "'+i.sublist+'"';
					break;
				case 'delete':
					a.textContent = 'deleted row in "'+i.sublist+'"';
					break;
			}
		}else{
			a.textContent = 'edited "'+i.field+'"';
		}
		_('links-history').appendChild(a);
	});
}

function switchHistoryBox(){
	var div = _('history-box');
	if(div.style.right=='0px'){
		div.style.right = '-15%';
	}else{
		div.style.right = '0px';
	}
}

function historyStepBack(){
	if(changeHistory.length===0)
		return false;
	var form = _('adminForm');
	var el = changeHistory.pop();
	canceledChanges.unshift(el);

	if(typeof el.sublist!=='undefined'){
		switch(el.action){
			case 'new':
				sublistDeleteRow(el.sublist, el.cont, el.id, false);
				break;
			case 'delete':
				sublistRestoreRow(el.sublist, el.cont, el.id);
				break;
		}
	}else{
		if(form[el.field].getAttribute('data-multilang') && form[el.field].getAttribute('data-lang')){
			switchFieldLang(form[el.field].getAttribute('data-multilang'), form[el.field].getAttribute('data-lang'));
		}

		form[el.field].setValue(el.old, false);
		form[el.field].focus();
		if(form[el.field].select)
			form[el.field].select();
		changedValues[el.field] = el.old;
	}

	rebuildHistoryBox();
}

function historyStepForward(){
	if(canceledChanges.length===0)
		return false;
	var form = _('adminForm');
	var el = canceledChanges.shift();
	changeHistory.push(el);

	if(typeof el.sublist!=='undefined'){
		switch(el.action){
			case 'new':
				sublistRestoreRow(el.sublist, el.cont, el.id);
				break;
			case 'delete':
				sublistDeleteRow(el.sublist, el.cont, el.id, false);
				break;
		}
	}else{
		if(form[el.field].getAttribute('data-multilang') && form[el.field].getAttribute('data-lang')) {
			switchFieldLang(form[el.field].getAttribute('data-multilang'), form[el.field].getAttribute('data-lang'));
		}

		form[el.field].setValue(el.new, false);
		form[el.field].focus();
		if(form[el.field].select)
			form[el.field].select();
		changedValues[el.field] = el.new;
	}

	rebuildHistoryBox();
}

function historyGoToStep(t, i){
	switch(t){
		case 'reset':
			while(changeHistory.length>0){
				historyStepBack();
			}
			break;
		case 'back':
			while(changeHistory.length>i+1){
				historyStepBack();
			}
			break;
		case 'forward':
			if(i+1>canceledChanges)
				return false;
			for(c=1;c<=i+1;c++){
				historyStepForward();
			}
			break;
	}
}

function historyWipe(){
	changedValues = {};
	changeHistory = [];
	canceledChanges = [];
	rebuildHistoryBox();
}

function newElement(){
	return loadElement(currentAdminPage.split('/')[0]).then(initalizeEmptyForm);
}

function save(){
	if(saving){
		alert('Already saving');
		return false;
	}

	saving = true;
	_('#toolbar-button-save img').src = absolute_path+'model/Output/files/loading.gif';
	resize();

	var request = currentAdminPage.split('/');

	setLoadingBar(0);

	return new Promise(function(resolve){
		setTimeout(function(){ // Gives a little bit of time for the fields to activate their "onchange" events
			resolve();
		}, 200);
	}).then(function(){
		var url, history_push;

		if(typeof request[2]!=='undefined'){
			// I am editing an existing element
			url = adminPrefix+request[0]+'/save/'+request[2];
			history_push = false;
		}else{
			// I am saving a new element
			url = adminPrefix+request[0]+'/save';
			history_push = true;
		}

		var form = _('adminForm');
		var savingValues = {};
		for(var k in changedValues){
			if(form[k].getAttribute('data-multilang') && typeof savingValues[k]==='undefined'){
				if(typeof savingValues[form[k].getAttribute('data-multilang')]==='undefined')
					savingValues[form[k].getAttribute('data-multilang')] = {};
				savingValues[form[k].getAttribute('data-multilang')][form[k].getAttribute('data-lang')] = changedValues[k];
			}else{
				savingValues[k] = changedValues[k];
			}
		}

		return ajax(false, url, '', 'c_id='+c_id+'&data='+encodeURIComponent(JSON.stringify(savingValues)), {
			'onprogress': function(event){
				if(event.total===0){
					var percentage = 0;
				}else{
					var percentage = Math.round(event.loaded / event.total * 100);
				}

				setLoadingBar(percentage);
			}
		}).then(function(r){
			setLoadingBar(0);

			saving = false;
			_('#toolbar-button-save img').src = absolute_path+'model/AdminTemplateEditt/files/img/toolbar/save.png';

			if(typeof r!=='object'){
				alert(r);
				return false;
			}
			if(r.status==='ok'){
				request[2] = r.id;

				return loadElement(request[0], request[2], history_push).then(function(){
					inPageMessage('Salvataggio correttamente effettuato.', 'green-message');
				});
			}else if(typeof r.err!='undefined'){
				alert(r.err);
			}else{
				alert('Generic error');
			}
		});
	});
}

function inPageMessage(text, className){
	var div = document.createElement('div');
	div.className = className;
	div.innerHTML = text;
	_('main-content').insertBefore(div, _('main-content').firstChild);
}

function instantSave(id, f, field){
	var riga = _('.results-table-row[data-id="'+id+'"]');
	if(!riga)
		return false;

	var v = field.getValue();
	field.style.opacity = 0.2;

	var ids = [];
	document.querySelectorAll('.results-table-row[data-id]').forEach(function(r){
		ids.push(r.getAttribute('data-id'));
	});

	var request = currentAdminPage.split('/');

	var saving = {};
	saving[f] = v;

	ajax(false, adminPrefix+request[0]+'/save/'+id, 'instant='+encodeURIComponent(ids.join(',')), 'c_id='+c_id+'&data='+encodeURIComponent(JSON.stringify(saving))).then(function(r){
		if(typeof r!='object'){
			alert(r);
			field.style.display = 'none';
		}else if(typeof r.err!='undefined'){
			alert(r.err);
			field.style.display = 'none';
		}else if(r.status!='ok'){
			alert('Error');
			field.style.display = 'none';
		}else{
			field.style.opacity = 1;

			for(var id in r.changed){
				var el = r.changed[id];

				var row = _('.results-table-row[data-id="'+id+'"]');
				if(!row)
					return;

				if(el.background)
					row.style.background = el.background;
				else
					row.style.background = '';

				if(el.color)
					row.style.color = el.color;
				else
					row.style.color = '';

				for(var k in el.columns){
					var cell = row.querySelector('[data-column="'+k+'"]');
					if(!cell)
						continue;

					var c = el.columns[k];

					if(c.background)
						cell.style.background = c.background;
					else
						cell.style.background = '';

					if(c.color)
						cell.style.color = c.color;
					else
						cell.style.color = '';

					if(cell.hasClass('editable-cell')){
						var f = cell.querySelector('input, select, textarea');
						f.setValue(c.value, false);
					}else{
						cell.firstElementChild.innerHTML = c.text;
					}
				}
			}
		}
	});

	var n = parseInt(riga.getAttribute('data-n'));
	if(_('instant-'+(n+1)+'-'+f)){
		_('instant-'+(n+1)+'-'+f).focus();
		_('instant-'+(n+1)+'-'+f).select();
	}
}

function allInOnePage(){
	var get = changeGetParameter('', 'sId', sId);
	get = changeGetParameter(get, 'nopag', 1);

	loadAdminPage([currentAdminPage.split('/')[0]], get);
}

function sublistAddRow(name, cont, id, trigger){
	if(typeof trigger==='undefined')
		trigger = true;

	var form = _('adminForm');

	if(typeof id==='undefined' || id===null){
		var next = 0;
		while(typeof form['ch-'+name+'-new'+next]!=='undefined')
			next++;

		id = 'new'+next;
	}

	if(typeof cont==='undefined' || cont===null)
		cont = name;

	var div = document.createElement('div');
	div.className = 'rob-field-cont sublist-row';
	div.id = 'cont-ch-'+cont+'-'+id;
	div.innerHTML = _('sublist-template-'+cont).innerHTML.replace(/\[n\]/g, id);

	if(addbutton = _('cont-ch-'+cont+'-addbutton')){
		_('cont-ch-'+cont).insertBefore(div, addbutton);
	}else{
		_('cont-ch-'+cont).appendChild(div);
	}

	/*fillPopup();*/
	monitorFields();

	changedValues['ch-'+name+'-'+id] = 1;

	if(trigger){
		changeHistory.push({
			'sublist': name,
			'cont': cont,
			'action': 'new',
			'id': id
		});

		rebuildHistoryBox();
	}

	return next;
}

function sublistDeleteRow(name, cont, id, trigger){
	if(typeof trigger==='undefined')
		trigger = true;

	var form = _('adminForm');
	if(typeof form['ch-'+name+'-'+id]!=='undefined')
		form['ch-'+name+'-'+id].setValue(0, false);
	_('cont-ch-'+cont+'-'+id).style.display = 'none';

	changedValues['ch-'+name+'-'+id] = 0;

	if(trigger){
		changeHistory.push({
			'sublist': name,
			'cont': cont,
			'action': 'delete',
			'id': id
		});

		rebuildHistoryBox();
	}
}

function sublistRestoreRow(name, cont, id){
	var form = _('adminForm');
	if(typeof form['ch-'+name+'-'+id]!=='undefined')
		form['ch-'+name+'-'+id].setValue(1, false);
	_('cont-ch-'+cont+'-'+id).style.display = 'block';
	changedValues['ch-'+name+'-'+id] = 1;
}

function switchAllFieldsLang(lang){
	document.querySelectorAll('.lang-switch-cont [data-lang]').forEach(function(el){
		if(el.getAttribute('data-lang')===lang)
			el.addClass('selected');
		else
			el.removeClass('selected');
	});

	document.querySelectorAll('.multilang-field-container[data-name]').forEach(function(f){
		switchFieldLang(f.getAttribute('data-name'), lang);
	});
}

function setLoadingBar(percentage){
	_('main-loading-bar').style.width = percentage+'%';
}

function duplicate(){
	if(changeHistory.length>0){
		alert('There are prending changes, can\'t duplicate.');
		return false;
	}

	var request = currentAdminPage.split('/');
	window.open(adminPrefix+request[0]+'/duplicate/'+request[2]);
}

function checkSubPages(){
	var promises = [];

	var containers = document.querySelectorAll('[data-subpages]');
	containers.forEach(function(cont){
		var tabsCont = document.querySelector('[data-tabs][data-name="'+cont.getAttribute('data-subpages')+'"]');
		var tabs = tabsCont.querySelectorAll('[data-tab]');
		tabs.forEach(function(tab){
			var page = cont.querySelector('[data-subpage="'+tab.getAttribute('data-tab')+'"]');
			if(!page){
				var subPageCont = document.createElement('div');
				subPageCont.setAttribute('data-subpage', tab.getAttribute('data-tab'));
				subPageCont.innerHTML = '[to-be-loaded]';
				cont.appendChild(subPageCont);
			}

			if(tab.getAttribute('data-oninit')){
				(function(){
					eval(this.getAttribute('data-oninit'));
				}).call(tab);
			}

			tab.addEventListener('click', function(event){
				sessionStorage.setItem(tabsCont.getAttribute('data-tabs'), this.getAttribute('data-tab'));
				loadSubPage(cont.getAttribute('data-subpages'), this.getAttribute('data-tab'));

				if(this.getAttribute('data-onclick')){
					eval(this.getAttribute('data-onclick'));
				}

				return false;
			});
		});

		var def = null;
		if(sessionStorage.getItem(tabsCont.getAttribute('data-tabs'))){
			def = sessionStorage.getItem(tabsCont.getAttribute('data-tabs'));
		}else if(tabsCont.getAttribute('data-default')){
			def = tabsCont.getAttribute('data-default');
		}else{
			def = tabsCont.querySelector('[data-tab]');
			if(def)
				def = def.getAttribute('data-tab');
		}

		if(def){
			promises.push(new Promise(function(resolve){
				loadSubPage(cont.getAttribute('data-subpages'), def).then(resolve);
			}));
		}
	});

	return Promise.all(promises);
}

function loadSubPage(cont_name, p){
	var tabsCont = document.querySelector('[data-tabs][data-name="'+cont_name+'"]');

	tabsCont.querySelectorAll('[data-tab]').forEach(function(el){
		var cont = document.querySelector('[data-subpages="'+cont_name+'"] [data-subpage="'+el.getAttribute('data-tab')+'"]');

		if(el.getAttribute('data-tab')===p){
			el.addClass('selected');

			cont.style.display = 'block';
		}else{
			el.removeClass('selected');

			cont.style.display = 'none';
		}
	});

	var cont = document.querySelector('[data-subpages="'+cont_name+'"] [data-subpage="'+p+'"]');
	if(cont.innerHTML==='[to-be-loaded]'){
		var request = currentAdminPage.split('/');
		if(request.length===2)
			request.push(0);

		loading(cont);
		return ajax(cont, adminPrefix+request.join('/')+'/'+p, '', '');
	}else{
		return new Promise(function(resolve){ resolve(); });
	}
}