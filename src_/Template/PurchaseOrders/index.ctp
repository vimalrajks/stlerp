

<?php 	
	$first="01";
	$last="31";
	$start_date=$first.'-'.$financial_month_first->month;
	$end_date=$last.'-'.$financial_month_last->month;
	
?>
<?php 

	if(!empty($status)){
		$url_excel=$status."/?".$url;
	}else{
		$url_excel="/?".$url;
	}

?>

<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Purchase Orders</span>
			<?php if($pull_request=="true"){ ?>
			: Select a Purchase-Order to convert into Grn
			<?php } ?>
		</div>
		<div class="actions">
			<div class="btn-group">
			<?php
			if($status==null or $status=='Pending'){ $class1='btn btn-primary'; }else{ $class1='btn btn-default'; }
			if($status=='Converted-Into-GRN'){ $class2='btn btn-primary'; }else{ $class2='btn btn-default'; }
			?>
			<?php if($pull_request!="true"){ ?>
				<?= $this->Html->link(
					'Pending',
					'/Purchase-Orders/index/Pending',
					['class' => $class1]
				); ?>
				<?= $this->Html->link(
					'Converted-Into-GRN',
					'/Purchase-Orders/index/Converted-Into-GRN',
					['class' => $class2]
				); ?>
				<?php echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/PurchaseOrders/Excel-Export/'.$url_excel.'',['class' =>'btn  green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
			<?php } ?>
			</div>
			
		</div>
	
	
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
			<form method="GET" >
				<input type="hidden" name="inventory_voucher" value="<?php echo @$inventory_voucher; ?>">
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td width="19%">
								<div class="input-group" style="" id="pnf_text">
								  <span class="input-group-addon">PO-</span><input type="text" name="purchase_no" class="form-control input-sm" placeholder="Purchase No" value="<?php echo @$purchase_no; ?>">
								</div>
							</td>
						    <td width="16%">	
							     <input type="text" name="file" class="form-control input-sm" placeholder="File" value="<?php echo @$file; ?>">
									
							</td>
							<td width="22%">
							      <input type="text" name="vendor" class="form-control input-sm" placeholder="Supplier" value="<?php echo @$vendor; ?>">
							</td>
							<td width="12%">
							    <?php echo $this->Form->input('items', ['empty'=>'--Items--','options' => $Items,'label' => false,'class' => 'form-control input-sm select2me','placeholder'=>'Category','value'=> h(@$items) ]); ?>
							</td>
							<td width="9%">
								<input type="text" name="From" class="form-control input-sm date-picker" placeholder="From" value="<?php echo @$From; ?>" data-date-format="dd-mm-yyyy" >
							</td>
							<td width="9%">
								<input type="text" name="To" class="form-control input-sm date-picker" placeholder="To" value="<?php echo @$To; ?>" data-date-format="dd-mm-yyyy" >
							</td>
							
							<input type="hidden" name="pull-request" value='<?php echo $pull_request; ?>'  />
							
							<td>
							     <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
				<?php $page_no=$this->Paginator->current('PurchaseOrders'); $page_no=($page_no-1)*20; ?>
				<table class="table table-bordered table-striped table-hover">
						<thead>
							<tr>
								<th>S.No</th>
								<th>Purchase No.</th>
								<th>Supplier Name</th>
								<th>Items Name</th>
								<?php if($status != "Converted-Into-GRN"){ ?>
								<th>Created Date</th>
								<?php } ?>
								<th>Delivery Date</th>
								<th style="text-align:right">Total</th>
								
								<th class="actions"><?= __('Actions') ?></th>
							</tr>
					
					</thead>

					<tbody>
						<?php  foreach ($purchaseOrders as $purchaseOrder): ?>
						<tr <?php if($status=='Pending'){ echo 'style="background-color:#f4f4f4"';   
							if(@$total_sales[@$purchaseOrder->id] != @$total_qty[@$purchaseOrder->id]){ 
						?>>
							<td><?= h(++$page_no) ?></td>
							
							<td><?= h(($purchaseOrder->po1.'/PO-'.str_pad($purchaseOrder->po2, 3, '0', STR_PAD_LEFT).'/'.$purchaseOrder->po3.'/'.$purchaseOrder->po4)) ?></td>
							
							<td><?= h($purchaseOrder->vendor->company_name) ?></td>
							<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php  foreach($purchaseOrder->purchase_order_rows as $purchase_order_row){ 
											if($purchase_order_row->purchase_order_id == $purchaseOrder->id){?>
											<li><p><?= h($purchase_order_row->item->name) ?></p></li>
											<?php }}?>
										</ul>
								</div>
							</td>
							<td style="text-align:center;"><?php 
					if(date("d-m-Y",strtotime( $purchaseOrder->date_created)) == "01-01-1970"){
								echo "-";
							}else{
								echo date("d-m-Y",strtotime( $purchaseOrder->date_created));
							} ?></td>
							
							<td style="text-align:center;"><?php 
					if(date("d-m-Y",strtotime( $purchaseOrder->delivery_date)) == "01-01-1970"){
								echo "-";
							}else{
								echo date("d-m-Y",strtotime( $purchaseOrder->delivery_date));
							} ?></td>
							<td align="right"><?= $this->Money->indianNumberFormat($purchaseOrder->total) ?></td>
						
							<td class="actions">
							<?php
							 $purchaseOrder_id =$purchaseOrder->id;
							 $purchaseOrder->id = $EncryptingDecrypting->encryptData($purchaseOrder->id);
							if(in_array($purchaseOrder->created_by,$allowed_emp)){
							if(in_array(31,$allowed_pages)){ ?>
								<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $purchaseOrder->id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
							<?php } ?>
							<?php
								if($status != 'Converted-Into-GRN' && $st_year_id==$purchaseOrder->financial_year_id) { 
								if($pull_request!="true" and in_array(14,$allowed_pages)){ 
								echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $purchaseOrder->id],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit'));} } } ?>
							
							
								<?php if($pull_request=="true"){ 
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into GRN','/Grns/AddNew?purchase-order='.$purchaseOrder->id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
								} ?>
								
								<a href="#" class="btn btn-xs blue tooltips  select_term_condition" qwerty="<?php echo $purchaseOrder_id; ?>" data-original-title="Pending Item"><i class="fa fa-eye"></i></a>
								

							</td>
						</tr>
						<?php }} endforeach; ?>
						
						<?php foreach ($purchaseOrders as $purchaseOrder): 
						
						?>
						<tr <?php if($status=='Converted-Into-GRN' && $st_year_id==$purchaseOrder->financial_year_id){ echo 'style="background-color:#f4f4f4"';   
							if(@$total_sales[@$purchaseOrder->id] == @$total_qty[@$purchaseOrder->id]){ 
						
						?>>
							<td><?= h(++$page_no) ?></td>
							
							<td><?= h(($purchaseOrder->po1.'/PO-'.str_pad($purchaseOrder->po2, 3, '0', STR_PAD_LEFT).'/'.$purchaseOrder->po3.'/'.$purchaseOrder->po4)) ?></td>
							
							<td><?= h($purchaseOrder->vendor->company_name) ?></td>
							<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php  foreach($purchaseOrder->purchase_order_rows as $purchase_order_row){ 
											if($purchase_order_row->purchase_order_id == $purchaseOrder->id){?>
											<li><p><?= h($purchase_order_row->item->name) ?></p></li>
											<?php }}?>
										</ul>
								</div>
							</td>
							<!--<td style="text-align:center;"><?php 
					if(date("d-m-Y",strtotime( $purchaseOrder->date_created)) == "01-01-1970"){
								echo "-";
							}else{
								echo date("d-m-Y",strtotime( $purchaseOrder->date_created));
							} ?></td>-->
							
							<td style="text-align:center;"><?php 
					if(date("d-m-Y",strtotime( $purchaseOrder->delivery_date)) == "01-01-1970"){
								echo "-";
							}else{
								echo date("d-m-Y",strtotime( $purchaseOrder->delivery_date));
							} ?></td>
							<td align="right"><?= $this->Money->indianNumberFormat($purchaseOrder->total) ?></td>
						
							<td class="actions">
							<?php
							$purchaseOrder->id = $EncryptingDecrypting->encryptData($purchaseOrder->id);
							if(in_array($purchaseOrder->created_by,$allowed_emp)){
								if(in_array(31,$allowed_pages)){ ?>
									<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $purchaseOrder->id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
							<?php } } ?>
							
								<?php if($pull_request=="true"){ 
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into GRN','/Grns/AddNew?purchase-order='.$purchaseOrder->id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
								} ?>
								
								
								<?php
								if($status != 'Converted-Into-GRN') {
								if($pull_request!="true" and in_array(14,$allowed_pages)){ 
								echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $purchaseOrder->id],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit'));} } ?>

							</td>
						</tr>
						<?php }} endforeach; ?>
						
						<?php foreach ($purchaseOrders as $purchaseOrder): 
								@$totalPo = implode(",", @$supplier_total_po[@$purchaseOrder->vendor_id]);
								//pr($text); 
						//pr(@$supplier_total_po[$purchaseOrder->vendor_id]);   ?>
						<tr <?php if($status=='true' || $status==null){ echo 'style="background-color:#f4f4f4"';  
							if(@$total_sales[@$purchaseOrder->id] != @$total_qty[@$purchaseOrder->id]){ //exit;
						?>>
							<td><?= h(++$page_no) ?></td>
							
							<td><?= h(($purchaseOrder->po1.'/PO-'.str_pad($purchaseOrder->po2, 3, '0', STR_PAD_LEFT).'/'.$purchaseOrder->po3.'/'.$purchaseOrder->po4)) ?></td>
							
							<td><?= h($purchaseOrder->vendor->company_name) ?></td>
							<td>
								<div class="btn-group">
									<button id="btnGroupVerticalDrop5" type="button" class="btn default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Items <i class="fa fa-angle-down"></i></button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="btnGroupVerticalDrop5">
										<?php  foreach($purchaseOrder->purchase_order_rows as $purchase_order_row){ 
											if($purchase_order_row->purchase_order_id == $purchaseOrder->id){?>
											<li><p><?= h($purchase_order_row->item->name) ?></p></li>
											<?php }}?>
										</ul>
								</div>
							</td>
							<td style="text-align:center;"><?php 
					if(date("d-m-Y",strtotime( $purchaseOrder->date_created)) == "01-01-1970"){
								echo "-";
							}else{
								echo date("d-m-Y",strtotime( $purchaseOrder->date_created));
							} ?></td>
							
							<td style="text-align:center;"><?php 
					if(date("d-m-Y",strtotime( $purchaseOrder->delivery_date)) == "01-01-1970"){
								echo "-";
							}else{
								echo date("d-m-Y",strtotime( $purchaseOrder->delivery_date));
							} ?></td>
						
							<td align="right"><?= $this->Money->indianNumberFormat($purchaseOrder->total) ?></td>
						
							<td class="actions">
							<?php $purchaseOrder_id =$purchaseOrder->id; ?>
							<?php 
								$purchaseOrder->id = $EncryptingDecrypting->encryptData($purchaseOrder->id);
								if($pull_request!="true" and in_array(31,$allowed_pages)){ ?>
									<?php echo $this->Html->link('<i class="fa fa-search"></i>',['action' => 'confirm', $purchaseOrder->id],array('escape'=>false,'target'=>'_blank','class'=>'btn btn-xs yellow tooltips','data-original-title'=>'View as PDF')); ?>
								<?php } ?>
							
								<?php 
									if($status != 'Converted-Into-GRN') {
									if($pull_request!="true" and in_array(14,$allowed_pages)){ 
									echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'edit', $purchaseOrder->id],array('escape'=>false,'class'=>'btn btn-xs blue tooltips','data-original-title'=>'Edit'));
									
									$td=(date("Y-m-d"));
										$dd=(date("Y-m-d",strtotime($purchaseOrder->delivery_date)));
										if($dd < date("Y-m-d",strtotime($td))){?>
										
										<?php //echo $this->Html->link('<i class="fa fa-envelope"></i>',array('escape'=>false,'class'=>'btn btn-primary btn-xs send_mail','data-original-title'=>'Send Mail','ledger_id'=$purchaseOrder->id,'totalPo'=$totalPo)); ?>
										
									<?php 
									
									} 
									?>
								     <a href="#" class="btn btn-xs blue tooltips  select_term_condition" qwerty="<?php echo $purchaseOrder_id; ?>" data-original-title="Pending Item"><i class="fa fa-eye"></i></a>
							
									<button type="button" ledger_id="<?php echo $purchaseOrder->id;  ?>" totalPo="<?php echo @$totalPo;  ?>" class="btn btn-xs blue tooltips send_mail" title="Send Mail"><i class="fa fa-envelope"></i></button>

									
									<?php
									}} 
									
									
									
							?>
							
								<?php if($pull_request=="true"){ 
									echo $this->Html->link('<i class="fa fa-repeat"></i>  Convert Into GRN','/Grns/AddNew?purchase-order='.$purchaseOrder->id,array('escape'=>false,'class'=>'btn btn-xs default blue-stripe'));
							} ?>
								
								
								

							</td>
						</tr>
						<?php }} endforeach; ?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
			
</div>
</div>

<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function() {
	
 $('.send_mail').die().live("click",function() {
	var totalPo=$(this).attr('totalPo');
	
		if (confirm("Are You Sure You Want To Send Mail!")) {
		   var url="<?php echo $this->Url->build(['controller'=>'PurchaseOrders','action'=>'sendMail']); ?>";
			url=url+'?totalPo='+totalPo;

			$.ajax({
				url: url,
				type: "GET",
			}).done(function(response) { 
			//alert(response);
				alert("Email Send successfully")
			}); 
		} else {
			txt = "You pressed Cancel!";
		}
	});
	
	

});
</script>
<script>
$(document).ready(function() {
	
	$('.select_term_condition').die().live("click",function() {  
		var sid=$(this).attr('qwerty');
		
		open_address(sid);
    });
	$('.closebtn2').on("click",function() {  
		$("#myModal2").hide();
	});
	function open_address(sid){
			
			var url="<?php echo $this->Url->build(['controller'=>'PurchaseOrders','action'=>'showPendingItem']); ?>";
			var url1= url+'/'+sid;//alert(url1);
			url=url+'/'+sid, 
			$.ajax({
				url: url,
			}).done(function(response) {
				$("#show_model").html(response);
			});
		}
		
    });
	
</script>

<div id="show_model">

</div>