<style>
fieldset {
    font-family: sans-serif;
    border: 2px solid #1F497D;
    background: rgba(221, 221, 221, 0.32);
    border-radius: 5px;
    padding: 15px;
	margin-top: 5px;
}

.divhight{

}

fieldset legend {
    background: #1F497D;
    color: #fff;
    padding: 5px 10px ;
    font-size: 13px;
    border-radius: 6px;
    box-shadow: 0 0 0 1px #ddd;
    margin-left: 10px;
}</style>
<div class="portlet light bordered ">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel uppercase">User Rights - <?php echo $EmployeeName; ?></span>
		</div>
	</div>
	<div class="portlet-body form">
		<?= $this->Form->create($userRight) ?>
		<div class="row ">
		<div class="divhight">
			<div class="col-md-12">
				<div class="col-md-6">
                	<fieldset>
        			<legend>Quotation</legend>
 					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(1,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.0.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>1,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(2,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.1.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>2,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(21,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.21.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>21,$checked_status]);
							?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(30,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.30.page_id', ['label' => 'Close','class' => '','type'=>'checkbox','value'=>30,$checked_status]);
							?>
						</label>
					</div>
                    </fieldset>
				</div>
				<div class="col-md-6">
                	<fieldset>
        			<legend>Sales-Order</b></legend>
 					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(3,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.3.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>3,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(4,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.4.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>4,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(22,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.22.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>22,$checked_status]); ?>
						</label>
						
					</div>
					</fieldset>
				</div>

				<div class="col-md-6">
                	<fieldset>
        			<legend>Job-Card</b></legend>
 					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(5,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.5.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>5,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(6,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.6.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>6,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(24,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.24.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>24,$checked_status]); ?>
						</label>
						<!--<label class="checkbox-inline">
							<?php 
							if(in_array(34,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.34.page_id', ['label' => 'Close','class' => '','type'=>'checkbox','value'=>34,$checked_status]); ?>
						</label>-->
						
					</div>
					</fieldset>
				</div>
				
				<!--<div class="col-md-6">
                	<fieldset>
        			<legend>Challan</b></legend>
 					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(11,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.11.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>11,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(12,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.12.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>12,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(28,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.28.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>28,$checked_status]); 
							?>
						</label>
					</div>
					</fieldset>
				</div>-->
				<div class="col-md-6">
                	<fieldset>
        			<legend>Invoice</b></legend>
 					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(7,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.7.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>7,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(8,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.8.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>8,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(23,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.23.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>23,$checked_status]); 
							?>
						</label>
						<!--<label class="checkbox-inline">
								<?php 
								if(in_array(33,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.33.page_id', ['label' => 'Close','class' => '','type'=>'checkbox','value'=>33,$checked_status]);
								?>
						</label>-->
					</div>
					</fieldset>
				</div>
				
				<div class="col-md-6">
                	<fieldset>
        			<legend>Purchase Order</b></legend>
					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(13,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.13.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>13,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(14,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.14.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>14,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(31,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.31.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>31,$checked_status]); 
							?>
						</label>
					</div>
					</fieldset>
				</div>
				<div class="col-md-6">
                	<fieldset>
        			<legend>Inventory Voucher</b></legend>
					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(9,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.9.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>9,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(10,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.10.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>10,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(154,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.154.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>154,$checked_status]); ?>
						</label>
					</div>
					</fieldset>
				</div>
				
				<div class="col-md-6">
                	<fieldset>
        			<legend>Grn</b></legend>
					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(15,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.15.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>15,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(16,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.16.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>16,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
                        							<?php
                        							if(in_array(35,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
                        							echo $this->Form->input('user_rights.35.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>35,$checked_status]); ?>
                        </label>
					</div>
					</fieldset>
				</div>
				<div class="col-md-6">
                	<fieldset>
        			<legend>Invoice Booking</b></legend>
						<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(17,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.17.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>17,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(18,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.18.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>18,$checked_status]); ?>
						</label>
                        <label class="checkbox-inline">
                        		<?php
                        			if(in_array(123,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
                        			echo $this->Form->input('user_rights.123.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>123,$checked_status]); ?>
                        </label>
						</div>
                        </fieldset>
					</div>
					<div class="col-md-6">
                	<fieldset>
        			<legend>MaterialIndents</b></legend>
					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(124,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.124.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>124,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(165,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.165.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>165,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(125,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.125.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>125,$checked_status]); ?>
						</label>
						<!--<label class="checkbox-inline">
							<?php 
							if(in_array(120,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.120.page_id', ['label' => 'Report','class' => '','type'=>'checkbox','value'=>120,$checked_status]); ?>
						</label>-->
						
					</div>
					</fieldset>
				</div>
				<div class="col-md-6">
                	<fieldset>
        			<legend>Sales Return</b></legend>
						<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(133,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.133.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>133,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(134,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.134.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>134,$checked_status]); ?>
						</label>
                        <label class="checkbox-inline">
						<?php
							if(in_array(135,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.135.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>135,$checked_status]); ?>
                        </label>  
						<label class="checkbox-inline">
						<?php
							if(in_array(136,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.136.page_id', ['label' => 'Report','class' => '','type'=>'checkbox','value'=>136,$checked_status]); ?>
                        </label>
						</div>
                        </fieldset>
					</div>
					<div class="col-md-6">
                	<fieldset>
        			<legend>Purchase Return</b></legend>
					<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(129,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.129.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>129,$checked_status]); ?>
						</label>
						
						<label class="checkbox-inline">
							<?php 
							if(in_array(130,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.130.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>130,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(131,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.131.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>131,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
						<?php
							if(in_array(132,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.132.page_id', ['label' => 'Report','class' => '','type'=>'checkbox','value'=>132,$checked_status]); ?>
                        </label>
						
					</div>
					</fieldset>
				</div>
				<div class="col-md-6">
                    	<fieldset>
        				<legend>InventoryTransferVouchers</b></legend>
						<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(137,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.137.page_id', ['label' => 'Create','class' => '','type'=>'checkbox','value'=>137,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(139,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.139.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>139,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(138,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.138.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>138,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(140,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.140.page_id', ['label' => 'Index','class' => '','type'=>'checkbox','value'=>140,$checked_status]); ?>
						</label>
						</div>
					</fieldset>
				</div></br>
				<div class="col-md-6">
                    	<fieldset>
        				<legend>Challans</b></legend>
						<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(11,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.11.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>11,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(12,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.12.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>12,$checked_status]); ?>
						</label>
						</div>
					</fieldset>
				</div></br>
				<div class="col-md-6">
                    	<fieldset>
        				<legend>User-Rights/Logins</b></legend>
						<div class="checkbox-list">
						<label class="checkbox-inline">
							<?php 
							if(in_array(19,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.19.page_id', ['label' => 'Login','class' => '','type'=>'checkbox','value'=>19,$checked_status]); ?>
						</label>
						<label class="checkbox-inline">
							<?php 
							if(in_array(20,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
							echo $this->Form->input('user_rights.20.page_id', ['label' => 'User-Right','class' => '','type'=>'checkbox','value'=>20,$checked_status]); ?>
						</label>
						</div>
					</fieldset>
				</div></br>
        <div class="col-md-12">
        <fieldset>
        <legend>Report</b></legend>
				<div class="col-md-12">
					<div class="titletext" style="text-decoration: underline;"><b>Over Due Report </b></div>
				</div>
				<div class="col-md-12"><br/></div>
				<div class="col-md-3">
					<div class="titletext"><b>For Customer</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(142,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.142.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>142,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>For Vendor</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(178,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.178.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>178,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-12">
					<div class="titletext" style="text-decoration: underline;"><b>Sales Report </b></div>
				</div>
				<div class="col-md-12"><br/></div>
				<div class="col-md-3">
					<div class="titletext"><b>Non GST</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(128,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.128.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>128,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>GST</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(179,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.179.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>179,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>GST SalesMan</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(180,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.180.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>180,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Segment Wise</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(181,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.181.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>181,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Purchase-Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(182,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.182.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>182,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Inventory Daily Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(163,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.163.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>163,$checked_status]); ?>
							</label>
						</div>
				</div>
		
				<div class="col-md-3">
					<div class="titletext"><b>Stock-Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(36,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.36.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>36,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Trial Balance</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(162,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.162.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>162,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Balance-Sheet</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(37,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.37.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>37,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Profit Loss Statement</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(38,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.38.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>38,$checked_status]); ?>
							</label>
						</div>
				</div>
					<div class="col-md-3">
					<div class="titletext"><b>Daily Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(41,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.41.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>41,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>HSN Wise Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(175,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.175.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>175,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Invoice HSN Wise</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(176,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.176.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>176,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Material Indent Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(39,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.39.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>39,$checked_status]); ?>
							</label>
						</div>
				</div>
				
				<div class="col-md-3">
					<div class="titletext"><b>Account-Statement</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(40,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.40.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>40,$checked_status]); ?>
							</label>
						</div>
				</div>
				
			<div class="col-md-3">
					<div class="titletext"><b>Account-Statement-Ref</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(183,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.183.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>183,$checked_status]); ?>
							</label>
						</div>
				</div>

				
				
				<div class="col-md-3">
					<div class="titletext"><b>User Logs Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(164,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.164.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>164,$checked_status]); ?>
							</label>
						</div>
				</div>
				
				
				<div class="col-md-4">
					<div class="titletext"><b>Bank Reconciliation Report</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(126,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.126.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>126,$checked_status]); ?>
							</label>
							
							<label class="checkbox-inline">
								<?php 
								if(in_array(127,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.127.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>127,$checked_status]); ?>
							</label>
							
						</div>
				</div>
				
             </fieldset> 
		</div>
		<br/>
        
		<div class="col-md-12">
			<fieldset>
        	<legend>Master Setup</legend> 
            <div class="col-md-12">
 				<div class="col-md-3">
					<div class="titletext"><b>Customer</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(42,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.42.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>42,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(43,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.43.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>43,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(44,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.44.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>44,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(45,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.45.page_id', ['label' => 'Edit Company','class' => '','type'=>'checkbox','value'=>45,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Employee</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(46,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.46.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>46,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(47,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.47.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>47,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext">&nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(48,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.48.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>48,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(49,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.49.page_id', ['label' => 'Edit Company','class' => '','type'=>'checkbox','value'=>49,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Items</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(50,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.50.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>50,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(51,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.51.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>51,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext">&nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(52,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.52.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>52,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(53,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.53.page_id', ['label' => 'Edit Company','class' => '','type'=>'checkbox','value'=>53,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Supplier</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(54,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.54.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>54,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(55,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.55.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>55,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext">&nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(56,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.56.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>56,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(57,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.57.page_id', ['label' => 'Edit Company','class' => '','type'=>'checkbox','value'=>57,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Company</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(58,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.58.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>58,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(59,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.59.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>59,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext">&nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(60,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.60.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>60,$checked_status]); ?>
							</label>
						</div>
					</div>
				</div>
			<div class="col-md-12">
				<div class="col-md-3">
					<div class="titletext"><b>Item Categories</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(61,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.61.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>61,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Item Group</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(62,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.62.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>62,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Item Sub-Group</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(63,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.63.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>63,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Unit</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(64,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.64.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>64,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Customer Groups</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(65,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.65.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>65,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Customer Segments</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(66,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.66.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>66,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>SaleTaxes</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(67,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.67.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>67,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Filenames</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(68,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.68.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>68,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Transporters</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(70,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.70.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>70,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Districts</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(71,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.71.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>71,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Designations</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(72,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.72.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>72,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Departments</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(73,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.73.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>73,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Ledger Accounts</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(74,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.74.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>74,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Account References</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(76,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.76.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>76,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Opening Balance</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(78,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.78.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>78,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Items Opening Balance</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(80,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.80.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>80,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Terms Conditions</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(82,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.82.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>82,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Holidays</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(218,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.218.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>218,$checked_status]); ?>
							</label>
						</div>
				</div>
				<!--<div class="col-md-3">
					<div class="titletext"><b>Item Price Factor</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(84,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.84.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>84,$checked_status]); ?>
							</label>
						</div>
				</div>-->
				<div class="col-md-3">
					<div class="titletext"><b>Quotation Close Reasons</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(86,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.86.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>86,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Leave Types</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(88,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.88.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>88,$checked_status]); ?>
							</label>
						</div>
				</div>
                <div class="col-md-3">
					<div class="titletext"><b>Financial Year</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(121,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.121.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>121,$checked_status]); ?>
							</label>
						</div>
				</div>
                <div class="col-md-3">
					<div class="titletext"><b>Financial Month</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(122,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.122.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>122,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Dispatch Details</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(167,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.167.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>167,$checked_status]); ?>
							</label>
						</div>
				</div>
				
				<div class="col-md-3">
					<div class="titletext"><b>Employee Hierarchy</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(169,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.169.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>169,$checked_status]); ?>
							</label>
						</div>
				</div>
                
                </div>
                </fieldset>
		</div>
		<div class="col-md-12">
        	<fieldset>
        		<legend>Voucher</legend>
			 <div class="col-md-12">
				<div class="col-md-3">
					<div class="titletext"><b>Voucher References</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(118,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.118.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>118,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(119,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.119.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>119,$checked_status]); ?>
							</label>
						</div>
				</div>
 		</div>
		<div class="col-md-12">
				<div class="col-md-3">
					<div class="titletext"><b>Payment</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(90,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.90.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>90,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(91,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.91.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>91,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(92,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.92.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>92,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(93,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.93.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>93,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Receipts</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(94,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.94.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>94,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(95,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.95.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>95,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(96,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.96.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>96,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(97,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.97.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>97,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Petty Cash Vouchers</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(98,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.98.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>98,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(99,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.99.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>99,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(100,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.100.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>100,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(101,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.101.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>101,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Contra Vouchers</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(102,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.102.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>102,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(103,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.103.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>103,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(104,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.104.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>104,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(105,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.105.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>105,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>CreditNotes</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(106,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.106.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>106,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(107,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.107.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>107,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(108,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.108.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>108,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(109,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.109.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>109,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Debit Notes</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(110,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.110.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>110,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(111,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.111.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>111,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(112,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.112.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>112,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(113,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.113.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>113,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Journal Vouchers</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(114,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.114.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>114,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(115,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.115.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>115,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(116,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.116.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>116,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(117,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.117.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>117,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"><b>Non Print Vouchers</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(171,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.171.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>171,$checked_status]); ?>
							</label>
						</div>
				</div><div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(172,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.172.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>172,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(173,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.173.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>173,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(174,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.174.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>174,$checked_status]); ?>
							</label>
						</div>
				</div>
				</div>
                </fieldset>
			</div>
			<div class="col-md-12">
				<fieldset>
					<legend>HR</legend>
					<div class="col-md-2">
						<div class="titletext"><b>Leave Application</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(184,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.184.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>184,$checked_status]); ?>
							</label>
						</div>
					</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(198,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.198.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>198,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(187,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.187.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>187,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(185,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.185.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>185,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(186,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.186.page_id', ['label' => 'Approve','class' => '','type'=>'checkbox','value'=>186,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(199,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.199.page_id', ['label' => 'Delete','class' => '','type'=>'checkbox','value'=>199,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
						<div class="titletext"><b>Travel Request</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(188,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.188.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>188,$checked_status]); ?>
							</label>
						</div>
					</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(200,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.200.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>200,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(190,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.190.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>190,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(191,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.191.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>191,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(189,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.189.page_id', ['label' => 'Approve','class' => '','type'=>'checkbox','value'=>189,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(201,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.201.page_id', ['label' => 'Delete','class' => '','type'=>'checkbox','value'=>201,$checked_status]); ?>
							</label>
						</div>
				</div>
					<div class="col-md-2">
						<div class="titletext"><b>Loan Application</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(192,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.192.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>192,$checked_status]); ?>
							</label>
						</div>
					</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(195,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.195.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>195,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(194,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.194.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>194,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(197,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.197.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>197,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(193,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.193.page_id', ['label' => 'Approve','class' => '','type'=>'checkbox','value'=>193,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(196,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.196.page_id', ['label' => 'Delete','class' => '','type'=>'checkbox','value'=>196,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
						<div class="titletext"><b>Salary Advances</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(202,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.202.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>202,$checked_status]); ?>
							</label>
						</div>
					</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(203,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.203.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>203,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(204,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.204.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>204,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(205,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.205.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>205,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(206,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.206.page_id', ['label' => 'Approve','class' => '','type'=>'checkbox','value'=>206,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-2">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(207,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.207.page_id', ['label' => 'Delete','class' => '','type'=>'checkbox','value'=>207,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
						<div class="titletext"><b>Employee Salary Division</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(208,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.208.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>208,$checked_status]); ?>
							</label>
						</div>
					</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(209,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.209.page_id', ['label' => 'Edit','class' => '','type'=>'checkbox','value'=>209,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(210,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.210.page_id', ['label' => 'Delete','class' => '','type'=>'checkbox','value'=>210,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(211,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.211.page_id', ['label' => 'List','class' => '','type'=>'checkbox','value'=>211,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
						<div class="titletext"><b>Employee Attendence</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(212,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.212.page_id', ['label' => 'Add','class' => '','type'=>'checkbox','value'=>212,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
						<div class="titletext"><b>Pay Slips</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(216,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.216.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>216,$checked_status]); ?>
							</label>
						</div>
					</div>	
				<div class="col-md-4">
						<div class="titletext"><b>Leave Sheet</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(217,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.217.page_id', ['label' => 'View','class' => '','type'=>'checkbox','value'=>217,$checked_status]); ?>
							</label>
						</div>
					</div>
				
				<div class="col-md-3">
						<div class="titletext"><b>Employee Salary</b></div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php 
								if(in_array(213,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.213.page_id', ['label' => 'Salary Sheet','class' => '','type'=>'checkbox','value'=>213,$checked_status]); ?>
							</label>
						</div>
					</div>	
					<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(214,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.214.page_id', ['label' => 'Print Salary Sheet','class' => '','type'=>'checkbox','value'=>214,$checked_status]); ?>
							</label>
						</div>
				</div>
				<div class="col-md-3">
					<div class="titletext"> &nbsp </div>
						<div class="checkbox-list">
							<label class="checkbox-inline">
								<?php
								if(in_array(215,$page_ids)){ $checked_status='checked'; }else{ $checked_status=''; }
								echo $this->Form->input('user_rights.215.page_id', ['label' => 'Employee For Salary','class' => '','type'=>'checkbox','value'=>215,$checked_status]); ?>
							</label>
						</div>
				</div>
					
				 </fieldset>	
			</div>
			</div>
		</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div align="center" style="width:100%; margin-top:10px">
				 <?= $this->Form->button(__('Update'),['class'=>'btn btn-lg btn-primary']) ?>
				</div>
			</div>
        </div>
		<?= $this->Form->end() ?>
	</div>


</div>
<style>
.titletext{
	font-size: 16px;
    color: #626262;
}
</style>