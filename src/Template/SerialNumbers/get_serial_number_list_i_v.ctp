
<?php  foreach($options as $option){
	echo $this->Form->input('sr_nos[]', ['hiddenField' => false,'label'=>false,'class'=>'form-control input-sm sr_no','type'=>'text','value'=>$option,'readonly'=>'readonly']); 
} ?>
