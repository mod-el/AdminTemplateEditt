<?php
if ($this->model->isLoaded('Multilang')) {
	$hasMultilang = false;
	foreach ($this->model->_Admin->form->getDataset() as $d) {
		if ($d->options['multilang']) {
			$hasMultilang = true;
		}
	}

	if ($hasMultilang) {
		$def_lang = $this->model->_Multilang->options['default'];

		echo '<div class="lang-switch-cont">';
		foreach ($this->model->_Multilang->langs as $l) {
			echo '<a href="#" onclick="switchAllFieldsLang(\'' . $l . '\'); return false"><img src="' . PATH . 'model/Form/files/img/langs/' . $l . '.png" alt="" data-lang="' . $l . '"' . ($l === $def_lang ? ' class="selected"' : '') . ' /></a>';
		}
		echo '</div>';
	}
}

$this->model->_Admin->form->render();

foreach ($this->model->_Admin->sublists as $s) {
	echo '<hr />';
	$this->model->_AdminFront->renderSublist($s['name'], $s['options']);
}
