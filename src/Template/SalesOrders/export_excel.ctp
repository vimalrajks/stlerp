<?php 

	$date= date("d-m-Y"); 
	$time=date('h:i:a',time());

	$filename="Sales_orders_report_".$date.'_'.$time;

	header ("Expires: 0");
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: application/vnd.ms-excel");
	header ("Content-Disposition: attachment; filename=".$filename.".xls");
	header ("Content-Description: Generated Report" );

?>			
				<table border="1">
					<thead>
					<tr>
						<td colspan="7" align="center">
						<b> Sales Order Report
						<?php if(!empty($From) || !empty($To)){ echo date('d-m-Y',strtotime($From)); ?> TO <?php echo date('d-m-Y',strtotime($To));} ?> </b>
						</td>
					</tr>
						<tr>
							<th>S. No.</th>
							<th>Sales Order No</th>
							<th>Quotation No</th>
							<th>Customer</th>
							<th>PO No.</th>
							<th>Amount</th>
							<th>Date</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php $i=0; foreach ($salesOrders as $salesOrder){ 
						 if((in_array($salesOrder->customer->employee_id,$allowed_emp) || in_array($salesOrder->customer->employee_id,$allowed_acc)) || ($salesOrder->created_by == $s_employee_id) || ($salesOrder->employee_id == $s_employee_id)){
						if($status=='Converted Into Invoice' ){
							if(@$total_sales[@$salesOrder->id] == @$total_qty[@$salesOrder->id] && $st_year_id==@$salesOrder->financial_year_id){ 
							
						?>
						<tr>
							<td><?= h(++$i) ?></td>
							<td><?= h(($salesOrder->so1.'/SO-'.str_pad($salesOrder->so2, 3, '0', STR_PAD_LEFT).'/'.$salesOrder->so3.'/'.$salesOrder->so4)) ?></td>
							<?php if($salesOrder->quotation_id != 0){ ?>
							<td>
							<?php echo $salesOrder->quotation->qt1.'/QT-'.str_pad($salesOrder->quotation->qt2, 3, '0', STR_PAD_LEFT).'/'.$salesOrder->quotation->qt3.'/'.$salesOrder->quotation->qt4; ?>
							</td><?php }else{ ?><td>-</td><?php } ?>
							<td><?= h($salesOrder->customer->customer_name) ?></td>
							<td><?= h($salesOrder->customer_po_no) ?></td>
							<td><?= h($salesOrder->total_taxable_value) ?></td>
							<td><?php echo date("d-m-Y",strtotime($salesOrder->created_on)); ?></td>
							<td>
								<?php 
									echo "Converted Into Invoice";
								?>
							</td>
						</tr>
						<?php }} else { ?>
							<?php if(@$total_sales[@$salesOrder->id] > @$total_qty[@$salesOrder->id]){ ?> 
							<tr>
							<td><?= h(++$i) ?></td>
							<td><?= h(($salesOrder->so1.'/SO-'.str_pad($salesOrder->so2, 3, '0', STR_PAD_LEFT).'/'.$salesOrder->so3.'/'.$salesOrder->so4)) ?></td>
							<?php if($salesOrder->quotation_id != 0){ ?>
							<td>
							<?php echo $salesOrder->quotation->qt1.'/QT-'.str_pad($salesOrder->quotation->qt2, 3, '0', STR_PAD_LEFT).'/'.$salesOrder->quotation->qt3.'/'.$salesOrder->quotation->qt4; ?>
							</td><?php }else{ ?><td>-</td><?php } ?>
							<td><?= h($salesOrder->customer->customer_name) ?></td>
							<td><?= h($salesOrder->customer_po_no) ?></td>
							<td><?= h($this->Money->indianNumberFormat($salesOrder->total_taxable_value)) ?></td>
							<td><?php echo date("d-m-Y",strtotime($salesOrder->created_on)); ?></td>
							<td>
								<?php 
									echo "Pending";
								?>
							</td>
						</tr>
							
						<?php } } }} ?>
					</tbody>
				</table>
				
