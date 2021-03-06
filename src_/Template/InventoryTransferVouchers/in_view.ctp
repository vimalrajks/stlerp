<style>
@media print{
	.maindiv{
		width:100% !important;
	}	
	.hidden-print{
		display:none;
	}
}
p{
margin-bottom: 0;
}
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
    padding: 5px !important;
}
</style>
<style type="text/css" media="print">
@page {
    size: auto;   /* auto is the initial value */
    margin: 0 5px 0 20px;  /* this affects the margin in the printer settings */
}
</style>
<a class="btn  blue hidden-print margin-bottom-5 pull-right" onclick="javascript:window.print();">Print <i class="fa fa-print"></i></a>
<div style="border:solid 1px #c7c7c7;background-color: #FFF;padding: 10px;margin: auto;width: 70%;font-size: 12px;" class="maindiv">
<table width="100%" class="divHeader">
		<tr>
			<td width="30%"><?php echo $this->Html->image('/logos/'.$inventoryTransferVoucher->company->logo, ['width' => '40%']); ?></td>
			<td align="center" width="30%" style="font-size: 12px;"><div align="center" style="font-size: 16px;font-weight: bold;color: #0685a8;">INVENTORY TRANSFER VOUCHER IN</div></td>
			<td align="right" width="30%" style="font-size: 12px;">
			<span style="font-size: 14px;"><?= h($inventoryTransferVoucher->company->name) ?></span>
			<span><?= $this->Text->autoParagraph(h($inventoryTransferVoucher->company->address)) ?></span>
			<span> <i class="fa fa-phone" aria-hidden="true"></i> <?= h($inventoryTransferVoucher->company->landline_no) ?></span> |
			<?= h($inventoryTransferVoucher->company->mobile_no) ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="border:solid 2px #0685a8;margin-bottom:5px;margin-top: 5px;"></div>
			</td>
		</tr>
	</table>
</br>
<table width="100%" style="margin-top: -17px;">
	<tr>
		<td  valign="top" align="left">
			<table border="0">
				<tr>
					<td align="left"><b>Inventory Transfer Voucher No</b></td>
					<td width="20" align="center">:</td>
					<td><?= h('#'.str_pad($inventoryTransferVoucher->voucher_no, 4, '0', STR_PAD_LEFT)) ?></td>
				</tr>
				<tr>
				<?php if(!empty($inventoryTransferVoucher->customer)){ ?>
					<td align="left"><b>Customer Name</b></td>
					<td width="20" align="center">:</td>
					<td><?= h($inventoryTransferVoucher->customer->customer_name); ?></td>
				<?php }else if(!empty($inventoryTransferVoucher->vendor)){ ?>
					<td align="left"><b>Supplier Name</b></td>
					<td width="20" align="center">:</td>
					<td><?= h($inventoryTransferVoucher->vendor->company_name); ?></td>
				<?php } ?>	
				</tr>
				<?php if(!empty($inventoryTransferVoucher->customer)){ ?>
				<tr>
					<td align="left"><b>File No.</b></td>
					<td width="20" align="center">:</td>
					<td><?= h($inventoryTransferVoucher->file_no) ?></td>
				</tr>
				<?php } ?>
			</table>
	   </td>
	   <td width="50%" valign="top" align="right">
				<table>
					<tr>
						<td><b>Transaction Date</b></td>
						<td width="20" align="center">:</td>
						<td><?= h(date("d-m-Y",strtotime($inventoryTransferVoucher->transaction_date))) ?></td>
					</tr>
					 <tr>
						<td><b>Created On</b></td>
						<td width="20" align="center">:</td>
						<td>
						<?php  if(date("d-m-Y",strtotime($inventoryTransferVoucher->created_on))=="01-01-1970"){
							echo "-";
						}else{ ?>
							<?= h(date("d-m-Y",strtotime($inventoryTransferVoucher->created_on))) ?> 
						<?php } ?>
						
						</td>
					</tr> 
				</table>
			</td>
	</tr>
</table>	
</br>
<?php if(!empty($inventoryTransferVoucher)){ ?>
<div class="portlet-body form">
<?php $status=0;
		foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $inventory_transfer_voucher_row ){
		if($inventory_transfer_voucher_row->status == 'In' &&$inventory_transfer_voucher_row->item->item_companies[0]->serial_number_enable == 1) {
		$status=1;
		}
	}?>
<table class="table table-bordered table-condensed">
	<thead> 
		<th width="10%">Sr.No.</th>
		<th width="60%">Item</th>
		<?php if($status==1) { ?>
		<th>Item Serial No</th>
		<?php } ?>
		<th>Rate</th>
		<th>Quantity</th>
		<th>Narration</th>
	</thead>
	
	<tbody>
		<?php $i=1; foreach($inventoryTransferVoucher->inventory_transfer_voucher_rows as $out_item){ ?>
			<tr>
				<td valign="top"><?php echo $i; ?></td>
				<td valign="top"><?php echo $out_item->item->name ?></td>
				<?php 
				if($status==1) {  
				if(!empty($out_item->item->item_companies[0]->serial_number_enable)){
				if($out_item->item->item_companies[0]->serial_number_enable == 1) { ?>
				<td>
					<table>
					<?php foreach ($out_item->item->serial_numbers as  $item_serial_number){ 
						if($item_serial_number->itv_id == $out_item->inventory_transfer_voucher_id){ ?>
						<tr>
						<td><?php echo $item_serial_number->name; ?></td>
						</tr>
					<?php }} ?>
					</table>
				</td>
				<?php }}else{  ?>
					<td><table>
					<?php foreach ($out_item->serial_numbers as  $item_serial_number){ 
					if($item_serial_number->itv_id == $out_item->inventory_transfer_voucher_id){ ?>
					<tr>
						<td><?php echo "-"; ?></td>
					</tr>
					<?php }} ?>
					</table>
					</td>
					<?php }} ?> 
				<td valign="top"><?php echo $out_item->amount ?></td>
				<td valign="top"><?php echo $out_item->quantity ?></td>
				<td valign="top"><?php echo $out_item->narration ?></td>
			</tr>
		<?php $i++; } ?>
		
	</tbody>
</table>
</br>
	<div style="border:solid 1px ;margin-top: -24px;"></div>

<table width="96%">
	<tr>
		<td align="right">
		<table >
			<tr>
			    <td align="center">
				<span style="font-weight: bold;font-size:14px;">For</span> <span style="font-size: 14px;font-weight: bold;"><?= h($inventoryTransferVoucher->company->name)?><br/></span>
				<?php 
				 echo $this->Html->Image('/signatures/'.$inventoryTransferVoucher->creator->signature,['height'=>'50px','style'=>'height:50px;']); 
				 ?></br>
				<span style="font-size: 14px;font-weight: bold;">Authorised Signatory</span>
				</br>
				<span style="font-size:14px;"><?= h($inventoryTransferVoucher->creator->name) ?></span><br/>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</div>
<?php } ?>
<table width="100%" class="divFooter">
	<tr>
		<td align="right">
	
		</td>
	</tr>
</table>	
</div>

 
 