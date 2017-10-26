<input type="hidden" id="sId" value="<?=entities($this->options['data']['sId'])?>" />
<input type="hidden" id="sortedBy" value="<?=entities(json_encode($this->options['data']['sortedBy']))?>" />
<input type="hidden" id="currentPage" value="<?=entities(json_encode($this->options['data']['current-page']))?>" />

<div class="pad20o no-overflow">
    <div id="results-table-count">
        <div><?=$this->options['data']['tot']?> risultati presenti</div>
        <span class="nowrap">[<a href="?nopag=1" onclick="allInOnePage(); return false"> tutti su una pagina </a>]</span>
    </div>

    <div id="results-table-pages">
        <?php
		$this->model->_Admin->paginator->render([
            'off' => '<a href="?sId='.$this->options['data']['sId'].'&amp;p=[p]" onclick="goToPage([p]); return false" class="zkpag-off">[text]</a>',
        ]);
        ?>
    </div>
</div>

<div id="table-headings">
    <div>
        <div class="special-cell" style="padding: 0 5px">
            <input type="checkbox" onchange="if(this.checked) selectAllRows(1); else selectAllRows(0)" />
        </div>
        <?php
        $mainDeletePrivilege = $this->model->_Admin->canUser('D');
        if($mainDeletePrivilege){
			?>
            <div class="special-cell"></div>
			<?php
        }

        foreach($this->options['data']['columns'] as $column_id=>$f){
            $sorted = false;
            foreach($this->options['data']['sortedBy'] as $idx=>$s){
                if($s[0]==$column_id){
                    $sorted = [
                        'dir'=>$s[1],
                        'idx'=>$idx+1,
                    ];
                    break;
                }
            }
            ?>
            <div style="width: <?=$this->model->_ResizeTable->widths[$column_id]?>px" data-column="<?=$column_id?>" id="column-<?=$column_id?>">
                <div class="table-headings-resize" onmousedown="startColumnResize(event, '<?=$column_id?>'); event.stopPropagation(); event.preventDefault()" ondblclick="autoResize('<?=$column_id?>')" data-context-menu="{'Ottimizza':function(){ autoResize('<?=$column_id?>'); }, 'Ottimizza colonne':function(){ autoResize(false); }}"></div>
                <div class="table-headings-label<?=$f['sortable'] ? ' sortable' : ''?><?=$sorted ? ' selected' : ''?>"<?php
                if($f['sortable']){
                    echo ' onclick="changeSorting(event, \''.$column_id.'\')"';
                }
                ?>><?=entities($f['label'])?><?php
                if($sorted){
                    echo $sorted['dir']=='ASC' ? ' &uarr;' : ' &darr;';
                    echo ' &sup'.$sorted['idx'].';';
                }
                ?></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<div id="results-table">
	<?php
    $c_row = 0;
    foreach($this->options['data']['elements'] as $id => $el){
		$form = false;
		$clickable = $this->model->_Admin->canUser('R', false, $el['element']);
        ?>
        <div>
            <div class="results-table-row" data-n="<?=$c_row++?>" data-id="<?=$id?>" data-clickable="<?=$clickable?>" style="<?=$el['background'] ? 'background: '.entities($el['background']).';' : ''?><?=$el['color'] ? 'color: '.entities($el['color']).';' : ''?>">
                <div class="special-cell" onmousedown="event.stopPropagation()" onmouseup="event.stopPropagation()" onclick="event.stopPropagation(); var check = this.firstElementChild.firstElementChild; if(check.getValue()) check.setValue(0); else check.setValue(1);">
                    <div>
                        <input type="checkbox" value="1" id="row-checkbox-<?=$id?>" data-id="<?=$id?>" onchange="selectRow('<?=$id?>', this.checked ? 1 : 0)" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()" onmouseup="event.stopPropagation()" />
                    </div>
                </div>
                <?php
				$canDelete = $this->model->_Admin->canUser('D', false, $el['element']);
				if($canDelete){
					?>
                    <div class="special-cell" onmousedown="event.stopPropagation()" onclick="event.stopPropagation()">
                        <div>
                            <a href="#" onclick="event.stopPropagation(); deleteRows(['<?=$id?>']); return false"><img src="<?=PATH?>model/AdminTemplateEditt/files/img/delete.png" alt="" style="vertical-align: middle" /></a>
                        </div>
                    </div>
					<?php
                }
				foreach($this->options['data']['columns'] as $column_id=>$f) {
					$c = $el['columns'][$column_id];
					?>
                    <div
                      style="<?=$c['background'] ? 'background: '.entities($c['background']).';' : ''?><?=$c['color'] ? 'color: '.entities($c['color']).';' : ''?>width: <?=$this->model->_ResizeTable->widths[$column_id]?>px"
                      data-column="<?=$column_id?>"
                      data-value="<?=entities($c['value'])?>"
                      title="<?=strip_tags($c['text'])?>"
                      <?php
                      if(!$f['clickable'] or $f['editable'])
                          echo ' onmousedown="event.stopPropagation()" onmouseup="event.stopPropagation()" onclick="event.stopPropagation()"';
                      if($f['editable'])
                          echo ' class="editable-cell"';
                      ?>>
                        <div>
                            <?php
                            if($f['editable']){
                                if($form===false)
                                    $form = $el['element']->getForm();
                                if(isset($form[$f['field']])){
									$form[$f['field']]->render([
										'hide-label' => '',
                                        'onchange' => 'instantSave(\''.$id.'\', \''.entities($f['field']).'\', this)',
                                    ]);
                                }
                            }else{
                                echo $c['text'];
                            }
                            ?>
                        </div>
                    </div>
					<?php
				}
				?>
            </div>
        </div>
        <?php
    }

	$totals = $this->options['data']['totals'];
	if(!empty($totals)){
		?>
        <div class="results-table-row" style="top: 0px" id="totals-row">
            <div class="special-cell"><div></div></div>
            <?php
		    if($mainDeletePrivilege) {
				?>
                <div class="special-cell">
                    <div></div>
                </div>
				<?php
			}
            $free_cells = 0;
            foreach($this->options['data']['columns'] as $column_id=>$f){
                if(isset($totals[$column_id]))
                    break;
                $free_cells++;
            }

            $dummy = $this->model->_ORM->create($this->model->_Admin->options['element'], ['table'=>$this->model->_Admin->options['table']]);
            $dummyForm = $dummy->getForm();

            $cc = 0; $totals_width = 0;
            foreach($this->options['data']['columns'] as $column_id=>$f){
                $cc++;

                if($cc<=$free_cells){
                    $totals_width += $this->model->_ResizeTable->widths[$column_id];
                    if($cc==$free_cells){
                        ?><div style="width: <?=$totals_width?>px"><div style="text-align: right; font-weight: bold">Totali:</div></div><?php
                    }
                    continue;
                }

                ?><div style="width: <?=$this->model->_ResizeTable->widths[$column_id]?>px" data-column="<?=$column_id?>"><div><?php
                    if(isset($totals[$column_id])){
                        if(is_string($f['field']) and isset($dummyForm[$f['field']]) and $dummyForm[$f['field']]->options['type']=='price')
                            echo makePrice($totals[$column_id]);
                        else
                            echo $totals[$column_id];
                    }
                    ?></div></div><?php
            }
		    ?>
        </div>
        <?php
	}
	?>
</div>