<table class='table' style='border:1px; ' border="1px" >
	<?php foreach($ClosingBalanceForPrint as $key=>$data){ 
		 ?>
		 <?php if($OpeningBalanceForPrint[$key]['balance'] > 0 || abs($TransactionDr[$key]['balance']) > 0 || abs($TransactionCr[$key]['balance']) > 0  || abs($data['balance']) > 0 ){ ?>
		<tr>
			<td width="20%">
				
				<?php echo $data['name']; ?>
					 
				
			</td>
				
				<?php if($OpeningBalanceForPrint[$key]['balance'] > 0){ ?>
								<td width="13%"><?php echo $OpeningBalanceForPrint[$key]['balance'];
										?></td>
								<td  width="13%"><?php echo "-" ?></td>
								<?php }else{ ?>
								<td  width="13%"><?php echo "-"; ?></td>
								<td  width="13%"><?php echo abs($OpeningBalanceForPrint[$key]['balance']);  ?></td>
								<?php } ?>


								
								<td width="13%"><?php echo abs($TransactionDr[$key]['balance']);
										?></td>
								
								<td width="13%"><?php echo abs($TransactionCr[$key]['balance']);
									  ?></td>
								
								


								<?php if($data['balance'] > 0){ ?>
								<td width="13%"><?php echo $data['balance'];
									@$totalDr=@$totalDr+$data['balance'];	?></td>
								<td width="13%"><?php echo "-" ?></td>
								<?php }else{ ?>
								<td width="13%"><?php echo "-"; ?></td>
								<td width="13%"><?php echo abs($data['balance']);  
									@$totalCr=@$totalCr+abs($data['balance']);  ?></td>
								<?php } ?>
					
							
		</tr>
		<?php } ?>
	<?php } ?>
</table>
