<style> 
.checkbox {
    margin-top:0px !important;
    margin-bottom:0px !important;
}
</style>

<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Material Indent Report</span>
		</div>
		<div class="actions">
			<table width="50%" class="table table-condensed" >
				<tbody>
					<tr>
						<td width="25%">
							<div id=""><?php 
								$options = [];
								$options = [['text'=>'All','value'=>'All'],['text'=>'Zero','value'=>'Zero'],['text'=>'Positive','value'=>'Positive']];
								echo $this->Form->input('total', ['empty'=>'--Sales Order--','options' => $options,'label' => false,'class' => 'form-control input-sm select2me stock1','placeholder'=>'Sub-Group','value'=> h(@$stock)]);  
								?>
							</div>
						</td>
						
						<td width="25%">
							<div id="">
							<?php 
								$options = [];
								$options = [['text'=>'All','value'=>'All'],['text'=>'Zero','value'=>'Zero'],['text'=>'Positive','value'=>'Positive']];
								echo $this->Form->input('total', ['empty'=>'--Job Card--','options' => $options,'label' => false,'class' => 'form-control input-sm select2me stock','placeholder'=>'Sub-Group','value'=> h(@$stock)]);  ?>
							</div>
						</td><td width="25%">
							<div id="">
							<?php if(sizeof($company_name)==1 && in_array(166,$allowed_pages)){
									foreach($company_name as $names){	 
												if(@$names == @$st_company_id){ ?>
									<?= $this->Html->link(
											'Add To Bucket',
											'/MaterialIndents/AddToCart',
											['class' => 'btn btn-success']
									); ?><?php  }}}?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		
		</div>
		<div class="portlet-body">
			<form method="GET" >
			<table width="50%" class="table table-condensed" >
				<tbody>
					<tr>
						<td width="35%">
						<?php if(!empty($company_name)){ //pr($company_name[0]); ?>
								<?php echo $this->Form->input('company_name', ['hiddenField'=>false,'options' =>$Companies,'label' => false,'class'=>'form-control input-sm select2me','multiple'=>'multiple','value'=>$selected_company]); ?>
						<?php }else{ ?>  
							<?php echo $this->Form->input('company_name', ['hiddenField'=>false,'options' =>$Companies,'label' => false,'class'=>'form-control input-sm select2me','multiple'=>'multiple','value'=>$st_company_id]); ?>
						<?php } ?>		
						</td>
						<td width="15%">
								<?php echo $this->Form->input('item_name', ['empty'=>'---Items---','options' => $Items,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$item_name) ]); ?>
						</td>
						<td width="15%">
								<?php echo $this->Form->input('item_category', ['empty'=>'---Category---','options' => $ItemCategories,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$item_category) ]); ?>
						</td>
						<td width="15%">
							<div id="item_group_div">
							<?php echo $this->Form->input('item_group_id', ['empty'=>'---Group---','options' =>$ItemGroups,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Group','value'=> h(@$item_group)]); ?></div>
						</td>
						<td width="15%">
							<div id="item_sub_group_div">
							<?php echo $this->Form->input('item_sub_group_id', ['empty'=>'---Sub-Group---','options' =>$ItemSubGroups,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Sub-Group','value'=> h(@$item_sub_group)]); ?></div>
						</td>
						<td width="15%">
							<div id="item_sub_group_div">
							<?php 
								$options = [];
								$options = [['text'=>'All','value'=>'All'],['text'=>'Positive','value'=>'Positive']];
							echo $this->Form->input('stock', ['empty'=>'--Indent--','options' => $options,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Indent','value'=> h(@$stock)]); ?></div>
						</td>
						
						<td width="10%">
							<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		
			<?= $this->Form->create($mit) ?>
			<div class="row">
				<div class="col-md-12">
				
				<?php $page_no=$this->Paginator->current('ItemLedgers'); $page_no=($page_no-1)*20; ?>
				<table class="table table-bordered "  id="main_tb">
					<thead>
						<tr>
							<th width="3%">Sr. No.</th>
							<th>Item Name</th>
							<th width="13%"  >Current Stock</th>
							<th width="10%">Sales Order </th>
							<th width="10%">Job card  </th>
							<th width="10%">Open PO  </th>
							<th width="10%">Open QO  </th>
							<th width="10%">Open MI  </th>
							<th width="15%">Required Quantity</th>
							<th width="15%">Minimum Stock</th>
							<th width="15%">Suggested Quantity</th>
							<th width="10%">Action</th>
						</tr>
					</thead>
					<tbody id="main_tbody" >
						<?php  $i=1; foreach($material_report as $data){
							$item_id=$data['item_id'];
							$Current_Stock=$data['Current_Stock'];
							$total_invoice_qty=@$invoice_qty[$data['item_id']]; 
							$total_sales_qty=@$sales_order_qty[$data['item_id']]; 
							$open_so_qty=$total_sales_qty-$total_invoice_qty;
							
							$total_grn_qty=@$grn_qty[$data['item_id']]; 
							$total_purchase_order_qty=@$purchase_order_qty[$data['item_id']]; 
							$open_po_qty=$total_purchase_order_qty-$total_grn_qty;
							
							$total_so_qty=@$so_qty[$data['item_id']]; 
							$total_qo_qty=@$qo_qty[$data['item_id']]; 
							$open_qo_qty=$total_qo_qty-$total_so_qty;
							
							$total_po_qty=@$po_qty[$data['item_id']]; 
							$total_mi_qty=@$mi_qty[$data['item_id']]; 
							$open_mi_qty=$total_mi_qty-$total_po_qty;
							
							$open_jc_qty=@$job_card_qty[$data['item_id']]; 
						//	pr($total_jc_qty); 
							$total_indent=$Current_Stock-@$open_so_qty-$open_jc_qty+$open_po_qty-$open_qo_qty+$open_mi_qty;
							$qty="";
							if($stock=="Positive" && $total_indent < 0){ //exit; ?>
								<tr class="tr1" row_no='<?php echo @$i; ?>'>
									<td ><?php echo $i; ?> </td>
									<td><?php /* echo $data['item_name']; */ echo $data['item_name']; ?></td>
									<td><?php echo $data['Current_Stock']; ?></td>
									
									<td style="text-align:center"><?php if($open_so_qty > 0){ 
										echo $this->Form->input('sales_order_qt', ['type' => 'hidden','class'=>'sales_order_qt','value'=>$open_so_qty]); 
										echo $this->Html->link(@$open_so_qty ,'/ItemLedgers/material_indent?status=salesorder&id='.@$sales_id[$item_id],['target' => '_blank']); 
									 }else{ 
									  echo $this->Form->input('sales_order_qt', ['type' => 'hidden','class'=>'sales_order_qt','value'=>'0']);
									 echo "-"; } ?></td>
									<td style="text-align:center"><?php if($open_jc_qty > 0){ 
										echo $this->Form->input('jc_qt', ['type' => 'hidden','class'=>'jc_qt','value'=>$open_jc_qty]);
										echo $this->Html->link(@$open_jc_qty ,'/ItemLedgers/material_indent?status=jobcard&id='.@$job_id[$item_id],['target' => '_blank']);
									 }else{ 
									  echo $this->Form->input('jc_qt', ['type' => 'hidden','class'=>'jc_qt','value'=>'0']);
									 echo "-"; } ?></td>
									<td style="text-align:center"><?php if($open_po_qty > 0){ 
										echo $this->Html->link(@$open_po_qty ,'/ItemLedgers/material_indent?status=purchaseorder&id='.@$purchase_id[$item_id],['target' => '_blank']); 
									 }else{ echo "-"; } ?></td>
									 
									 <td style="text-align:center"><?php if($open_qo_qty > 0){ 
										echo $this->Html->link(@$open_qo_qty ,'/ItemLedgers/material_indent?status=quotation&id='.@$qotation_id[$item_id],['target' => '_blank']); 
									 }else{ echo "-"; } ?></td>
									 
									 <td style="text-align:center"><?php if($open_mi_qty > 0){ 
										echo $this->Html->link(@$open_mi_qty ,'/ItemLedgers/material_indent?status=mi&id='.@$mi_id[$item_id],['target' => '_blank']); 
									 }else{ echo "-"; } ?></td>
									 <td style="text-align:center">
									<?php if(@$total_indent < 0){
											 echo abs(@$total_indent);
									}else{ echo "-";} ?>
									</td> 
									
									<td style="text-align:center">
									<?php if(@$data['minimum_stock'] > 0){
											 echo abs(@$data['minimum_stock']);
									}else{ echo "-";} ?>
									</td>
									
									<td style="text-align:center">
										<?php if(@$total_indent || @$data['minimum_stock'] ){
											if(@$total_indent > 0){
												echo abs(@$data['minimum_stock']); 
											}else{
												echo abs(@$data['minimum_stock']+abs(@$total_indent)); 
											}
										}
												?>
									
									</td>
									 
									 <td align="center">
										<label class="hello">
										
														<button type="button" id="item<?php echo $item_id;?>" class="btn btn-primary btn-sm add_to_bucket" item_id="<?php echo $item_id; ?>" suggestindent="<?php echo @$total_indent;
										 ?>"><i class="fa fa-plus"></i></button>
										</label>
									</td>
								</tr>
							<?php }else{ ?>
									<tr class="tr1" row_no='<?php echo @$i; ?>'>
										<td ><?php echo $i; ?> </td>
										<td><?php /* echo $data['item_name']; */ echo $data['item_name']; ?></td>
										<td><?php echo $data['Current_Stock']; ?></td>
										
										<td style="text-align:center"><?php if($open_so_qty > 0){ 
											echo $this->Form->input('sales_order_qt', ['type' => 'hidden','class'=>'sales_order_qt','value'=>$open_so_qty]); 
											echo $this->Html->link(@$open_so_qty ,'/ItemLedgers/material_indent?status=salesorder&id='.@$sales_id[$item_id],['target' => '_blank']); 
											
										 }else{ 
										 echo $this->Form->input('sales_order_qt', ['type' => 'hidden','class'=>'sales_order_qt','value'=>'0']);
										 echo "-"; } ?></td>
										<td style="text-align:center"><?php if($open_jc_qty > 0){ 
											echo $this->Form->input('jc_qt', ['type' => 'hidden','class'=>'jc_qt','value'=>$open_jc_qty]); 
											echo $this->Html->link(@$open_jc_qty ,'/ItemLedgers/material_indent?status=jobcard&id='.@$job_id[$item_id],['target' => '_blank']); 
										 }else{ 
										  echo $this->Form->input('jc_qt', ['type' => 'hidden','class'=>'jc_qt','value'=>'0']);
										 echo "-"; } ?></td>
										<td style="text-align:center"><?php if($open_po_qty > 0){ 
											echo $this->Html->link(@$open_po_qty ,'/ItemLedgers/material_indent?status=purchaseorder&id='.@$purchase_id[$item_id],['target' => '_blank']); 
										 }else{ echo "-"; } ?></td>
										 
										 <td style="text-align:center"><?php if($open_qo_qty > 0){ 
											echo $this->Html->link(@$open_qo_qty ,'/ItemLedgers/material_indent?status=quotation&id='.@$qotation_id[$item_id],['target' => '_blank']); 
										 }else{ echo "-"; } ?></td>
										 
										 <td style="text-align:center"><?php if($open_mi_qty > 0){ 
											echo $this->Html->link(@$open_mi_qty ,'/ItemLedgers/material_indent?status=mi&id='.@$mi_id[$item_id],['target' => '_blank']); 
										 }else{ echo "-"; } ?></td>
										 <td style="text-align:center">
										<?php if(@$total_indent < 0){
												 echo abs(@$total_indent);
										}else{ echo "-";} ?>
										</td> 
										
										<td style="text-align:center">
										<?php if(@$data['minimum_stock'] > 0){
												 echo abs(@$data['minimum_stock']);
										}else{ echo "-";} ?>
										</td>
										
										<td style="text-align:center">
											<?php if(@$total_indent || @$data['minimum_stock'] ){
												if(@$total_indent > 0){
													echo abs(@$data['minimum_stock']); 
												}else{
													echo abs(@$data['minimum_stock']+abs(@$total_indent)); 
												}
											}
													?>
										
										</td>
										 
										 <td align="center">
											<label class="hello">
											
															<button type="button" id="item<?php echo $item_id;?>" class="btn btn-primary btn-sm add_to_bucket" item_id="<?php echo $item_id; ?>" suggestindent="<?php echo @$total_indent;
											 ?>"><i class="fa fa-plus"></i></button>
											</label>
										</td>
									
									
									</tr>
							<?php }
							
						?>
						
						<?php $i++; }  ?>
						
					</tbody>
				</table>
					
				
				</div>
			</div>
			<?= $this->Form->end() ?>		
		</div>
	</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>

<script>
$(document).ready(function() {
	////////
	$('.stock').die().live("change",function(){
		var stock = $(this).val();
		$("#main_tb tbody tr.tr1").each(function(){
			//var qt=$(this).find('.sales_order_qt').val(); 
			var qt=$(this).find('td:nth-child(4) input').val();
			var jc_qt=$(this).find('td:nth-child(5) input').val(); 
			if(stock =='Positive'){
				if(jc_qt > 0){
					$(this).show();
				}else{
					$(this).hide();
				}
				
			}else if(stock =='Zero'){
				if(jc_qt == 0){
					$(this).show();
				}else{
					$(this).hide();
				}
			}else{
				$(this).show();
			}
		});
		
	});
	
	$('.stock1').die().live("change",function(){
		var stock = $(this).val();
		$("#main_tb tbody tr.tr1").each(function(){
			//var qt=$(this).find('.sales_order_qt').val(); 
			var qt=$(this).find('td:nth-child(4) input').val();
			//var jc_qt=$(this).find('td:nth-child(5) input').val(); 
			if(stock =='Positive'){
				if(qt > 0){
					$(this).show();
				}else{
					$(this).hide();
				}
				
			}else if(stock =='Zero'){
				if(qt == 0){
					$(this).show();
				}else{
					$(this).hide();
				}
			}else{
				$(this).show();
			}
		});
		
	});
	
	
	$('select[name="item_category"]').on("change",function() {
		$('#item_group_div').html('Loading...');
		var itemCategoryId=$('select[name="item_category"] option:selected').val();
		var url="<?php echo $this->Url->build(['controller'=>'ItemGroups','action'=>'ItemGroupDropdown']); ?>";
		url=url+'/'+itemCategoryId,
		$.ajax({
			url: url,
			type: 'GET',
		}).done(function(response) {
			$('#item_group_div').html(response);
			$('select[name="item_group_id"]').select2();
		});
	});	
	//////
	$('select[name="item_group_id"]').die().live("change",function() {
		$('#item_sub_group_div').html('Loading...');
		var itemGroupId=$('select[name="item_group_id"] option:selected').val();
		var url="<?php echo $this->Url->build(['controller'=>'ItemSubGroups','action'=>'ItemSubGroupDropdown']); ?>";
		url=url+'/'+itemGroupId,
		$.ajax({
			url: url,
			type: 'GET',
		}).done(function(response) {
			$('#item_sub_group_div').html(response);
			$('select[name="item_sub_group_id"]').select2();
		});
	});
	////
	$('.add_to_bucket').die().live("click",function() {
		
		var t=$(this);
		var item_id=$(this).attr('item_id');
		var id=$(this).attr('id');
		var suggestindent=$(this).attr('suggestindent');
		var url="<?php echo $this->Url->build(['controller'=>'ItemLedgers','action'=>'addToBucket']); ?>";
		url=url+'/'+item_id+'/'+suggestindent,
		$.ajax({
			url: url,
			type: 'GET',
		}).done(function(response) {
			
			t.text('');
			//t.hide();
			//t.text('Added To Bucket').css('color','#3ea49d');
			t.removeClass('btn btn-primary btn-sm add_to_bucket').text('Added To Bucket').css('color','#3ea49d').css('border',' none').css('background',' none').css('cursor','unset').off('click');
		});
 		
    })
	var p=0;
	function rename_rows(){ 
		$("#main_tb tbody tr.tr1").each(function(){
			var val=$(this).find('td:nth-child(9) input[type="checkbox"]:checked').val();
			if(val){
				$(this).css('background-color','#fffcda');
 			}else{
 				$(this).css('background-color','#FFF');
			}
		});
	}	
});		
</script>