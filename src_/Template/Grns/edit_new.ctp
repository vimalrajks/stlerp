<?php $this->Form->templates([
     'inputContainer' => '{{content}}'
                                               ]); 
?>
<?php 	$first="01";
		$last="31";
		$start_date=$first.'-'.$financial_month_first->month;
		$end_date=$last.'-'.$financial_month_last->month;
		$start_date=strtotime(date("Y-m-d",strtotime($start_date)));
		$transaction_date=strtotime($grn->transaction_date);
if($transaction_date <  $start_date ) {
	echo "Financial Month has been Closed";
} else { ?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Edit Goods Receipt Note</span>
		</div>
	</div>
	
	<div class="portlet-body form">
		<?= $this->Form->create($grn,['id'=>'form_sample_3']) ?>
		<?php 	$first="01";
				$last="31";
				$start_date=$first.'-'.$financial_month_first->month;
				$end_date=$last.'-'.$financial_month_last->month;
				//pr($start_date); exit;
		?>
		<div class="form-body">
			<div class="form-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Company <span class="required" aria-required="true">*</span></label>
							<br/>
							<?php echo @$grn->company->name; ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">GRN No. <span class="required" aria-required="true">*</span></label>
							<div class="row">
								<div class="col-md-4">
									<?php echo $this->Form->input('grn1', ['label' => false,'class' => 'form-control input-sm','readonly','value'=>@$grn->company->alias]); ?>
								</div>
								<div class="col-md-4">
									<?php echo $this->Form->input('grn3', ['label' => false,'class' => 'form-control input-sm','placeholder'=>'File', 'value'=>@$grn->grn3,'readonly']); ?>
								</div>
								<div class="col-md-4">
									<?php echo $this->Form->input('grn4', ['label' => false,'class' => 'form-control input-sm','readonly']); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Transaction Date</label>
							<br/>
							<?php echo $this->Form->input('transaction_date', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','data-date-start-date' => $start_date,'data-date-end-date' => $end_date,'value' => date("d-m-Y",strtotime($grn->transaction_date)),'required']); ?>
						</div>
						<span style="color: red;"><?php if($chkdate == 'Not Found'){  ?>
					You are not in Current Financial Year
				<?php } ?></span>
					</div>
				</div><br/>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Supplier <span class="required" aria-required="true">*</span></label>
							<br/>
							<?php echo @$grn->vendor->company_name; ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Road Permit No<span class="required" aria-required="true">*</span></label>
							<?php echo $this->Form->input('road_permit_no', ['label' => false,'class' => 'form-control input-sm','placeholder' => 'Road permit No']); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Narration</label>
							<?php echo $this->Form->input('narration', ['type' => 'textarea','label' => false,'class' => 'form-control input-sm ','placeholder' => 'Narration']); ?>
						</div>
					</div>
				</div>
			
				<table class="table tableitm" id="main_tb">
					<thead>
						<tr>
							<th width="10%">Sr.No. </th>
							<th width="70%">Items</th>
							<th width="15%">Quantity</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
					
						<?php  
							$existing_rows=[];
							$current_rows=[];
							$current_row_items=[];
							foreach($grn->purchase_order->grns as $data){
								foreach($data->grn_rows as $data2){
									$existing_rows[$data2->item_id]=@$existing_rows[$data2->item_id]+$data2->quantity;
								}
							}
							
							foreach($grn->grn_rows as $current_invoice_row){ 
							
							//pr($current_invoice_row); 
								@$existing_rows[$current_invoice_row->item_id]=$existing_rows[$current_invoice_row->item_id]-$current_invoice_row->quantity;
								$current_rows[]=$current_invoice_row->purchase_order_row_id;
								$current_row_items[$current_invoice_row->purchase_order_row_id]=$current_invoice_row->quantity;
								$descriptions[$current_invoice_row->item_id]=$current_invoice_row->description;
								
							}
							$grnRowIds=[];
							foreach ($grn->grn_rows as $grn_row)
							{
								$grnRowIds[$grn_row->purchase_order_row_id] = $grn_row->id;
							}
							$serial_no =[];
							foreach ($grn->grn_rows as $grn_row)
							{
								$serial_no[$grn_row->purchase_order_row_id] = $grn_row->serial_numbers;
							}
							$q=0; foreach ($grn->purchase_order->purchase_order_rows as $grn_rows):
							if($actuleQty[$grn_rows->id]>0){
							?>
							
							<tr class="tr1" row_no='<?php echo @$grn_rows->id; ?>'>
								<td rowspan="2"><?php echo ++$q; --$q; ?></td>
								<td>
									<?php echo $this->Form->input('q', ['type' => 'hidden','value'=>@$grn_rows->item_id,'class'=>'item']); 
									echo $this->Form->input('q', ['type' => 'hidden','value'=>@$grn_rows->item->item_companies[0]->serial_number_enable]);
									echo $grn_rows->item->name;
									echo $this->Form->input('q', ['type' => 'hidden','class'=>'purchase_order_row_id','value'=>@$grn_rows->id]);
									echo $this->Form->input('q', ['type' => 'hidden','class'=>'grn_row_id','value'=>@$grnRowIds[$grn_rows->id]]);
									?>								
								</td>
								<td>
								<?php  
								
								if(!empty($serial_no[$grn_rows->id]))
									{   $old_Quantity=[];
								        foreach($serial_no[$grn_rows->id] as $old_quantity)
										{
											$old_Quantity[] =$old_quantity; //$serial_no[$grn_rows->id];
										}
									}
									else{
										unset($old_Quantity);
										$old_Quantity =1;
									}
																	
								echo $this->Form->input('q', ['type' => 'text','label' => false,'class' => 'form-control input-sm quan quantity','placeholder' => 'Quantity','value' => @$current_row_items[$grn_rows->id],'max'=>@$maxQty[$grn_rows->id],'min'=>sizeof(@$old_Quantity)]); 
								?>
								<span>Max: <?php
								if(!empty($maxQty[$grn_rows->id]))
								{
									echo @$maxQty[$grn_rows->id];
								}
								else
								{
									echo $grn_rows->quantity;
								}
								?></span>
								
								</td>
								<td>
									<label class="hide_lebel"><?php 
									if(in_array($grn_rows->id,$current_rows)){
									$check='checked';
										}else{
											$check=' ';
										}
									$old_Quantity=[];
									/* pr($grn_rows);
									foreach($grn_rows->grn_rows[0]->serial_numbers as $serial_number)
									{ */
									if(!empty($serial_no[$grn_rows->id]))
									{
										foreach($serial_no[$grn_rows->id] as $old_quantity)
										{
											$old_Quantity[] =$old_quantity; //$serial_no[$grn_rows->id];
										}
									}
										//$serial_number;
									//}
									$disable='';
									
									if(sizeof(@$serial_no[$grn_rows->id])>0)
									{
										$disable = 'disabled';
									}
									else{
										$disable='';
									}
									echo $this->Form->input('check.'.$q, ['label' => false,'type'=>'checkbox','class'=>'rename_check','old_qty_size'=>sizeof($old_Quantity),'old_qty'=>@$current_row_items[$grn_rows->item_id],'value' => @$grn_rows->id,$check,'max_qty'=>$grn_rows->quantity-@$existing_rows[$grn_rows->item_id],@$disable]); ?></label>
								</td>
								
							</tr>
							<tr class="tr2" row_no='<?php echo @$grn_rows->id; ?>'>

								<?php  $i=1; 
								if(!empty($grn_rows->grn_rows[0]->serial_numbers)){
								foreach($grn_rows->grn_rows[0]->serial_numbers as $serial_number){
									if($serial_number->item_id == $grn_rows->item_id){ ?>

									<div style="margin-bottom:6px;">
									
									</div>
								<?php  $i++;  }  }}?><br/>
								<td colspan="2" class="td_append">
								
								</td>
								<td colspan="1" class="demo">

								<?php   
								if(!empty($serial_no[$grn_rows->id]))
								{$i=1;
								//pr($serial_no);
								foreach($serial_no[$grn_rows->id] as $serial_number){
									if($serial_number->item_id == $grn_rows->item_id){
									 
									if($i==1)
									{
										$count_serial_no = sizeof($grn->serial_numbers);
										echo $this->Form->input('count_serial_no', ['label' => false,'type'=>'hidden','id'=>'count_serial_no','value' => $count_serial_no,'readonly']);
									}
								
								?>
										<?php if($serial_number->status=='Out'){  ?>

										<div class="row">
										<div class="col-md-10"><?php echo $this->Form->input('q', ['label' => false,'type'=>'text','value' => $serial_number->name,'readonly']); ?></div>
										</div>
										<div class="col-md-2"></div>
										<?php  } else { ?>
										<div class="row">
										<div class="col-md-10"><?php echo $this->Form->input('q', ['label' => false,'type'=>'text','value' => $serial_number->name,'readonly']); ?></div>
										
										<div class="col-md-2">
										<?php 
										if(@$parentSerialNo[$serial_number->id]!=$serial_number->id){ ?>
											<?= $this->Html->link('<i class="fa fa-trash"></i> ',
													['action' => 'DeleteSerialNumbers', $serial_number->id, $serial_number->item_id,$grn->id,$grnRowIds[$grn_rows->id]], 
													[
														'escape' => false,
														'class' => 'btn btn-xs red',
														'confirm' => __('Are you sure, you want to delete {0}?', $serial_number->id)
													]
												) ?>
										<?php } ?>
										</div>
										</div>
								<?php  $i++; } }  }}
									echo $this->Form->input('q', ['type'=>'hidden','value' => @$grn_rows->id,'class'=>'hid']); ?>
								</td>
							</tr>
							<?php  $q++; }
							?>
						<?php  endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php echo $this->Form->input('purchase_order_id', ['type' => 'hidden','value'=>@$grn->purchase_order_id]); ?>
			<div class="form-actions">
				<div class="row">
					<div class="col-md-offset-3 col-md-9">
					<?php if($chkdate == 'Not Found'){  ?>
						<label class="btn btn-danger"> You are not in Current Financial Year </label>
					<?php } else { ?>
						<button type="submit" class="btn btn-primary">EDIT GRN</button>	
					<?php } ?>						
					</div>
				</div>
			</div>
		</div>
		<?= $this->Form->end() ?>
	</div>
</div>	
<?php } ?>
<style>
.table thead tr th {
    color: #FFF;
	background-color: #254b73;
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
		rules: {
			grn1:{
				required: true,
			},
			grn3:{
				required: true,
			},
			grn4:{
				required: true,
			},
			
		},
		errorPlacement: function (error, element) { // render error placement for each input type
			if (element.parent(".input-group").size() > 0) {
				error.insertAfter(element.parent(".input-group"));
			} else if (element.attr("data-error-container")) { 
				error.appendTo(element.attr("data-error-container"));
			} else if (element.parents('.radio-list').size() > 0) { 
				error.appendTo(element.parents('.radio-list').attr("data-error-container"));
			} else if (element.parents('.radio-inline').size() > 0) { 
				error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
			} else if (element.parents('.checkbox-list').size() > 0) {
				error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
			} else if (element.parents('.checkbox-inline').size() > 0) { 
				error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
			} else {
				error.insertAfter(element); // for other inputs, just perform default behavior
			}
		},

		invalidHandler: function (event, validator) { //display error alert on form submit   
			success3.hide();
			error3.show();
			Metronic.scrollTo(error3, -200);
		},

		highlight: function (element) { // hightlight error inputs
		   $(element)
				.closest('.form-group').addClass('has-error'); // set error class to the control group
		},

		unhighlight: function (element) { // revert the change done by hightlight
			$(element)
				.closest('.form-group').removeClass('has-error'); // set error class to the control group
		},

		success: function (label) {
			label
				.closest('.form-group').removeClass('has-error'); // set success class to the control group
		},

		submitHandler: function (form) {
			
			success1.show();
			error1.hide();
			form[0].submit(); // submit the form
		}

	});
	//--	 END OF VALIDATION
	
	$('.quan').die().live("keyup",function() {
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
	});
	update_sr_textbox();
	//$('.update_serial_number').on("click",function() {
		function update_sr_textbox(){
		var r=0;
		$("#main_tb tbody tr.tr1").each(function(){
			var row_no=$(this).attr('row_no');
			var serial_number_enable=$(this).find('td:nth-child(2) input[type="hidden"]:nth-child(2)').val();
			var val=$(this).find('td:nth-child(4) input[type="checkbox"]:checked').val(); 
			var qty=$(this).find('td:nth-child(3) input[type="text"]').val();
			var item_id=$(this).find('td:nth-child(2) input[type="hidden"]:nth-child(1)').val(); 
			var l=$('.tr2[row_no="'+row_no+'"]').find('input.sr_no').length; 
		//alert(serial_number_enable);
				if(val && serial_number_enable=='1')
				{ 
					if(qty.search(/[^0-9]/) != -1)
					{
						alert("Item serial number is enabled !!! Please Enter Only Digits");
						$(this).find('td:nth-child(3) input[type="text"]').val('');
					}
					else
					{ //alert(qty+' < '+l);
						if(qty < l){
							
							for(i=l;i>qty;i--){ 
							$('.tr2[row_no="'+row_no+'"]').find('input[ids="sr_no['+i+']"]').remove();
							
							}
						}
						if(qty > l)
						{ 
							l=l+1;
							//alert(l);
							var old_qty=parseInt($(this).find('td:nth-child(4) input[type="checkbox"]:checked').attr('old_qty_size'));
							if(old_qty!=qty)
							{
								for(i=l;i<=qty;i++){
								
								$('.tr2[row_no="'+row_no+'"] td:nth-child(1)').append('<div style="margin-bottom:6px;"><input type="text" class="sr_no" name="grn_rows['+val+'][serial_numbers]['+r+']" ids="sr_no['+i+']" id="sr_no'+r+row_no+'" required/></div>');
								
								$('.tr2[row_no="'+row_no+'"] td:nth-child(1)').find('input#sr_no'+r+row_no).rules('add', {required: true});
								r++;	
								$('.tr2[row_no="'+row_no+'"]').find('input[ids="sr_no['+i+']"]').remove();
								}
							}
						}
					}
				}
				
				else{
					$('tr.tr2[row_no="'+row_no+'"] td:nth-child(1)').find('input.sr_no').remove();
				}
			
		});
	}
		
	
	$('.rename_check').die().live("click",function() { 
		rename_rows();
		update_sr_textbox();
    });
	
	$('.quantity').die().live("blur",function() {
			var is_checked=$(this).closest('tr').find('td:nth-child(4) input[type="checkbox"]:checked').val();
			var count_serial_no = $('#count_serial_no').val();
			var serial_number_enable=$(this).closest('tr').find('td:nth-child(2) input[type="hidden"]:nth-child(2)').val();
			var qty=parseInt($(this).closest('tr').find('td:nth-child(3) input[type="text"]').val());
			var maxQty=parseInt($(this).closest('tr').find('td:nth-child(3) input[type="text"]').attr('max'));
			var old_qty=parseInt($(this).closest('tr').find('td:nth-child(4) input[type="checkbox"]:checked').attr('old_qty_size'));
			if(!old_qty){ old_qty=0; }
			
			var row_no=$(this).closest('tr').attr('row_no');
			var l=$('.tr2[row_no="'+row_no+'"]').find('input').length;
			var item_id=$(this).closest('tr').find('td:nth-child(2) input[type="hidden"]:nth-child(1)').val();
			var val=$(this).closest('tr').find('td:nth-child(4) input[type="checkbox"]:checked').val();
			/*//alert(val);
 			console.log(is_checked);
			console.log(serial_number_enable);
			console.log(old_qty);
			console.log(row_no); */
			if(is_checked && serial_number_enable=='1')
			{
				var QTY = $(this).closest('tr').find('td:nth-child(3) input[type="text"]').val();
				if(QTY.search(/[^0-9]/) != -1)
				{
					alert("Item serial number is enabled !!! Please Enter Only Digits");
					$(this).closest('tr').find('td:nth-child(3) input[type="text"]').val('');
				}
				else
				{
					for(i=0; i <= old_qty; i++)
					{ 
						$('.tr2[row_no="'+row_no+'"]').find('td div.td_append'+i+row_no+'').remove();
					}
					$('.tr2[row_no="'+row_no+'"]').find('td.td_append').html(''); 
					var quantity = qty-old_qty;
					quantity = quantity+old_qty; 
					if(maxQty>quantity || maxQty==quantity)
					{  
						for(i=0; i < (qty-old_qty); i++)
						{ 
						
							 $('.tr2[row_no="'+row_no+'"]').find('td.td_append').append('<div style="margin-bottom:6px;" class="td_append'+i+row_no+'"><input type="text" class="sr_no renameSerial" name="grn_rows['+val+'][serial_numbers][]" ids="sr_no['+i+']" id="sr_no'+l+row_no+'" required/></div>');
							
							$('.tr2[row_no="'+row_no+'"] td:nth-child(1)').find('input#sr_no'+l+row_no).rules('add', {required: true});
							rename_rows();				
						}
					}
				}
			} 
    });
	rename_rows();
	
	
	function rename_rows()
	{
		$("#main_tb tbody tr.tr1").each(function(){
			var row_no=$(this).attr('row_no');
			var val=$(this).find('td:nth-child(4) input[type="checkbox"]:checked').val();
			var qty=$(this).find('td:nth-child(4) input[type="checkbox"]:checked').attr('max_qty');					
			if(val){
				$(this).find('td:nth-child(2) input[type="hidden"]:nth-child(1).item').attr({ name:"grn_rows["+val+"][item_id]"});
				$(this).find('td:nth-child(2) input.purchase_order_row_id').attr({ name:"grn_rows["+val+"][purchase_order_row_id]"});
				$(this).find('td:nth-child(2) input.grn_row_id').attr({ name:"grn_rows["+val+"][id]"});
				$(this).find('td:nth-child(3) input').attr({ name:"grn_rows["+val+"][quantity]", id:"grn_rows-"+val+"-quantity"}).removeAttr('readonly');
				$(this).css('background-color','#fffcda');
				$('#main_tb tbody tr.tr2[row_no="'+row_no+'"]').css('background-color','#fffcda');
			}
			else{
				$(this).find('td:nth-child(2) input').attr({ name:"q"});
				$(this).find('td:nth-child(3) input').attr({ name:"q", id:"q",readonly:"readonly"});
				
				$(this).css('background-color','#FFF');
				$('#main_tb tbody tr.tr2[row_no="'+row_no+'"]').css('background-color','#FFF');
			}
		});
		$("#main_tb tbody tr.tr2").each(function(){
			var val=$(this).find('td:nth-child(2) input.hid').val();
			var no=0;
			$(this).find('.renameSerial').each(function(){
				$(this).attr({ name:"grn_rows["+val+"][serial_number][]"});
				no++;
			});
		});
	}
});		
</script>
