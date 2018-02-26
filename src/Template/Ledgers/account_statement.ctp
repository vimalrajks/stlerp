<?php //pr($Ledgers->toArray()); exit;
$url_excel="/?".$url;

	if(empty(@$transaction_from_date)){
			$transaction_from_date=" ";
		}else{
			$transaction_from_date=$transaction_from_date;
		} 

		if(empty($transaction_to_date)){
			$transaction_to_date=" ";
		}else{
			$transaction_to_date=$transaction_to_date;
		}
	$opening_balance_ar1=[];
	$closing_balance_ar=[];
	if(!empty(@$Ledgers))
	{
		foreach($Ledgers as $Ledger)
		{
			if($Ledger->voucher_source == 'Opening Balance')
			{
				@$opening_balance_ar1['debit']+=$Ledger->debit;
				@$opening_balance_ar1['credit']+=$Ledger->credit;
			}
			else
			{
				@$opening_balance_total['debit']+=$Ledger->debit;
				@$opening_balance_total['credit']+=$Ledger->credit;			
			}
			
			@$closing_balance_ar['debit']+=$Ledger->debit;
			@$closing_balance_ar['credit']+=$Ledger->credit;
			//pr($closing_balance_ar);
		}	
		
	}

?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">Account Statement</span>
		</div>
		<div class="actions">
			<?php echo $this->Html->link( '<i class="fa fa-file-excel-o"></i> Excel', '/Ledgers/Export-Ob/'.$url_excel.'',['class' =>'btn btn-sm green tooltips','target'=>'_blank','escape'=>false,'data-original-title'=>'Download as excel']); ?>
		</div>
		<div class="portlet-body form">
	<form method="GET" >
				<table class="table table-condensed" >
				<tbody>
					<tr>
					<td>
						<div class="row">
							<div class="col-md-4">
									<?php echo $this->Form->input('ledger_account_id', ['empty'=>'--Select--','options' => $ledger,'empty' => "--Select Ledger Account--",'label' => false,'class' => 'form-control input-sm select2me','required','value'=>@$ledger_account_id]); ?>
							</div>
							<div class="col-md-4">
								<?php echo $this->Form->input('From', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => @date('d-m-Y', strtotime($from)),'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
								
							</div>
							<div class="col-md-4">
								<?php echo $this->Form->input('To', ['type' => 'text','label' => false,'class' => 'form-control input-sm date-picker','data-date-format' => 'dd-mm-yyyy','value' => @date('d-m-Y', strtotime($To)),'data-date-start-date' => date("d-m-Y",strtotime($financial_year->date_from)),'data-date-end-date' => date("d-m-Y",strtotime($financial_year->date_to))]); ?>
							</div>
						</div>
					</td>
					<td><button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button></td>
					</tr>
				</tbody>
			</table>
	</form>
		<!-- BEGIN FORM-->
<?php if(!empty($Ledger_Account_data)){  ?>
		<div class="row ">
			<div class="col-md-12">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<div class="col-md-12 " style="text-align:center; font-size: 20px;"><?php if(!empty(@$Ledger_Account_data->alias)){ echo @$Ledger_Account_data->name.'('.@$Ledger_Account_data->alias.')';
					}else{ echo @$Ledger_Account_data->name; }?></div>
					<div class="col-md-12" style="text-align:left; font-size: 15px;"> <?php echo $Ledger_Account_data->account_second_subgroup->account_first_subgroup->account_group->account_category->name; ?>->
					<?php echo $Ledger_Account_data->account_second_subgroup->account_first_subgroup->account_group->name; ?>->
					<?php echo $Ledger_Account_data->account_second_subgroup->account_first_subgroup->name; ?>->
					<?php echo $Ledger_Account_data->account_second_subgroup->name; ?></div>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div><br/>


		<div class="row ">
		<div class="col-md-12">
			<div class="col-md-8"></div>	
			<div class="col-md-4 caption-subject " align="left" style="background-color:#E7E2CB; font-size: 16px;"><b>Opening Balance : 
				<?php 
						$opening_balance_data=0;
					//	pr($opening_balance_ar); exit;
						if(!empty(@$opening_balance_ar)){ 
							if(@$opening_balance_ar['debit'] > @$opening_balance_ar['credit']){
								$opening_balance_data=@$opening_balance_ar['debit'] - @$opening_balance_ar['credit'];
								echo $this->Number->format(@$opening_balance_data.'Dr',[ 'places' => 2]);	echo " Dr";
							}
							else {
								$opening_balance_data=@$opening_balance_ar['credit'] - @$opening_balance_ar['debit'];
								echo $this->Number->format(@$opening_balance_data.'Cr',[ 'places' => 2]); echo " Cr";
							}					
						}
						else {   echo $this->Number->format('0',[ 'places' => 2]); }
						$close_dr=0;
						$close_cr=0;
						if((@$opening_balance_ar['debit'] > 0) || (@$opening_balance_ar['credit'] > 0)){  
							if(@$opening_balance_ar['debit'] > @$opening_balance_ar['credit']){  
								$close_dr=@$opening_balance_data+@$opening_balance_total['debit'];
								$close_cr=@$opening_balance_total['credit'];
							}
							else if(@$opening_balance_ar['credit'] > @$opening_balance_ar['debit']){ 
								$close_cr=@$opening_balance_data+@$opening_balance_total['credit'];
								$close_dr=@$opening_balance_total['debit'];
							}else if($opening_balance_ar['debit']== $opening_balance_ar['credit']){ 
								if(@$closing_balance_ar['debit'] > @$closing_balance_ar['credit']){ 
								$close_dr=@$closing_balance_ar['debit'];
								$close_cr=@$closing_balance_ar['credit'];
								}else{
									$close_dr=@$closing_balance_ar['debit'];
									$close_cr=@$closing_balance_ar['credit'];
								}
							}
							}else if((@$opening_balance_ar['debit']== 0) && (@$opening_balance_ar['credit']== 0)){ 
								$close_dr=@$opening_balance_total['debit'];
								$close_cr=@$opening_balance_total['credit'];
								
							}else if($opening_balance_ar['debit']== $opening_balance_ar['credit']){ 
								if(@$closing_balance_ar['debit'] > @$closing_balance_ar['credit']){ 
								$close_dr=@$closing_balance_ar['debit'];
								$close_cr=@$closing_balance_ar['credit'];
								}else{
									$close_dr=@$closing_balance_ar['debit'];
									$close_cr=@$closing_balance_ar['credit'];
								}
							}	
						//pr($close_dr); exit;
				?>  
			</b>
			
			</div>
		</div>
		<div class="col-md-12">
				
		 
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>Transaction Date</th>
						<th>Source</th>
						<th>Voucher No</th>
						<th>Reference</th>
						<th>Party</th>
						<th>GST</th>
						<th>VAT</th>
						<th>CGST</th>
						<th>SGST</th>
						<th>IGST</th>
						<th>Total</th>
						<th style="text-align:right;">Dr</th>
						<th style="text-align:right;">Cr</th>
					</tr>
				</thead>
				<tbody>
				<?php  $total_balance_acc=0; $total_debit=0; $total_credit=0;
				foreach($Ledgers as $ledger): 
				$url_path="";
				$cgst_amt=0;
				$sgst_amt=0;
				$igst_amt=0;
				$emp_id="No";
				if($ledger->voucher_source=="Journal Voucher"){
					$Receipt=$url_link[$ledger->id];
					//pr($Receipt->voucher_no); 
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/JournalVouchers/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Payment Voucher"){
					$Receipt=$url_link[$ledger->id];
					//pr($Receipt->voucher_no);exit;
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/Payments/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Petty Cash Payment Voucher"){
					$Receipt=$url_link[$ledger->id];
					//pr($url_link[$ledger->id]);exit;
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/petty-cash-vouchers/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Contra Voucher"){
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/contra-vouchers/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Receipt Voucher"){ 
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/receipts/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Invoice"){ 
					$invoice=$url_link[$ledger->id];
					$voucher_no=h(($invoice->in1.'/IN-'.str_pad($invoice->in2, 3, '0', STR_PAD_LEFT).'/'.$invoice->in3.'/'.$invoice->in4));
					if($invoice->invoice_type=="GST"){
					$url_path="/invoices/gst-confirm/".$ledger->voucher_id;	
					}else{
					$url_path="/invoices/confirm/".$ledger->voucher_id;
					}
					if($invoice->invoice_type=="GST"){
						$cgst_amt=$invoice->total_cgst_amount;
						$sgst_amt=$invoice->total_sgst_amount;
						$igst_amt=$invoice->total_igst_amount;
						
					}else{
						$cgst_amt="-";
						$sgst_amt="-";
						$igst_amt="-";
					}
					if(in_array($invoice->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
					
					
				}else if($ledger->voucher_source=="Sale Return"){ 
					$salereturn=$url_link[$ledger->id];
					//pr($salereturn->sale_return_type); exit; 
					$voucher_no=h(($salereturn->sr1.'/SR-'.str_pad($salereturn->sr2, 3, '0', STR_PAD_LEFT).'/'.$salereturn->sr3.'/'.$salereturn->sr4));
					if($salereturn->sale_return_type=="GST"){
					$url_path="/sale-returns/gst-sales-edit/".$ledger->voucher_id;	
					}else{
					$url_path="/sale-returns/edit/".$ledger->voucher_id;
					}
					if($salereturn->sale_return_type=="GST"){
						$cgst_amt=$salereturn->total_cgst_amount;
						$sgst_amt=$salereturn->total_sgst_amount;
						$igst_amt=$salereturn->total_igst_amount;
						
					}else{
						$cgst_amt="-";
						$sgst_amt="-";
						$igst_amt="-";
					}
					
					
				}else if($ledger->voucher_source=="Invoice Booking"){
					$ibs=$url_link[$ledger->id];
					//pr($ibs); exit;
					$voucher_no=h(($ibs->ib1.'/IB-'.str_pad($ibs->ib2, 3, '0', STR_PAD_LEFT).'/'.$ibs->ib3.'/'.$ibs->ib4));
					if($ibs->gst=="yes"){
					$url_path="/invoice-bookings/gst-invoice-booking-view/".$ledger->voucher_id;	
					}else{
					$url_path="/invoice-bookings/view/".$ledger->voucher_id;
					}
					
					//$url_path="/invoice-bookings/view/".$ledger->voucher_id;
					if($ibs->gst=="yes"){ 
						foreach($ibs->invoice_booking_rows as $ibr)
						{ 
							$cgst_amt=$cgst_amt+$ibr->cgst;
							$sgst_amt=$sgst_amt+$ibr->sgst;
							$igst_amt=$igst_amt+$ibr->igst;
						}
					}else{
							$cgst_amt="-";
							$sgst_amt="-";
							$igst_amt="-";
					}
					if(in_array($ibs->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Non Print Payment Voucher"){
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/nppayments/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Debit Note"){
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/debit-notes/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Credit Note"){
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/credit-notes/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}else if($ledger->voucher_source=="Purchase Return"){
					$Receipt=$url_link[$ledger->id];
					$voucher_no=h(str_pad($Receipt->voucher_no,4,'0',STR_PAD_LEFT));
					$url_path="/purchase-returns/view/".$ledger->voucher_id;
					if(in_array($Receipt->created_by,$allowed_emp)){
							$emp_id="Yes";
					}
				}
				
				if($ledger->voucher_source != 'Opening Balance')	
				{
				?>
				<tr>
						<td><?php echo date("d-m-Y",strtotime($ledger->transaction_date)); ?></td>
						<td><?= h($ledger->voucher_source); ?></td>
						<td>
						
						<?php
							if($emp_id=="Yes"){
								if(!empty($url_path)){
									echo $this->Html->link($voucher_no ,$url_path,['target' => '_blank']);
								}else{
									echo str_pad($ledger->voucher_id,4,'0',STR_PAD_LEFT);
								}
							}else{
								echo $voucher_no;
							}
						?>
						</td>
						<td>
							<?php if($ledger->voucher_source=="Invoice"){ ?>
								<?php echo $url_link[$ledger->id]->customer->customer_name.' '.$url_link[$ledger->id]->customer->alias; ?>
							<?php }elseif($ledger->voucher_source=="Invoice Booking"){ ?>
								<?php echo $url_link[$ledger->id]->vendor->company_name; ?>
							<?php } ?>
						</td>
						<td>
							<?php if($ledger->voucher_source=="Invoice"){ ?>
								<?php echo $url_link[$ledger->id]->customer->gst_no; ?>
							<?php }elseif($ledger->voucher_source=="Invoice Booking"){ ?>
								<?php echo $url_link[$ledger->id]->vendor->gst_no; ?>
							<?php } ?>
						</td>
						<td align="right">
							<?php if($ledger->voucher_source=="Invoice"){ ?>cst_vat
								<?php echo @$url_link[$ledger->id]->sale_tax->invoice_description; ?>
							<?php }elseif($ledger->voucher_source=="Invoice Booking"){ ?>
								<?php //echo $url_link[$ledger->id]->cst_vat;
								if($url_link[$ledger->id]->cst_vat=="VAT"){
										echo $this->Number->format($url_link[$ledger->id]->total_saletax,[ 'places' => 2]); 
									}else{
										echo "-";
									}
							 } ?>
						</td>
						<td align="right"><?= $this->Number->format($cgst_amt,[ 'places' => 2]); ?></td>
						<td align="right"><?= $this->Number->format($sgst_amt,[ 'places' => 2]); ?></td>
						<td align="right"><?= $this->Number->format($igst_amt,[ 'places' => 2]); ?></td>
						<?php if($ledger->voucher_source=="Invoice Booking"){ ?>
						<td align="right"><?= $this->Number->format($url_link[$ledger->id]->total,[ 'places' => 2]); ?></td>
						 <?php }else{ ?>
						 <td><?php echo "-"; ?></td>
						 <?php } ?>
						<td align="right"><?= $this->Number->format($ledger->debit,[ 'places' => 2]); 
							$total_debit+=$ledger->debit; ?></td>
						<td align="right"><?= $this->Number->format($ledger->credit,[ 'places' => 2]); 
							$total_credit+=$ledger->credit; ?></td>

				</tr>
				<?php } endforeach; ?>
				<tr>
					<td colspan="10" align="right">Total</td>
					<td align="right" ><?= number_format(@$opening_balance_total['debit'],2,'.',',') ;?> Dr</td>
					<td align="right" ><?= number_format(@$opening_balance_total['credit'],2,'.',',')?> Cr</td>
					
				<tr>
				</tbody>
			</table>
			</div>
			
			<div class="col-md-12">
				<div class="col-md-8"></div>	
				<div class="col-md-4 caption-subject " align="left" style="background-color:#E3F2EE; font-size: 16px;"><b>Closing Balance:  </b>
				<?php 
				 
				$closing_balance=@$close_dr-@$close_cr;
					
						echo $this->Number->format(abs($closing_balance),['places'=>2]);
						if($closing_balance>0){
							echo 'Dr';
						}else if($closing_balance <0){
							echo 'Cr';
						}
						
				?>
				</div>
			</div>
			
		</div>
<?php } ?>
</div></div>
</div>