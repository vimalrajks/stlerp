<?php
$options=[];
foreach($ReferenceBalances as $ReferenceBalance){
	if($cr_dr=="Dr"){
		$due_amount=$ReferenceBalance->credit-$ReferenceBalance->debit;
		$total=$ReferenceBalance->credit;
	}else{
		$due_amount=$ReferenceBalance->debit-$ReferenceBalance->credit;
		$total=$ReferenceBalance->debit;
	}
	
	if($due_amount>0){
		$options[]=['text' =>$ReferenceBalance->reference_no.' ( Due: '.$due_amount.', Total: '.$total.')', 'value' => $ReferenceBalance->reference_no, 'due_amount' => $due_amount];
	}
}
echo $this->Form->input('ref_no', ['empty'=>'--Select-','options'=>$options,'label' => false,'class' => 'form-control input-sm ']); ?>