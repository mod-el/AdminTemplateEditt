<?php
if ($this->model->isLoaded('Multilang')) {
	$hasMultilang = false;
	foreach ($this->model->_Admin->form->getDataset() as $d) {
		if ($d->options['multilang']) {
			$hasMultilang = true;
		}
	}

	if ($hasMultilang)
		$this->model->_Admin->form->renderLangSelector();
}

$this->model->_Admin->form->render();

foreach ($this->model->_Admin->sublists as $s) {
	echo '<hr />';
	$this->model->_AdminFront->renderSublist($s['name'], $s['options']);
}
