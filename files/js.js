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
	var button = document.querySelector('.main-menu-tasto[href="' + adminPrefix + request[0] + '"], .main-menu-sub[href="' + adminPrefix + request[0] + '"]');
	if (button)
		openMenuTo(button.getAttribute('data-menu-id'));
	else
		closeAllMenuGroups();
}