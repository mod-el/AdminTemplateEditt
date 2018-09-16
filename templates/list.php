<input type="hidden" id="sId" value="<?= entities($sId) ?>"/>
<input type="hidden" id="sortedBy" value="<?= entities(json_encode($sortedBy)) ?>"/>
<input type="hidden" id="currentPage" value="<?= entities(json_encode($this->model->_Admin->paginator->pag)) ?>"/>

<?php
if (!isset($visualizer) or !$visualizer)
	return;

?>
<div class="pad20o no-overflow">
	<div id="results-table-count">
		<div><?= $this->model->_Admin->paginator->options['tot'] ?> risultati presenti</div>
		<span class="nowrap">[<a href="?nopag=1" onclick="allInOnePage(); return false"> tutti su una pagina </a>]</span>
	</div>

	<div id="results-table-pages">
		<?php
		$this->model->_Admin->paginator->render([
			'off' => '<a href="?sId=' . $sId . '&amp;p=[p]" onclick="goToPage([p]); return false" class="zkpag-off">[text]</a>',
		]);
		?>
	</div>
</div>

<form id="adminForm" onsubmit="if(saveButton = _('toolbar-button-save')) saveButton.onclick.call(saveButton); return false">
	<?php
	$visualizer->render([
		'list' => $list,
		'sortedBy' => $sortedBy,
		'draggable' => $draggable,
	]);
	?>
	<input type="submit" style="display: none"/>
</form>