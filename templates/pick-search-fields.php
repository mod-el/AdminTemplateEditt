<fieldset class="pad10">
    <form action="?" method="post" id="pick-search-fields-form" onsubmit="saveSearchFields(); return false">
        <?php $this->model->_CSRF->csrfInput(); ?>
        <h2>Cerca nei seguenti campi:</h2>
        <div class="pad10v text-center">
            <input type="submit" value="Salva preferenza" />
        </div>
        <?php
		$tableModel = $this->model->_Db->getTable($this->model->_Admin->options['table']);
		$columns = $tableModel->columns;

		$setColumns = isset($_COOKIE['model-admin-'.$this->model->_Admin->request[0].'-searchFields']) ? json_decode($_COOKIE['model-admin-'.$this->model->_Admin->request[0].'-searchFields'], true) : array();

		if($this->model->isLoaded('Multilang') and array_key_exists($this->model->_Admin->options['table'], $this->model->_Multilang->tables)){
			$mlTableOptions = $this->model->_Multilang->tables[$this->model->_Admin->options['table']];
			$mlTable = $this->model->_Admin->options['table'].$mlTableOptions['suffix'];
			$mlTableModel = $this->model->_Db->getTable($mlTable);
			foreach ($mlTableModel->columns as $k=>$col){
				if(isset($columns[$k]) or $k==$mlTableOptions['keyfield'] or $k==$mlTableOptions['lang'])
					continue;
				$columns[$k] = $col;
			}
		}

        foreach($columns as $k=>$col){
			if($this->model->_Admin->options['primary']==$k or $col['foreign_key'] or !in_array($col['type'], [
                'tinyint',
                'smallint',
                'int',
                'mediumint',
                'bigint',
                'decimal',
                'varchar',
                'char',
                'text',
                'tinytext',
                'enum',
            ])){
				continue;
			}
            ?>
            <div>
                <input type="checkbox" name="<?=$k?>" data-managesearchfields="<?=$k?>" id="campo-<?=$k?>"<?=(!$setColumns or in_array($k, $setColumns)) ? ' checked' : ''?> />
                <label for="campo-<?=$k?>"><?=entities($k)?></label>
            </div>
            <?php
        }
        ?>
        <div class="pad10v text-center">
            <input type="submit" value="Salva preferenza" />
        </div>
    </form>
</fieldset>