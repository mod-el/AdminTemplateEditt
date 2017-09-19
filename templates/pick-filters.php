<fieldset class="pad10" style="width: 1000px">
    <form action="#" method="post" id="pick-filters-form" onsubmit="saveFilters(); return false">
		<?php csrfInput(); ?>
        <h2>Seleziona i filtri:</h2>
        <div class="pad5v text-center">
            <input type="submit" value="Salva preferenza" />
        </div>
		<?php
        $forms = $this->model->_AdminTemplateEditt->getFiltersForms(true);
		$currentFilters = [];
		foreach($forms['top'] as $k => $t){
			$currentFilters[$k] = [
				'type' => $t,
				'form' => 'top',
			];
		}
		foreach($forms['filters'] as $k => $t){
			$currentFilters[$k] = [
				'type' => $t,
				'form' => 'filters',
			];
		}

		$table = $this->model->_Db->getTable($this->model->_Admin->options['table']);

		$campi = [
			'all' => true,
		];
		foreach($table->columns as $k=>$col) {
			if (in_array($k, ['id', 'zk_deleted', 'zkversion'])/* or $k === $this->model->_Admin->options['gestisci_ordine']*/)
				continue;
			$campi[$k] = $col;
		}

		$customFilters = $this->model->_Admin->getCustomFiltersForm()->getDataset();
		foreach($customFilters as $k=>$d){
			if(!isset($campi[$k]))
				$campi[$k] = true;
		}

		foreach($campi as $k=>$col){
			?>
            <div class="grid">
                <div class="w25" style="vertical-align: middle">
					<?=entities($k=='all' ? 'Ricerca generale' : $k)?>
                </div>
                <div class="w75" style="vertical-align: middle">
                    <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-0" value="0"<?=!isset($currentFilters[$k]) ? ' checked' : ''?> />
                    <label for="campo-<?=$k?>-type-0">No</label>
                    <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-=" value="="<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='=') ? ' checked' : ''?> />
                    <label for="campo-<?=$k?>-type-=">S&igrave;</label>
					<?php
                    if($k!=='all'){
						$numeric = false;
						if($col!==true and !$col['foreign_key']){
							switch($col['type']){
								case 'tinyint':
									/*if(!isset($form[$k]) or !in_array($form[$k]->type, ['checkbox', 'radio', 'select']))
										$numeric = true;
									break;*/
								case 'smallint':
								case 'int':
								case 'mediumint':
								case 'bigint':
								case 'decimal':
								case 'float':
								case 'date':
								case 'datetime':
								case 'time':
									$numeric = true;
									break;
							}
						}

						if($numeric){
							?>
                            <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-range" value="range"<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='range') ? ' checked' : ''?> />
                            <label for="campo-<?=$k?>-type-range">Range</label>
                            <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-<" value="<"<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='<') ? ' checked' : ''?> />
                            <label for="campo-<?=$k?>-type-<">&lt;</label>
                            <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-<=" value="<="<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='<=') ? ' checked' : ''?> />
                            <label for="campo-<?=$k?>-type-<=">&lt;=</label>
                            <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type->=" value=">="<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='>=') ? ' checked' : ''?> />
                            <label for="campo-<?=$k?>-type->=">&gt;=</label>
                            <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type->" value=">"<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='>') ? ' checked' : ''?> />
                            <label for="campo-<?=$k?>-type->">&gt;</label>
							<?php
						}else{
							?>
							<?php
							if($col!==true and in_array($col['type'], ['char', 'varchar', 'tinytext', 'smalltext', 'text', 'mediumtext', 'bigtext'])){
								?>
                                <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-contains" value="contains"<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='contains') ? ' checked' : ''?> />
                                <label for="campo-<?=$k?>-type-contains">Contiene...</label>
                                <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-starts" value="starts"<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='starts') ? ' checked' : ''?> />
                                <label for="campo-<?=$k?>-type-starts">Inizia...</label>
								<?php
							}
						}
						?>
                        <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-!=" value="!="<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='!=') ? ' checked' : ''?> />
                        <label for="campo-<?=$k?>-type-!=">!=</label>
                        <input type="radio" name="<?=$k?>-type" data-managefilters="<?=$k?>-type" id="campo-<?=$k?>-type-empty" value="empty"<?=(isset($currentFilters[$k]) and $currentFilters[$k]['type']=='empty') ? ' checked' : ''?> />
                        <label for="campo-<?=$k?>-type-empty">Vuoto</label>
                        <?php
                    }
					?>
                </div>
                <div class="w2" style="vertical-align: middle">
					<?php
					if(isset($currentFilters[$k])){
						$selectedForm = $currentFilters[$k]['form'];
					}else{
						$selectedForm = 'filters';
					}
					?>
                    <input type="radio" name="<?=$k?>-form" data-managefilters="<?=$k?>-form" id="campo-<?=$k?>-form-top" value="top"<?=$selectedForm=='top' ? ' checked' : ''?> />
                    <label for="campo-<?=$k?>-form-top">Top</label>
                    <input type="radio" name="<?=$k?>-form" data-managefilters="<?=$k?>-form" id="campo-<?=$k?>-form-filters" value="filters"<?=$selectedForm=='filters' ? ' checked' : ''?> />
                    <label for="campo-<?=$k?>-form-filters">Sec</label>
                </div>
            </div>
			<?php
		}
		?>
        <div class="pad5v text-center">
            <input type="submit" value="Salva preferenza" />
        </div>
    </form>
</fieldset>