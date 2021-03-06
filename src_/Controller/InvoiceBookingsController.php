<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\View\Helper\NumberHelper;
/**
 * InvoiceBookings Controller
 *
 * @property \App\Model\Table\InvoiceBookingsTable $InvoiceBookings
 */
class InvoiceBookingsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index($status=null)
    {
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_year_id = $session->read('st_year_id');
		$purchase_return=$this->request->query('purchase-return');
	    $where = [];
		$book_no = $this->request->query('book_no');
		$grn_no = $this->request->query('grn_no');
		$file = $this->request->query('file');
		$file_grn_no = $this->request->query('file_grn_no');
		$in_no = $this->request->query('in_no');
		$From = $this->request->query('From');
		$To = $this->request->query('To');
		$vendor_name = $this->request->query('vendor_name');
		
		$this->set(compact('book_no','grn_no','From','To','in_no','file_grn_no','file','vendor_name'));
		
		if(!empty($book_no))
		{
			$where['InvoiceBookings.ib2 LIKE']=$book_no;
		}
		
		if(!empty($file)){
			$where['InvoiceBookings.ib3 LIKE']='%'.$file.'%';
		}
		
		if(!empty($grn_no)){ 
			$where['Grns.grn2 LIKE']=$grn_no;
		}
		
		if(!empty($file_grn_no)){
			$where['Grns.grn3 LIKE']='%'.$file_grn_no.'%';
		}
		
		if(!empty($in_no)){
			$where['InvoiceBookings.invoice_no LIKE']='%'.$in_no.'%';
		}
		
		if(!empty($vendor_name)){
			$where['Vendors.company_name LIKE']='%'.$vendor_name.'%';
		}
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InvoiceBookings.created_on >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InvoiceBookings.created_on <=']=$To;
		}
		//pr($where); exit;
        $this->paginate = [
            'contain' => ['Grns','Vendors']
        ];
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['InvoiceBookings.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		if($purchase_return=='true'){
			
			$invoiceBookings = $this->paginate($this->InvoiceBookings->find()->where($where)->where($wheree)->where(['InvoiceBookings.company_id'=>$st_company_id])->order(['InvoiceBookings.id' => 'DESC']));
		}else{ 
			$invoiceBookings = $this->paginate($this->InvoiceBookings->find()->where($wheree)->where($where)->where(['InvoiceBookings.company_id'=>$st_company_id,'InvoiceBookings.financial_year_id'=>$st_year_id])->order(['InvoiceBookings.ib2' => 'DESC']));
		}
		//pr($invoiceBookings);exit;
        $this->set(compact('invoiceBookings','status','purchase_return'));
        $this->set('_serialize', ['invoiceBookings']);
		$this->set(compact('url'));
    }
	
	
	public function purchaseBookingReport($status=null)
    {
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_year_id = $session->read('st_year_id');
	    $where = [];$where1 = [];
		$book_no = $this->request->query('book_no');
		$in_no = $this->request->query('in_no');
		$file = $this->request->query('file');
		$From = $this->request->query('From');
		$To = $this->request->query('To');
		$Po_From = $this->request->query('Po_From');
		$Po_To = $this->request->query('Po_To');
		$vendor_id = $this->request->query('vendor_id');
		$item_id = $this->request->query('items');
		
		$this->set(compact('book_no','From','To','in_no','file','vendor_id','Po_To','Po_From'));
		
		if(!empty($book_no))
		{
			$where['InvoiceBookings.ib2 LIKE']=$book_no;
		}
		
		if(!empty($in_no)){
			$where['InvoiceBookings.invoice_no LIKE']='%'.$in_no.'%';
		}
		
		if(!empty($vendor_id)){
			$where['InvoiceBookings.vendor_id LIKE']=$vendor_id;
		}
		
		if(!empty($file)){
			$where['InvoiceBookings.ib3 LIKE']='%'.$file.'%';
		}
		
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InvoiceBookings.created_on >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InvoiceBookings.created_on <=']=$To;
		}
		if(!empty($Po_From)){
			$Po_From=date("Y-m-d",strtotime($this->request->query('Po_From')));
			$where1['PurchaseOrders.date_created >=']=$Po_From;
		}
		if(!empty($Po_To)){
			$Po_To=date("Y-m-d",strtotime($this->request->query('Po_To')));
			$where1['PurchaseOrders.date_created <=']=$Po_To;
		}
		//pr($where); exit;
			if(!empty($item_id))
			{ 
		
				$InvoiceBookingRows = $this->InvoiceBookings->InvoiceBookingRows->find();
				$invoiceBookings = $this->InvoiceBookings->find();
				$invoiceBookings->select(['id','total_sales'=>$InvoiceBookingRows->func()->sum('InvoiceBookingRows.quantity')])
				->innerJoinWith('InvoiceBookingRows')
				->group(['InvoiceBookings.id'])
				->matching('InvoiceBookingRows.Items', function ($q) use($item_id,$st_company_id) {
											return $q->where(['Items.id' =>$item_id,'company_id'=>$st_company_id]);
							})
				->contain(['Vendors','InvoiceBookingRows'=>['Items'],'Grns'=>['PurchaseOrders'=>function ($q) use($where1){
				return $q->where($where1);
			}]])
				->autoFields(true)
				->where(['InvoiceBookings.company_id'=>$st_company_id,'InvoiceBookings.financial_year_id'=>$st_year_id])
				->where($where);
			}
			else{
			$invoiceBookings = $this->InvoiceBookings->find()->where($where)->where(['InvoiceBookings.company_id'=>$st_company_id,'InvoiceBookings.financial_year_id'=>$st_year_id,'InvoiceBookings.gst' =>'yes'])->contain(['Vendors','Grns'=>['PurchaseOrders'=>function ($q) use($where1){
				return $q->where($where1);
			}]])->order(['InvoiceBookings.id' => 'DESC']);
			}
			$vendor = $this->InvoiceBookings->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);
			$Items = $this->InvoiceBookings->InvoiceBookingRows->Items->find('list')->order(['Items.name' => 'ASC']);
		//pr($invoiceBookings->toArray());exit;
        $this->set(compact('invoiceBookings','status','purchase_return','url','vendor','Items','item_id'));
      
    }
	/* public function DataMigrate()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$Grns=$this->InvoiceBookings->Grns->GrnRows->find();
		foreach($Grns as $Grn){
		$InvoiceBookings=$this->InvoiceBookings->find()->contain(['InvoiceBookingRows'])->where(['InvoiceBookings.grn_id'=>$Grn->grn_id])->toArray();
				//pr($InvoiceBookings); exit;
			if($InvoiceBookings){
				if(sizeof($InvoiceBookings) > 0){ 
					foreach($InvoiceBookings as $InvoiceBooking){
						foreach($InvoiceBooking->invoice_booking_rows as $invoice_booking_row){
							$query = $this->InvoiceBookings->InvoiceBookingRows->query();
							 $query->update()
								->set(['grn_row_id' => $Grn->id])
								->where(['item_id' => $Grn->item_id,'invoice_booking_id'=>$InvoiceBooking->id])
								->execute();
							
							$query1 = $this->InvoiceBookings->ItemLedgers->query();
							$query1->update()
								->set(['rate' => $invoice_booking_row->rate,'rate_updated' => 'Yes'])
								->where(['source_row_id' => $invoice_booking_row->grn_row_id,'source_model'=>'Grns'])
								->execute();
								//pr($invoice_booking_row->grn_row_id); exit;
							}
					}
				}
			}
		}
		
	echo "done"; exit;
	} */
	
	/* public function OldRefBal()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$InvoiceBookings=$this->InvoiceBookings->find()->toArray();
		foreach($InvoiceBookings as $InvoiceBooking){ 
			$old_datas=$this->InvoiceBookings->OldReferenceDetails->find()->where(['invoice_booking_id'=>$InvoiceBooking->id])->toArray();
			
			if($old_datas){
				foreach($old_datas as $old_data){
					$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
					$ReferenceDetail->company_id=$InvoiceBooking->company_id;
					$ReferenceDetail->invoice_booking_id=$old_data->invoice_booking_id;
					$ReferenceDetail->reference_no=$old_data->reference_no;
					$ReferenceDetail->reference_type=$old_data->reference_type;
					$ReferenceDetail->ledger_account_id = $old_data->ledger_account_id;
					$ReferenceDetail->credit = $old_data->credit;
					$ReferenceDetail->debit = $old_data->debit;
					$ReferenceDetail->transaction_date =$InvoiceBooking->created_on;  
					$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
				}
			}
			
		}
		
		
		echo "Done";
		exit;
	}
	*/
	/* 
	public function OldOpeningRefBal()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$old_datas=$this->InvoiceBookings->OldReferenceDetails->find()->where(['receipt_id'=>0,'payment_id'=>0,'invoice_id'=>0,'invoice_booking_id'=>0,'credit_note_id'=>0,'journal_voucher_id'=>0,'sale_return_id'=>0,'purchase_return_id'=>0,'petty_cash_voucher_id'=>0,'nppayment_id'=>0,'contra_voucher_id'=>0])->toArray();
			//pr($old_datas); exit;
			if($old_datas){
				foreach($old_datas as $old_data){
					$lc=$this->InvoiceBookings->LedgerAccounts->get($old_data->ledger_account_id);
					//pr($lc->company_id); exit;
					$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
					$ReferenceDetail->company_id=$lc->company_id;
					$ReferenceDetail->reference_no=$old_data->reference_no;
					$ReferenceDetail->reference_type=$old_data->reference_type;
					$ReferenceDetail->opening_balance='Yes';
					$ReferenceDetail->ledger_account_id = $old_data->ledger_account_id;
					$ReferenceDetail->credit = $old_data->credit;
					$ReferenceDetail->debit = $old_data->debit;
					$ReferenceDetail->transaction_date ='2017-04-01';  //pr($ReferenceDetail); exit;
					$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
				}
			}
	
		
		
		echo "Done";
		exit;
	}  */
	
	/* public function LedgerEntry()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$InvoiceBookings=$this->InvoiceBookings->find()->contain(['InvoiceBookingRows']);
		$i=0;
		$j=0;
		foreach($InvoiceBookings as $invoiceBooking){ 
			$invoice_ledget_amt=0;
			$invoice_other_charges=0;
			
			if($invoiceBooking->gst=='no'){ $i++;
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){ $j++;
					
					$invoice_ledget_amt+=$invoice_booking_row->amount; 
					$invoice_other_charges+=$invoice_booking_row->other_charges; 
				}
				
				$accountReferences = $this->InvoiceBookings->AccountReferences->get(2);
				if($invoiceBooking->cst_vat=='CST'){ //echo "CST"; pr($invoiceBooking); exit;
					
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $invoiceBooking->total;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
				}else{  
					
				
					

					if($invoice_other_charges < 0){
						$ledger_amount=$invoice_ledget_amt+abs($invoice_other_charges);
					}else if($invoice_other_charges > 0){
						$ledger_amount=$invoice_ledget_amt;
					}else{
						$ledger_amount=$invoice_ledget_amt;
					}
					
					
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $ledger_amount;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					//ledger posting for VAT ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->ledger_account_for_vat;
					$ledger->debit = $invoiceBooking->total_saletax;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoiceBooking->total_saletax > 0){
						$this->InvoiceBookings->Ledgers->save($ledger);
					}
					
					//ledger posting for DISCOUNT ACCOUNT
					$ledger_account_for_discount=$this->InvoiceBookings->LedgerAccounts->find()->where(['invoice_booking_other_charge_post'=>1,'name'=>'Discount','company_id'=>$invoiceBooking->company_id])->first();
					
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $ledger_account_for_discount['id'];
					
					if($invoice_other_charges < 0){
						$ledger->credit = abs($invoice_other_charges);
						$ledger->debit = 0;
					}else if($invoice_other_charges > 0){
						$ledger->debit = $invoice_other_charges;
						$ledger->credit = 0;	
					}
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoice_other_charges != 0){
					$this->InvoiceBookings->Ledgers->save($ledger);
					}
				}
				
				$c_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$invoiceBooking->company_id,'source_model'=>'Vendors','source_id'=>$invoiceBooking->vendor_id])->first();
				$ledger = $this->InvoiceBookings->Ledgers->newEntity();
				$ledger->ledger_account_id = $c_LedgerAccount->id;
				$ledger->debit = 0;
				$ledger->credit =$invoiceBooking->total;
				$ledger->voucher_id = $invoiceBooking->id;
				$ledger->company_id = $invoiceBooking->company_id;
				$ledger->transaction_date = $invoiceBooking->supplier_date;
				$ledger->voucher_source = 'Invoice Booking';
				$this->InvoiceBookings->Ledgers->save($ledger);
				
			}else if($invoiceBooking->gst=='yes'){    $invoice_other_charges=0;
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){  
					if($invoice_booking_row->cgst > 0){
						$cg_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$invoiceBooking->company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->cgst_per])->first(); 
						$ledger = $this->InvoiceBookings->Ledgers->newEntity();
						$ledger->ledger_account_id = $cg_LedgerAccount->id;
						$ledger->debit = $invoice_booking_row->cgst;
						$ledger->credit = 0;
						$ledger->voucher_id = $invoiceBooking->id;
						$ledger->voucher_source = 'Invoice Booking';
						$ledger->company_id = $invoiceBooking->company_id;
						$ledger->transaction_date = $invoiceBooking->supplier_date; 
						$this->InvoiceBookings->Ledgers->save($ledger); 
					}
					if($invoice_booking_row->sgst > 0){
						$s_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$invoiceBooking->company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->sgst_per])->first();
						$ledger = $this->InvoiceBookings->Ledgers->newEntity();
						$ledger->ledger_account_id = $s_LedgerAccount->id;
						$ledger->debit = $invoice_booking_row->sgst;
						$ledger->credit = 0;
						$ledger->voucher_id = $invoiceBooking->id;
						$ledger->voucher_source = 'Invoice Booking';
						$ledger->company_id = $invoiceBooking->company_id;
						$ledger->transaction_date = $invoiceBooking->supplier_date;
						$this->InvoiceBookings->Ledgers->save($ledger); 
					}
					if($invoice_booking_row->igst > 0){
						$i_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$invoiceBooking->company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->igst_per])->first();
						$ledger = $this->InvoiceBookings->Ledgers->newEntity();
						$ledger->ledger_account_id = $i_LedgerAccount->id;
						$ledger->debit = $invoice_booking_row->igst;
						$ledger->credit = 0;
						$ledger->voucher_id = $invoiceBooking->id;
						$ledger->voucher_source = 'Invoice Booking';
						$ledger->company_id = $invoiceBooking->company_id;
						$ledger->transaction_date = $invoiceBooking->supplier_date;
						$this->InvoiceBookings->Ledgers->save($ledger); 
					}
					
					$invoice_other_charges+=$invoice_booking_row->other_charges; 
				}
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $invoiceBooking->taxable_value;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					$ledger_account_for_discount=$this->InvoiceBookings->LedgerAccounts->find()->where(['invoice_booking_other_charge_post'=>1,'name'=>'Discount','company_id'=>$invoiceBooking->company_id])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					
					$ledger->ledger_account_id = $ledger_account_for_discount['id'];
					
					if($invoice_other_charges < 0){
						$ledger->credit = abs($invoice_other_charges);
						$ledger->debit = 0;
					}else if($invoice_other_charges > 0){ 
						$ledger->debit = abs($invoice_other_charges);
						$ledger->credit = 0;	
					}
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoice_other_charges != 0){
						$this->InvoiceBookings->Ledgers->save($ledger);
					}
				
				
				
					//Ledger posting for SUPPLIER
					$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$invoiceBooking->company_id,'source_model'=>'Vendors','source_id'=>$invoiceBooking->vendor_id])->first();
					
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $v_LedgerAccount->id;
					$ledger->debit = 0;
					$ledger->credit =$invoiceBooking->total;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$this->InvoiceBookings->Ledgers->save($ledger);
				
			}
			
		}
		
		
		echo "done"; exit;
	}
	 */
	public function exportExcel($status=null){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_year_id = $session->read('st_year_id');
		$purchase_return=$this->request->query('purchase-return');
	    $where = [];
		$book_no = $this->request->query('book_no');
		$grn_no = $this->request->query('grn_no');
		$file = $this->request->query('file');
		$file_grn_no = $this->request->query('file_grn_no');
		$in_no = $this->request->query('in_no');
		$From = $this->request->query('From');
		$To = $this->request->query('To');
		$vendor_name = $this->request->query('vendor_name');
		
		$this->set(compact('book_no','grn_no','From','To','in_no','file_grn_no','file','vendor_name'));
		
		if(!empty($book_no)){
			$where['InvoiceBookings.ib2 LIKE']=$book_no;
		}
		
		if(!empty($file)){
			$where['InvoiceBookings.ib3 LIKE']='%'.$file.'%';
		}
		
		if(!empty($grn_no)){ 
			$where['Grns.grn2 LIKE']=$grn_no;
		}
		
		if(!empty($file_grn_no)){
			$where['Grns.grn3 LIKE']='%'.$file_grn_no.'%';
		}
		
		if(!empty($in_no)){
			$where['InvoiceBookings.invoice_no LIKE']='%'.$in_no.'%';
		}
		
		if(!empty($vendor_name)){
			$where['Vendors.company_name LIKE']='%'.$vendor_name.'%';
		}
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InvoiceBookings.created_on >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InvoiceBookings.created_on <=']=$To;
		}
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['InvoiceBookings.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		if($purchase_return=='true'){
			
			$invoiceBookings = $this->paginate($this->InvoiceBookings->find()->where($where)->where($wheree)->where(['InvoiceBookings.company_id'=>$st_company_id])->order(['InvoiceBookings.id' => 'DESC']));
		}else{ 
			$invoiceBookings = $this->paginate($this->InvoiceBookings->find()->where($wheree)->where($where)->where(['InvoiceBookings.company_id'=>$st_company_id,'InvoiceBookings.financial_year_id'=>$st_year_id])->order(['InvoiceBookings.id' => 'DESC']));
		}
		
			
		//pr($invoiceBookings);exit;
        $this->set(compact('invoiceBookings','status','purchase_return'));
        $this->set('_serialize', ['invoiceBookings']);
		$this->set(compact('url'));
	}
	
	public function Report(){
		$LedgerAccounts =$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>25]);
		
		$sml_ledger=[];
		$fmsl_ledger=[];
		
		foreach($LedgerAccounts as $LedgerAccount){
			$smlexists = $this->InvoiceBookings->ItemLedgers->exists(['source_model' =>$LedgerAccount->source_model,'source_id'=>$LedgerAccount->id,'company_id'=>26]);
			
			$fmslexists = $this->InvoiceBookings->ItemLedgers->exists(['source_model' =>$LedgerAccount->source_model,'source_id'=>$LedgerAccount->id,'company_id'=>27]);
			
			if(!$smlexists){
				$sml_ledger[]=$LedgerAccount->name.' ('.$LedgerAccount->alias.')';
			}
		
			if(!$fmslexists){
				$fmsl_ledger[]=$LedgerAccount->name.' ('.$LedgerAccount->alias.')';
			}
		
	}
	$data=	array_unique(array_merge($sml_ledger,$fmsl_ledger));
		pr($data);exit;
		exit;
	}
	public function PurchaseReturnIndex($status = null){
		$this->viewBuilder()->layout('index_layout');
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$purchase_return=$this->request->query('purchase-return');
		$status=$this->request->query('status');
		@$book_no = $this->request->query('book_no');
		$where=[];
		$status = 0 ;
		if(!empty($book_no)){
			$book_no=$this->request->query('book_no');
			if(!empty($book_no)){
				$where['InvoiceBookings.ib2 LIKE']=$book_no;
			}
			$invoiceBookings =$this->InvoiceBookings->find()->contain(['Grns','Vendors'])->where($where)->where(['InvoiceBookings.company_id'=>$st_company_id,'InvoiceBookings.gst'=>'no'])->order(['InvoiceBookings.id' => 'DESC']);
			$status=1;
		}	
		//pr($invoiceBookings->toArray());exit;
		$this->set(compact('invoiceBookings','status','purchase_return','book_no'));
        $this->set('_serialize', ['invoiceBookings']);
		$this->set(compact('url'));
	}
	
	public function gstPurchaseReturn($status = null){
		$this->viewBuilder()->layout('index_layout');
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_year_id = $session->read('st_year_id');
		$purchase_return=$this->request->query('purchase-return');
		$status=$this->request->query('status');
		@$book_no = $this->request->query('book_no');
		$where=[];
		$status = 0 ;
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['InvoiceBookings.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		if(!empty($book_no)){
			$book_no=$this->request->query('book_no');
			if(!empty($book_no)){
				$where['InvoiceBookings.ib2 LIKE']=$book_no;
			}
			$invoiceBookings =$this->InvoiceBookings->find()->contain(['Grns','Vendors'])->where($where)->where($wheree)->where(['InvoiceBookings.company_id'=>$st_company_id,'InvoiceBookings.gst'=>'yes'])->toArray();
			$status=1;
		}	
		
		
		//pr($invoiceBookings[0]); exit;
		$InvoiceBookingExist="No";
		if(!empty($invoiceBookings)){
			$SalesReturnexists = $this->InvoiceBookings->PurchaseReturns->exists(['PurchaseReturns.invoice_booking_id' => 
			$invoiceBookings[0]->id]);
			if($SalesReturnexists==1){
				$PurchaseReturns=$this->InvoiceBookings->PurchaseReturns->find()->where(['PurchaseReturns.invoice_booking_id' => $invoiceBookings[0]->id,'PurchaseReturns.company_id'=>$st_company_id])->first();
				$PurchaseReturnId=$PurchaseReturns->id;
				$InvoiceBookingExist="Yes";
			}
		}
		//pr($invoiceBookings); exit;
		$this->set(compact('invoiceBookings','status','purchase_return','book_no','PurchaseReturnId','InvoiceBookingExist'));
        $this->set('_serialize', ['invoiceBookings']);
		$this->set(compact('url'));
	}
	
	
	
	
	
    /**
     * View method
     *
     * @param string|null $id Invoice Booking id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$this->viewBuilder()->layout('index_layout');
        $invoiceBooking = $this->InvoiceBookings->get($id, [
            'contain' => ['InvoiceBookingRows'=>['Items'],'Vendors','Creator','Companies']
        ]);
		if($invoiceBooking->ledger_account_for_vat > 0){
			$LedgerAccount=$this->InvoiceBookings->LedgerAccounts->get($invoiceBooking->ledger_account_for_vat);
		}
		
		$purchase_acc='';
		if($st_company_id==25){  
			if($invoiceBooking->purchase_ledger_account==35){
				$purchase_acc="CST Purchase";
			}else{
				$purchase_acc="VAT Purchase";
			}
		}else if($st_company_id==26){
			if($invoiceBooking->purchase_ledger_account==161){
				$purchase_acc="CST Purchase";
			}else{
				$purchase_acc="VAT Purchase";
			}
		}else if($st_company_id==27){
			if($invoiceBooking->purchase_ledger_account==309){
				$purchase_acc="CST Purchase";
			}else{
				$purchase_acc="VAT Purchase";
			}
		}
		
		
		$c_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$invoiceBooking->vendor_id])->first();
		
		$ReferenceDetails=$this->InvoiceBookings->ReferenceDetails->find()->where(['ledger_account_id'=>$c_LedgerAccount->id,'invoice_booking_id'=>$invoiceBooking->id]);
		
		
        $this->set('invoiceBooking', $invoiceBooking);
		$this->set(compact('LedgerAccount', 'ReferenceDetails','purchase_acc'));
        $this->set('_serialize', ['invoiceBooking']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
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
			
			
			   
		$grn_id=@(int)$this->request->query('grn');
		$grn=array();
		if(!empty($grn_id)){
			$grn = $this->InvoiceBookings->Grns->get($grn_id, [
				'contain' => ['GrnRows'=>['Items'],'Companies','Vendors','PurchaseOrders'=>['PurchaseOrderRows']]
			]);
			if($grn->purchase_order->discount_type=='%'){
					$discount=($grn->purchase_order->total*$grn->purchase_order->discount)/100;
			}else{
				$discount=$grn->purchase_order->discount;
			}
			$excise_duty=$grn->purchase_order->excise_duty;
			$tot_sale_tax=(($grn->purchase_order->total-$discount)*$grn->purchase_order->sale_tax_per)/100;
			
			$vendor_id=$grn->vendor->id; 
			$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$vendor_id])->first();
			
			$vendor_ledger_acc_id=$v_LedgerAccount->id;
		}
		$last_ib_no=$this->InvoiceBookings->find()->select(['ib2'])->where(['company_id' => $st_company_id])->order(['ib2' => 'DESC'])->first();
		if($last_ib_no){
			@$last_ib_no->ib2=$last_ib_no->ib2+1;
		}else{
			@$last_ib_no->ib2=1;
			}
		$q=0; $item_total_rate=0;
		foreach ($grn->grn_rows as $grn_rows){
			$dis=($discount*$grn->purchase_order->purchase_order_rows[$q]->amount)/$grn->purchase_order->total;
			$item_discount=$dis/$grn->purchase_order->purchase_order_rows[$q]->quantity;
			$item_total_rate+=$grn->purchase_order->purchase_order_rows[$q]->amount-$dis;
			$q++;
		} 
		$this->set(compact('grn','last_ib_no','discount','tot_sale_tax','chkdate','item_total_rate','excise_duty'));
		$invoiceBooking = $this->InvoiceBookings->newEntity();
		if ($this->request->is('post')) { 
		@$ref_rows=@$this->request->data['ref_rows'];
		
            $invoiceBooking = $this->InvoiceBookings->patchEntity($invoiceBooking, $this->request->data);
			$invoiceBooking->grn_id=$grn_id; 
			$invoiceBooking->created_on=date("Y-m-d");
			$invoiceBooking->company_id=$st_company_id;
			$invoiceBooking->supplier_date=date("Y-m-d",strtotime($invoiceBooking->supplier_date)); 
			$invoiceBooking->created_by=$this->viewVars['s_employee_id'];
			$invoiceBooking->due_payment=$invoiceBooking->total;

			$cst_purchase=0;
			if($st_company_id=='25'){
				$cst_purchase=35;
			}else if($st_company_id=='26'){
				$cst_purchase=161;
			}else if($st_company_id=='27'){
				$cst_purchase=309;
			}
			
			
            if ($this->InvoiceBookings->save($invoiceBooking)) {
				$i=0;
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row)
				{
				$item_id=$invoice_booking_row->item_id;
				$rate=$invoice_booking_row->rate;
				$query = $this->InvoiceBookings->ItemLedgers->query();
				$query->update()
					->set(['rate' => $rate, 'rate_updated' => 'Yes'])
					->where(['item_id' => $item_id, 'source_id' => $grn_id, 'company_id' => $st_company_id, 'source_model'=> 'Grns'])
					->execute();
				
				$results=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.item_id' => $item_id,'ItemLedgers.in_out' => 'In','rate_updated' => 'Yes','company_id' => $st_company_id,'quantity >'=>0])->toArray(); 
				
				$j=0; $qty_total=0; $rate_total=0; $per_unit_cost=0;
				foreach($results as $result){
					$qty=$result->quantity;
					$rate=$result->rate;
					@$total_amount=$qty*$rate;
					$rate_total=$rate_total+$total_amount;
					$qty_total=$qty_total+$qty;
				$j++;
				}
				if($qty_total!=0)
				{
					$per_unit_cost=$rate_total/$qty_total;
				}
				$query1 = $this->InvoiceBookings->Items->ItemCompanies->query();
				$query1->update()
					->set(['dynamic_cost' => @$per_unit_cost])
					->where(['company_id' => $st_company_id,'item_id'=>$item_id])
					->execute();
				$i++;
				}
				if(!empty($grn_id)){
					//$grn = $this->InvoiceBookings->Grns->get($grn_id);
					$grn = $this->InvoiceBookings->Grns->get($grn_id, [
								'contain' => ['GrnRows'=>['Items'],'Companies','Vendors','PurchaseOrders'=>['PurchaseOrderRows']]
							]);
					$grn->status='Invoice-Booked';
					$this->InvoiceBookings->Grns->save($grn);
				}
				$accountReferences = $this->InvoiceBookings->AccountReferences->get(2);
				if($invoiceBooking->purchase_ledger_account==$cst_purchase){
					//ledger posting for PURCHASE ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $invoiceBooking->total;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
				}else{
					//ledger posting for PURCHASE ACCOUNT
					$ledger_amount=$invoiceBooking->total_amount-$invoiceBooking->total_discount+$invoiceBooking->total_pnf +$invoiceBooking->total_ex;
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $ledger_amount;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					//ledger posting for VAT ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->ledger_account_for_vat;
					$ledger->debit = $invoiceBooking->total_saletax;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoiceBooking->total_saletax > 0){
						$this->InvoiceBookings->Ledgers->save($ledger);
					}
					
					//ledger posting for DISCOUNT ACCOUNT
					$ledger_account_for_discount=$this->InvoiceBookings->LedgerAccounts->find()->where(['invoice_booking_other_charge_post'=>1,'name'=>'Discount','company_id'=>$st_company_id])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $ledger_account_for_discount['id'];
					if($invoiceBooking->total_other_charges < 0){
						$ledger->credit = abs($invoiceBooking->total_other_charges);
						$ledger->debit = 0;
					}else if($invoiceBooking->total_other_charges > 0){
						$ledger->debit = $invoiceBooking->total_other_charges;
						$ledger->credit = 0;	
					}
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoiceBooking->total_other_charges != 0){
					$this->InvoiceBookings->Ledgers->save($ledger);
					}
				}
				
				
				//Ledger posting for SUPPLIER
				$c_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$grn->vendor_id])->first();
				$ledger = $this->InvoiceBookings->Ledgers->newEntity();
				$ledger->ledger_account_id = $c_LedgerAccount->id;
				$ledger->debit = 0;
				$ledger->credit =$invoiceBooking->total;
				$ledger->voucher_id = $invoiceBooking->id;
				$ledger->company_id = $invoiceBooking->company_id;
				$ledger->transaction_date = $invoiceBooking->supplier_date;
				$ledger->voucher_source = 'Invoice Booking';
				$this->InvoiceBookings->Ledgers->save($ledger);
				
				//Reference Number coding
					if(sizeof(@$ref_rows)>0){
						
						foreach($ref_rows as $ref_row){ 
							$ref_row=(object)$ref_row;
							
							$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
							$ReferenceDetail->company_id=$st_company_id;
							$ReferenceDetail->reference_type=$ref_row->ref_type;
							$ReferenceDetail->reference_no=$ref_row->ref_no;
							$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
							if($ref_row->ref_cr_dr=="Dr"){
								$ReferenceDetail->debit = $ref_row->ref_amount;
								$ReferenceDetail->credit = 0;
							}else{
								$ReferenceDetail->credit = $ref_row->ref_amount;
								$ReferenceDetail->debit = 0;
							}
							$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
							$ReferenceDetail->transaction_date =$invoiceBooking->supplier_date;
							
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
							
						}
						$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
						if($invoiceBooking->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $invoiceBooking->on_account;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $invoiceBooking->on_account;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
						$ReferenceDetail->transaction_date = $invoiceBooking->supplier_date;
						if($invoiceBooking->on_account > 0){
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
						}
					}

				
				
                $this->Flash->success(__('The invoice booking has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else { pr($invoiceBooking); exit;
                $this->Flash->error(__('The invoice booking could not be saved. Please, try again.'));
            }
        }
		
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(2);
		$ledger_account_details = $this->InvoiceBookings->LedgerAccounts->find('list')->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(4);
		$ledger_account_vat = $this->InvoiceBookings->LedgerAccounts->find('list'
				,['keyField' => 		function ($row) {
					return $row['id'];
				},
				'valueField' => function ($row) {
					if(!empty($row['alias'])){
						return  $row['name'] . ' (' . $row['alias'] . ')';
					}else{
						return $row['name'];
					}
					
				}])->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		
		$cst_purchase=0;
			if($st_company_id=='25'){
				$cst_purchase=35;
			}else if($st_company_id=='26'){
				$cst_purchase=161;
			}else if($st_company_id=='27'){
				$cst_purchase=309;
		
			}	
		$companies = $this->InvoiceBookings->Companies->find('all');
        $grns = $this->InvoiceBookings->Grns->find('list');
        $this->set(compact('invoiceBooking', 'grns','companies','ledger_account_details','v_LedgerAccount', 'ledger_account_vat','fromdate1','tody1','st_company_id'));
        $this->set('_serialize', ['invoiceBooking']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Invoice Booking id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
	$this->viewBuilder()->layout('index_layout');
	$session = $this->request->session();
	$id = $this->EncryptingDecrypting->decryptData($id);
	$invoice_booking_id=$id;
	$st_company_id = $session->read('st_company_id');

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


		$invoiceBooking = $this->InvoiceBookings->get($id, [
            'contain' => ['ReferenceDetails','InvoiceBookingRows' => ['Items'],'Grns'=>['Companies','Vendors','GrnRows'=>['Items'],'PurchaseOrders'=>['PurchaseOrderRows']]]
        ]);
		
		$vendor_id=$invoiceBooking->grn->vendor->id; 
		$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$vendor_id])->first();
		$vendor_ledger_acc_id=$v_LedgerAccount->id;
		
		//$ReferenceDetails = $this->InvoiceBookings->ReferenceDetails->find()->where(['ledger_account_id'=>$vendor_ledger_acc_id,'invoice_booking_id'=>$id])->toArray();
		
		$Em = new FinancialYearsController;
	    $financial_year_data = $Em->checkFinancialYear($invoiceBooking->created_on);

		
        if ($this->request->is(['patch', 'post', 'put'])) {
            $invoiceBooking = $this->InvoiceBookings->patchEntity($invoiceBooking, $this->request->data);
			$invoiceBooking->supplier_date=date("Y-m-d",strtotime($invoiceBooking->supplier_date)); 
			$cst_purchase=0;
					if($st_company_id=='25'){
						$cst_purchase=35;
					}else if($st_company_id=='26'){
						$cst_purchase=161;
					}else if($st_company_id=='27'){
						$cst_purchase=309;
					}
			$invoiceBooking->edited_on = date("Y-m-d"); 
			$invoiceBooking->edited_by=$this->viewVars['s_employee_id'];
			//pr($invoiceBooking->total_other_charges);
			pr($invoiceBooking);
			exit;
            if ($this->InvoiceBookings->save($invoiceBooking)) { 
				$ref_rows=@$this->request->data['ref_rows'];
				$invoiceBookingId=$invoiceBooking->id;
				$grn_id=$invoiceBooking->grn_id;
				
				if(!empty($grn_id)){
					$grn = $this->InvoiceBookings->Grns->get($grn_id, [
					'contain' => ['GrnRows'=>['Items'],'Companies','Vendors','PurchaseOrders'=>['PurchaseOrderRows']]
					]);
				}
				$this->InvoiceBookings->Ledgers->deleteAll(['voucher_id' =>$invoiceBookingId, 'voucher_source' => 'Invoice Booking']);
				$this->InvoiceBookings->ReferenceDetails->deleteAll(['invoice_booking_id' => $invoiceBookingId]);
				$i=0; 
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row)
				{
				$item_id=$invoice_booking_row->item_id;
				$rate=$invoice_booking_row->rate;
				$query = $this->InvoiceBookings->ItemLedgers->query();
				$query->update()
					->set(['rate' => $rate, 'rate_updated' => 'Yes'])
					->where(['item_id' => $item_id, 'source_id' => $grn_id, 'company_id' => $st_company_id, 'source_model'=> 'Grns'])
					->execute();
				$results=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.item_id' => $item_id,'ItemLedgers.in_out' => 'In','rate_updated' => 'Yes','company_id' => $st_company_id,'quantity >'=>0]); 
				$j=0; $qty_total=0; $rate_total=0; $per_unit_cost=0;
				
				foreach($results as $result){
					$qty=$result->quantity;
					$rate=$result->rate;
					@$total_amount=$qty*$rate;
					$rate_total=$rate_total+$total_amount;
					$qty_total=$qty_total+$qty;
				$j++;
				}
				if($qty_total!=0)
				{
					$per_unit_cost=$rate_total/$qty_total;
				}
				$query1 = $this->InvoiceBookings->Items->ItemCompanies->query();
				$query1->update()
					->set(['dynamic_cost' => $per_unit_cost])
					->where(['item_id' => $item_id,'company_id'=>$st_company_id])
					->execute();
				$i++;
				}
				
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
					$unit_rate = $this->weightedAvgCostIvs($invoice_booking_row->item_id,$invoiceBooking->supplier_date);
				}
			//	pr($unit_rate); exit;
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
					$unit_rate = $this->updateIvsInItemRate($invoice_booking_row->item_id,$invoiceBooking->supplier_date);
				}
				
				$accountReferences = $this->InvoiceBookings->AccountReferences->get(2);
				
				if($invoiceBooking->purchase_ledger_account==$cst_purchase){ 
					//ledger posting for PURCHASE ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $invoiceBooking->total;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
				}else{
					//ledger posting for PURCHASE ACCOUNT
					$ledger_amount=$invoiceBooking->total_amount-$invoiceBooking->total_discount+$invoiceBooking->total_pnf +$invoiceBooking->total_ex;
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $ledger_amount;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					
					//ledger posting for PURCHASE ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->ledger_account_for_vat;
					$ledger->debit = $invoiceBooking->total_saletax;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					//ledger posting for DISCOUNT ACCOUNT
					$ledger_account_for_discount=$this->InvoiceBookings->LedgerAccounts->find()->where(['invoice_booking_other_charge_post'=>1,'name'=>'Discount','company_id'=>$st_company_id])->first();
					
					
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $ledger_account_for_discount['id'];
					
					if($invoiceBooking->total_other_charges < 0){
						$ledger->credit = abs($invoiceBooking->total_other_charges);
						$ledger->debit = 0;
					}else if($invoiceBooking->total_other_charges > 0){
						$ledger->debit = $invoiceBooking->total_other_charges;
						$ledger->credit = 0;	
					}
					
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoiceBooking->total_other_charges != 0){
					$this->InvoiceBookings->Ledgers->save($ledger);
					}
				}
				
				
				//Ledger posting for SUPPLIER
				$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$vendor_id])->first();
				
				$ledger = $this->InvoiceBookings->Ledgers->newEntity();
				$ledger->ledger_account_id = $v_LedgerAccount->id;
				$ledger->debit = 0;
				$ledger->credit =$invoiceBooking->total;
				$ledger->voucher_id = $invoiceBooking->id;
				$ledger->transaction_date = $invoiceBooking->supplier_date;
				$ledger->company_id = $invoiceBooking->company_id;
				$ledger->voucher_source = 'Invoice Booking';
				$this->InvoiceBookings->Ledgers->save($ledger);
				//pr($invoiceBooking); exit;
				//Reference Number coding 
				
				if(sizeof(@$ref_rows)>0){
						
						foreach($ref_rows as $ref_row){ 
							$ref_row=(object)$ref_row;
							
							$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
							$ReferenceDetail->company_id=$st_company_id;
							$ReferenceDetail->reference_type=$ref_row->ref_type;
							$ReferenceDetail->reference_no=$ref_row->ref_no;
							$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
							if($ref_row->ref_cr_dr=="Dr"){
								$ReferenceDetail->debit = $ref_row->ref_amount;
								$ReferenceDetail->credit = 0;
							}else{
								$ReferenceDetail->credit = $ref_row->ref_amount;
								$ReferenceDetail->debit = 0;
							}
							$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
							$ReferenceDetail->transaction_date = $invoiceBooking->supplier_date;
							
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
							
						}
						$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
						if($invoiceBooking->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $invoiceBooking->on_account;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $invoiceBooking->on_account;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
						$ReferenceDetail->transaction_date = $invoiceBooking->supplier_date;
						if($invoiceBooking->on_account > 0){
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
						}
					}

				
                $this->Flash->success(__('The invoice booking has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The invoice booking could not be saved. Please, try again.'));
            }
        }
        $grns = $this->InvoiceBookings->Grns->find('list');
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(2);
		$ledger_account_details = $this->InvoiceBookings->LedgerAccounts->find('list')->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(4);
		$ledger_account_vat = $this->InvoiceBookings->LedgerAccounts->find('list'
				,['keyField' => 		function ($row) {
					return $row['id'];
				},
				'valueField' => function ($row) {
					if(!empty($row['alias'])){
						return  $row['name'] . ' (' . $row['alias'] . ')';
					}else{
						return $row['name'];
					}
					
				}])->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
        $this->set(compact('invoiceBooking','ReferenceDetails', 'grns','financial_year_data','invoice_booking_id','v_LedgerAccount', 'ledger_account_details', 'ledger_account_vat','chkdate','fromdate1','tody1','st_company_id'));
        $this->set('_serialize', ['invoiceBooking']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Invoice Booking id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invoiceBooking = $this->InvoiceBookings->get($id);
        if ($this->InvoiceBookings->delete($invoiceBooking)) {
            $this->Flash->success(__('The invoice booking has been deleted.'));
        } else {
            $this->Flash->error(__('The invoice booking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	function DueInvoiceBookingsForPayment($paid_to_id=null){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$Vendor=$this->InvoiceBookings->Vendors->find()->where(['ledger_account_id'=>$paid_to_id])->first();
		if(!$Vendor){ echo 'Select paid to.'; exit; }
		$InvoiceBookings = $this->InvoiceBookings->find()->where(['company_id'=>$st_company_id,'vendor_id'=>$Vendor->id,'due_payment >'=>0]);
		 $this->set(compact('InvoiceBookings','Vendor'));
	}
	
	
	public function fetchRefNumbers($ledger_account_id){
		$this->viewBuilder()->layout('');
		$ReferenceBalances=$this->InvoiceBookings->ReferenceBalances->find()->where(['ledger_account_id'=>$ledger_account_id]);
		$this->set(compact('ReferenceBalances','cr_dr'));
	}
	function checkRefNumberUnique($received_from_id,$i){
		$reference_no=$this->request->query['ref_rows'][$i]['ref_no'];
		$ReferenceBalances=$this->InvoiceBookings->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id,'reference_no'=>$reference_no]);
		if($ReferenceBalances->count()==0){
			echo 'true'; exit;
		}else{
			echo 'false';
		}
		exit;
	}
	
	public function fetchRefNumbersEdit($received_from_id=null,$reference_no=null,$credit=null){
		$this->viewBuilder()->layout('');
		$ReferenceBalances=$this->InvoiceBookings->ReferenceBalances->find()->where(['ledger_account_id'=>$received_from_id]);
		$this->set(compact('ReferenceBalances', 'reference_no', 'credit'));
	}
	
	function deleteOneRefNumbers(){
		$old_received_from_id=$this->request->query['old_received_from_id'];
		$invoice_booking_id=$this->request->query['invoice_booking_id'];
		$old_ref=$this->request->query['old_ref'];
		$old_ref_type=$this->request->query['old_ref_type'];
		
		if($old_ref_type=="New Reference" || $old_ref_type=="Advance Reference"){
			$this->InvoiceBookings->ReferenceBalances->deleteAll(['ledger_account_id'=>$old_received_from_id,'reference_no'=>$old_ref]);
			$this->InvoiceBookings->ReferenceDetails->deleteAll(['ledger_account_id'=>$old_received_from_id,'reference_no'=>$old_ref]);
		}elseif($old_ref_type=="Against Reference"){
			$ReferenceDetail=$this->InvoiceBookings->ReferenceDetails->find()->where(['ledger_account_id'=>$old_received_from_id,'invoice_booking_id'=>$invoice_booking_id,'reference_no'=>$old_ref])->first();
			if(!empty($ReferenceDetail->dedit)){
				$ReferenceBalance=$this->InvoiceBookings->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no])->first();
				$ReferenceBalance=$this->InvoiceBookings->ReferenceBalances->get($ReferenceBalance->id);
				$ReferenceBalance->dedit=$ReferenceBalance->dedit-$ReferenceDetail->dedit;
				$this->InvoiceBookings->ReferenceBalances->save($ReferenceBalance);
			}elseif(!empty($ReferenceDetail->credit)){
				$ReferenceBalance=$this->InvoiceBookings->ReferenceBalances->find()->where(['ledger_account_id' => $ReferenceDetail->ledger_account_id, 'reference_no' => $ReferenceDetail->reference_no])->first();
				$ReferenceBalance=$this->InvoiceBookings->ReferenceBalances->get($ReferenceBalance->id);
				$ReferenceBalance->credit=$ReferenceBalance->credit-$ReferenceDetail->credit;
				$this->InvoiceBookings->ReferenceBalances->save($ReferenceBalance);
			}
			$RDetail=$this->InvoiceBookings->ReferenceDetails->get($ReferenceDetail->id);
			$this->InvoiceBookings->ReferenceDetails->delete($RDetail);
		}
		
		exit;
	}
	public function exportSaleExcel(){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$From=$this->request->query('From');
		$To=$this->request->query('To');
		$where=[];
		$this->set(compact('From','To'));
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InvoiceBookings.created_on >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InvoiceBookings.created_on <=']=$To;
		}
		
		
		$InvoiceBookings = $this->InvoiceBookings->find()->contain(['InvoiceBookingRows','Vendors'])->where($where)->order(['InvoiceBookings.id' => 'DESC'])->where(['InvoiceBookings.company_id'=>$st_company_id,'gst'=>'no']);
		//pr($InvoiceBookings->toArray()); exit;
		$this->set(compact('InvoiceBookings'));
	}
	public function purchaseReport(){
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$From=$this->request->query('From');
		$To=$this->request->query('To');
		$where=[];
		$this->set(compact('From','To'));
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['InvoiceBookings.created_on >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['InvoiceBookings.created_on <=']=$To;
		}
		
		$this->viewBuilder()->layout('index_layout');
		$InvoiceBookings = $this->InvoiceBookings->find()->contain(['InvoiceBookingRows','Vendors'])->where($where)->order(['InvoiceBookings.id' => 'DESC'])->where(['InvoiceBookings.company_id'=>$st_company_id,'gst'=>'no']);
		//pr($InvoiceBookings->toArray()); exit;
		$this->set(compact('InvoiceBookings','url'));
	}
	
	public function gstInvoiceBooking()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
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
			
			
			   
		$grn_id=$this->request->query('grn');
		$grn_id = $this->EncryptingDecrypting->decryptData($grn_id);
		$grn=array();
		if(!empty($grn_id)){
			$grn = $this->InvoiceBookings->Grns->get($grn_id, [
				'contain' => ['GrnRows'=>['Items'],'Companies','Vendors','PurchaseOrders'=>['PurchaseOrderRows']]
			]);
			if($grn->purchase_order->discount_type=='%'){
					$discount=($grn->purchase_order->total*$grn->purchase_order->discount)/100;
			}else{
				$discount=$grn->purchase_order->discount;
			}
			$excise_duty=$grn->purchase_order->excise_duty;
			$tot_sale_tax=(($grn->purchase_order->total-$discount)*$grn->purchase_order->sale_tax_per)/100;
			
			$vendor_id=$grn->vendor->id; 
			$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$vendor_id])->first();
			
			$vendor_ledger_acc_id=$v_LedgerAccount->id;
		}
		$last_ib_no=$this->InvoiceBookings->find()->select(['ib2'])->where(['company_id' => $st_company_id,'financial_year_id'=>$st_year_id])->order(['ib2' => 'DESC'])->first();
		if($last_ib_no){
			@$last_ib_no->ib2=$last_ib_no->ib2+1;
		}else{
			@$last_ib_no->ib2=1;
			} //pr($last_ib_no->ib2); exit;
		$q=0; $item_total_rate=0;
		foreach ($grn->grn_rows as $grn_rows){
			$dis=($discount*$grn->purchase_order->purchase_order_rows[$q]->amount)/$grn->purchase_order->total;
			$item_discount=$dis/$grn->purchase_order->purchase_order_rows[$q]->quantity;
			$item_total_rate+=$grn->purchase_order->purchase_order_rows[$q]->amount-$dis;
			$q++;
		} 
		$this->set(compact('grn','last_ib_no','discount','tot_sale_tax','chkdate','item_total_rate','excise_duty'));
		$invoiceBooking = $this->InvoiceBookings->newEntity();
		if ($this->request->is('post')) { //pr($this->request->data());exit;
        $ref_rows=@$this->request->data['ref_rows'];
		
            $invoiceBooking = $this->InvoiceBookings->patchEntity($invoiceBooking, $this->request->data);
			$invoiceBooking->grn_id=$grn_id;
			$invoiceBooking->financial_year_id=$st_year_id;			
			$invoiceBooking->created_on=date("Y-m-d");
			$invoiceBooking->company_id=$st_company_id;
			$invoiceBooking->supplier_date=date("Y-m-d",strtotime($invoiceBooking->supplier_date)); 
			$invoiceBooking->created_by=$this->viewVars['s_employee_id'];
			$invoiceBooking->due_payment=$invoiceBooking->total;

			if ($this->InvoiceBookings->save($invoiceBooking)) {
				//pr($invoiceBooking);exit;
				/////start code for update supplier date into grn transaction date 
					$query1 = $this->InvoiceBookings->Grns->ItemLedgers->query();
					$query1->update()
					->set(['processed_on' => $invoiceBooking->supplier_date])
					->where(['source_model' => 'Grns','source_id'=>$invoiceBooking->grn_id])
					->execute();

					$query1 = $this->InvoiceBookings->Grns->query();
					$query1->update()
					->set(['transaction_date' => $invoiceBooking->supplier_date])
					->where(['id'=>$invoiceBooking->grn_id])
					->execute();
				/////ends code for update supplier date into grn transaction date 
				
				$i=0;
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row)
				{
				$item_id=$invoice_booking_row->item_id;
				$rate=$invoice_booking_row->rate;
				$query = $this->InvoiceBookings->ItemLedgers->query();
				$query->update()
					->set(['rate' => $rate, 'rate_updated' => 'Yes'])
					->where(['item_id' => $item_id, 'source_id' => $grn_id, 'company_id' => $st_company_id, 'source_model'=> 'Grns','source_row_id'=>$invoice_booking_row->grn_row_id])
					->execute();
				
				$results=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.item_id' => $item_id,'ItemLedgers.in_out' => 'In','rate_updated' => 'Yes','company_id' => $st_company_id ,'quantity >'=>0])->toArray(); 
				
				$j=0; $qty_total=0; $rate_total=0; $per_unit_cost=0;
				foreach($results as $result){
					$qty=$result->quantity;
					$rate=$result->rate;
					@$total_amount=$qty*$rate;
					$rate_total=$rate_total+$total_amount;
					$qty_total=$qty_total+$qty;
				$j++;
				}
				if($qty_total!=0)
				{
					$per_unit_cost=$rate_total/$qty_total;
				}
				$query1 = $this->InvoiceBookings->Items->ItemCompanies->query();
				$query1->update()
					->set(['dynamic_cost' => @$per_unit_cost])
					->where(['company_id' => $st_company_id,'item_id'=>$item_id])
					->execute();
					
				if($invoice_booking_row->cgst > 0){
					$cg_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->cgst_per])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $cg_LedgerAccount->id;
					$ledger->debit = $invoice_booking_row->cgst;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->transaction_date = $invoiceBooking->supplier_date; 
					$this->InvoiceBookings->Ledgers->save($ledger); 
				}
				if($invoice_booking_row->sgst > 0){
					$s_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->sgst_per])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $s_LedgerAccount->id;
					$ledger->debit = $invoice_booking_row->sgst;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger); 
				}
				if($invoice_booking_row->igst > 0){
					$i_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->igst_per])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $i_LedgerAccount->id;
					$ledger->debit = $invoice_booking_row->igst;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger); 
				}
				$i++;
				}
				if(!empty($grn_id)){
					//$grn = $this->InvoiceBookings->Grns->get($grn_id);
					$grn = $this->InvoiceBookings->Grns->get($grn_id, [
								'contain' => ['GrnRows'=>['Items'],'Companies','Vendors','PurchaseOrders'=>['PurchaseOrderRows']]
							]);
					$grn->status='Invoice-Booked';
					$this->InvoiceBookings->Grns->save($grn);
				}
				$accountReferences = $this->InvoiceBookings->AccountReferences->get(2);
					//ledger posting for PURCHASE ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $invoiceBooking->taxable_value;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					$ledger_account_for_discount=$this->InvoiceBookings->LedgerAccounts->find()->where(['invoice_booking_other_charge_post'=>1,'name'=>'Discount','company_id'=>$st_company_id])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $ledger_account_for_discount['id'];
					
					if($invoiceBooking->total_other_charge < 0){
						$ledger->credit = abs($invoiceBooking->total_other_charge);
						$ledger->debit = 0;
					}else if($invoiceBooking->total_other_charge > 0){ 
						$ledger->debit =abs($invoiceBooking->total_other_charge);
						$ledger->credit = 0;	
					}
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoiceBooking->total_other_charge != 0){
						
					$this->InvoiceBookings->Ledgers->save($ledger);
					}
				
				
				//Ledger posting for SUPPLIER
				$c_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$grn->vendor_id])->first();
				$ledger = $this->InvoiceBookings->Ledgers->newEntity();
				$ledger->ledger_account_id = $c_LedgerAccount->id;
				$ledger->debit = 0;
				$ledger->credit =$invoiceBooking->total;
				$ledger->voucher_id = $invoiceBooking->id;
				$ledger->company_id = $invoiceBooking->company_id;
				$ledger->transaction_date = $invoiceBooking->supplier_date;
				$ledger->voucher_source = 'Invoice Booking';
				$this->InvoiceBookings->Ledgers->save($ledger);
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
					$unit_rate = $this->weightedAvgCostIvs($invoice_booking_row->item_id,$invoiceBooking->supplier_date);
				}
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
					$unit_rate = $this->updateIvsInItemRate($invoice_booking_row->item_id,$invoiceBooking->supplier_date);
				}
				//Reference Number coding
					if(sizeof(@$ref_rows)== 0){
						$query = $this->InvoiceBookings->ReferenceDetails->query();
							$query->insert(['ledger_account_id', 'invoice_booking_id', 'reference_no', 'credit', 'debit', 'reference_type','transaction_date'])
							->values([
								'ledger_account_id' => $v_LedgerAccount->id,
								'invoice_booking_id' => $invoiceBooking->id,
								'reference_no' =>$invoiceBooking->invoice_no,
								'credit' => $invoiceBooking->total, 
								'debit' => 0,
								'reference_type' => 'New Reference',
								'transaction_date' => $invoiceBooking->supplier_date
							]);
							
							$query->execute();
						
					}else if(sizeof(@$ref_rows)>0){
						
						foreach($ref_rows as $ref_row){ 
							$ref_row=(object)$ref_row;
							
							$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
							$ReferenceDetail->company_id=$st_company_id;
							$ReferenceDetail->reference_type=$ref_row->ref_type;
							$ReferenceDetail->reference_no=$ref_row->ref_no;
							$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
							if($ref_row->ref_cr_dr=="Dr"){
								$ReferenceDetail->debit = $ref_row->ref_amount;
								$ReferenceDetail->credit = 0;
							}else{
								$ReferenceDetail->credit = $ref_row->ref_amount;
								$ReferenceDetail->debit = 0;
							}
							$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
							$ReferenceDetail->transaction_date =$invoiceBooking->supplier_date;
							
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
							
						}
						$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
						if($invoiceBooking->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $invoiceBooking->on_account;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $invoiceBooking->on_account;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
						$ReferenceDetail->transaction_date = $invoiceBooking->supplier_date;
						if($invoiceBooking->on_account > 0){
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
						}
					}

			   $this->Flash->success(__('The invoice booking has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else { //pr($invoiceBooking); exit;
                $this->Flash->error(__('The invoice booking could not be saved. Please, try again.'));
            }
        }
		
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(2);
		$ledger_account_details = $this->InvoiceBookings->LedgerAccounts->find()->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		
		//pr($ledger_account_details->toArray()); exit;
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(4);
		$ledger_account_vat = $this->InvoiceBookings->LedgerAccounts->find('list'
				,['keyField' => 		function ($row) {
					return $row['id'];
				},
				'valueField' => function ($row) {
					if(!empty($row['alias'])){
						return  $row['name'] . ' (' . $row['alias'] . ')';
					}else{
						return $row['name'];
					}
					
				}])->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		
			
			$GstTaxes = $this->InvoiceBookings->SaleTaxes->find()->where(['SaleTaxes.account_second_subgroup_id'=>6])->matching(
					'SaleTaxCompanies', function ($q) use($st_company_id) {
						return $q->where(['SaleTaxCompanies.company_id' => $st_company_id]);
					} 
				);
			//	pr($GstTaxes->toArray());exit;
        $companies = $this->InvoiceBookings->Companies->find('all');
        $grns = $this->InvoiceBookings->Grns->find('list');
		//pr($ledger_account_details->toArray());exit;
        $this->set(compact('invoiceBooking', 'grns','companies','ledger_account_details','v_LedgerAccount', 'ledger_account_vat','fromdate1','tody1','GstTaxes','st_company_id','todate1'));
        $this->set('_serialize', ['invoiceBooking']);
    }
	
	public function UpdateGstInvoiceBooking($id = null)
    {  
	$this->viewBuilder()->layout('index_layout');
	$session = $this->request->session();
	$id = $this->EncryptingDecrypting->decryptData($id);
	$invoice_booking_id=$id;
	
	$st_company_id = $session->read('st_company_id');

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

		
		$invoiceBooking = $this->InvoiceBookings->get($id, [
            'contain' => ['ReferenceDetails','InvoiceBookingRows' => ['Items'],'Grns'=>['Companies','Vendors','GrnRows'=>['Items'],'PurchaseOrders'=>['PurchaseOrderRows']]]
        ]);
		
		$vendor_id=$invoiceBooking->grn->vendor->id; 
		$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$vendor_id])->first();
		$vendor_ledger_acc_id=$v_LedgerAccount->id;
				
		$Em = new FinancialYearsController;
	    $financial_year_data = $Em->checkFinancialYear($invoiceBooking->created_on);

		
        if ($this->request->is(['patch', 'post', 'put'])) {
            $invoiceBooking = $this->InvoiceBookings->patchEntity($invoiceBooking, $this->request->data);
			$invoiceBooking->supplier_date=date("Y-m-d",strtotime($invoiceBooking->supplier_date)); 
			
			$invoiceBooking->edited_on = date("Y-m-d"); 
			$invoiceBooking->edited_by=$this->viewVars['s_employee_id'];
			//pr($invoiceBooking); exit;
            if ($this->InvoiceBookings->save($invoiceBooking)) { 
				/////start code for update supplier date into grn transaction date 
					$query1 = $this->InvoiceBookings->Grns->ItemLedgers->query();
					$query1->update()
					->set(['processed_on' => $invoiceBooking->supplier_date])
					->where(['source_model' => 'Grns','source_id'=>$invoiceBooking->grn_id])
					->execute();

					$query1 = $this->InvoiceBookings->Grns->query();
					$query1->update()
					->set(['transaction_date' => $invoiceBooking->supplier_date])
					->where(['id'=>$invoiceBooking->grn_id])
					->execute();
				/////ends code for update supplier date into grn transaction date 
			
				$ref_rows=@$this->request->data['ref_rows'];
				$invoiceBookingId=$invoiceBooking->id;
				$grn_id=$invoiceBooking->grn_id;
				
				if(!empty($grn_id)){
					$grn = $this->InvoiceBookings->Grns->get($grn_id, [
					'contain' => ['GrnRows'=>['Items'],'Companies','Vendors','PurchaseOrders'=>['PurchaseOrderRows']]
					]);
				}
				$this->InvoiceBookings->Ledgers->deleteAll(['voucher_id' =>$invoiceBookingId, 'voucher_source' => 'Invoice Booking']);
				$this->InvoiceBookings->ReferenceDetails->deleteAll(['invoice_booking_id' => $invoiceBookingId]);
				$i=0; 
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row)
				{
				$item_id=$invoice_booking_row->item_id;
				$rate=$invoice_booking_row->rate;
				$query = $this->InvoiceBookings->ItemLedgers->query();
				$query->update()
					->set(['rate' => $rate, 'rate_updated' => 'Yes'])
					->where(['item_id' => $item_id, 'source_id' => $grn_id, 'company_id' => $st_company_id, 'source_model'=> 'Grns','source_row_id'=>$invoice_booking_row->grn_row_id])
					->execute();
				$results=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.item_id' => $item_id,'ItemLedgers.in_out' => 'In','rate_updated' => 'Yes','company_id' => $st_company_id,'quantity >'=>0]); 
				$j=0; $qty_total=0; $rate_total=0; $per_unit_cost=0;
				foreach($results as $result){
					$qty=$result->quantity;
					$rate=$result->rate;
					@$total_amount=$qty*$rate;
					$rate_total=$rate_total+$total_amount;
					$qty_total=$qty_total+$qty;
				$j++;
				}
				
				if($qty_total!=0)
				{
					$per_unit_cost=$rate_total/$qty_total;
				}
				$query1 = $this->InvoiceBookings->Items->ItemCompanies->query();
				$query1->update()
					->set(['dynamic_cost' => $per_unit_cost])
					->where(['item_id' => $item_id,'company_id'=>$st_company_id])
					->execute();
				$i++;
				
				if($invoice_booking_row->cgst > 0){
					$cg_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->cgst_per])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $cg_LedgerAccount->id;
					$ledger->debit = $invoice_booking_row->cgst;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->transaction_date = $invoiceBooking->supplier_date; 
					$this->InvoiceBookings->Ledgers->save($ledger); 
				}
				if($invoice_booking_row->sgst > 0){
					$s_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->sgst_per])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $s_LedgerAccount->id;
					$ledger->debit = $invoice_booking_row->sgst;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger); 
				}
				if($invoice_booking_row->igst > 0){
					$i_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'SaleTaxes','source_id'=>$invoice_booking_row->igst_per])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $i_LedgerAccount->id;
					$ledger->debit = $invoice_booking_row->igst;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger); 
				}
				}
				$accountReferences = $this->InvoiceBookings->AccountReferences->get(2);
				
				
					//ledger posting for PURCHASE ACCOUNT
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $invoiceBooking->purchase_ledger_account;
					$ledger->debit = $invoiceBooking->taxable_value;
					$ledger->credit = 0;
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					$this->InvoiceBookings->Ledgers->save($ledger);
					
					$ledger_account_for_discount=$this->InvoiceBookings->LedgerAccounts->find()->where(['invoice_booking_other_charge_post'=>1,'name'=>'Discount','company_id'=>$st_company_id])->first();
					$ledger = $this->InvoiceBookings->Ledgers->newEntity();
					$ledger->ledger_account_id = $ledger_account_for_discount['id'];
					
					if($invoiceBooking->total_other_charge < 0){
						$ledger->credit = abs($invoiceBooking->total_other_charge);
						$ledger->debit = 0;
					}else if($invoiceBooking->total_other_charge > 0){ 
						$ledger->debit = abs($invoiceBooking->total_other_charge);
						$ledger->credit = 0;	
					}
					$ledger->voucher_id = $invoiceBooking->id;
					$ledger->company_id = $invoiceBooking->company_id;
					$ledger->voucher_source = 'Invoice Booking';
					$ledger->transaction_date = $invoiceBooking->supplier_date;
					if($invoiceBooking->total_other_charge != 0){
						
					$this->InvoiceBookings->Ledgers->save($ledger);
					}
				
				
				
				//Ledger posting for SUPPLIER
				$v_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$vendor_id])->first();
				
				$ledger = $this->InvoiceBookings->Ledgers->newEntity();
				$ledger->ledger_account_id = $v_LedgerAccount->id;
				$ledger->debit = 0;
				$ledger->credit =$invoiceBooking->total;
				$ledger->voucher_id = $invoiceBooking->id;
				$ledger->transaction_date = $invoiceBooking->supplier_date;
				$ledger->company_id = $invoiceBooking->company_id;
				$ledger->voucher_source = 'Invoice Booking';
				$this->InvoiceBookings->Ledgers->save($ledger);
				
				
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
					$unit_rate = $this->weightedAvgCostIvs($invoice_booking_row->item_id,$invoiceBooking->supplier_date);
				}
			//	pr($unit_rate); exit;
				foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
					$unit_rate = $this->updateIvsInItemRate($invoice_booking_row->item_id,$invoiceBooking->supplier_date);
				}
				//echo "hhhh";	exit;
				
				//pr($invoiceBooking); exit;
				//Reference Number coding 
				if(sizeof(@$ref_rows)== 0){
						$query = $this->InvoiceBookings->ReferenceDetails->query();
							$query->insert(['ledger_account_id', 'invoice_booking_id', 'reference_no', 'credit', 'debit', 'reference_type','transaction_date'])
							->values([
								'ledger_account_id' => $v_LedgerAccount->id,
								'invoice_booking_id' => $invoiceBooking->id,
								'reference_no' =>$invoiceBooking->invoice_no,
								'credit' => $invoiceBooking->total, 
								'debit' => 0,
								'reference_type' => 'New Reference',
								'transaction_date' => $invoiceBooking->supplier_date
							]);
							
							$query->execute();
						
					}else if(sizeof(@$ref_rows)>0){
						
						foreach($ref_rows as $ref_row){ 
							$ref_row=(object)$ref_row;
							
							$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
							$ReferenceDetail->company_id=$st_company_id;
							$ReferenceDetail->reference_type=$ref_row->ref_type;
							$ReferenceDetail->reference_no=$ref_row->ref_no;
							$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
							if($ref_row->ref_cr_dr=="Dr"){
								$ReferenceDetail->debit = $ref_row->ref_amount;
								$ReferenceDetail->credit = 0;
							}else{
								$ReferenceDetail->credit = $ref_row->ref_amount;
								$ReferenceDetail->debit = 0;
							}
							$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
							$ReferenceDetail->transaction_date = $invoiceBooking->supplier_date;
							
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
							
						}
						$ReferenceDetail = $this->InvoiceBookings->ReferenceDetails->newEntity();
						$ReferenceDetail->company_id=$st_company_id;
						$ReferenceDetail->reference_type="On_account";
						$ReferenceDetail->ledger_account_id = $v_LedgerAccount->id;
						if($invoiceBooking->on_acc_cr_dr=="Dr"){
							$ReferenceDetail->debit = $invoiceBooking->on_account;
							$ReferenceDetail->credit = 0;
						}else{
							$ReferenceDetail->credit = $invoiceBooking->on_account;
							$ReferenceDetail->debit = 0;
						}
						$ReferenceDetail->invoice_booking_id = $invoiceBooking->id;
						$ReferenceDetail->transaction_date = $invoiceBooking->supplier_date;
						if($invoiceBooking->on_account > 0){
							$this->InvoiceBookings->ReferenceDetails->save($ReferenceDetail);
						}
					}

                $this->Flash->success(__('The invoice booking has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The invoice booking could not be saved. Please, try again.'));
            }
        }
        $grns = $this->InvoiceBookings->Grns->find('list');
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(2);
		$ledger_account_details = $this->InvoiceBookings->LedgerAccounts->find()->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		
		$AccountReference= $this->InvoiceBookings->AccountReferences->get(4);
		$ledger_account_vat = $this->InvoiceBookings->LedgerAccounts->find('list')->contain(['AccountSecondSubgroups'=>['AccountFirstSubgroups' => function($q) use($AccountReference){
			return $q->where(['AccountFirstSubgroups.id'=>$AccountReference->account_first_subgroup_id]);
		}]])->order(['LedgerAccounts.name' => 'ASC'])->where(['LedgerAccounts.company_id'=>$st_company_id]);
		$GstTaxes = $this->InvoiceBookings->SaleTaxes->find()->where(['SaleTaxes.account_second_subgroup_id'=>6])->matching(
					'SaleTaxCompanies', function ($q) use($st_company_id) {
						return $q->where(['SaleTaxCompanies.company_id' => $st_company_id]);
					} 
				);
        $this->set(compact('invoiceBooking','ReferenceDetails', 'grns','financial_year_data','ReferenceBalances','invoice_booking_id','v_LedgerAccount', 'ledger_account_details', 'ledger_account_vat','chkdate','fromdate1','tody1','GstTaxes','st_company_id'));
        $this->set('_serialize', ['invoiceBooking']);
    }
	
	public function updateIvsInItemRate($item_id=null,$supplier_date=null){ //pr($item_id); exit;
			$this->viewBuilder()->layout('');
			$session = $this->request->session();
			$st_company_id = $session->read('st_company_id');
			$ivs = $this->InvoiceBookings->Ivs->find()->contain(['IvRows'=>['Items','IvRowItems'=>['Items'=>['ItemCompanies']]]])->toArray();
			
			$outExist = $this->InvoiceBookings->ItemLedgers->exists(['ItemLedgers.source_model' => 'Inventory Vouchers','ItemLedgers.item_id'=>$item_id,'ItemLedgers.company_id'=>$st_company_id]); 
			if($outExist >0){
			foreach($ivs as $iv){ 
				
					foreach($iv->iv_rows as $iv_row){ 
						$InItemAmount=0;
						$OutItemAmount=0;
						foreach($iv_row->iv_row_items as $iv_row_item){
							$ItemLedgersOuts=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.source_model'=>'Inventory Vouchers','ItemLedgers.company_id'=>$st_company_id,'iv_row_item_id'=>$iv_row_item->id])->first();
							if($ItemLedgersOuts){
								//pr($ItemLedgersOuts->rate);
								//pr($ItemLedgersOuts->quantity);
							$OutItemAmount+=$ItemLedgersOuts->rate*$ItemLedgersOuts->quantity;
							}
							
						}  
						$InItemAmount=$OutItemAmount/$iv_row->quantity;
						$query1 = $this->InvoiceBookings->ItemLedgers->query();
						$query1->update()
							->set(['rate' => $InItemAmount])
							->where(['source_model'=>'Inventory Vouchers','company_id'=>$st_company_id,'iv_row_id'=>$iv_row->id])
							->execute();
						//pr($InItemAmount);
					}
				}
			}
		 
	}
	public function weightedAvgCostIvs($item_id=null,$supplier_date=null){ 
			$this->viewBuilder()->layout('');
			$session = $this->request->session();
			$st_company_id = $session->read('st_company_id');
			//pr($supplier_date); exit;
			$ItemLedgersOuts=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$item_id,'ItemLedgers.company_id'=>$st_company_id,'in_out'=>'Out'])->where(['ItemLedgers.source_model IN'=>['Inventory Vouchers','Inventory Transfer Voucher']])->toArray();
//pr($ItemLedgersOuts); exit;
			$ItemSerialNo=$this->InvoiceBookings->Items->ItemCompanies->find()->where(['ItemCompanies.company_id'=>$st_company_id,'ItemCompanies.item_id'=>$item_id])->first();
			
			$serial_number_enable=$ItemSerialNo->serial_number_enable;

			if($serial_number_enable==1){ 
				
				foreach($ItemLedgersOuts as $ItemLedgersOut){
					
					if($ItemLedgersOut->source_model=="Inventory Vouchers"){
						$ItemSerialOuts=$this->InvoiceBookings->ItemLedgers->Items->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$item_id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'Out','SerialNumbers.iv_row_items '=>$ItemLedgersOut->iv_row_item_id])->toArray();
					}else if($ItemLedgersOut->source_model=="Inventory Transfer Voucher"){
						$ItemSerialOuts=$this->InvoiceBookings->ItemLedgers->Items->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$item_id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'Out','SerialNumbers.iv_row_items '=>$ItemLedgersOut->iv_row_item_id])->toArray();
					}
					$ItemAmt=0;
					$Itemqty=0;

					foreach($ItemSerialOuts as $ItemSerialOut){ 
						$ItemSerialParent=$this->InvoiceBookings->ItemLedgers->Items->SerialNumbers->get($ItemSerialOut->parent_id);
						if(@$ItemSerialParent->grn_id > 0){ 
							$ItemLedgerData =$this->InvoiceBookings->ItemLedgers->find()->where(['source_id'=>$ItemSerialParent->grn_id,'source_model'=>"Grns",'source_row_id'=>$ItemSerialParent->grn_row_id])->first();
							$ItemAmt+=@$ItemLedgerData->rate*@$ItemLedgerData->quantity;
							$Itemqty+=@$ItemLedgerData->quantity;
							
						}
						if(@$ItemSerialParent->sale_return_id > 0){ 
							$ItemLedgerData =$this->InvoiceBookings->ItemLedgers->find()->where(['source_id'=>$ItemSerialParent->sale_return_id,'source_model'=>"Sale Return",'source_row_id'=>$ItemSerialParent->sales_return_row_id])->first();
							$ItemAmt+=@$ItemLedgerData->rate*@$ItemLedgerData->quantity;
							$Itemqty+=@$ItemLedgerData->quantity;
							
						}
						if(@$ItemSerialNumber->itv_id > 0){
							$ItemLedgerData =$this->InvoiceBookings->ItemLedgers->find()->where(['source_id'=>$ItemSerialParent->itv_id,'source_model'=>"Inventory Transfer Voucher",'source_row_id'=>$ItemSerialParent->itv_row_id])->first();
							$ItemAmt+=@$ItemLedgerData->rate*@$ItemLedgerData->quantity;
							$Itemqty+=@$ItemLedgerData->quantity;
							
						}
						if(@$ItemSerialParent->iv_row_id > 0){ 
							$ItemLedgerData =$this->InvoiceBookings->ItemLedgers->find()->where(['source_model'=>"Inventory Vouchers",'iv_row_id'=>$ItemSerialParent->iv_row_id])->first();
							$ItemAmt+=@$ItemLedgerData->rate*@$ItemLedgerData->quantity;
							$Itemqty+=@$ItemLedgerData->quantity;
							
						}
						
					}
					if($Itemqty==0){
						$itemUnitRate=0;
					}else{
						$itemUnitRate=$ItemAmt/$Itemqty;
					}

					$query1 = $this->InvoiceBookings->ItemLedgers->query();
					$query1->update()
						->set(['rate' => $itemUnitRate])
						->where(['id' => $ItemLedgersOut->id])
						->execute();
				}
			}else{
				foreach($ItemLedgersOuts as $ItemLedgersOut){
					$stock=[];  $sumValue=0; $stockNew=[]; $where=[];
					
					if(!empty($supplier_date)){
						$where['ItemLedgers.processed_on <']=$ItemLedgersOut->processed_on;
					}
					
					$StockLedgers=$this->InvoiceBookings->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$item_id,'ItemLedgers.company_id'=>$st_company_id])->where($where)->order(['ItemLedgers.processed_on'=>'ASC'])->toArray();
					
					foreach($StockLedgers as $StockLedger){  
						if($StockLedger->in_out=='In'){ 
							if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
							$stockNew[]=['qty'=>$StockLedger->quantity, 'rate'=>$StockLedger->rate];
							}
						}
					}

					foreach($StockLedgers as $StockLedger){
						if($StockLedger->in_out=='Out'){	
							if(sizeof(@$stockNew)==0){
							break;
							}
							$outQty=$StockLedger->quantity;
							a:
							if(sizeof(@$stockNew)==0){
								break;
							}
							$R=@$stockNew[0]['qty']-$outQty;
							if($R>0){
								$stockNew[0]['qty']=$R;
							}
							else if($R<0){
								unset($stockNew[0]);
								@$stockNew=array_values(@$stockNew);
								$outQty=abs($R);
								goto a;
							}
							else{
								unset($stockNew[0]);
								$stockNew=array_values($stockNew);
							}
						}
					}
					$closingValue=0;
					$total_stock=0;
					$total_amt=0;
					$unit_rate=0;
					foreach($stockNew as $qw){
							$total_stock+=$qw['qty'];
							$total_amt+=$qw['rate']*$qw['qty'];
						
					} 

					if($total_amt > 0 && $total_stock > 0){
						 $unit_rate = $total_amt/$total_stock; 
					}
						
					
					
					$query1 = $this->InvoiceBookings->ItemLedgers->query();
					$query1->update()
						->set(['rate' => $unit_rate])
						->where(['id' => $ItemLedgersOut->id])
						->execute();
						
					}
				//pr($unit_rate); exit;
			}
			return;
		}

	public function GstInvoiceBookingView($id = null)
    {
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$id = $this->EncryptingDecrypting->decryptData($id);
		$this->viewBuilder()->layout('index_layout');
        $invoiceBooking = $this->InvoiceBookings->get($id, [
            'contain' => ['InvoiceBookingRows'=>['Items'],'Vendors','Creator','Companies','Grns'=>['PurchaseOrders']]
        ]);
		//pr($invoiceBooking);exit;
		$cgst_per=[];
		$sgst_per=[];
		$igst_per=[];
		foreach($invoiceBooking->invoice_booking_rows as $invoice_booking_row){
			if($invoice_booking_row->cgst_per > 0){
				$cgst_per[$invoice_booking_row->id]=$this->InvoiceBookings->SaleTaxes->get(@$invoice_booking_row->cgst_per);
			}
			if($invoice_booking_row->sgst_per > 0){
				$sgst_per[$invoice_booking_row->id]=$this->InvoiceBookings->SaleTaxes->get(@$invoice_booking_row->sgst_per);
			}
			if($invoice_booking_row->igst_per > 0){
				$igst_per[$invoice_booking_row->id]=$this->InvoiceBookings->SaleTaxes->get(@$invoice_booking_row->igst_per);
			}
		}
		
		$purchase_acc='';
		if($invoiceBooking->purchase_ledger_account > 0){
			$purchase_acc=$this->InvoiceBookings->LedgerAccounts->get($invoiceBooking->purchase_ledger_account);
		}
		
		$c_LedgerAccount=$this->InvoiceBookings->LedgerAccounts->find()->where(['company_id'=>$st_company_id,'source_model'=>'Vendors','source_id'=>$invoiceBooking->vendor_id])->first();
		
		$ReferenceDetails=$this->InvoiceBookings->ReferenceDetails->find()->where(['ledger_account_id'=>$c_LedgerAccount->id,'invoice_booking_id'=>$invoiceBooking->id]);
		
		
        $this->set('invoiceBooking', $invoiceBooking);
		$this->set(compact('LedgerAccount', 'ReferenceDetails','purchase_acc','cgst_per','sgst_per','igst_per'));
        $this->set('_serialize', ['invoiceBooking']);
    }
	public function NonGstpurchaseReport(){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		exit;
	}
	
	
	public function entryCount(){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');

		$InvoiceBookings=$this->InvoiceBookings->find()->contain(['Grns']);

		foreach($InvoiceBookings as $InvoiceBooking){ 
			$query1 = $this->InvoiceBookings->Grns->ItemLedgers->query();
			$query1->update()
			->set(['processed_on' => $InvoiceBooking->supplier_date])
			->where(['source_model' => 'Grns','source_id'=>$InvoiceBooking->grn->id])
			->execute();
			
			$query1 = $this->InvoiceBookings->Grns->query();
			$query1->update()
			->set(['transaction_date' => $InvoiceBooking->supplier_date])
			->where(['id'=>$InvoiceBooking->grn->id])
			->execute();
			//pr($InvoiceBooking); exit;

		} 
		  
		 exit;
	}
}
