<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PettyCashVouchers Controller
 *
 * @property \App\Model\Table\PettyCashVouchersTable $PettyCashVouchers
 */
class PettyCashVouchersController extends AppController
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
		$financial_year = $this->PettyCashVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->PettyCashVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->PettyCashVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		
		$where = [];
		$vouch_no = $this->request->query('vouch_no');
		$From = $this->request->query('From');
		$To = $this->request->query('To');
		
		$this->set(compact('vouch_no','From','To'));
		
		if(!empty($vouch_no)){
			$where['PettyCashVouchers.voucher_no LIKE']=$vouch_no;
		}
		
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['PettyCashVouchers.transaction_date >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['PettyCashVouchers.transaction_date <=']=$To;
		}
		
		
		
        $this->paginate = [
            'contain' => []
        ];
        $pettycashvouchers = $this->paginate($this->PettyCashVouchers->find()->where(['company_id'=>$st_company_id])->where($where)->contain(['PettyCashVoucherRows'])->order(['voucher_no'=>'DESC']));
        $this->set(compact('pettycashvouchers','url','financial_month_first','financial_month_last'));
        $this->set('_serialize', ['pettycashvouchers']);
    }
	
	/* 	public function DataMigrate()
	{
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id'); 
		$PettyCashVouchers = $this->PettyCashVouchers->find()->contain(['PettyCashVoucherRows'])->toArray();
		//pr($PettyCashVouchers); exit;
		foreach($PettyCashVouchers as $PettyCashVoucher){
			$total_dr=0;
			$total_cr=0;
			$bankAmt=0;
				foreach($PettyCashVoucher->petty_cash_voucher_rows as $petty_cash_voucher_row){ 
					$OldReferenceDetails = $this->PettyCashVouchers->ReferenceDetails->OldReferenceDetails->find()->where(['petty_cash_voucher_id'=>$PettyCashVoucher->id,'ledger_account_id'=>$petty_cash_voucher_row->received_from_id])->toArray();
					
					if($OldReferenceDetails){
						foreach($OldReferenceDetails as $old_data){ //pr($old_data); exit;
							$ReferenceDetail = $this->PettyCashVouchers->ReferenceDetails->newEntity();
							$ReferenceDetail->company_id=$PettyCashVoucher->company_id;
							$ReferenceDetail->ledger_account_id=$old_data->ledger_account_id;
							$ReferenceDetail->reference_type=$old_data->reference_type;
							$ReferenceDetail->reference_no=$old_data->reference_no;
							$ReferenceDetail->debit = $old_data->debit;
							$ReferenceDetail->credit = $old_data->credit;
							$ReferenceDetail->petty_cash_voucher_id = $PettyCashVoucher->id;
							$ReferenceDetail->petty_cash_voucher_row_id = $petty_cash_voucher_row->id;
							$ReferenceDetail->transaction_date = $PettyCashVoucher->transaction_date; 
							$this->PettyCashVouchers->ReferenceDetails->save($ReferenceDetail);
							}
						
					}
					
					$ledger = $this->PettyCashVouchers->Ledgers->newEntity();
					$ledger->company_id=$PettyCashVoucher->company_id;
					$ledger->ledger_account_id = $petty_cash_voucher_row->received_from_id;
					if($petty_cash_voucher_row->cr_dr=="Cr"){
					$ledger->credit = $petty_cash_voucher_row->amount;
					$ledger->debit = 0;
					$total_cr=$total_cr+$petty_cash_voucher_row->amount;
					}else{
					$ledger->credit = 0;
					$ledger->debit = $petty_cash_voucher_row->amount;
					$total_dr=$total_dr+$petty_cash_voucher_row->amount;
					}
					$ledger->voucher_id = $PettyCashVoucher->id;
					$ledger->voucher_source = 'Petty Cash Payment Voucher';
					$ledger->transaction_date = $PettyCashVoucher->transaction_date; 
					$this->PettyCashVouchers->Ledgers->save($ledger);
				//pr($payment_row); exit;
				}
				$bankAmt=$total_dr-$total_cr;
				$ledger = $this->PettyCashVouchers->Ledgers->newEntity();
				$ledger->company_id=$PettyCashVoucher->company_id;
				$ledger->ledger_account_id = $PettyCashVoucher->bank_cash_id;
				if($bankAmt > 0){
					$ledger->credit = $bankAmt;
					$ledger->debit = 0;
				}else{
					$ledger->debit = abs($bankAmt);
					$ledger->credit = 0;
				}
				
				$ledger->voucher_id = $PettyCashVoucher->id;
				$ledger->voucher_source = 'Petty Cash Payment Voucher';
				$ledger->transaction_date = $PettyCashVoucher->transaction_date; //pr($ledger); exit;
				if($bankAmt != 0){
					$this->PettyCashVouchers->Ledgers->save($ledger);
				}
			
			
		}
		
		echo "Done";
		exit;
	}
 */
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
			$where['PettyCashVouchers.voucher_no LIKE']=$vouch_no;
		}
		
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['PettyCashVouchers.transaction_date >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['PettyCashVouchers.transaction_date <=']=$To;
		}
        $pettycashvouchers = $this->PettyCashVouchers->find()->where(['company_id'=>$st_company_id])->where($where)->contain(['PettyCashVoucherRows'])->order(['voucher_no'=>'DESC']);
        $this->set(compact('pettycashvouchers','url'));
        $this->set('_serialize', ['pettycashvouchers']);
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
			$where['PettyCashVouchers.voucher_no LIKE']=$vouch_no;
		}
		
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['PettyCashVouchers.transaction_date >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['PettyCashVouchers.transaction_date <=']=$To;
		}
        $pettycashvouchers = $this->PettyCashVouchers->find()->where(['company_id'=>$st_company_id])->where($where)->contain(['PettyCashVoucherRows'])->order(['voucher_no'=>'DESC']);
        $this->set(compact('pettycashvouchers','url'));
        $this->set('_serialize', ['pettycashvouchers']);
	}
    /**
     * View method
     *
     * @param string|null $id Petty Cash Voucher id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->layout('index_layout');
        $pettycashvoucher = $this->PettyCashVouchers->get($id, [
            'contain' => ['BankCashes', 'Companies', 'PettyCashVoucherRows' => ['ReferenceDetails','ReceivedFroms'], 'Creator']
        ]);
		
		$petty_cash_voucher_row_data=[];
		$petty_cash_grn_data=[];
		$petty_cash_invoice_data=[];
		$aval=0;
		foreach($pettycashvoucher->petty_cash_voucher_rows as $petty_cash_voucher_row){
			if(!empty($petty_cash_voucher_row->grn_ids)){
			$petty_cash_voucher_row_data = explode(',',trim(@$petty_cash_voucher_row->grn_ids,','));
			$i=0;
				foreach($petty_cash_voucher_row_data as $petty_cash_voucher_row_data){
				$Grn= $this->PettyCashVouchers->Grns->get($petty_cash_voucher_row_data);
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
				$Invoice= $this->PettyCashVouchers->Invoices->get($petty_cash_voucher_row_data);
				$petty_cash_invoice_data[$petty_cash_voucher_row->id][$j]=$Invoice;
				$j++;
				$aval=1;
				}
			}
	    }
		//pr($petty_cash_grn_data); exit;
		$ref_bal=[];
		foreach($pettycashvoucher->petty_cash_voucher_rows as $petty_cash_voucher_row){
			$ReferenceBalancess=$this->PettyCashVouchers->ReferenceDetails->find()->where(['ledger_account_id'=>$petty_cash_voucher_row->received_from_id,'petty_cash_voucher_id'=>$pettycashvoucher->id]);
			$ref_bal[$petty_cash_voucher_row->received_from_id]=$ReferenceBalancess->toArray();
		}
		//pr($ref_bal);exit;
										
		$this->set(compact('ref_bal','petty_cash_grn_data','petty_cash_invoice_data','aval'));

        $this->set('pettycashvoucher', $pettycashvoucher);
        $this->set('_serialize', ['pettycashvoucher']);
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
		$financial_year = $this->PettyCashVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->PettyCashVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->PettyCashVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
        
        $pettycashvoucher = $this->PettyCashVouchers->newEntity();

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
			foreach( $this->request->data['petty_cash_voucher_rows'] as $key =>  $pr)
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
            $pettycashvoucher = $this->PettyCashVouchers->patchEntity($pettycashvoucher, $this->request->data);
            $pettycashvoucher->company_id=$st_company_id;
            //Voucher Number Increment
            $last_voucher_no=$this->PettyCashVouchers->find()->select(['voucher_no'])->where(['company_id' => $st_company_id])->order(['voucher_no' => 'DESC'])->first();
            if($last_voucher_no){
                $pettycashvoucher->voucher_no=$last_voucher_no->voucher_no+1;
            }else{
                $pettycashvoucher->voucher_no=1;
            }
            
            $pettycashvoucher->created_on=date("Y-m-d");
            $pettycashvoucher->created_by=$s_employee_id;
            $pettycashvoucher->transaction_date=date("Y-m-d",strtotime($pettycashvoucher->transaction_date));
            //pr($pettycashvoucher); exit;
			foreach($pettycashvoucher->petty_cash_voucher_rows as $key => $petty_cash_voucher_row)
			{
				$petty_cash_voucher_row->grn_ids = @$grnIds[$key];
				$petty_cash_voucher_row->invoice_ids =@$invoiceIds[$key];
			}
			
			
			
            if ($this->PettyCashVouchers->save($pettycashvoucher)) {
				foreach($pettycashvoucher->petty_cash_voucher_rows as $key => $petty_cash_voucher_row)
				{
					if(count($grnIds)>0)
					{
						$grnArrays = explode(',',@$grnIds[$key]);
						foreach($grnArrays as $grnArray)
						{ 
							$grn = $this->PettyCashVouchers->Grns->query();
							$grn->update()
							->set(['purchase_thela_bhada_status' => 'yes','pettycashvoucher_id' => $pettycashvoucher->id])
							->where(['id' => $grnArray])
							->execute();
						}
					}
					if(count($invoiceIds)>0)
					{
						$invoiceArrays = explode(',',@$invoiceIds[$key]);
						foreach($invoiceArrays as $invoiceArray)
						{ 
							$grn = $this->PettyCashVouchers->Invoices->query();
							$grn->update()
							->set(['sales_thela_bhada_status' => 'yes','pettycashvoucher_id' => $pettycashvoucher->id])
							->where(['id' => $invoiceArray])
							->execute();
						}
					}
				}
               $total_cr=0; $total_dr=0; $total_dr=0; $i=0;
                foreach($pettycashvoucher->petty_cash_voucher_rows as $petty_cash_voucher_row){
 					$ledger = $this->PettyCashVouchers->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $petty_cash_voucher_row->received_from_id;
					if($petty_cash_voucher_row->cr_dr=="Cr"){
					$ledger->credit = $petty_cash_voucher_row->amount;
					$ledger->debit = 0;
					$total_cr=$total_cr+$petty_cash_voucher_row->amount;
					}else{
					$ledger->credit = 0;
					$ledger->debit = $petty_cash_voucher_row->amount;
					$total_dr=$total_dr+$petty_cash_voucher_row->amount;
					}
					$ledger->voucher_id = $pettycashvoucher->id;
					$ledger->voucher_source = 'Petty Cash Payment Voucher';
					$ledger->transaction_date = $pettycashvoucher->transaction_date;
					$this->PettyCashVouchers->Ledgers->save($ledger);
					if(!empty($petty_cash_voucher_row->ref_rows))
					{
					foreach($petty_cash_voucher_row->ref_rows as $ref_rows){
						$ReferenceDetail = $this->PettyCashVouchers->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type=$ref_rows['ref_type'];
						$ReferenceDetail->reference_no=$ref_rows['ref_no'];
						$ReferenceDetail->ledger_account_id = $petty_cash_voucher_row->received_from_id;
						if($ref_rows['ref_cr_dr']=="Dr"){
							$ReferenceDetail->debit = $ref_rows['ref_amount'];
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $ref_rows['ref_amount'];
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->petty_cash_voucher_id = $pettycashvoucher->id;
						$ReferenceDetail->petty_cash_voucher_row_id = $petty_cash_voucher_row->id;
						$ReferenceDetail->transaction_date = $pettycashvoucher->transaction_date;
						$this->PettyCashVouchers->ReferenceDetails->save($ReferenceDetail);
					} 
					
						$ReferenceDetail = $this->PettyCashVouchers->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $petty_cash_voucher_row->received_from_id;
						if($petty_cash_voucher_row->on_acc_dr_cr=="Dr"){
							$ReferenceDetail->debit = $petty_cash_voucher_row->on_acc;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $petty_cash_voucher_row->on_acc;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->petty_cash_voucher_id = $pettycashvoucher->id;
						$ReferenceDetail->petty_cash_voucher_row_id = $petty_cash_voucher_row->id;
						$ReferenceDetail->transaction_date = $pettycashvoucher->transaction_date;
						if($petty_cash_voucher_row->on_acc > 0){ 
							$this->PettyCashVouchers->ReferenceDetails->save($ReferenceDetail);
						} 
					}
                }

				
					$bankAmt=$total_dr-$total_cr;
					//pr($bankAmt); exit;
					//Ledger posting for bankcash
					$ledger = $this->PettyCashVouchers->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $pettycashvoucher->bank_cash_id;
					if($bankAmt > 0){
						$ledger->credit = $bankAmt;
						$ledger->debit = 0;
					}else{
						$ledger->debit = abs($bankAmt);
						$ledger->credit = 0;
					}
					
					$ledger->voucher_id = $pettycashvoucher->id;
					$ledger->voucher_source = 'Petty Cash Payment Voucher';
					$ledger->transaction_date = $pettycashvoucher->transaction_date;
					if($bankAmt != 0){
						$this->PettyCashVouchers->Ledgers->save($ledger);
					}  				
				
                $this->Flash->success(__('The petty cash has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The petty cash could not be saved. Please, try again.'));
            }
        }
        $vr=$this->PettyCashVouchers->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Petty Cash Payment','sub_entity'=>'Cash/Bank'])->first();
        $ReceiptVouchersCashBank=$vr->id;
        $vouchersReferences = $this->PettyCashVouchers->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
        $BankCashes_selected='yes';
        if(sizeof($where)>0){
            $bankCashes = $this->PettyCashVouchers->BankCashes->find('list',
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
        
        $vr=$this->PettyCashVouchers->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Petty Cash Payment','sub_entity'=>'Paid To'])->first();
        $ReceiptVouchersReceivedFrom=$vr->id;
        $vouchersReferences = $this->PettyCashVouchers->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
        $ReceivedFroms_selected='yes';
        if(sizeof($where)>0){
            $receivedFroms = $this->PettyCashVouchers->ReceivedFroms->find('list',
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
        $this->set(compact('pettycashvoucher', 'bankCashes', 'receivedFroms', 'financial_year', 'BankCashes_selected', 'ReceivedFroms_selected','chkdate','financial_month_first','financial_month_last'));
        $this->set('_serialize', ['pettycashvoucher']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Petty Cash Voucher id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('index_layout');
        
        $s_employee_id=$this->viewVars['s_employee_id'];
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		
        $st_year_id = $session->read('st_year_id');
		$financial_year = $this->PettyCashVouchers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->PettyCashVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->PettyCashVouchers->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
        
        $pettycashvoucher = $this->PettyCashVouchers->get($id, [
            'contain' => ['PettyCashVoucherRows'=>['LedgerAccounts','ReferenceDetails']]
        ]);

        $old_ref_rows=[];
        $old_received_from_ids=[];
        $old_reference_numbers=[];

			   $session = $this->request->session();
			   $st_year_id = $session->read('st_year_id');

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

        
        /* foreach($pettycashvoucher->petty_cash_voucher_rows as $petty_cash_voucher_row){
            $ReferenceDetails=$this->PettyCashVouchers->ReferenceDetails->find()->where(['ledger_account_id'=>$petty_cash_voucher_row->received_from_id,'petty_cash_voucher_id'=>$pettycashvoucher->id,'auto_inc'=>$petty_cash_voucher_row->auto_inc]);
          

            foreach($ReferenceDetails as $ReferenceDetail){
            $old_reference_numbers[$petty_cash_voucher_row->auto_inc]=$ReferenceDetail->reference_no;
            }
            $old_ref_rows[$petty_cash_voucher_row->auto_inc]=$ReferenceDetails->toArray();
            $old_received_from_ids[]=$petty_cash_voucher_row->received_from_id;
        }
		//pr( $o ld_ref_rows); exit;*/
        
        if ($this->request->is(['patch', 'post', 'put'])) {
			$grnIds=[];$invoiceIds=[];
			foreach( $this->request->data['petty_cash_voucher_rows'] as $key =>  $pr)
			{ 
				$grnstring="";$invoiceString="";
				if(!empty($pr['grn_ids']))
				{
					$pr['grn_ids'] = array_filter($pr['grn_ids']);
					foreach( $pr['grn_ids'] as  $dr)
					{
						$grnstring .=$dr.',';
					}
					$grnIds[$key] =$grnstring;
				}
				if(!empty($pr['invoice_ids']))
				{
					$pr['invoice_ids'] = array_filter($pr['invoice_ids']);
					foreach( $pr['invoice_ids'] as  $dr)
					{
							$invoiceString .=$dr.',';
					}
					$invoiceIds[$key] =$invoiceString;
				}
			}
			
            $pettycashvoucher = $this->PettyCashVouchers->patchEntity($pettycashvoucher, $this->request->data);
            $pettycashvoucher->company_id=$st_company_id;
            
            
            $pettycashvoucher->edited_on=date("Y-m-d");
            $pettycashvoucher->edited_by=$s_employee_id;
            $pettycashvoucher->transaction_date=date("Y-m-d",strtotime($pettycashvoucher->transaction_date));
                
            //Save receipt
          
			$grn    = $this->PettyCashVouchers->Grns->query();
				    $grn->update()
				    ->set(['purchase_thela_bhada_status' => 'no','pettycashvoucher_id' => ''])
				    ->where(['pettycashvoucher_id' => $pettycashvoucher->id])
				    ->execute();
		   $invoice = $this->PettyCashVouchers->Invoices->query();
					  $invoice->update()
					  ->set(['sales_thela_bhada_status' => 'no','pettycashvoucher_id' => ''])
					  ->where(['pettycashvoucher_id' => $pettycashvoucher->id])
					  ->execute();
			foreach($pettycashvoucher->petty_cash_voucher_rows as $key => $petty_cash_voucher_row)
			{
				$petty_cash_voucher_row->grn_ids = @$grnIds[$key];
				$petty_cash_voucher_row->invoice_ids =@$invoiceIds[$key];
			}
            if ($this->PettyCashVouchers->save($pettycashvoucher)) {
				
				foreach($pettycashvoucher->petty_cash_voucher_rows as $key => $petty_cash_voucher_row)
				{
					if(count($grnIds)>0)
					{          
						$grnArrays = explode(',',@$grnIds[$key]);
						foreach($grnArrays as $grnArray)
						{ 
							$grn = $this->PettyCashVouchers->Grns->query();
							$grn->update()
							->set(['purchase_thela_bhada_status' => 'yes','pettycashvoucher_id' => $pettycashvoucher->id])
							->where(['id' => $grnArray])
							->execute();
					   }
					}
					if(count($invoiceIds)>0)
					{          
						$invoiceArrays = explode(',',@$invoiceIds[$key]);
						foreach($invoiceArrays as $invoiceArray)
						{ 
							$invoice = $this->PettyCashVouchers->Invoices->query();
							$invoice->update()
							->set(['sales_thela_bhada_status' => 'yes','pettycashvoucher_id' => $pettycashvoucher->id])
							->where(['id' => $invoiceArray])
							->execute();
					   }
					}
				}
				
				
                $this->PettyCashVouchers->Ledgers->deleteAll(['voucher_id' => $pettycashvoucher->id, 'voucher_source' => 'Petty Cash Payment Voucher']);
				
				$this->PettyCashVouchers->ReferenceDetails->deleteAll(['petty_cash_voucher_id' => $pettycashvoucher->id]);
				$total_cr=0; $total_dr=0; $total_dr=0; $i=0;
                foreach($pettycashvoucher->petty_cash_voucher_rows as $petty_cash_voucher_row){
 					$ledger = $this->PettyCashVouchers->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $petty_cash_voucher_row->received_from_id;
					if($petty_cash_voucher_row->cr_dr=="Cr"){
					$ledger->credit = $petty_cash_voucher_row->amount;
					$ledger->debit = 0;
					$total_cr=$total_cr+$petty_cash_voucher_row->amount;
					}else{
					$ledger->credit = 0;
					$ledger->debit = $petty_cash_voucher_row->amount;
					$total_dr=$total_dr+$petty_cash_voucher_row->amount;
					}
					$ledger->voucher_id = $pettycashvoucher->id;
					$ledger->voucher_source = 'Petty Cash Payment Voucher';
					$ledger->transaction_date = $pettycashvoucher->transaction_date;
					$this->PettyCashVouchers->Ledgers->save($ledger);
					if(!empty($petty_cash_voucher_row->ref_rows))
					{
					foreach($petty_cash_voucher_row->ref_rows as $ref_rows){ 
						$ReferenceDetail = $this->PettyCashVouchers->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type=$ref_rows['ref_type'];
						$ReferenceDetail->reference_no=$ref_rows['ref_no'];
						$ReferenceDetail->ledger_account_id = $petty_cash_voucher_row->received_from_id;
						if($ref_rows['ref_cr_dr']=="Dr"){
							$ReferenceDetail->debit = $ref_rows['ref_amount'];
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $ref_rows['ref_amount'];
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->petty_cash_voucher_id = $pettycashvoucher->id;
						$ReferenceDetail->petty_cash_voucher_row_id = $petty_cash_voucher_row->id;
						$ReferenceDetail->transaction_date = $pettycashvoucher->transaction_date;
						$this->PettyCashVouchers->ReferenceDetails->save($ReferenceDetail);
					} 
					} //pr($petty_cash_voucher_row->on_acc_cr_dr);
					
						$ReferenceDetail = $this->PettyCashVouchers->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $petty_cash_voucher_row->received_from_id;
						if($petty_cash_voucher_row->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $petty_cash_voucher_row->on_acc;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $petty_cash_voucher_row->on_acc;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->petty_cash_voucher_id = $pettycashvoucher->id;
						$ReferenceDetail->petty_cash_voucher_row_id = $petty_cash_voucher_row->id;
						$ReferenceDetail->transaction_date = $pettycashvoucher->transaction_date;
						if($petty_cash_voucher_row->on_acc > 0){ 
							$this->PettyCashVouchers->ReferenceDetails->save($ReferenceDetail);
						} 
                }
 //exit;
				
					$bankAmt=$total_dr-$total_cr;
					//pr($bankAmt);
					//Ledger posting for bankcash
					$ledger = $this->PettyCashVouchers->Ledgers->newEntity();
					$ledger->company_id=$st_company_id;
					$ledger->ledger_account_id = $pettycashvoucher->bank_cash_id;
					if($bankAmt > 0){
						$ledger->credit = $bankAmt;
						$ledger->debit = 0;
					}else{
						$ledger->debit = abs($bankAmt);
						$ledger->credit = 0;
					}
					
					$ledger->voucher_id = $pettycashvoucher->id;
					$ledger->voucher_source = 'Petty Cash Payment Voucher';
					$ledger->transaction_date = $pettycashvoucher->transaction_date;
					if($bankAmt != 0){
						$this->PettyCashVouchers->Ledgers->save($ledger);
					}  
                
                
                $this->Flash->success(__('The receipt has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The receipt could not be saved. Please, try again.'));
            }
        }
       
        $vr=$this->PettyCashVouchers->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Petty Cash Payment','sub_entity'=>'Cash/Bank'])->first();
        $ReceiptVouchersCashBank=$vr->id;
        $vouchersReferences = $this->PettyCashVouchers->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }
        $BankCashes_selected='yes';
        if(sizeof($where)>0){
            $bankCashes = $this->PettyCashVouchers->BankCashes->find('list',
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
        
        $vr=$this->PettyCashVouchers->VouchersReferences->find()->where(['company_id'=>$st_company_id,'module'=>'Petty Cash Payment','sub_entity'=>'Paid To'])->first();
        $ReceiptVouchersReceivedFrom=$vr->id;
        $vouchersReferences = $this->PettyCashVouchers->VouchersReferences->get($vr->id, [
            'contain' => ['VoucherLedgerAccounts']
        ]);
        $where=[];
        foreach($vouchersReferences->voucher_ledger_accounts as $data){
            $where[]=$data->ledger_account_id;
        }

       /*  $ReceivedFroms_selected='yes';
        if(sizeof($where)>0){
            $receivedFroms = $this->PettyCashVouchers->ReceivedFroms->find('list',
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
        } */
		
		$receivedFroms=[];
		
		$ReceivedFroms_selected='yes';
		if(sizeof($where)>0){
			$receivedDatas = $this->PettyCashVouchers->ReceivedFroms->find()->where(['ReceivedFroms.id IN' => $where]);
			foreach($receivedDatas as $receivedData){
				$receivedFroms[$receivedData->id]=['text'=>$receivedData->name,'value'=>$receivedData->id,'thelatype'=>$receivedData->grn_invoice];
			}
		}else{
			$ReceivedFroms_selected='no';
		}
         //pr($pettycashvoucher); exit;
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$grn=$this->PettyCashVouchers->Grns->find()->where(['company_id' => $st_company_id]);
		$invoice=$this->PettyCashVouchers->Invoices->find()->where(['company_id' => $st_company_id]);
        $this->set(compact('pettycashvoucher', 'bankCashes', 'receivedFroms', 'financial_year', 'BankCashes_selected', 'ReceivedFroms_selected', 'old_ref_rows','chkdate','grn','invoice','financial_month_first','financial_month_last'));
        $this->set('_serialize', ['pettycashvoucher']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Petty Cash Voucher id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pettycashvoucher = $this->PettyCashVouchers->get($id);
        if ($this->PettyCashVouchers->delete($pettycashvoucher)) {
            $this->Flash->success(__('The petty cash voucher has been deleted.'));
        } else {
            $this->Flash->error(__('The petty cash voucher could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function fetchRefNumbers($received_from_id=null,$cr_dr=null){
        $this->viewBuilder()->layout('');
        $ReferenceBalances=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id]);
        $this->set(compact('ReferenceBalances','cr_dr'));
    }
    
  public function fetchRefNumbersEdit($auto_inc=null,$reference_no=null,$debit=null,$credit=null,$cr_dr=null,$received_from_id=null){
		$this->viewBuilder()->layout('');// pr($reference_no);
		$ReferenceBalances=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id,'auto_inc'=>$auto_inc,'reference_no'=>$reference_no]);
		$this->set(compact('ReferenceBalances', 'reference_no', 'debit','credit','cr_dr','received_from_id'));
			
	}
    
   	function checkRefNumberUnique($received_from_id,$i,$is_old,$auto_inc){
		$reference_no=$this->request->query['ref_rows'][$auto_inc][$i]['ref_no'];
		$ReferenceBalances=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id,'reference_no'=>$reference_no]);
		if($ReferenceBalances->count()==0){
			echo 'true';
		}else{
			echo 'false';
		}
		exit;
	}
	
    
    function checkRefNumberUniqueEdit($received_from_id,$i,$is_old,$auto_inc){
        $reference_no=$this->request->query['ref_rows'][$auto_inc][$i]['ref_no'];
        $ReferenceBalances=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id,'reference_no'=>$reference_no]);
        if($ReferenceBalances->count()==1 && $is_old=="yes"){
            echo 'true';
        }elseif($ReferenceBalances->count()==0){
            echo 'true';
        }else{
            echo 'false';
        }
        exit;
    }
    
	function deleteAllRefNumbers($old_received_from_id,$petty_cash_voucher_id,$auto_inc){
		//$auto_inc=$this->request->query['auto_inc'];
		//pr($auto_inc); exit;
		$ReferenceDetails=$this->PettyCashVouchers->ReferenceDetails->find()->where(['ledger_account_id'=>$old_received_from_id,'petty_cash_voucher_id'=>$petty_cash_voucher_id,'auto_inc'=>$auto_inc]);
		foreach($ReferenceDetails as $ReferenceDetail){
			if($ReferenceDetail->reference_type=="New Reference" || $ReferenceDetail->reference_type=="Advance Reference"){
				$this->PettyCashVouchers->ReferenceBalances->deleteAll(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no,'auto_inc'=>$auto_inc]);
				$RDetail=$this->PettyCashVouchers->ReferenceDetails->get($ReferenceDetail->id);
				$this->PettyCashVouchers->ReferenceDetails->delete($RDetail);
			}elseif($ReferenceDetail->reference_type=="Against Reference"){
				if(!empty($ReferenceDetail->credit)){
					$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no,'auto_inc'=>$auto_inc])->first();
					$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->get($ReferenceBalance->id);
					$ReferenceBalance->credit=$ReferenceBalance->credit-$ReferenceDetail->credit;
					$this->PettyCashVouchers->ReferenceBalances->save($ReferenceBalance);
				}elseif(!empty($ReferenceDetail->debit)){
					$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no,'auto_inc'=>$auto_inc])->first();
					$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->get($ReferenceBalance->id);
					$ReferenceBalance->debit=$ReferenceBalance->debit-$ReferenceDetail->debit;
					$this->PettyCashVouchers->ReferenceBalances->save($ReferenceBalance);
				}
				$RDetail=$this->PettyCashVouchers->ReferenceDetails->get($ReferenceDetail->id);
				$this->PettyCashVouchers->ReferenceDetails->delete($RDetail);
			}
		}		exit;
	}
    

	function deleteOneRefNumbers(){
		$old_received_from_id=$this->request->query['old_received_from_id'];
		$petty_cash_voucher_id=$this->request->query['petty_cash_voucher_id'];
		$old_ref=$this->request->query['old_ref'];
		$old_ref_type=$this->request->query['old_ref_type'];
		$auto_inc=$this->request->query['auto_inc'];
		
		if($old_ref_type=="New Reference" || $old_ref_type=="Advance Reference"){
			$this->PettyCashVouchers->ReferenceBalances->deleteAll(['ledger_account_id'=>$old_received_from_id,'reference_no'=>$old_ref,'auto_inc'=>$auto_inc]);
			$this->PettyCashVouchers->ReferenceDetails->deleteAll(['ledger_account_id'=>$old_received_from_id,'reference_no'=>$old_ref,'auto_inc'=>$auto_inc]);
		}elseif($old_ref_type=="Against Reference"){
			$ReferenceDetail=$this->PettyCashVouchers->ReferenceDetails->find()->where(['ledger_account_id'=>$old_received_from_id,'petty_cash_voucher_id'=>$petty_cash_voucher_id,'reference_no'=>$old_ref,'auto_inc'=>$auto_inc])->first();
			if(!empty($ReferenceDetail->credit)){
				$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no,'auto_inc'=>$auto_inc])->first();
				$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->get($ReferenceBalance->id);
				$ReferenceBalance->credit=$ReferenceBalance->credit-$ReferenceDetail->credit;
				$this->PettyCashVouchers->ReferenceBalances->save($ReferenceBalance);
			}elseif(!empty($ReferenceDetail->debit)){
				$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no,'auto_inc'=>$auto_inc])->first();
				$ReferenceBalance=$this->PettyCashVouchers->ReferenceBalances->get($ReferenceBalance->id);
				$ReferenceBalance->debit=$ReferenceBalance->debit-$ReferenceDetail->debit;
				$this->PettyCashVouchers->ReferenceBalances->save($ReferenceBalance);
			}
			$RDetail=$this->PettyCashVouchers->ReferenceDetails->get($ReferenceDetail->id);
			$this->PettyCashVouchers->ReferenceDetails->delete($RDetail);
		}
		
		exit;
	}


}
