var menuResizing = false;
var menuIsOpen = true;

/*
 Given a set of pages, builds the left menu
 */
function buildMenu(pages) {
	let cont = _('main-menu');
	cont.innerHTML = '';
	pages.forEach((p, idx) => {
		let pageData = getLinkFromPage(p, idx);
		let link = pageData.link, click = pageData.click;

		let button = document.createElement('a');
		button.setAttribute('href', link);
		button.setAttribute('id', 'menu-group-' + idx);
		button.setAttribute('data-menu-id', idx);
		button.className = 'main-menu-tasto';
		if (click)
			button.addEventListener('click', click);
		button.innerHTML = '<span class="cont-testo-menu">' + entities(p['name']) + '</span>';
		cont.appendChild(button);

		if (typeof p.sub !== 'undefined' && p.sub.length > 0) {
			let subCont = document.createElement('div');
			subCont.className = 'main-menu-cont expandible';
			subCont.setAttribute('id', 'menu-group-' + idx + '-cont');
			subCont.style.height = '0px';
			subCont.setAttribute('data-menu-id', idx);
			subCont.innerHTML = '<div></div>';
			subCont = cont.appendChild(subCont);

			fillMenuSubCont(subCont.firstElementChild, idx, p.sub, 1);
		}
	});
}

function loadLoginPage() {
	_('header-right').style.display = 'none';
	_('header-user-cont').style.display = 'none';
	buildMenu([]);
	closeMenu();
	return loadPage(adminPrefix + 'login');
}

function unloadLoginPage() {
	_('header-right').style.display = 'block';
	_('header-user-cont').style.display = 'inline-block';
	openMenu();
}

function switchMenu() {
	if (menuIsOpen)
		closeMenu();
	else
		openMenu();
}

function openMenu() {
	_('main-container').removeClass('no-menu');

	var hideMenu = _('main-menu-cont').getAttribute('data-hide');
	if (window.innerWidth >= 800 && hideMenu !== 'always') {
		_('img-open-menu').style.opacity = 0;
		_('header').style.paddingLeft = '0';
	}

	menuIsOpen = true;
	localStorage.setItem('sidenav-open-menu', "1");
	setTimeout(function () {
		resize(false);
	}, 500);
}

function closeMenu() {
	_('main-container').addClass('no-menu');
	_('img-open-menu').style.opacity = 1;

	/*_('main-menu-cont').style.width = '0%';
	_('header').style.paddingLeft = '40px';*/
	menuIsOpen = false;
	localStorage.setItem('sidenav-open-menu', "0");
	setTimeout(function () {
		resize(false);
	}, 500);
}


function fillMenuSubCont(cont, parentIdx, pages, lvl) {
	pages.forEach((p, idx) => {
		let pageData = getLinkFromPage(p, parentIdx + '-' + idx);
		let link = pageData.link, click = pageData.click;

		let button = document.createElement('a');
		button.setAttribute('href', link);
		button.setAttribute('id', 'menu-group-' + parentIdx + '-' + idx);
		button.setAttribute('data-menu-id', parentIdx + '-' + idx);
		button.className = 'main-menu-sub';
		if (click)
			button.addEventListener('click', click);
		button.innerHTML = '<img src="' + PATHBASE + 'model/AdminTemplateEditt/assets/img/page.png" alt=""/> <span class="cont-testo-menu">' + entities(p['name']) + '</span>';
		cont.appendChild(button);

		if (typeof p.sub !== 'undefined' && p.sub.length > 0) {
			let subCont = document.createElement('div');
			subCont.className = 'main-menu-cont expandible';
			subCont.setAttribute('id', 'menu-group-' + parentIdx + '-' + idx + '-cont');
			subCont.style.height = '0px';
			subCont.style.paddingLeft = (15 * lvl) + 'px';
			subCont.setAttribute('data-menu-id', parentIdx + '-' + idx);
			subCont.innerHTML = '<div></div>';
			subCont = cont.appendChild(subCont);

			fillMenuSubCont(subCont.firstElementChild, parentIdx + '-' + idx, p.sub, lvl + 1);
		}
	});
}

