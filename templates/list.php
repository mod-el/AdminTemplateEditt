<input type="hidden" id="sId" value="<?= entities($this->options['sId']) ?>"/>
<input type="hidden" id="sortedBy" value="<?= entities(json_encode($this->options['sortedBy'])) ?>"/>
<input type="hidden" id="currentPage" value="<?= entities(json_encode($this->model->_Admin->paginator->pag)) ?>"/>

<?php
if (!$this->options['visualizer'])
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
			'off' => '<a href="?sId=' . $this->options['sId'] . '&amp;p=[p]" onclick="goToPage([p]); return false" class="zkpag-off">[text]</a>',
		]);
		?>
    </div>
</div>

<form id="adminForm" onsubmit="if(saveButton = _('toolbar-button-save')) saveButton.onclick.call(saveButton); return false">
	<?php
	$this->options['visualizer']->render([
		'list' => $this->options['list'],
		'sortedBy' => $this->options['sortedBy'],
		'draggable' => $this->options['draggable'],
	]);
	?>
    <input type="submit" style="display: none"/>
</form>