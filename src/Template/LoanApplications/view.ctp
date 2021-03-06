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
</style>
<style type="text/css" media="print">
@page {
    size: auto;   /* auto is the initial value */
    margin: 0 5px 0 20px;  /* this affects the margin in the printer settings */
}
</style>

<div style="border:solid 1px #c7c7c7;background-color: #FFF;padding: 10px;margin: auto;width: 55%; height:100%;font-size: 12px;" class="maindiv">    
        <table width="100%" class="divHeader">
        <tr>
            <td width="30%"></td>
            <td align="center" width="30%" style="font-size: 12px;"><div align="center" style="font-size: 16px;font-weight: bold;color: #0685a8;">Loan application</div></td>
            <td align="right" width="40%" style="font-size: 12px;">
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div style="border:solid 2px #0685a8;margin-bottom:5px;margin-top: 5px;"></div>
            </td>
        </tr>
    </table>
    <table width="100%">
       <tr>
                        <td>Employee Name</td>
                        <td width="20" align="center">:</td>
                        <td><?= h($loanApplication->employee->name) ?></td>
                    </tr>
                   <tr>
                        <td>Amount Of Loan</td>
                        <td width="20" align="center">:</td>
						<td><?= h($loanApplication->amount_of_loan) ?></td>
                    </tr>
					<tr>
                        <td>Reason For Loan</td>
                        <td width="20" align="center">:</td>
						<td><?= h($loanApplication->reason_for_loan) ?></td>
                    </tr>
					<tr>
                        <td>Remarks</td>
                        <td width="20" align="center">:</td>
						<td><?= h($loanApplication->remark) ?></td>
                    </tr>
					<?php if($loanApplication->status=="approved"){ ?>
					 <tr>
                        <td>Installments start from</td>
                        <td width="20" align="center">:</td>
                        <td><?= h(@$loanApplication->installment_start_month.'-'.$loanApplication->installment_start_year) ?></td>
                    </tr>
					<tr>
                        <td>Instalment Amount</td>
                        <td width="20" align="center">:</td>
						<td><?= h($loanApplication->instalment_amount) ?></td>
                    </tr>
					<tr>
                        <td>No. Of Instalment</td>
                        <td width="20" align="center">:</td>
						<td><?= h($loanApplication->no_of_instalment) ?></td>
                    </tr>
					<?php }else{ ?>
					<tr>
                        <td>Status</td>
                        <td width="20" align="center">:</td>
						<td><?= h($loanApplication->status) ?></td>
                    </tr>
					<?php } ?>
        </tr>
    </table>

    
  
</div>

