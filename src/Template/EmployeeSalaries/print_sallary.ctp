<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Print salary sheet</h3>
	</div>
	<div class="panel-body">
		<?php $yearFrom=date('Y',strtotime($financial_year->date_from)); ?>
		<form method="post" class="hide_at_print">
			<select name="month_year">
				<option value="4-<?php echo $yearFrom; ?>">April-<?php echo $yearFrom; ?></option>
				<option value="5-<?php echo $yearFrom; ?>">May-<?php echo $yearFrom; ?></option>
				<option value="6-<?php echo $yearFrom; ?>">June-<?php echo $yearFrom; ?></option>
				<option value="7-<?php echo $yearFrom; ?>">July-<?php echo $yearFrom; ?></option>
				<option value="8-<?php echo $yearFrom; ?>">August-<?php echo $yearFrom; ?></option>
				<option value="9-<?php echo $yearFrom; ?>">September-<?php echo $yearFrom; ?></option>
				<option value="10-<?php echo $yearFrom; ?>">October-<?php echo $yearFrom; ?></option>
				<option value="11-<?php echo $yearFrom; ?>">November-<?php echo $yearFrom; ?></option>
				<option value="12-<?php echo $yearFrom; ?>">December-<?php echo $yearFrom; ?></option>
				<option value="1-<?php echo $yearFrom+1; ?>">January-<?php echo $yearFrom+1; ?></option>
				<option value="2-<?php echo $yearFrom+1; ?>">February-<?php echo $yearFrom+1; ?></option>
				<option value="3-<?php echo $yearFrom+1; ?>">March-<?php echo $yearFrom+1; ?></option>
			</select>
			<button type="submit">View</button>
		</form>
		
		<?php if(sizeof(@$Employees)>0){
			$division=[];
			$allDivisions=[];
			$salary_type=[];
			$Loan=[];
			$Others=[];
			foreach($Employees as $Employee){
				foreach($Employee->salaries as $salarie){
					if(@$salarie->emp_sal_div->company_id==$st_company_id){
					
						$allDivisions[$salarie->employee_salary_division_id]=@$salarie->emp_sal_div->name;
						$salary_type[$Employee->id][$salarie->employee_salary_division_id]=@$salarie->emp_sal_div->salary_type;
						$division[$Employee->id][$salarie->employee_salary_division_id]=$salarie->amount;
						
					}
					if(@$salarie->loan_amount > 0){
						$Loan[$Employee->id]=$salarie->loan_amount;
					}
					@$Others[$Employee->id]=@$Others[$Employee->id]+$salarie->other_amount;
				}
			}
			//pr($Loan);
			//pr($salary_type);
			
			?>
			<button type="button" onclick="window.print()" class="hide_at_print">Print</button>
			<button type="button" onclick="ExportToExcel('qwerty');" class="hide_at_print">Excel</button>
			<table class="table table-condensed table-bordered table-hover" id="qwerty">
				<tr>
					<th>Employee Name</th>
					<th>Atten.</th>
					<?php foreach($allDivisions as $DivisionId=>$DivisionName){
						echo '<th>'.$DivisionName.'</th>';
					} ?>
					<th>Loan installment</th>
					<th>Others amount</th>
					<th>Total</th>
				</tr>
				<?php $grand_total=0; $total_sal=[];  $total_loan=0; $total_other=0;  $Totalcolumn=[];  $loanTot=0;
				foreach($Employees as $Employee){ ?>
				<tr>
					<td><?php echo $Employee->name; ?></td>
					<td><?php echo @$EmployeeAtten[@$Employee->id]; ?></td>
					<?php $total_add=0; $total_ded=0;  
					$colspan=0;
					foreach($allDivisions as $DivisionId=>$DivisionName){
						$colspan++;
						
						echo '<td align="right">'.round(@$division[$Employee->id][$DivisionId]).'</td>';
						 if(@$salary_type[$Employee->id][$DivisionId]=="addition"){
							@$total_add+=round(@$division[$Employee->id][$DivisionId],2);
						}else  if(@$salary_type[$Employee->id][$DivisionId]=="deduction"){
							@$total_ded+=round(@$division[$Employee->id][$DivisionId],2);
						}
						@$Totalcolumn[$DivisionId]=@$Totalcolumn[$DivisionId]+round(@$division[$Employee->id][$DivisionId],2);
					} ?>
					<td align="right"><?php echo @$Loan[$Employee->id]; $total_loan+=@$Loan[$Employee->id];?></td>
					<td align="right"><?php echo @$Others[$Employee->id]; $total_other+=@$Others[$Employee->id];?></td>
					<?php $p=@$total_add-@$total_ded-@$Loan[$Employee->id];
						$q=$p-@$Others[$Employee->id]; 
						$grand_total+=round($q,2);
					?>
					<td align="right"><?php echo round(@$q); ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td></td>
					<?php foreach($allDivisions as $DivisionId=>$DivisionName){
						echo '<th style="text-align:right;">'.($Totalcolumn[$DivisionId]).'</th>';
					} ?>
					<th style="text-align:right;"><?php echo $total_loan; ?></th>
					<th style="text-align:right;"><?php echo $total_other; ?></th>
					<th align="right"><b><?php echo $grand_total; ?></b></th>
				</tr>
			</table>
		<?php }else{
			echo 'Salary not submited.';
		}?>
		
	</div>
</div>
<style type="text/css" media="print">
@page {
    size: auto;   /* auto is the initial value */
    margin: 0;  /* this affects the margin in the printer settings */
}
</style>
<script type="text/javascript">
	function ExportToExcel(mytblId){
       var htmltable= document.getElementById(mytblId);
       var html = htmltable.outerHTML;
       window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
    }
</script>