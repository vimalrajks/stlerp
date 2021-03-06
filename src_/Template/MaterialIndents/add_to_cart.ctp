
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption" >
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Add Material Indent</span>
		</div>
		
	</div>

	<?php// pr($ItemBuckets); exit; ?>
	<div class="portlet-body form">
		<!-- BEGIN FORM-->
		 <?= $this->Form->create($materialIndent,['id'=>'form_sample_3']) ?>
		<div class="form-body">
			<table class="table tableitm">
				<thead>
					<tr>
						<th>Sr.No. </th>
						<th>Items name</th>
						<th>Suggested Indent Quantity</th>
						<th>Quantity</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$q=0;
					
					foreach ($ItemBuckets as $ItemBucket){
					 ?>
					
					
					<tr class="tr1" > 
							<td><?php echo ++$q;?></td>
							
							<td><?php echo $this->Form->input('material_indent_rows.'.$q.'.item_id', ['label' => false,'type'=>'hidden','value'=>$ItemBucket['item_id'],'class'=>'itemids']); ?>
							<?php echo $this->Form->input('q', ['label' => false,'type'=>'hidden','value'=>$ItemBucket['item']['item_companies'][0]['serial_number_enable'],'class'=>'sr_nos']); ?>
							<?php echo $ItemBucket['item']['name']; ?></td>
							<td><?php echo abs(@$ItemBucket['quantity']); ?></td>
							<td><?php echo $this->Form->input('material_indent_rows.'.$q.'.required_quantity', ['label' => false,'type'=>'text','required','class'=>'quantity']); ?></td>
							<td>
							<?= $this->Html->link('Delete ',
									['action' => 'delete', $ItemBucket->id], 
									[
										'escape' => false,
										'confirm' => __('Are you sure, you want to delete {0}?', $ItemBucket->id)
									]
								) ?>
						
							</td>
					</tr>
					<?php } ?>
					
				</tbody>
				
			</table>
		</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary" id='submitbtn'>CREATE MATERIAL INDENT</button>
			</div>
		<?= $this->Form->end() ?>
	</div>
	
<style>
.table thead tr th {
    color: #FFF;
	background-color: #254b73;
}
.padding-right-decrease{
	padding-right: 0;
}
.padding-left-decrease{
	padding-left: 0;
}
</style>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>

<script>

$(document).ready(function() { 
//--------- FORM VALIDATION
	var form3 = $('#form_sample_3');
	var error3 = $('.alert-danger', form3);
	var success3 = $('.alert-success', form3);
	form3.validate({
		errorElement: 'span', //default input error message container
		errorClass: 'help-block help-block-error', // default input error message class
		focusInvalid: true, // do not focus the last invalid input
	});
	
	$('.quantity').die().live("keyup",function() {
		var tr_obj=$(this).closest('tr');  
		var item_id=tr_obj.find('td:nth-child(2) input.itemids').val()
		
		if(item_id > 0){ 
			var serial_number_enable=tr_obj.find('td:nth-child(2) input.sr_nos').val();
			
				if(serial_number_enable == '1'){
					var quantity=tr_obj.find('td:nth-child(4) input').val();
					 if(quantity.search(/[^0-9]/) != -1)
						{
							alert("Item serial number is enabled !!! Please Enter Only Digits")
							tr_obj.find('td:nth-child(4) input').val("");
						}
					
				}
		}
		
    });	
	
	
	/* $('.quantity').die().live("keyup",function() {
			var asc=$(this).val();
			var numbers =  /^[0-9]*\.?[0-9]*$/;
			if(asc==0)
			{
				$(this).val('');
				return false; 
			}
			else if(asc.match(numbers))  
			{  
			} 
			else  
			{  
				$(this).val('');
				return false;  
			}
	}); */

});
</script>	
