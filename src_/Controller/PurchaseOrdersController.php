<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\View\Helper\TextHelper;
use Cake\View\Helper\NumberHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\Routing\Router;
/**
 * PurchaseOrders Controller
 *
 * @property \App\Model\Table\PurchaseOrdersTable $PurchaseOrders
 */
class PurchaseOrdersController extends AppController
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
        $this->paginate = [
            'contain' => ['Companies', 'Vendors']
        ];
		
		$pull_request=$this->request->query('pull-request');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->PurchaseOrders->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->PurchaseOrders->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->PurchaseOrders->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		
		
		$where=[];
		$purchase_no=$this->request->query('purchase_no');
		$file=$this->request->query('file');
		$vendor=$this->request->query('vendor');
		$total=$this->request->query('total');
		$items=$this->request->query('items');
		$From=$this->request->query('From');
		$To=$this->request->query('To');
		$this->set(compact('purchase_no','vendor','From','To','total','file','items'));
		if(!empty($purchase_no)){
			$where['po2 LIKE']=$purchase_no;
		}
		if(!empty($file)){
			$where['po3 LIKE']='%'.$file.'%';
		}
		if(!empty($total)){
			$where['total LIKE']=$total;
		}
		if(!empty($vendor)){
			$where['Vendors.company_name LIKE']='%'.$vendor.'%';
		}
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['PurchaseOrders.date_created >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['PurchaseOrders.date_created <=']=$To;
		}
		$where1=[];
		$purchaseOrders=[];
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['PurchaseOrders.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		if($status==null or $status=='Pending'){
			$having=['total_sales >' => 0];
			//$where1=['PurchaseOrderRows.processed_quantity < PurchaseOrderRows.quantity'];
		}elseif($status=='Converted-Into-GRN'){
			$having=['total_sales =' => 0];
			//$where1=['PurchaseOrderRows.processed_quantity = PurchaseOrderRows.quantity'];
		}
		
		
		if(!empty($items)){ 
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->matching('PurchaseOrderRows.Items', function ($q) use($items,$st_company_id) {
											return $q->where(['Items.id' =>$items,'company_id'=>$st_company_id]);
							})
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)
				->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
		}else{	
			if($pull_request=="true"){ 
				$tdate=date('Y-m-d',strtotime($financial_year->date_to)); 
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id,'PurchaseOrders.date_created <='=>$tdate])
				->where($where)
				->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
				//pr($purchaseOrders); exit;
			}
			else if($status=="Converted-Into-GRN" ){    
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)
				->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
			}
			else { 
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)
				->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
				
				$purchaseOrderDatas = $this->PurchaseOrders->find();
				$purchaseOrderDatas->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)
				->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
				
				$supplier_total_po=[];
					$total_sales=[]; $total_qty=[];
					if(!empty($purchaseOrderDatas)){
							foreach(@$purchaseOrderDatas as $salesorder){ //pr($salesorder); exit; 
							$total_sales[$salesorder->id]=$salesorder->total_sales;
							foreach($salesorder->purchase_order_rows as $sales_order_row){
								foreach($sales_order_row->grn_rows as $invoice_row){ 
										if(sizeof($invoice_row) > 0){ 
											@$total_qty[$salesorder->id]+=$invoice_row->quantity;
										}
								}
							}
							//$supplier_total_po[$salesorder->vendor_id][]=$salesorder->id;
						}
					}
					if(!empty($purchaseOrderDatas)){
						foreach($purchaseOrderDatas as $purchaseOrder){
							if(@$total_sales[@$purchaseOrder->id] != @$total_qty[@$purchaseOrder->id]){
								$supplier_total_po[$purchaseOrder->vendor_id][]=$purchaseOrder->id;
							}
						}
					}	
							
				}
			
		} 
		//pr($purchaseOrders->toArray()); exit;
		//pr($status); exit;
		$supplier_total_po=[];
		$total_sales=[]; $total_qty=[];
		if(!empty($purchaseOrders)){
				foreach(@$purchaseOrders as $salesorder){ //pr($salesorder); exit; 
				$total_sales[$salesorder->id]=$salesorder->total_sales;
				foreach($salesorder->purchase_order_rows as $sales_order_row){
					foreach($sales_order_row->grn_rows as $invoice_row){ 
							if(sizeof($invoice_row) > 0){ 
								@$total_qty[$salesorder->id]+=$invoice_row->quantity;
							}
					}
				}
				//$supplier_total_po[$salesorder->vendor_id][]=$salesorder->id;
			}
		}
		if(!empty($purchaseOrders)){
			foreach($purchaseOrders as $purchaseOrder){
				if(@$total_sales[@$purchaseOrder->id] != @$total_qty[@$purchaseOrder->id]){
					$supplier_total_po[$purchaseOrder->vendor_id][]=$purchaseOrder->id;
				}
			}
		}	
		
		$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find()->toArray();
		$Items = $this->PurchaseOrders->PurchaseOrderRows->Items->find('list')->order(['Items.name' => 'ASC']);
        $this->set(compact('purchaseOrders','pull_request','status','PurchaseOrderRows','PurchaseItems','Items','financial_month_first','financial_month_last','total_qty','total_sales','st_year_id','supplier_total_po'));
        $this->set('_serialize', ['purchaseOrders']);
		$this->set(compact('url'));
    }

	public function excelExport($status=null){
		
		$this->viewBuilder()->layout('');
       if(empty($status)){ $status="Pending"; }
		$pull_request=$this->request->query('pull-request');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->PurchaseOrders->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->PurchaseOrders->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->PurchaseOrders->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		
		
		$where=[];
		$purchase_no=$this->request->query('purchase_no');
		$file=$this->request->query('file');
		$vendor=$this->request->query('vendor');
		$total=$this->request->query('total');
		$items=$this->request->query('items');
		$From=$this->request->query('From');
		$To=$this->request->query('To');
		$this->set(compact('purchase_no','vendor','From','To','total','file','items'));
		if(!empty($purchase_no)){
			$where['po2 LIKE']=$purchase_no;
		}
		if(!empty($file)){
			$where['po3 LIKE']='%'.$file.'%';
		}
		if(!empty($total)){
			$where['total LIKE']=$total;
		}
		if(!empty($vendor)){
			$where['Vendors.company_name LIKE']='%'.$vendor.'%';
		}
		if(!empty($From)){
			$From=date("Y-m-d",strtotime($this->request->query('From')));
			$where['PurchaseOrders.date_created >=']=$From;
		}
		if(!empty($To)){
			$To=date("Y-m-d",strtotime($this->request->query('To')));
			$where['PurchaseOrders.date_created <=']=$To;
		}
		$where1=[];
		
		if($status==null or $status=='Pending'){
			$having=['total_sales >' => 0];
			//$where1=['PurchaseOrderRows.processed_quantity < PurchaseOrderRows.quantity'];
		}elseif($status=='Converted-Into-GRN'){
			$having=['total_sales =' => 0];
			//$where1=['PurchaseOrderRows.processed_quantity = PurchaseOrderRows.quantity'];
		}
		
		$styear=[1,3,2];
			if(in_array($st_year_id,$styear)){ 
				$wheree['PurchaseOrders.financial_year_id'] = $st_year_id;
			}else{
				$wheree=[];
			}
		if(!empty($items)){ 
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->matching('PurchaseOrderRows.Items', function ($q) use($items,$st_company_id) {
											return $q->where(['Items.id' =>$items,'company_id'=>$st_company_id]);
							})
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)
				->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
		}else{	
			if($pull_request=="true"){
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
			}
			if($status==null || $status=="Converted-Into-GRN" ){
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
			}
			if($status==null || $status=="Pending" ){ 
				$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find();
				$purchaseOrders = $this->PurchaseOrders->find();
				$purchaseOrders->select(['id','total_sales'=>$PurchaseOrderRows->func()->sum('PurchaseOrderRows.quantity')])
				->innerJoinWith('PurchaseOrderRows')
				->group(['PurchaseOrders.id'])
				->contain(['Companies', 'Vendors','PurchaseOrderRows'=>['Items','GrnRows']])
				->autoFields(true)
				->where(['PurchaseOrders.company_id'=>$st_company_id])
				->where($where)->where($wheree)
				->order(['PurchaseOrders.id'=>'DESC']);
			}
			
		} 
		//pr($purchaseOrders->toArray()); exit;
		//pr($status); exit;
		
		$total_sales=[]; $total_qty=[];
		foreach($purchaseOrders as $salesorder){
			$total_sales[$salesorder->id]=$salesorder->total_sales;
			foreach($salesorder->purchase_order_rows as $sales_order_row){
				foreach($sales_order_row->grn_rows as $invoice_row){ 
						if(sizeof($invoice_row) > 0){
							@$total_qty[$salesorder->id]+=$invoice_row->quantity;
						}
				}
			}
		}
		//pr($total_sales); 
		//pr($total_qty); 
		
		//exit;
		
		$PurchaseOrderRows = $this->PurchaseOrders->PurchaseOrderRows->find()->toArray();
		$Items = $this->PurchaseOrders->PurchaseOrderRows->Items->find('list')->order(['Items.name' => 'ASC']);
        $this->set(compact('purchaseOrders','pull_request','status','PurchaseOrderRows','PurchaseItems','Items','financial_month_first','financial_month_last','total_qty','total_sales'));
        $this->set('_serialize', ['purchaseOrders']);
		$this->set(compact('url'));
	}
    /**
     * View method
     *
     * @param string|null $id Purchase Order id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['Companies', 'Vendors', 'Grns', 'PurchaseOrderRows']
        ]);

        $this->set('purchaseOrder', $purchaseOrder);
        $this->set('_serialize', ['purchaseOrder']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
	
    public function add($to_be_send=null)
    { 
		if($to_be_send){
			$to_be_send=json_decode($to_be_send);
			$to_be_send2=[];
			foreach($to_be_send as $id=>$qty){
				$PurchaseOrderRow=$this->PurchaseOrders->MaterialIndentRows->get($id, [
					'contain' => ['Items'=>['ItemCompanies']]
				]);
				
				$to_be_send2[$id]=['qty'=>$qty,'item_name'=>$PurchaseOrderRow->item->name,'item_id'=>$PurchaseOrderRow->item_id,'row_id'=>$PurchaseOrderRow->id,'serial_number_enable'=>$PurchaseOrderRow->item->item_companies[0]->serial_number_enable];
			}
		}
		// pr($to_be_send2); exit;
		$this->viewBuilder()->layout('index_layout');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$Company = $this->PurchaseOrders->Companies->get($st_company_id);
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->PurchaseOrders->FinancialYears->find()->where(['id'=>$st_year_id])->first();
       $SessionCheckDate = $this->FinancialYears->get($st_year_id);
       $fromdate1 = date("Y-m-d",strtotime($SessionCheckDate->date_from));   
       $todate1 = date("Y-m-d",strtotime($SessionCheckDate->date_to)); 
       $tody1 = date("Y-m-d");

       $fromdate = strtotime($fromdate1);
       $todate = strtotime($todate1); 
       $tody = strtotime($tody1);

      if($fromdate > $tody || $todate < $tody)
       {
       	   $chkdate = 'Not Found';
       }
       else
       {
       	  $chkdate = 'Found';
       }
	   
	   $Material_indent_id=@(int)$this->request->query('Material_indent');
	 
		$materialIndents=array(); 
		$process_status='New';
		if(!empty($Material_indent_id)){
			$materialIndents = $this->PurchaseOrders->MaterialIndents->get($Material_indent_id, [
					'contain' => ['MaterialIndentRows' => function ($q) {
						return $q->where(['MaterialIndentRows.required_quantity > MaterialIndentRows.processed_quantity'])->contain(['Items']);
					}]
				]);
			$process_status='Pulled From Material Indent';
			
		}
		if(!empty($material)){ 
			$material_items=array(); 
			$materials=json_decode($material);
			$material_items_for_purchases=[];
			$this->set(compact('material_items_for_purchases'));
		}
		
		
        $purchaseOrder = $this->PurchaseOrders->newEntity();
        if ($this->request->is('post')) {
			$last_po_no=$this->PurchaseOrders->find()->select(['po2'])->where(['company_id' => $st_company_id,'financial_year_id'=>$st_year_id])->order(['po2' => 'DESC'])->first();
			if($last_po_no){
				$purchaseOrder->po2=$last_po_no->po2+1;
			}else{
				$purchaseOrder->po2=1;
			}
            $purchaseOrder = $this->PurchaseOrders->patchEntity($purchaseOrder, $this->request->data);
			$purchaseOrder->delivery_date=date("Y-m-d",strtotime($purchaseOrder->delivery_date));
			$purchaseOrder->created_by=$s_employee_id; 
			$purchaseOrder->company_id=$st_company_id;
			$purchaseOrder->financial_year_id=$st_year_id;
			$purchaseOrder->material_indent_id=$Material_indent_id;
			$purchaseOrder->sale_tax_description=$purchaseOrder->sale_tax_description; 
			$purchaseOrder->date_created=date("Y-m-d",strtotime($purchaseOrder->date_created));
		//	pr($purchaseOrder); exit;

			if ($this->PurchaseOrders->save($purchaseOrder)) {
				
/* 			foreach($purchaseOrder->purchase_order_rows as $purchase_order_row){

				if($purchase_order_row->pull_status=="PULLED_FROM_MI"){
					$query = $this->PurchaseOrders->MaterialIndentRows->find()
					->where(['MaterialIndentRows.id'=>$purchase_order_row->material_indent_row_id]);
					
					$MaterialIndentRows=$query->matching('MaterialIndents', function ($q) use($st_company_id){
						return $q->where(['MaterialIndents.company_id' => $st_company_id]);
					})->order(['MaterialIndents.created_on'=>'ASC']);
					//pr($MaterialIndentRows->toArray()); exit;
					
					$material_rows[$purchase_order_row->item_id]=[];
					foreach($MaterialIndentRows as $MaterialIndentRow){
						$material_rows[$MaterialIndentRow->item_id][]=['id'=>$MaterialIndentRow->id,'item_id'=>$MaterialIndentRow->item_id,'required_quantity'=>$MaterialIndentRow->required_quantity,'processed_quantity'=>$MaterialIndentRow->processed_quantity];
					}
					//pr($material_rows); exit;
				}
			} */
			