function getCssMenuWidth() {
	let root = document.documentElement;
	let w = parseInt(root.style.getPropertyValue('--menu-width'));
	if (isNaN(w))
		w = 220;
	return w;
}

function setCssMenuWidth(width) {
	let root = document.documentElement;
	root.style.setProperty('--menu-width', width + 'px');
}

function startMenuResize(event) {
	let coords = getMouseCoords(event);
	menuResizing = {'startX': coords.x, 'startW': getCssMenuWidth(), 'endW': false};
}

window.addEventListener('load', () => {
	let w = parseInt(localStorage.getItem('menu-width'));
	if (w && !isNaN(w))
		setCssMenuWidth(localStorage.getItem('menu-width'));
});

document.addEventListener('mousemove', event => {
	if (menuResizing !== false) {
		let coords = getMouseCoords(event);
		let diff = coords.x - menuResizing.startX;
		let newW = menuResizing.startW + diff;
		if (newW > window.innerWidth * 0.4)
			newW = Math.floor(window.innerWidth * 0.4);

		setCssMenuWidth(newW);

		menuResizing.endW = newW;
	}
});

document.addEventListener('mouseup', event => {
	if (menuResizing !== false) {
		console.log(menuResizing);
		if (menuResizing.endW !== false) {
			if (menuResizing.endW < 25) {
				closeMenu();
			} else {
				openMenu();
				localStorage.setItem('menu-width', menuResizing.endW);
			}
		}
		menuResizing = false;
	}
});

/*
 Resizes page dynamic components, called on page open and at every resize
 */
function resize(menu = true) {
	if (menu) {
		let hideMenu = _('main-menu-cont').getAttribute('data-hide');
		switch (hideMenu) {
			case 'always':
				if ((lastPosition = localStorage.getItem('sidenav-open-menu')) !== null) {
					if (lastPosition === "0")
						closeMenu();
					else if (lastPosition === "1")
						openMenu();
				}
				break;
			case 'mobile':
				if (window.innerWidth < 800)
					closeMenu();
				break;
			case 'never':
				if (!menuIsOpen)
					openMenu();
				break;
		}
	}

	let table = _('.results-table');
	if (table) {
		let sub_h = _('breadcrumbs').offsetHeight + _('#main-content > div:first-of-type').offsetHeight + _('table-headings').offsetHeight + 10;
		table.style.height = (_('main-page').offsetHeight - sub_h) + 'px';
	}

	let topForm = _('topForm');
	if (topForm) {
		if (window.innerWidth < 800) {
			let filtersFormCont = _('filtersFormCont');
			if (topForm.parentNode !== filtersFormCont.parentNode)
				filtersFormCont.parentNode.insertBefore(topForm, filtersFormCont);
			topForm.style.width = '100%';
		} else {
			let toolbar = _('toolbar');
			if (topForm.parentNode !== toolbar)
				toolbar.appendChild(topForm);

			let w = toolbar.clientWidth - 12;
			toolbar.querySelectorAll('.toolbar-button').forEach(function (button) {
				w -= button.offsetWidth;
			});
			topForm.style.width = w + 'px';
		}
	}
}

function getLinkFromPage(p, idx) {
	let link = '', click = null;

	if (p.path) {
		if (p.direct) {
			link = adminPrefix + p.path + '/edit/' + p.direct;
			click = function (event) {
				event.preventDefault();
				loadAdminPage(p.path + '/edit/' + p.direct, '', true, true);
				return false;
			};
		} else {
			link = adminPrefix + p.path;
			click = function (event) {
				event.preventDefault();
				loadAdminPage(p.path);
				return false;
			};
		}
	} else {
		link = '#';
		click = function (event) {
			event.preventDefault();
			switchMenuGroup(idx);
			return false;
		};
	}

	return {
		'link': link,
		'click': click
	};
}

