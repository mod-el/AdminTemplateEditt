<!DOCTYPE html>

<style type="text/css">
    table{
        border-collapse: collapse;
    }

    td{
        border: solid #999 1px;
        font-size: 11px;
        padding: 5px;
    }
</style>

<script type="text/javascript">
	window.onload = function(){
		window.print();
	}
</script>

<div style="width: 210mm">
    <div style="padding-bottom: 10px">
		<?php
        if(file_exists(INCLUDE_PATH.'img'.DIRECTORY_SEPARATOR.'logo.png')){
            ?><a href="<?=$this->model->_Admin->getUrlPrefix()?>"><img src="<?=PATH?>img/logo.png" alt="" style="max-width: 30%" /></a><?php
        }else{
            ?><span style="font-size: 26px"><?=entities(APP_NAME)?></span><?php
        }
        ?>
    </div>

    <table>
        <tr>
			<?php
			foreach($this->options['data']['columns'] as $column_id => $f){
				if(!$f['print'])
					continue;
				?><td style="font-weight: bold"><?=entities($f['label'])?></td><?php
			}
			?>
        </tr>

		<?php
		foreach($this->options['data']['elements'] as $id => $el){
			?>
            <tr>
				<?php
				foreach($this->options['data']['columns'] as $column_id => $f) {
					if(!$f['print'])
						continue;

					$c = $el['columns'][$column_id];

					$cellBackground = $c['background'] ?: $el['background'];
					$cellColor = $c['color'] ?: $el['color'];
					?>
                    <td style="<?=$cellBackground ? 'background: '.entities($cellBackground).';' : ''?><?=$cellColor ? 'color: '.entities($cellColor).';' : ''?>">
                        <?=$c['text']?>
                    </td>
                    <?php
				}
				?>
            </tr>
			<?php
		}

		$totals = $this->options['data']['totals'];
		if(!empty($totals)){
			$free_cells = 0;
			foreach($this->options['data']['columns'] as $column_id => $f){
			    if(!$f['print'])
			        continue;
				if(isset($totals[$column_id]))
					break;
				$free_cells++;
			}

			$dummy = $this->model->_ORM->create($this->model->_Admin->options['element'] ?: '\\Model\\ORM\\Element', ['table' => $this->model->_Admin->options['table']]);
			$dummyForm = $dummy->getForm();

			?><tr><?php
			if($free_cells>0)
				echo '<td colspan="'.$free_cells.'" style="text-align: right; font-weight: bold">Totali:</td>';

			$cc = 0;
			foreach($this->options['data']['columns'] as $column_id=>$f){
				if(!$f['print'])
					continue;
				$cc++;
				if($cc<=$free_cells)
					continue;

				?><td><?php
				if(isset($totals[$column_id])){
					if(is_string($f['field']) and isset($dummyForm[$f['field']]) and $dummyForm[$f['field']]->options['type']=='price')
						echo makePrice($totals[$column_id]);
					else
						echo $totals[$column_id];
				}
				?></td><?php
			}
			?></tr><?php
		}
		?>
    </table>
</div>