/* 			foreach($purchaseOrder->purchase_order_rows as $purchase_order_row){ 
			
				if($purchase_order_row->pull_status=="PULLED_FROM_MI"){
					$mi_rows=$material_rows[$purchase_order_row->item_id];
					ksort($mi_rows);
					
					$purchase_order_qty=$purchase_order_row->quantity;
					//pr($purchase_order_qty);
					foreach($mi_rows as $mi_row){  
						$mi_remaining_qty=$mi_row['required_quantity']-$mi_row['processed_quantity']; 
						//pr($mi_remaining_qty);
						$reminder=$mi_remaining_qty-$purchase_order_qty;
				
						if($reminder>0){
							$mi_row = $this->PurchaseOrders->MaterialIndentRows->get($mi_row['id']);
							$mi_row->processed_quantity=$mi_row->processed_quantity+$purchase_order_qty;
							$mi_row->status='Open';
							$this->PurchaseOrders->MaterialIndentRows->save($mi_row);
							break;
						}else if($reminder==0){ 
							$mi_row = $this->PurchaseOrders->MaterialIndentRows->get($mi_row['id']);
							$mi_row->processed_quantity=$mi_row->processed_quantity+$purchase_order_qty;
							$mi_row->status='Close';
							$this->PurchaseOrders->MaterialIndentRows->save($mi_row);
							goto send;
						}else{   
							$mi_row = $this->PurchaseOrders->MaterialIndentRows->get($mi_row['id']);
							$mi_row->processed_quantity =$mi_row->processed_quantity+abs($mi_remaining_qty);
							//pr($mi_row->processed_quantity); exit;
							$mi_row->status='Close';
							$this->PurchaseOrders->MaterialIndentRows->save($mi_row);
						}
						$purchase_order_qty=abs($reminder);
							
					}
					send:
				}
			} */
                $this->Flash->success(__('The purchase order has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else { pr($purchaseOrder); exit;
                $this->Flash->error(__('The purchase order could not be saved. Please, try again.'));
            }
        }
		$filenames = $this->PurchaseOrders->Filenames->find('list', ['valueField' => function ($row) {
				return $row['file1'] . '-' . $row['file2'];
			},
			'keyField' => function ($row) {
				return $row['file1'] . '-' . $row['file2'];
			}])->where(['file1' => 'BE']);
        //$vendor = $this->PurchaseOrders->Vendors->find()->order(['Vendors.company_name' => 'ASC']);
		$vendor = $this->PurchaseOrders->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);
				//pr($vendor->toArray()); exit;
		 $customers = $this->PurchaseOrders->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->matching('CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		$SaleTaxes = $this->PurchaseOrders->SaleTaxes->find('all')->where(['SaleTaxes.freeze'=>0])->matching(
					'SaleTaxCompanies', function ($q) use($st_company_id) {
						return $q->where(['SaleTaxCompanies.company_id' => $st_company_id]);
					} 
				);
		$items = $this->PurchaseOrders->PurchaseOrderRows->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
					'ItemCompanies', function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					}
				);
		$itemoptions=[];		
		foreach($items as $item){
			$itemoptions[]=['text'=>$item->name,'value'=>$item->id,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
		}
		
			
		$st_LedgerAccounts=$this->PurchaseOrders->SaleTaxes->SaleTaxCompanies->find('all')->where(['freeze'=>0,'company_id'=>$st_company_id]);

		$sale_tax_ledger_accounts=[];
		$sale_tax_ledger_accounts1=[];
			foreach($st_LedgerAccounts as $st_LedgerAccount){
				$SaleTaxes = $this->PurchaseOrders->SaleTaxes->find()->where(['id'=>$st_LedgerAccount->sale_tax_id])->first();
				@$sale_tax_ledger_accounts[@$st_LedgerAccount->sale_tax_id]=@$SaleTaxes->invoice_description;
				@$sale_tax_ledger_accounts1[@$st_LedgerAccount->sale_tax_id]=@$SaleTaxes->tax_figure;
				
			}
//pr($sale_tax_ledger_accounts); exit;

		$transporters = $this->PurchaseOrders->Transporters->find('list')->order(['Transporters.transporter_name' => 'ASC']);
        $this->set(compact('purchaseOrder', 'materialIndents','Company', 'vendor','filenames','items','SaleTaxes','transporters','customers','chkdate','to_be_send2','sale_tax_ledger_accounts','sale_tax_ledger_accounts1','itemoptions','financial_year'));
        $this->set('_serialize', ['purchaseOrder']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Purchase Order id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$s_employee_id=$this->viewVars['s_employee_id'];
		$id = $this->EncryptingDecrypting->decryptData($id);
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		$st_company_id = $session->read('st_company_id');
		
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->PurchaseOrders->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$financial_month_first = $this->PurchaseOrders->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->first();
		$financial_month_last = $this->PurchaseOrders->FinancialMonths->find()->where(['financial_year_id'=>$st_year_id,'status'=>'Open'])->last();
		//pr($financial_year); exit;
        $purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['PurchaseOrderRows'=>['Items'=>['ItemCompanies'],'GrnRows']]
        ]);
		
		$minItemQty    =[];
		if(!empty($purchaseOrder->purchase_order_rows))
		{
			foreach($purchaseOrder->purchase_order_rows as $purchase_order_row)
			{
				if(!empty($purchase_order_row->grn_rows))
				{
					foreach($purchase_order_row->grn_rows as $grn_row)
					{
						@$minItemQty[@$purchase_order_row->id] += @$grn_row->quantity;
					}
				}
			}
		}
		
		$purchaseOrder_old=$this->PurchaseOrders->get($id, [
            'contain' => ['PurchaseOrderRows'=>['Items'=>['ItemCompanies']]]
        ]);

		
	//$MaterialIndents = $this->PurchaseOrders->MaterialIndents->find()->contain(['MaterialIndentRows','PurchaseOrders'=>['PurchaseOrderRows']])->where(['MaterialIndents.company_id'=>$st_company_id]);
	
	$MaterialIndents = $this->PurchaseOrders->MaterialIndents->find()->contain(['MaterialIndentRows'=>['PurchaseOrderRows']])->where(['MaterialIndents.company_id'=>$st_company_id]);
	
	
		//pr($MaterialIndents->toArray()); exit;
	$mi_qty=[];
	$po_qty=[];
	$mi_id=[];
		foreach($MaterialIndents as $MaterialIndent){ $sales_qty=[];
			foreach($MaterialIndent->material_indent_rows as $purchase_order){
				foreach($purchase_order->purchase_order_rows as $purchase_order_row){ 
					if($purchase_order_row->material_indent_row_id){
						@$po_qty[$purchase_order_row['material_indent_row_id']]+=$purchase_order_row['quantity'];
					}
				}
			}
			foreach(@$MaterialIndent->material_indent_rows as $material_indent_row){  
				@$mi_qty[$material_indent_row['id']]+=$material_indent_row['required_quantity'];
				@$sales_qty[$material_indent_row['id']]+=$material_indent_row['required_quantity'];
			}
			foreach(@$sales_qty as $key=>$sales_order_qt){ 
				if(@$sales_order_qt > @$po_qty[$key] ){
				$materialIn = $this->PurchaseOrders->MaterialIndents->get($MaterialIndent->id);
				@$mi_id[]=@$materialIn;
				}
			}
		}
		/* pr($mi_qty);
		pr($po_qty); exit;	 */

		
		$Em = new FinancialYearsController;
        $financial_year_data = $Em->checkFinancialYear($purchaseOrder->date_created);


        if ($this->request->is(['patch', 'post', 'put'])) {
			
            $purchaseOrder = $this->PurchaseOrders->patchEntity($purchaseOrder, $this->request->data);
			$purchaseOrder->date_created=date("Y-m-d",strtotime($purchaseOrder->date_created));
			$purchaseOrder->delivery_date=date("Y-m-d",strtotime($purchaseOrder->delivery_date));
			$purchaseOrder->company_id=$st_company_id;
			$purchaseOrder->edited_on = date("Y-m-d"); 
			$purchaseOrder->edited_by=$this->viewVars['s_employee_id'];
			$purchaseOrder->material_indent_id=$purchaseOrder->material_indent_id;

			if ($this->PurchaseOrders->save($purchaseOrder)) {
			
                $this->Flash->success(__('The purchase order has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The purchase order could not be saved. Please, try again.'));
            }
        }
		$Company = $this->PurchaseOrders->Companies->get($st_company_id);
		//$companies = $this->PurchaseOrders->Companies->find();
		$filenames = $this->PurchaseOrders->Filenames->find('list', ['valueField' => function ($row) {
				return $row['file1'] . '-' . $row['file2'];
			},
			'keyField' => function ($row) {
				return $row['file1'] . '-' . $row['file2'];
			}])->where(['file1' => 'BE']);
		
		$st_LedgerAccounts=$this->PurchaseOrders->LedgerAccounts->find()->where(['source_model'=>'SaleTaxes','company_id'=>$st_company_id]);	
		$sale_tax_ledger_accounts=[];
		$sale_tax_ledger_accounts1=[];
			foreach($st_LedgerAccounts as $st_LedgerAccount){
				$SaleTaxes = $this->PurchaseOrders->SaleTaxes->find()->where(['id'=>$st_LedgerAccount->source_id])->first();
				@$sale_tax_ledger_accounts[@$st_LedgerAccount->source_id]=$SaleTaxes->invoice_description;
				@$sale_tax_ledger_accounts1[@$st_LedgerAccount->source_id]=$SaleTaxes->tax_figure;
				
			}
		$vendor = $this->PurchaseOrders->Vendors->find('all')->order(['Vendors.company_name' => 'ASC'])->matching('VendorCompanies', function ($q) use($st_company_id) {
						return $q->where(['VendorCompanies.company_id' => $st_company_id]);
					}
				);
		 $customers = $this->PurchaseOrders->Customers->find('all')->order(['Customers.customer_name' => 'ASC'])->matching('CustomerCompanies', function ($q) use($st_company_id) {
						return $q->where(['CustomerCompanies.company_id' => $st_company_id]);
					}
				);
		$SaleTaxes = $this->PurchaseOrders->SaleTaxes->find('all')->where(['SaleTaxes.freeze'=>0])->matching(
					'SaleTaxCompanies', function ($q) use($st_company_id) {
						return $q->where(['SaleTaxCompanies.company_id' => $st_company_id]);
					} 
				);
		$items = $this->PurchaseOrders->PurchaseOrderRows->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
					'ItemCompanies', function ($q) use($st_company_id) {
						return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0])->orWhere(['ItemCompanies.company_id'=>$st_company_id,'ItemCompanies.freeze' => 1]);
					}
				);

				
			$ItemsOptions=[];
			foreach($items as $item){ 
						$ItemsOptions[]=['value'=>$item->id,'text'=>$item->name,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
			}		

			$Item_datas = $this->PurchaseOrders->PurchaseOrderRows->Items->find()->where(['source IN'=>['Purchessed','Purchessed/Manufactured']])->order(['Items.name' => 'ASC'])->matching(
						'ItemCompanies', function ($q) use($st_company_id) {
							return $q->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
						}
					);		
					
			$ItemsOptionsData=[];
			foreach($Item_datas as $item){ 
						$ItemsOptionsData[]=['value'=>$item->id,'text'=>$item->name,'serial_number_enable'=>@$item->_matchingData['ItemCompanies']->serial_number_enable];
			}	
		
		$transporters = $this->PurchaseOrders->Transporters->find('list')->order(['Transporters.transporter_name' => 'ASC']);
       
        $this->set(compact('purchaseOrder','Company', 'vendor','filenames','customers','SaleTaxes','transporters','items','financial_year_data','sale_tax_ledger_accounts','sale_tax_ledger_accounts1','financial_month_first','financial_month_last','max_item_qty','minItemQty','ItemsOptionsData','ItemsOptions'));

		//$customers = $this->PurchaseOrders->Customers->find('all')->order(['Customers.customer_name' => 'ASC']);
		/* pr($mi_qty);
		pr($po_qty); exit; */
		
		
		$transporters = $this->PurchaseOrders->Transporters->find('list')->order(['Transporters.transporter_name' => 'ASC']);
       
        $this->set(compact('purchaseOrder', 'Company', 'vendor','filenames','customers','SaleTaxes','transporters','items','financial_year_data','sale_tax_ledger_accounts','sale_tax_ledger_accounts1','financial_month_first','financial_month_last','max_item_qty','minItemQty','mi_qty','po_qty','financial_year'));
        $this->set('_serialize', ['purchaseOrder']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Purchase Order id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purchaseOrder = $this->PurchaseOrders->get($id);
        if ($this->PurchaseOrders->delete($purchaseOrder)) {
            $this->Flash->success(__('The purchase order has been deleted.'));
        } else {
            $this->Flash->error(__('The purchase order could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
		public function pdf($id = null)
    {
		$this->viewBuilder()->layout('');
		$id = $this->EncryptingDecrypting->decryptData($id);
         $purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['Companies','Vendors','PurchaseOrderRows'=> ['Items'=>['Units']],'Transporters','Creator']
			]);
		//pr($purchaseOrder); exit;
        $this->set('purchaseOrder', $purchaseOrder);
        $this->set('_serialize', ['purchaseOrder']);
    }
	
	public function confirm($id = null)
    {
		$this->viewBuilder()->layout('pdf_layout');
		$id = $this->EncryptingDecrypting->decryptData($id);
			$purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['PurchaseOrderRows']
			]);
		
		
			if ($this->request->is(['patch', 'post', 'put'])) {
				if(!empty($this->request->data['pdf_font_size'])){
				$pdf_font_size=$this->request->data['pdf_font_size'];
				$query = $this->PurchaseOrders->query();
					$query->update()
						->set(['pdf_font_size' => $pdf_font_size])
						->where(['id' => $id])
						->execute();
			}
			if(!empty($this->request->data['purchase_order_rows'])){
				foreach($this->request->data['purchase_order_rows'] as $purchase_order_rows_id=>$value){
					$purchaseOrderRow=$this->PurchaseOrders->PurchaseOrderRows->get($purchase_order_rows_id);
					$purchaseOrderRow->height=$value["height"];
					$this->PurchaseOrders->PurchaseOrderRows->save($purchaseOrderRow);
				}
			}
			$id = $this->EncryptingDecrypting->encryptData($id);
			return $this->redirect(['action' => 'confirm',$id]);
        }
		$this->set(compact('purchaseOrder','id'));
        $this->set('id', $id);
    }
	
	public function pdfForMail($ids = null){
		$id = base64_decode($ids);
		$this->viewBuilder()->layout('');
         $purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['Companies','Vendors','PurchaseOrderRows'=> ['Items'=>['Units']],'Transporters','Creator']
			]);
		//pr($purchaseOrder); exit;
        $this->set('purchaseOrder', $purchaseOrder);
        $this->set('_serialize', ['purchaseOrder']);
	}
	
	public function confirmForMail($ids = null)
    {
		$id = base64_decode($ids);
		$this->viewBuilder()->layout('pdf_layout');
			$purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['PurchaseOrderRows']
			]);
		
		
			if ($this->request->is(['patch', 'post', 'put'])) {
				if(!empty($this->request->data['pdf_font_size'])){
				$pdf_font_size=$this->request->data['pdf_font_size'];
				$query = $this->PurchaseOrders->query();
					$query->update()
						->set(['pdf_font_size' => $pdf_font_size])
						->where(['id' => $id])
						->execute();
			}
			if(!empty($this->request->data['purchase_order_rows'])){
				foreach($this->request->data['purchase_order_rows'] as $purchase_order_rows_id=>$value){
					$purchaseOrderRow=$this->PurchaseOrders->PurchaseOrderRows->get($purchase_order_rows_id);
					$purchaseOrderRow->height=$value["height"];
					$this->PurchaseOrders->PurchaseOrderRows->save($purchaseOrderRow);
				}
			}
			return $this->redirect(['action' => 'confirmForMail/'.$id]);
        }
		$this->set(compact('purchaseOrder','id'));
        $this->set('id', $id);
    }
	
	public function customerFromFilename($filename=null){
		$this->viewBuilder()->layout('');
		$filename=explode('-',$filename);
		$Filename=$this->PurchaseOrders->Filenames->find()->where(['file1'=>$filename[0],'file2'=>$filename[1]])->first();
		
		$Customer=$this->PurchaseOrders->Customers->get($Filename->customer_id, [
            'contain' => ['CustomerAddress']
			]);
		//pr($Customer); exit;
		$this->set(compact('Customer'));
	}

	public function getproceedqty(){
		$purchaseorderrows = $this->PurchaseOrders->PurchaseOrderRows->find()->where(['processed_quantity > quantity']);
		$data=[];
		foreach($purchaseorderrows as $purchaseorderrow){
			$data[$purchaseorderrow->id]=$purchaseorderrow->purchase_order_id;
		}
		pr($data);exit;
	}
	
	public function showPendingItem($id=null){
		
			$PurchaseOrders = $this->PurchaseOrders->find()->contain(['PurchaseOrderRows'=>['GrnRows' => function($q) {
				return $q->select(['grn_id','purchase_order_row_id','item_id','total_qty' => $q->func()->sum('GrnRows.quantity')])->group('GrnRows.purchase_order_row_id');
			}]])->where(['PurchaseOrders.id'=>$id]);
			
			$sales_order_qty=[];
			 $invoice_qty=[];
			$salesData=[];
			
				foreach($PurchaseOrders as $SalesOrder){ $sales_qty=[]; $inc_qty=[]; 
					foreach($SalesOrder->purchase_order_rows as $sales_order_row){ 
						foreach($sales_order_row->grn_rows as $invoice_row){ //pr($invoice_row); exit;
							@$invoice_qty[$invoice_row['item_id']]+=$invoice_row['total_qty'];
							@$inc_qty[$invoice_row['item_id']]+=$invoice_row['total_qty'];
						}
						@$sales_order_qty[$sales_order_row['item_id']]+=$sales_order_row['quantity'];
						@$sales_qty[$sales_order_row['item_id']]+=$sales_order_row['quantity'];
					}
					
					
					foreach(@$sales_qty as $key=>$sales_order_qt){
							if(@$sales_order_qt > @$inc_qty[$key] ){ 
								$pen=@$sales_order_qt-@$inc_qty[$key];
								$itm= $this->PurchaseOrders->PurchaseOrderRows->Items->get($key);
								@$salesData[$itm->name]=$pen;
							}
					}
				}
		
		$this->set(compact('salesData'));
					
		
	}
	
	public function sendMail(){
		$totalPo=$this->request->query('totalPo');
		$totalPo = explode(",", $totalPo); 
		$PurchaseOrders = $this->PurchaseOrders->get($totalPo[0], [
            'contain' => ['Creator','Vendors'=>['VendorContactPersons'=>function($q){
				return $q->where(['VendorContactPersons.default_person'=>1]);
			}]]]);
		//pr($PurchaseOrders);exit;
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$company_data=$this->PurchaseOrders->Companies->get($st_company_id);
		
		$email = new Email('default');
		$email->transport('gmail');
		$email_to=$PurchaseOrders->vendor->vendor_contact_persons[0]->email;
		$cc_mail=$PurchaseOrders->creator->company_email;
		
		//$email_to="dimpaljain892@gmail.com";
		//$cc_mail="dimpaljain892@gmail.com";
		$url = Router::Url(['controller' => 'PurchaseOrders', 'action' => 'confirmForMail'], true);
		$delevery_date=[]; $po_no=[]; $due_day=[]; $so_no=[];
		foreach($totalPo as $data){
			$purchaseOrder = $this->PurchaseOrders->get($data);
			$delevery_date[$purchaseOrder->id]=date("d-m-Y",strtotime($purchaseOrder->delivery_date));
			$po_date[$purchaseOrder->id]=date("d-m-Y",strtotime($purchaseOrder->date_created));
			$po_no[$purchaseOrder->id]= h(($purchaseOrder->po1.'/PO-'.str_pad($purchaseOrder->po2, 3, '0', STR_PAD_LEFT).'/'.$purchaseOrder->po3.'/'.$purchaseOrder->po4));
			//$due_day[$purchaseOrder->id]=date("Y-m-d")-date("Y-m-d",strtotime($purchaseOrder->delivery_date));
			$date1=date("Y-m-d");
			$date2=date("Y-m-d",strtotime($purchaseOrder->delivery_date));
			//pr($date1); 
			//pr($date2);
			$date1=date_create($date1);
			$date2=date_create($date2);			
			$diff=date_diff($date2,$date1);
			echo $diff->format("%R%a days");
			$due_day[$purchaseOrder->id]=$diff->format("%R%a days");
			$so_no[$purchaseOrder->id]=$purchaseOrder->customer_so_no;
			//pr($diff); 
		}
		//pr($due_day); exit;
		//pr($due_day);
		//exit;
		
		$from_name=$company_data->alias;
		$sub="STL-Purchase Order Delivery Reminder";
		//pr($email_to);
		//pr($cc_mail);exit; 
		//$email_to="dimpaljain892@gmail.com";
		//$cc_mail="gopal@phppoets.in";
		//pr($email_to);exit; 
		$email->from(['dispatch@mogragroup.com' => $from_name])
		->to($email_to)
		->cc($cc_mail)
		->replyTo('dispatch@mogragroup.com')
		->subject($sub)
		->template('send_purchase_order')
		->emailFormat('html')
		->viewVars(['PurchaseOrders'=>$PurchaseOrders,'company'=>$company_data->name,'due_day'=>$due_day,'delevery_date'=>$delevery_date,'po_no'=>$po_no,'po_date'=>$po_date,'url'=>$url,'so_no'=>$so_no]);  
		$email->send();
		echo "Email Send successfully ";
		exit;
	}
}