/*
 Opens or close a menu group
 */
function switchMenuGroup(id) {
	var tasto = _('menu-group-' + id);
	var cont = _('menu-group-' + id + '-cont');
	if (tasto.hasClass('selected')) {
		closeMenuGroup(tasto, cont);
	} else {
		openMenuGroup(tasto, cont);
	}
}

/*
 Opens a menu group
 */
function openMenuGroup(tasto, cont) {
	tasto.addClass('selected');
	if (cont) {
		cont.style.height = cont.firstElementChild.offsetHeight + 'px';
		setTimeout(function () {
			cont.style.height = 'auto';
		}, 500);
	}
}

/*
 Closes a menu group
 */
function closeMenuGroup(tasto, cont) {
	tasto.removeClass('selected');
	if (cont) {
		if (cont.style.height === '0px')
			return;
		cont.addClass('no-transition');
		cont.style.height = cont.firstElementChild.offsetHeight + 'px';
		cont.offsetHeight; // Reflow
		cont.removeClass('no-transition');
		cont.style.height = '0px';
		cont.offsetHeight; // Reflow
	}
}

/*
 Closes all menu groups except for the ones provided in the first argument
 */
function closeAllMenuGroups(except = []) {
	document.querySelectorAll('.main-menu-sub, .main-menu-tasto').forEach(button => {
		if (!in_array(button.getAttribute('data-menu-id'), except)) {
			let cont = _('.main-menu-cont[data-menu-id="' + button.getAttribute('data-menu-id') + '"]');
			closeMenuGroup(button, cont);
		}
	});
}

/*
 Open the menù pages selecting a specific link
 */
function openMenuTo(id) {
	let button = _('menu-group-' + id);
	if (!button)
		return false;

	let toOpen = [];
	let div = button;
	while (div) {
		if (typeof div.getAttribute !== 'undefined' && div.getAttribute('data-menu-id') !== null)
			toOpen.push(div.getAttribute('data-menu-id'));
		div = div.parentNode;
	}

	closeAllMenuGroups(toOpen);
	toOpen.forEach(id => {
		let button = _('menu-group-' + id);
		let cont = _('menu-group-' + id + '-cont');
		openMenuGroup(button, cont);
	});
}

/*
 Given a specific request, opens the left menu to the appropriate button
 */
function selectFromMainMenu(request) {
	let button = document.querySelector('.main-menu-tasto[href="' + adminPrefix + request[0] + '"], .main-menu-sub[href="' + adminPrefix + request[0] + '"], .main-menu-tasto[href="' + adminPrefix + request.join('/') + '"], .main-menu-sub[href="' + adminPrefix + request.join('/') + '"]');
	if (button)
		openMenuTo(button.getAttribute('data-menu-id'));
	else
		closeAllMenuGroups();
}

/*
 Displays or hides the notifications box
 */
function toggleNotifications() {
	let cont = _('header-notifications-container');
	let campanellina = _('notifications-bell');

	if (cont.style.display === 'none') {
		cont.style.display = 'block';
		campanellina.addClass('active');
		cont.loading().ajax(adminPrefix + 'model-admin-notifications/list', {
			'ajax': 1,
			'user_idx': 'Admin'
		}).then(checkNotifications);
	} else {
		cont.style.display = 'none';
		campanellina.removeClass('active');
	}
}

function switchFiltersForm(origin) {
	if (_('filtersForm')) {
		if (window.innerWidth < 800) {
			toolsLightbox('filtersForm', {
				'origin': origin,
				'width': 'calc(100% - 20px)',
				'left': '10px',
				'offset-y': 10
			});
		} else {
			toolsLightbox('filtersForm', {
				'origin': origin,
				'width': '60%',
				'left': getCssMenuWidth() + 'px',
				'offset-y': 10
			});
		}
	}
}