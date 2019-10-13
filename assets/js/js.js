/*
 Given a set of pages, builds the left menu
 */
function buildMenu(pages) {
	let cont = _('main-menu-ajaxcont');
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

function fillMenuSubCont(cont, parentIdx, pages, lvl) {
	pages.forEach((p, idx) => {
		let pageData = getLinkFromPage(p, idx);
		let link = pageData.link, click = pageData.click;

		let button = document.createElement('a');
		button.setAttribute('href', link);
		button.setAttribute('id', 'menu-group-' + parentIdx + '-' + idx);
		button.setAttribute('data-menu-id', parentIdx + '-' + idx);
		button.className = 'main-menu-sub';
		if (click)
			button.addEventListener('click', click);
		button.innerHTML = '<img src="' + absolute_path + 'model/AdminTemplateEditt/files/img/page.png" alt=""/> <span class="cont-testo-menu">' + entities(p['name']) + '</span>';
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

function getLinkFromPage(p, idx) {
	let link = '', click = null;

	if (p.path) {
		if (p.direct) {
			link = adminPrefix + p.path + '/edit/' + p.direct;
			click = function (event) {
				event.preventDefault();
				loadElement(p.path, p.direct);
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
function closeAllMenuGroups(except) {
	if (typeof except === 'undefined')
		except = [];
	document.querySelectorAll('.main-menu-sub, .main-menu-tasto').forEach(function (tasto) {
		if (!in_array(tasto.getAttribute('data-menu-id'), except)) {
			var cont = _('.main-menu-cont[data-menu-id="' + tasto.getAttribute('data-menu-id') + '"]');
			closeMenuGroup(tasto, cont);
		}
	});
}

/*
 Open the menù pages selecting a specific link
 */
function openMenuTo(id) {
	var tasto = _('menu-group-' + id);
	if (!tasto)
		return false;

	var toOpen = [];
	var div = tasto;
	while (div) {
		if (typeof div.getAttribute !== 'undefined' && div.getAttribute('data-menu-id') !== null)
			toOpen.push(div.getAttribute('data-menu-id'));
		div = div.parentNode;
	}

	closeAllMenuGroups(toOpen);
	toOpen.forEach(function (id) {
		var tasto = _('menu-group-' + id);
		var cont = _('menu-group-' + id + '-cont');
		openMenuGroup(tasto, cont);
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