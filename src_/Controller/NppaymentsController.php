<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Nppayments Controller
 *
 * @property \App\Model\Table\NppaymentsTable $Nppayments
 */
class NppaymentsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
        $this->viewBuilder()->layout('index_layout');
        
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->Nppayments->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->Nppayments->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->Nppayments->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		
        $where = [];
        $vouch_no = $this->request->query('vouch_no');
        $From = $this->request->query('From');
        $To = $this->request->query('To');
        
        $this->set(compact('vouch_no','From','To'));
        
        if(!empty($vouch_no)){
            $where['Nppayments.voucher_no LIKE']=$vouch_no;
        }
        
        if(!empty($From)){
            $From=date("Y-m-d",strtotime($this->request->query('From')));
            $where['Nppayments.transaction_date >=']=$From;
        }
        if(!empty($To)){
            $To=date("Y-m-d",strtotime($this->request->query('To')));
            $where['Nppayments.transaction_date <=']=$To;
        }
        
        $this->paginate = [
            'contain' => []
        ];
        
        
        $nppayments = $this->paginate($this->Nppayments->find()->where($where)->where(['company_id'=>$st_company_id,'Nppayments.financial_year_id'=>$st_year_id])->contain(['NppaymentRows'=>function($q){
            $NppaymentRows = $this->Nppayments->NppaymentRows->find();
            $totalCrCase = $NppaymentRows->newExpr()
                ->addCase(
                    $NppaymentRows->newExpr()->add(['cr_dr' => 'Cr']),
                    $NppaymentRows->newExpr()->add(['amount']),
                    'integer'
                );
            $totalDrCase = $NppaymentRows->newExpr()
                ->addCase(
                    $NppaymentRows->newExpr()->add(['cr_dr' => 'Dr']),
                    $NppaymentRows->newExpr()->add(['amount']),
                    'integer'
                );
            return $NppaymentRows->select([
                    'total_cr' => $NppaymentRows->func()->sum($totalCrCase),
                    'total_dr' => $NppaymentRows->func()->sum($totalDrCase)
                ])
                ->group('nppayment_id')
                
                ->autoFields(true);
            
        }])->order(['voucher_no'=>'DESC']));
        

        $this->set(compact('nppayments','url','financial_month_first','financial_month_last'));
        $this->set('_serialize', ['nppayments']);
    }
	
	/* public function DataMigrate()
	{
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id'); 
		$Nppayments = $this->Nppayments->find()->contain(['NppaymentRows'])->toArray();
		
		foreach($Nppayments as $Nppayment){
			$total_dr=0;
			$total_cr=0;
			$bankAmt=0;
				foreach($Nppayment->nppayment_rows as $nppayment_row){ //pr($nppayment_row); exit;
					$OldReferenceDetails = $this->Nppayments->ReferenceDetails->OldReferenceDetails->find()->where(['nppayment_id'=>$Nppayment->id,'ledger_account_id'=>$nppayment_row->received_from_id])->toArray();
					
					if($OldReferenceDetails){
						foreach($OldReferenceDetails as $old_data){ //pr($old_data); exit;
							$ReferenceDetail = $this->Nppayments->ReferenceDetails->newEntity();
							$ReferenceDetail->company_id=$Nppayment->company_id;
							$ReferenceDetail->ledger_account_id=$old_data->ledger_account_id;
							$ReferenceDetail->reference_type=$old_data->reference_type;
							$ReferenceDetail->reference_no=$old_data->reference_no;
							$ReferenceDetail->debit = $old_data->debit;
							$ReferenceDetail->credit = $old_data->credit;
							$ReferenceDetail->nppayment_id = $Nppayment->id;
							$ReferenceDetail->nppayment_row_id = $nppayment_row->id;
							$ReferenceDetail->transaction_date = $Nppayment->transaction_date; 
							$this->Nppayments->ReferenceDetails->save($ReferenceDetail);
							}
						
					}
					
					$ledger = $this->Nppayments->Ledgers->newEntity();
					$ledger->company_id=$Nppayment->company_id;
					$ledger->ledger_account_id = $nppayment_row->received_from_id;
					if($nppayment_row->cr_dr=="Cr"){
					$ledger->credit = $nppayment_row->amount;
					$ledger->debit = 0;
					$total_cr=$total_cr+$nppayment_row->amount;
					}else{
					$ledger->credit = 0;
					$ledger->debit = $nppayment_row->amount;
					$total_dr=$total_dr+$nppayment_row->amount;
					}
					$ledger->voucher_id = $Nppayment->id;
					$ledger->voucher_source = 'Non Print Payment Voucher';
					$ledger->transaction_date = $Nppayment->transaction_date;
					$this->Nppayments->Ledgers->save($ledger);
				//pr($payment_row); exit;
				}
				$bankAmt=$total_dr-$total_cr;
				$ledger = $this->Nppayments->Ledgers->newEntity();
				$ledger->company_id=$Nppayment->company_id;
				$ledger->ledger_account_id = $Nppayment->bank_cash_id;
				if($bankAmt > 0){
					$ledger->credit = $bankAmt;
					$ledger->debit = 0;
				}else{
					$ledger->debit = abs($bankAmt);
					$ledger->credit = 0;
				}
				
				$ledger->voucher_id = $Nppayment->id;
				$ledger->voucher_source = 'Non Print Payment Voucher';
				$ledger->transaction_date = $Nppayment->transaction_date; //pr($ledger); exit;
				if($bankAmt != 0){
					$this->Nppayments->Ledgers->save($ledger);
				}
			
			
		}
		
		echo "Done";
		exit;
	} */
	
	public function exportExcel(){
		
		$this->viewBuilder()->layout('');
        
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        
        $where = [];
        $vouch_no = $this->request->query('vouch_no');
        $From = $this->request->query('From');
        $To = $this->request->query('To');
        
        $this->set(compact('vouch_no','From','To'));
        
        if(!empty($vouch_no)){
            $where['Nppayments.voucher_no LIKE']=$vouch_no;
        }
        
        if(!empty($From)){
            $From=date("Y-m-d",strtotime($this->request->query('From')));
            $where['Nppayments.transaction_date >=']=$From;
        }
        if(!empty($To)){
            $To=date("Y-m-d",strtotime($this->request->query('To')));
            $where['Nppayments.transaction_date <=']=$To;
        }
       
        
         $nppayments = $this->Nppayments->find()->where($where)->where(['company_id'=>$st_company_id])->contain(['NppaymentRows'=>function($q){
            $NppaymentRows = $this->Nppayments->NppaymentRows->find();
            $totalCrCase = $NppaymentRows->newExpr()
                ->addCase(
                    $NppaymentRows->newExpr()->add(['cr_dr' => 'Cr']),
                    $NppaymentRows->newExpr()->add(['amount']),
                    'integer'
                );
            $totalDrCase = $NppaymentRows->newExpr()
                ->addCase(
                    $NppaymentRows->newExpr()->add(['cr_dr' => 'Dr']),
                    $NppaymentRows->newExpr()->add(['amount']),
                    'integer'
                );
            return $NppaymentRows->select([
                    'total_cr' => $NppaymentRows->func()->sum($totalCrCase),
                    'total_dr' => $NppaymentRows->func()->sum($totalDrCase)
                ])
                ->group('nppayment_id')
                
                ->autoFields(true);
            
        }])->order(['voucher_no'=>'DESC']);
        

        $this->set(compact('nppayments','From','To'));
        $this->set('_serialize', ['nppayments']);
	}

	public function exportExcell(){
		
		$this->viewBuilder()->layout('');
        
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        
        $where = [];
        $vouch_no = $this->request->query('vouch_no');
        $From = $this->request->query('From');
        $To = $this->request->query('To');
        
        $this->set(compact('vouch_no','From','To'));
        
        if(!empty($vouch_no)){
            $where['Nppayments.voucher_no LIKE']=$vouch_no;
        }
        
        if(!empty($From)){
            $From=date("Y-m-d",strtotime($this->request->query('From')));
            $where['Nppayments.transaction_date >=']=$From;
        }
        if(!empty($To)){
            $To=date("Y-m-d",strtotime($this->request->query('To')));
            $where['Nppayments.transaction_date <=']=$To;
        }
       
        
         $nppayments = $this->Nppayments->find()->where($where)->where(['company_id'=>$st_company_id])->contain(['NppaymentRows'=>function($q){
            $NppaymentRows = $this->Nppayments->NppaymentRows->find();
            $totalCrCase = $NppaymentRows->newExpr()
                ->addCase(
                    $NppaymentRows->newExpr()->add(['cr_dr' => 'Cr']),
                    $NppaymentRows->newExpr()->add(['amount']),
                    'integer'
                );
            $totalDrCase = $NppaymentRows->newExpr()
                ->addCase(
                    $NppaymentRows->newExpr()->add(['cr_dr' => 'Dr']),
                    $NppaymentRows->newExpr()->add(['amount']),
                    'integer'
                );
            return $NppaymentRows->select([
                    'total_cr' => $NppaymentRows->func()->sum($totalCrCase),
                    'total_dr' => $NppaymentRows->func()->sum($totalDrCase)
                ])
                ->group('nppayment_id')
                
                ->autoFields(true);
            
        }])->order(['voucher_no'=>'DESC']);
        

        $this->set(compact('nppayments','From','To'));
        $this->set('_serialize', ['nppayments']);
	}
    /**
     * View method
     *
     * @param string|null $id Nppayment id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->layout('index_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
        $nppayment = $this->Nppayments->get($id, [
            'contain' => ['BankCashes','FinancialYears'=>['Companies'], 'Companies', 'NppaymentRows' => ['ReferenceDetails','ReceivedFroms'], 'Creator']
        ]);
		
		$petty_cash_voucher_row_data=[];
		$petty_cash_grn_data=[];
		$petty_cash_invoice_data=[];
		$aval=0;
		foreach($nppayment->nppayment_rows as $petty_cash_voucher_row){
			if(!empty($petty_cash_voucher_row->grn_ids)){
			$petty_cash_voucher_row_data = explode(',',trim(@$petty_cash_voucher_row->grn_ids,','));
			$i=0;
				foreach($petty_cash_voucher_row_data as $petty_cash_voucher_row_data){
				$Grn= $this->Nppayments->Grns->get($petty_cash_voucher_row_data);
				//echo $petty_cash_voucher_row->id;
				$petty_cash_grn_data[$petty_cash_voucher_row->id][$i]=$Grn;
				$i++;
				$aval=1;
				}
			}	
			if(!empty($petty_cash_voucher_row->invoice_ids)){
			$petty_cash_voucher_row_data = explode(',',trim(@$petty_cash_voucher_row->invoice_ids,','));
			$j=0;
				foreach($petty_cash_voucher_row_data as $petty_cash_voucher_row_data){
				$Invoice= $this->Nppayments->Invoices->get($petty_cash_voucher_row_data);
				$petty_cash_invoice_data[$petty_cash_voucher_row->id][$j]=$Invoice;
				$j++;
				$aval=1;
				}
			}
	    }
		$this->set(compact('petty_cash_grn_data','petty_cash_invoice_data','aval'));
        $this->set('nppayment', $nppayment);
        $this->set('_serialize', ['nppayment']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->viewBuilder()->layout('index_layout');
        
        $s_employee_id=$this->viewVars['s_employee_id'];
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        $st_year_id = $session->read('st_year_id');
    
		$financial_year = $this->Nppayments->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->Nppayments->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->Nppayments->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
        $nppayment = $this->Nppayments->newEntity();

		   $SessionCheckDate = $this->FinancialYears->get($st_year_id);
		   $fromdate1 = date("Y-m-d",strtotime($SessionCheckDate->date_from));   
		   $todate1 = date("Y-m-d",strtotime($SessionCheckDate->date_to)); 
		   $tody1 = date("Y-m-d");

		   $fromdate = strtotime($fromdate1);
		   $todate = strtotime($todate1); 
		   $tody = strtotime($tody1);

		 if($fromdate < $tody || $todate > $tody)
		   {
			 if($SessionCheckDate['status'] == 'Open')
			 { $chkdate = 'Found'; }
			 else
			 { $chkdate = 'Not Found'; }

		   }
		   else
			{
				$chkdate = 'Not Found';	
			}		
        

		
        if ($this->request->is('post')) {
			$grnIds=[];$invoiceIds=[];
			foreach( $this->request->data['nppayment_rows'] as $key =>  $pr)
			{
				$grnstring="";$invoiceString="";
				if(!empty($pr['grn_ids']))
				{
					foreach( $pr['grn_ids'] as  $dr)
					{
							$grnstring .=$dr.',';
					}
					$grnIds[$key] =$grnstring;
				}
				if(!empty($pr['invoice_ids']))
				{
					foreach( $pr['invoice_ids'] as  $dr)
					{
							$invoiceString .=$dr.',';
					}
					$invoiceIds[$key] =$invoiceString;
				}
			}
            $nppayment = $this->Nppayments->patchEntity($nppayment, $this->request->data);
            $nppayment->company_id=$st_company_id;
            //Voucher Number Increment
            $last_voucher_no=$this->Nppayments->find()->select(['voucher_no'])->where(['company_id' => $st_company_id,'financial_year_id'=>$st_year_id])->order(['voucher_no' => 'DESC'])->first();
            if($last_voucher_no){
                $nppayment->voucher_no=$last_voucher_no->voucher_no+1;
            }else{
                $nppayment->voucher_no=1;
            }
            $nppayment->financial_year_id=$st_year_id;
            $nppayment->created_on=date("Y-m-d");
            $nppayment->created_by=$s_employee_id;
            $nppayment->transaction_date=date("Y-m-d",strtotime($nppayment->transaction_date));
            //pr($nppayment); exit;
			foreach($nppayment->nppayment_rows as $key => $nppayment_row)
			{
				$nppayment_row->grn_ids = @$grnIds[$key];
				$nppayment_row->invoice_ids =@$invoiceIds[$key];
			}
			
			
			//pr($nppayment); exit;
			
			
            if ($this->Nppayments->save($nppayment)) {
				foreach($nppayment->nppayment_rows as $key => $nppayment_row)
				{
					if(count($grnIds)>0)
					{
						$grnArrays = explode(',',@$grnIds[$key]);
						foreach($grnArrays as $grnArray)
						{ 
							$grn = $this->Nppayments->Grns->query();
							$grn->update()
							->set(['purchase_thela_bhada_status' => 'yes','nppayment_id' => $nppayment->id])
							->where(['id' => $grnArray])
							->execute();
						}
					}
					if(count($invoiceIds)>0)
					{
						$invoiceArrays = explode(',',@$invoiceIds[$key]);
						foreach($invoiceArrays as $invoiceArray)
						{ 
							$grn = $this->Nppayments->Invoices->query();
							$grn->update()
							->set(['sales_thela_bhada_status' => 'yes','nppayment_id' => $nppayment->id])
							->where(['id' => $invoiceArray])
							->execute();
						}
					}
				}
                $total_cr=0; $total_dr=0;
                foreach($nppayment->nppayment_rows as $nppayment_row){
 					$ledger = $this->Nppayments->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $nppayment_row->received_from_id;
					if($nppayment_row->cr_dr=="Cr"){
					$ledger->credit = $nppayment_row->amount;
					$ledger->debit = 0;
					$total_cr=$total_cr+$nppayment_row->amount;
					}else{
					$ledger->credit = 0;
					$ledger->debit = $nppayment_row->amount;
					$total_dr=$total_dr+$nppayment_row->amount;
					}
					$ledger->voucher_id = $nppayment->id;
					$ledger->voucher_source = 'Non Print Payment Voucher';
					$ledger->transaction_date = $nppayment->transaction_date;
					$this->Nppayments->Ledgers->save($ledger);
					if(!empty($nppayment_row->ref_rows))
					{
					foreach($nppayment_row->ref_rows as $ref_rows){
						$ReferenceDetail = $this->Nppayments->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type=$ref_rows['ref_type'];
						$ReferenceDetail->reference_no=$ref_rows['ref_no'];
						$ReferenceDetail->ledger_account_id = $nppayment_row->received_from_id;
						if($ref_rows['ref_cr_dr']=="Dr"){
							$ReferenceDetail->debit = $ref_rows['ref_amount'];
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $ref_rows['ref_amount'];
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->nppayment_id = $nppayment->id;
						$ReferenceDetail->nppayment_row_id = $nppayment_row->id;
						$ReferenceDetail->transaction_date = $nppayment->transaction_date;
						$this->Nppayments->ReferenceDetails->save($ReferenceDetail);
					} 
					
						$ReferenceDetail = $this->Nppayments->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $nppayment_row->received_from_id;
						if($nppayment_row->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $nppayment_row->on_acc;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $nppayment_row->on_acc;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->nppayment_id = $nppayment->id;
						$ReferenceDetail->nppayment_row_id = $nppayment_row->id;
						$ReferenceDetail->transaction_date = $nppayment->transaction_date;
						if($nppayment_row->on_acc > 0){ 
							$this->Nppayments->ReferenceDetails->save($ReferenceDetail);
						}
					}		
                    
                }
					$bankAmt=$total_dr-$total_cr;
					//pr($bankAmt); exit;
					//Ledger posting for bankcash
					$ledger = $this->Nppayments->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $nppayment->bank_cash_id;
					if($bankAmt > 0){
						$ledger->credit = $bankAmt;
						$ledger->debit = 0;
					}else{
						$ledger->debit = abs($bankAmt);
						$ledger->credit = 0;
					}
					
					$ledger->voucher_id = $nppayment->id;
					$ledger->voucher_source = 'Non Print Payment Voucher';
					$ledger->transaction_date = $nppayment->transaction_date;
					if($bankAmt != 0){
						$this->Nppayments->Ledgers->save($ledger);
					}              
                
                
                $this->Flash->success(__('The non print payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {

                $this->Flash->error(__('The non print payment could not be saved. Please, try again.'));
            }
        }
        $vr=$this->Nppayments->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Non Print Payment Voucher','sub_entity'=>'Cash/Bank'])->first();
        $ReceiptVouchersCashBank=$vr->id;
        $vouchersReferences = $this->Nppayments->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
        $BankCashes_selected='yes';
        if(sizeof($where)>0){
            $bankCashes = $this->Nppayments->BankCashes->find('list',
                ['keyField' => function ($row) {
                    return $row['id'];
                },
                'valueField' => function ($row) {
                    if(!empty($row['alias'])){
                        return  $row['name'] . ' (' . $row['alias'] . ')';
                    }else{
                        return $row['name'];
                    }
                    
                }])->where(['BankCashes.id IN' => $where]);
        }else{
            $BankCashes_selected='no';
        }
        //pr($bankCashes->toArray())    ; exit;
        
        $vr=$this->Nppayments->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Non Print Payment Voucher','sub_entity'=>'Paid To'])->first();
        $ReceiptVouchersReceivedFrom=$vr->id;
        $vouchersReferences = $this->Nppayments->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
        $ReceivedFroms_selected='yes';
        if(sizeof($where)>0){
            $receivedFroms = $this->Nppayments->ReceivedFroms->find('list',
                ['keyField' => function ($row) {
                    return $row['id'];
                },
                'valueField' => function ($row) {
                    if(!empty($row['alias'])){
                        return  $row['name'] . ' (' . $row['alias'] . ')';
                    }else{
                        return $row['name'];
                    }
                    
                }])->where(['ReceivedFroms.id IN' => $where]);
        }else{
            $ReceivedFroms_selected='no';
        }
		//pr($receivedFroms->toArray()); exit;
        $this->set(compact('nppayment', 'bankCashes', 'receivedFroms', 'financial_year', 'BankCashes_selected', 'ReceivedFroms_selected','chkdate','financial_month_first','financial_month_last'));
        $this->set('_serialize', ['nppayment']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Nppayment id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('index_layout');
        $id = $this->EncryptingDecrypting->decryptData($id);
        $s_employee_id=$this->viewVars['s_employee_id'];
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->Nppayments->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->Nppayments->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->Nppayments->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();

		   $SessionCheckDate = $this->FinancialYears->get($st_year_id);
		   $fromdate1 = date("Y-m-d",strtotime($SessionCheckDate->date_from));   
		   $todate1 = date("Y-m-d",strtotime($SessionCheckDate->date_to)); 
		   $tody1 = date("Y-m-d");

		   $fromdate = strtotime($fromdate1);
		   $todate = strtotime($todate1); 
		   $tody = strtotime($tody1);

		  if($fromdate < $tody || $todate > $tody)
		   {
			 if($SessionCheckDate['status'] == 'Open')
			 { $chkdate = 'Found'; }
			 else
			 { $chkdate = 'Not Found'; }

		   }
		   else
			{
				$chkdate = 'Not Found';	
			}		

        
        $nppayment = $this->Nppayments->get($id, [
            'contain' => ['NppaymentRows'=>['LedgerAccounts','ReferenceDetails','ReceivedFroms']]
        ]);
        //pr( $nppayment); exit;
        if ($this->request->is(['patch', 'post', 'put'])) {
			$grnIds=[];$invoiceIds=[];
			//pr($this->request->data['nppayment_rows']); exit;
			foreach( $this->request->data['nppayment_rows'] as $key =>  $pr)
			{
				$grnstring="";$invoiceString="";
				if(!empty($pr['grn_ids']))
				{
					foreach( $pr['grn_ids'] as  $dr)
					{
							$grnstring .=$dr.',';
					}
					$grnIds[$key] =$grnstring;
				}
				if(!empty($pr['invoice_ids']))
				{
					foreach( $pr['invoice_ids'] as  $dr)
					{
							$invoiceString .=$dr.',';
					}
					$invoiceIds[$key] =$invoiceString;
				}
			}
            $nppayment = $this->Nppayments->patchEntity($nppayment, $this->request->data);
            $nppayment->company_id=$st_company_id;
            
            $nppayment->edited_on=date("Y-m-d");
            $nppayment->edited_by=$s_employee_id;
            $nppayment->transaction_date=date("Y-m-d",strtotime($nppayment->transaction_date));
                
            //Save receipt
			$grn    = $this->Nppayments->Grns->query();
				    $grn->update()
				    ->set(['purchase_thela_bhada_status' => 'no','nppayment_id' => ''])
				    ->where(['nppayment_id' => $nppayment->id])
				    ->execute();
		   $invoice = $this->Nppayments->Invoices->query();
					  $invoice->update()
					  ->set(['sales_thela_bhada_status' => 'no','nppayment_id' => ''])
					  ->where(['nppayment_id' => $nppayment->id])
					  ->execute();
			foreach($nppayment->nppayment_rows as $key => $nppayment_row)
			{
				$nppayment_row->grn_ids = @$grnIds[$key];
				$nppayment_row->invoice_ids =@$invoiceIds[$key];
			}
            if($this->Nppayments->save($nppayment)) {
				$old_ledger_data=$this->Nppayments->Ledgers->find()->where(['voucher_id' => $nppayment->id, 'voucher_source' => 'Non Print Payment Voucher','ledger_account_id'=>$nppayment->bank_cash_id])->first();
				
				$this->Nppayments->Ledgers->deleteAll(['voucher_id' => $nppayment->id, 'voucher_source' => 'Non Print Payment Voucher']);
				$this->Nppayments->ReferenceDetails->deleteAll(['nppayment_id' => $nppayment->id]);

				
				foreach($nppayment->nppayment_rows as $key => $nppayment_row)
				{
					if(count($grnIds)>0)
					{          
						$grnArrays = explode(',',@$grnIds[$key]);
						foreach($grnArrays as $grnArray)
						{ 
							$grn = $this->Nppayments->Grns->query();
							$grn->update()
							->set(['purchase_thela_bhada_status' => 'yes','nppayment_id' => $nppayment->id])
							->where(['id' => $grnArray])
							->execute();
					   }
					}
					if(count($invoiceIds)>0)
					{          
						$invoiceArrays = explode(',',@$invoiceIds[$key]);
						foreach($invoiceArrays as $invoiceArray)
						{ 
							$invoice = $this->Nppayments->Invoices->query();
							$invoice->update()
							->set(['sales_thela_bhada_status' => 'yes','nppayment_id' => $nppayment->id])
							->where(['id' => $invoiceArray])
							->execute();
					   }
					}
				}
                $total_cr=0; $total_dr=0;
                foreach($nppayment->nppayment_rows as $nppayment_row){  
 					$ledger = $this->Nppayments->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $nppayment_row->received_from_id;
					if($nppayment_row->cr_dr=="Cr"){
					$ledger->credit = $nppayment_row->amount;
					$ledger->debit = 0;
					$total_cr=$total_cr+$nppayment_row->amount;
					}else{
					$ledger->credit = 0;
					$ledger->debit = $nppayment_row->amount;
					$total_dr=$total_dr+$nppayment_row->amount;
					}
					$ledger->voucher_id = $nppayment->id;
					$ledger->voucher_source = 'Non Print Payment Voucher';
					$ledger->transaction_date = $nppayment->transaction_date;
					$this->Nppayments->Ledgers->save($ledger);
					if(!empty($nppayment_row->ref_rows))
					{
					foreach($nppayment_row->ref_rows as $ref_rows){   
						$ReferenceDetail = $this->Nppayments->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type=$ref_rows['ref_type'];
						$ReferenceDetail->reference_no=$ref_rows['ref_no'];
						$ReferenceDetail->ledger_account_id = $nppayment_row->received_from_id;
						if($ref_rows['ref_cr_dr']=="Dr"){
							$ReferenceDetail->debit = $ref_rows['ref_amount'];
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $ref_rows['ref_amount'];
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->nppayment_id = $nppayment->id;
						$ReferenceDetail->nppayment_row_id = $nppayment_row->id;
						$ReferenceDetail->transaction_date = $nppayment->transaction_date;
						$this->Nppayments->ReferenceDetails->save($ReferenceDetail);
					} 
					
						$ReferenceDetail = $this->Nppayments->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $nppayment_row->received_from_id;
						if($nppayment_row->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $nppayment_row->on_acc;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $nppayment_row->on_acc;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->nppayment_id = $nppayment->id;
						$ReferenceDetail->nppayment_row_id = $nppayment_row->id;
						$ReferenceDetail->transaction_date = $nppayment->transaction_date;
						if($nppayment_row->on_acc > 0){ 
							$this->Nppayments->ReferenceDetails->save($ReferenceDetail);
						}                   
                    }
                }
					$bankAmt=$total_dr-$total_cr;
					//pr($bankAmt); exit;
					//Ledger posting for bankcash
					$ledger = $this->Nppayments->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $nppayment->bank_cash_id;
					if($bankAmt > 0){
						$ledger->credit = $bankAmt;
						$ledger->debit = 0;
					}else{
						$ledger->debit = abs($bankAmt);
						$ledger->credit = 0;
					}
					if($old_ledger_data){
						$ledger->reconciliation_date = $old_ledger_data->reconciliation_date;
					}
					$ledger->voucher_id = $nppayment->id;
					$ledger->voucher_source = 'Non Print Payment Voucher';
					$ledger->transaction_date = $nppayment->transaction_date;
					if($bankAmt != 0){
						$this->Nppayments->Ledgers->save($ledger);
					} 
                 
                
                $this->Flash->success(__('The receipt has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The receipt could not be saved. Please, try again.'));
            }
        }
        
        $vr=$this->Nppayments->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Non Print Payment Voucher','sub_entity'=>'Cash/Bank'])->first();
        $ReceiptVouchersCashBank=$vr->id;
        $vouchersReferences = $this->Nppayments->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
        $BankCashes_selected='yes';
        if(sizeof($where)>0){
            $bankCashes = $this->Nppayments->BankCashes->find('list',
                ['keyField' => function ($row) {
                    return $row['id'];
                },
                'valueField' => function ($row) {
                    if(!empty($row['alias'])){
                        return  $row['name'] . ' (' . $row['alias'] . ')';
                    }else{
                        return $row['name'];
                    }
                    
                }])->where(['BankCashes.id IN' => $where]);
        }else{
            $BankCashes_selected='no';
        }
        
        $vr=$this->Nppayments->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Non Print Payment Voucher','sub_entity'=>'Paid To'])->first();
        $ReceiptVouchersReceivedFrom=$vr->id;
        $vouchersReferences = $this->Nppayments->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
       $receivedFroms=[];
		
		$ReceivedFroms_selected='yes';
		if(sizeof($where)>0){
			$receivedDatas = $this->Nppayments->ReceivedFroms->find()->where(['ReceivedFroms.id IN' => $where]);
			foreach($receivedDatas as $receivedData){
				$receivedFroms[$receivedData->id]=['text'=>$receivedData->name,'value'=>$receivedData->id,'thelatype'=>$receivedData->grn_invoice];
			}
		}else{
			$ReceivedFroms_selected='no';
		}	
        $session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$grn=$this->Nppayments->Grns->find()->where(['company_id' => $st_company_id]);
		$invoice=$this->Nppayments->Invoices->find()->where(['company_id' => $st_company_id]);
        $this->set(compact('nppayment', 'bankCashes', 'receivedFroms', 'financial_year', 'BankCashes_selected', 'ReceivedFroms_selected', 'old_ref_rows','chkdate','grn','invoice','financial_month_first','financial_month_last'));
        $this->set('_serialize', ['nppayment']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Nppayment id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $nppayment = $this->Nppayments->get($id);
        if ($this->Nppayments->delete($nppayment)) {
            $this->Flash->success(__('The non print payment has been deleted.'));
        } else {
            $this->Flash->error(__('The non print payment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function fetchRefNumbers($received_from_id=null,$cr_dr=null){
        $this->viewBuilder()->layout('');
        $ReferenceBalances=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id]);
        $this->set(compact('ReferenceBalances','cr_dr'));
    }
    
    public function fetchRefNumbersEdit($received_from_id=null,$reference_no=null,$debit=null,$credit=null,$cr_dr=null){
        $this->viewBuilder()->layout('');
		 $received_from_id = $this->request->query['received_from_id'];
		 $reference_no = $this->request->query['reference_no'];
		 $debit = $this->request->query['debit'];
		 $credit = $this->request->query['credit'];
		 $cr_dr = $this->request->query['cr_dr'];

        $ReferenceBalances=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id]);
        $this->set(compact('ReferenceBalances', 'reference_no', 'credit', 'debit', 'cr_dr'));
    }
    
    function checkRefNumberUnique($received_from_id,$i){
        $reference_no=$this->request->query['ref_rows'][$received_from_id][$i]['ref_no'];
        $ReferenceBalances=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id,'reference_no'=>$reference_no]);
        if($ReferenceBalances->count()==0){
            echo 'true';
        }else{
            echo 'false';
        }
    }
    
    function checkRefNumberUniqueEdit($received_from_id,$i,$is_old){
        $reference_no=$this->request->query['ref_rows'][$received_from_id][$i]['ref_no'];
        $ReferenceBalances=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id,'reference_no'=>$reference_no]);
        if($ReferenceBalances->count()==1 && $is_old=="yes"){
            echo 'true';
        }elseif($ReferenceBalances->count()==0){
            echo 'true';
        }else{
            echo 'false';
        }exit;
    }
    
    function deleteAllRefNumbers($old_received_from_id,$receipt_id){
        $ReferenceDetails=$this->Nppayments->ReferenceDetails->find()->where(['ledger_account_id'=>$old_received_from_id,'receipt_id'=>$receipt_id]);
        foreach($ReferenceDetails as $ReferenceDetail){
            if($ReferenceDetail->reference_type=="New Reference" || $ReferenceDetail->reference_type=="Advance Reference"){
                $this->Payments->ReferenceBalances->deleteAll(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no]);
                
                $RDetail=$this->Nppayments->ReferenceDetails->get($ReferenceDetail->id);
                $this->Nppayments->ReferenceDetails->delete($RDetail);
            }elseif($ReferenceDetail->reference_type=="Against Reference"){
                if(!empty($ReferenceDetail->credit)){
                    $ReferenceBalance=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no])->first();
                    $ReferenceBalance=$this->Nppayments->ReferenceBalances->get($ReferenceBalance->id);
                    $ReferenceBalance->credit=$ReferenceBalance->credit-$ReferenceDetail->credit;
                    $this->Payments->ReferenceBalances->save($ReferenceBalance);
                }elseif(!empty($ReferenceDetail->debit)){
                    $ReferenceBalance=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no])->first();
                    $ReferenceBalance=$this->Nppayments->ReferenceBalances->get($ReferenceBalance->id);
                    $ReferenceBalance->debit=$ReferenceBalance->debit-$ReferenceDetail->debit;
                    $this->Nppayments->ReferenceBalances->save($ReferenceBalance);
                }
                $RDetail=$this->Nppayments->ReferenceDetails->get($ReferenceDetail->id);
                $this->Nppayments->ReferenceDetails->delete($RDetail);
            }
        }      exit;
    }
    
    function deleteOneRefNo($nppayment_id=null,$old_received_from_id=null,$old_ref=null,$old_ref_type=null){
        $old_received_from_id=$this->request->query['old_received_from_id'];
        $nppayment_id=$this->request->query['nppayment_id'];
        $old_ref=$this->request->query['old_ref'];
        $old_ref_type=$this->request->query['old_ref_type'];
        if($old_ref_type=="New Reference" || $old_ref_type=="Advance Reference"){
            $this->Nppayments->ReferenceBalances->deleteAll(['ledger_account_id'=>$old_received_from_id,'reference_no'=>$old_ref]);
            $this->Nppayments->ReferenceDetails->deleteAll(['ledger_account_id'=>$old_received_from_id,'reference_no'=>$old_ref]);
        }elseif($old_ref_type=="Against Reference"){
            $ReferenceDetail=$this->Nppayments->ReferenceDetails->find()->where(['ledger_account_id'=>$old_received_from_id,'nppayment_id'=>$nppayment_id,'reference_no'=>$old_ref])->first();
            
            if(!empty($ReferenceDetail->credit)){
                $ReferenceBalance=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no])->first();
                $ReferenceBalance=$this->Nppayments->ReferenceBalances->get($ReferenceBalance->id);
                $ReferenceBalance->credit=$ReferenceBalance->credit-$ReferenceDetail->credit;
                $this->Nppayments->ReferenceBalances->save($ReferenceBalance);
            }elseif(!empty($ReferenceDetail->debit)){
                $ReferenceBalance=$this->Nppayments->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no])->first();
                $ReferenceBalance=$this->Nppayments->ReferenceBalances->get($ReferenceBalance->id);
                $ReferenceBalance->debit=$ReferenceBalance->debit-$ReferenceDetail->debit;
                $this->Nppayments->ReferenceBalances->save($ReferenceBalance);
            }
            $RDetail=$this->Nppayments->ReferenceDetails->get($ReferenceDetail->id);
            $this->Nppayments->ReferenceDetails->delete($RDetail);
        }
		exit;
    }
}
