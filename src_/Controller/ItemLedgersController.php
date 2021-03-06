<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ItemLedgers Controller
 *
 * @property \App\Model\Table\ItemLedgersTable $ItemLedgers
 */
class ItemLedgersController extends AppController
{
 
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index($item_id=null)
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        $this->paginate = [
            'contain' => ['Items']
        ];
        $itemLedgers2 = $this->paginate($this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$item_id,'ItemLedgers.company_id'=>$st_company_id])->order(['processed_on'=>'DESC']));
		$itemLedgers=[];
		foreach($itemLedgers2 as $itemLedger){
			if($itemLedger->source_model =='Items'){
				$itemLedger->voucher_info='-';
				$itemLedger->party_type='Item';
				$itemLedger->party_info='-'; 
			}else{
				$result=$this->GetVoucherParty($itemLedger->source_model,$itemLedger->source_id); 
				$itemLedger->voucher_info=$result['voucher_info'];
				$itemLedger->party_type=$result['party_type'];
				$itemLedger->party_info=$result['party_info']; 	
			}
			$itemLedgers[]=$itemLedger;
		}
        $this->set(compact('itemLedgers'));
        $this->set('_serialize', ['itemLedgers']);
    }
	
	 public function DataMigrate()
    {
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$this->loadModel('NewItems');
		$NewItems=$this->NewItems->find()->where(['NewItems.source_model'=>'Items']);
		
		foreach($NewItems as $NewItem){
			$itemLedger = $this->ItemLedgers->newEntity();
			$itemLedger->item_id = $NewItem->item_id;
			$itemLedger->quantity = $NewItem->quantity;
			$itemLedger->rate = $NewItem->rate;
			$itemLedger->source_model = 'Items';
			$itemLedger->source_id = $NewItem->item_id;
			$itemLedger->in_out = 'In';
			$itemLedger->company_id = $NewItem->company_id;
			$itemLedger->processed_on ="2017-04-01";
			$this->ItemLedgers->save($itemLedger);
		}
		/* $NewItems=$this->NewItems->find()->where(['NewItems.source_model'=>'Grns']);
		foreach($NewItems as $NewItem){ pr($NewItem);exit;
			$itemLedger = $this->ItemLedgers->newEntity();
			$itemLedger->item_id = $NewItem->item_id;
			$itemLedger->quantity = $NewItem->quantity;
			$itemLedger->rate = $NewItem->rate;
			$itemLedger->source_model = 'Grns';
			$itemLedger->source_id = $NewItem->item_id;
			$itemLedger->in_out = 'In';
			$itemLedger->company_id = $NewItem->company_id;
			$itemLedger->processed_on =$NewItem->company_id;
			$this->ItemLedgers->save($itemLedger);
		} */
		
		@$NewSerialNumbers=$this->ItemLedgers->NewSerialNumbers->find()->where(['NewSerialNumbers.master_item_id > '=>0]);
		
		//pr($NewSerialNumbers->toArray()); exit;
		foreach($NewSerialNumbers as $NewSerialNumber){ 
			$SerialNumber = $this->ItemLedgers->SerialNumbers->newEntity();
			$SerialNumber->item_id = $NewSerialNumber->item_id;
			$SerialNumber->name = $NewSerialNumber->serial_no;
			$SerialNumber->status = 'In';
			$SerialNumber->is_opening_balance = 'Yes';
			$SerialNumber->company_id = $NewSerialNumber->company_id;
			$SerialNumber->transaction_date ='2017-04-01';
			$this->ItemLedgers->SerialNumbers->save($SerialNumber);
		}
		echo "done"; exit;
		 exit;
	} 
	
	public function GetVoucherParty($source_model=null,$source_id=null)
    {
		
		//return $source_model.$source_id;
		if($source_model=="Grns"){
			$Grn=$this->ItemLedgers->Grns->get($source_id);
			//pr($Grn); 
			$Vendor=$this->ItemLedgers->Vendors->get($Grn->vendor_id);
			return ['voucher_info'=>$Grn,'party_type'=>'-','party_info'=>$Vendor];
		}
		
		if($source_model=="Inventory Vouchers"){ //echo "IV"; exit;
			$InventoryVoucher=$this->ItemLedgers->Ivs->get($source_id);
			//pr($InventoryVoucher); 
			return ['voucher_info'=>$InventoryVoucher,'party_type'=>'-','party_info'=>''];
		}
		if($source_model=="Invoices"){
			$Invoice=$this->ItemLedgers->Invoices->get($source_id);
			$Customer=$this->ItemLedgers->Customers->get($Invoice->customer_id);
			return ['voucher_info'=>$Invoice,'party_type'=>'Customer','party_info'=>$Customer];
		}
		if($source_model=="Challan"){ 
			$Challan=$this->ItemLedgers->Challans->get($source_id);
			
			if($Challan->challan_for=='Customer'){
			$Party=$this->ItemLedgers->Customers->get($Challan->customer_id);
			}else{ 
			$Party=$this->ItemLedgers->Vendors->get($Challan->vendor_id);
			}
			return ['voucher_info'=>$Challan,'party_type'=>$Challan->challan_for,'party_info'=>$Party];
		}
		if($source_model=="Purchase Return"){
			$PurchaseReturn=$this->ItemLedgers->PurchaseReturns->get($source_id,[
						'contain'=>['FinancialYears']
					]);
			
			$Vendor=$this->ItemLedgers->Vendors->get($PurchaseReturn->vendor_id);
			return ['voucher_info'=>$PurchaseReturn,'party_type'=>'Purchase','party_info'=>$Vendor];
		}
		if($source_model=="Sale Return"){
			$SaleReturn=$this->ItemLedgers->SaleReturns->get($source_id);
			$Customer=$this->ItemLedgers->Customers->get($SaleReturn->customer_id);
			return ['voucher_info'=>$SaleReturn,'party_type'=>'Sale','party_info'=>$Customer];
		}
		 if($source_model=="Inventory Transfer Voucher"){ 
			$InventoryTransferVouchers=$this->ItemLedgers->InventoryTransferVouchers->get($source_id);//pr($source_id);exit;
			//$Item=$this->ItemLedgers->Items->get($source_id);
			return ['voucher_info'=>$InventoryTransferVouchers,'party_type'=>'-','party_info'=>'-'];
		} 
		if($source_model=="Inventory Return"){ 
			$Inventoryreturn=$this->ItemLedgers->Rivs->get($source_id);
			//pr($source_id);exit;
			//pr($Inventoryreturn);exit;
			return ['voucher_info'=>$Inventoryreturn,'party_type'=>'-','party_info'=>'-'];
		} 
       return $source_model.$source_id;
    }
	
	public function stockLedger(){
		
	$this->viewBuilder()->layout('index_layout');
        $session = $this->request->session();
        $items=$this->request->query('items');
		$From=$this->request->query('From');
		$To=$this->request->query('To');
		
		$this->set(compact('From','To'));
		$where=[];
		if(!empty($items)){  
			$where['id']=$items;
			
		}
				
		$Items = $this->ItemLedgers->Items->find('list')->where($where)->order(['Items.name' => 'ASC']);
		$Items_list = $this->ItemLedgers->Items->find('list')->order(['Items.name' => 'ASC']);
		$this->set(compact('Items', 'items','From','To','Items_list'));
		$this->set('_serialize', ['itemLedgers']); 
    }
	
	public function GetItemVouchers($source_model_id=null,$source_id1=null)
    {
		if($source_model_id=="Invoices"){
			$Invoice=$this->ItemLedgers->Invoices->get($source_id1);
			return ['voucher_info'=>$Invoice];
		}
		
		if($source_model_id=="Grns"){ 
			$Grns=$this->ItemLedgers->Grns->get($source_id1);
			return ['voucher_info'=>$Grns];
		}
		
		if($source_model_id=="Inventory Vouchers"){ 
			$InventoryVoucher=$this->ItemLedgers->InventoryVouchers->get($source_id1);
			return ['voucher_info'=>$InventoryVoucher];
		}
		
		if($source_model_id=="Challan"){ 
			$Challan=$this->ItemLedgers->Challans->get($source_id1);
			
			return ['voucher_info'=>$Challan];
		}
		if($source_model_id=="Purchase Return"){
			$PurchaseReturn=$this->ItemLedgers->PurchaseReturns->get($source_id1);
			
			return ['voucher_info'=>$PurchaseReturn];
		}
		if($source_model_id=="Sale Return"){
			$SaleReturn=$this->ItemLedgers->SaleReturns->get($source_id1);
			return ['voucher_info'=>$SaleReturn];
		}
		 if($source_model_id=="Inventory Transfer Voucher"){ 
			$InventoryTransferVouchers=$this->ItemLedgers->InventoryTransferVouchers->get($source_id1);
			
			return ['voucher_info'=>$InventoryTransferVouchers];
		} 
		if($source_model_id=="Inventory Return"){ 
			$Inventoryreturn=$this->ItemLedgers->Rivs->get($source_id1);
			return ['voucher_info'=>$Inventoryreturn];
		} 
		return $source_model_id.$source_id1;
    }
    /**
     * View method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $itemLedger = $this->ItemLedgers->get($id, [
            'contain' => ['Items', 'Sources', 'Companies']
        ]);

        $this->set('itemLedger', $itemLedger);
        $this->set('_serialize', ['itemLedger']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $itemLedger = $this->ItemLedgers->newEntity();
        if ($this->request->is('post')) {
            $itemLedger = $this->ItemLedgers->patchEntity($itemLedger, $this->request->data);
            if ($this->ItemLedgers->save($itemLedger)) {
                $this->Flash->success(__('The item ledger has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
            }
        }
        $items = $this->ItemLedgers->Items->find('list', ['limit' => 200]);
        $sources = $this->ItemLedgers->Sources->find('list', ['limit' => 200]);
        $companies = $this->ItemLedgers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('itemLedger', 'items', 'sources', 'companies'));
        $this->set('_serialize', ['itemLedger']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $itemLedger = $this->ItemLedgers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $itemLedger = $this->ItemLedgers->patchEntity($itemLedger, $this->request->data);
            if ($this->ItemLedgers->save($itemLedger)) {
                $this->Flash->success(__('The item ledger has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
            }
        }
        $items = $this->ItemLedgers->Items->find('list', ['limit' => 200]);
        $sources = $this->ItemLedgers->Sources->find('list', ['limit' => 200]);
        $companies = $this->ItemLedgers->Companies->find('list', ['limit' => 200]);
        $this->set(compact('itemLedger', 'items', 'sources', 'companies'));
        $this->set('_serialize', ['itemLedger']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $itemLedger = $this->ItemLedgers->get($id);
        if ($this->ItemLedgers->delete($itemLedger)) {
            $this->Flash->success(__('The item ledger has been deleted.'));
        } else {
            $this->Flash->error(__('The item ledger could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function stockReport($stockstatus=null){
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$this->viewBuilder()->layout('index_layout');
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$stockstatus=$this->request->query('stockstatus');
		$item_name=$this->request->query('item_name');
		$item_category=$this->request->query('item_category');
		$item_group=$this->request->query('item_group_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$stock=$this->request->query('stock');
		$status=$this->request->query('status');
		$item_sub_group=$this->request->query('item_sub_group_id');
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->ItemLedgers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$date = $financial_year->date_from;
		$due_date= $financial_year->date_from;
		if(empty($from_date)){
			$from_date=$date;
			$to_date=date('Y-m-d');
		};
		$where=[];
		$where1=[];
		
		$this->set(compact('item_category','item_group','item_sub_group','stock','item_name'));
		if(!empty($item_name)){ 
			$where['Item_id']=$item_name;
			
		}
		if(!empty($item_category)){
			$where['Items.item_category_id']=$item_category;
		}
		if(!empty($item_group)){
			$where['Items.item_group_id ']=$item_group;
		}
		if(!empty($item_sub_group)){
			$where['Items.item_sub_group_id ']=$item_sub_group;
		}
		if(!empty($search_date)){
			$search_date=date("Y-m-d",strtotime($search_date));
            $where1['processed_on <=']=$search_date;
		}
			//pr($where);exit;
		$item_stocks =[];$items_names =[];
		
		$query = $this->ItemLedgers->find()->where(['ItemLedgers.processed_on >='=> date("Y-m-d",strtotime($from_date)), 'ItemLedgers.processed_on <=' =>date("Y-m-d",strtotime($to_date)),'company_id'=>$st_company_id]);

		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['in_out' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['in_out' => 'Out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);

			
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->contain(['Items'=>function ($q) use($where) { 
				return $q->where($where)->contain(['Units']);
				}])
		->where(['company_id'=>$st_company_id])
		->group('item_id')
		->autoFields(true)
		->where($where)
		
		->order(['Items.name'=>'ASC']);
		$results =$query->toArray();
		
		//pr($results); exit;
		
		
		if($stock == "Negative"){
			foreach($results as $result){
				if($result->total_in - $result->total_out < 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				}
			}
		}elseif($stock == "Zero"){
			foreach($results as $result){
				if($result->total_in - $result->total_out == 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				}
			}
		}elseif($stock == "Positive"){
			foreach($results as $result){
				if($result->total_in - $result->total_out > 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
					//pr($item_stocks);
				}
			}
		}elseif($stockstatus == "Positive"){
			foreach($results as $result){
				if($result->total_in - $result->total_out > 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
					
				}
			}
		}else{
			foreach($results as $result){
				
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				
				}
			}
		$ItemLedgers = $this->ItemLedgers->find()->contain(['Items'=>function ($q) use($where){
			return $q->where($where);
		}])->where(['ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.rate >'=>0]);
		
		$item_rate=[];
		$in_qty=[];
		foreach($ItemLedgers as $ItemLedger){
				if($ItemLedger->in_out == 'In'){
					@$item_rate[$ItemLedger->item_id] += ($ItemLedger->quantity*$ItemLedger->rate);
					@$in_qty[$ItemLedger->item_id] += $ItemLedger->quantity;
				}
		}
		
		
		$where=[];
		if(!empty($item_name)){ 
			$where['Items.id']=$item_name;
			
		}
		if(!empty($item_category)){
			$where['Items.item_category_id']=$item_category;
		}
		if(!empty($item_group)){
			$where['Items.item_group_id ']=$item_group;
		}
		if(!empty($item_sub_group)){
			$where['Items.item_sub_group_id ']=$item_sub_group;
		}
		if(!empty($search_date)){
			$search_date=date("Y-m-d",strtotime($search_date));
            $where1['processed_on <=']=$search_date;
		}
		 $ItemDatas=[];
		 $ItemUnits=[];
		if(!$stock){
		$Items =$this->ItemLedgers->Items->find()->contain(['Units','ItemCompanies'=>function($p) use($st_company_id){
						return $p->where(['ItemCompanies.company_id' => $st_company_id]);
		}])->where($where);
		
		
		foreach($Items as $Item){ 
			$ItemLedgersexists = $this->ItemLedgers->exists(['item_id' => $Item->id,'company_id'=>$st_company_id]);
			if(empty($ItemLedgersexists)){
				$ItemDatas[$Item->id]=$Item->name;
				$ItemUnits[$Item->id]=$Item->unit->name;
			}
		}
	}		
		$ItemCategories = $this->ItemLedgers->Items->ItemCategories->find('list')->order(['ItemCategories.name' => 'ASC']);
		$ItemGroups = $this->ItemLedgers->Items->ItemGroups->find('list')->order(['ItemGroups.name' => 'ASC']);
		$ItemSubGroups = $this->ItemLedgers->Items->ItemSubGroups->find('list')->order(['ItemSubGroups.name' => 'ASC']);
		$Items = $this->ItemLedgers->Items->find('list')->order(['Items.name' => 'ASC']);
        $this->set(compact('itemLedgers', 'item_name','item_stocks','items_names','ItemCategories','ItemGroups','ItemSubGroups','item_rate','in_qty','Items','from_date','to_date','ItemDatas','items_unit_names','ItemUnits','url','stockstatus'));
		$this->set('_serialize', ['itemLedgers']); 
    }
	
	public function stockValuation(){ 
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		$Items =$this->ItemLedgers->Items->find()->contain(['ItemCompanies'=>function($p) use($st_company_id){
		return $p->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
		}]);
		
		$stock=[];  $sumValue=0; $itemSerialRate=[]; $itemSerialQuantity=[];
		foreach($Items as $Item){
			if(@$Item->item_companies[0]->serial_number_enable==0){  
				$StockLedgers=$this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$Item->id,'ItemLedgers.company_id'=>$st_company_id])->order(['ItemLedgers.processed_on'=>'ASC']);
				foreach($StockLedgers as $StockLedger){ 
					if($StockLedger->in_out=='In'){ 
						if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
							for($inc=0.01;$inc<$StockLedger->quantity;$inc+=0.01){
								$stock[$Item->id][]=$StockLedger->rate;
							}
						}
					}
				}
				foreach($StockLedgers as $StockLedger){
					if($StockLedger->in_out=='Out'){
						if(sizeof(@$stock[$Item->id])>0){
							$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity*100); 
						//	$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity); 
						}
					}
				}
				if(sizeof(@$stock[$Item->id]) > 0){ 
					foreach(@$stock[$Item->id] as $stockRate){
						@$sumValue+=@$stockRate/100;
					}
				}
			}else if(@$Item->item_companies[0]->serial_number_enable==1){
				$ItemSerialNumbers=$this->ItemLedgers->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$Item->id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In'])->toArray();
				foreach($ItemSerialNumbers as $ItemSerialNumber){		
					if(@$ItemSerialNumber->grn_id > 0){ 
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]);
						if($outExist == 0){
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->grn_id,'source_model'=>"Grns",'source_row_id'=>$ItemSerialNumber->grn_row_id])->first();
						//	pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					if(@$ItemSerialNumber->sale_return_id > 0){ 
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]);
						if($outExist == 0){
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->sale_return_id,'source_model'=>"Sale Return",'source_row_id'=>$ItemSerialNumber->sales_return_row_id])->first();
						//	pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					if(@$ItemSerialNumber->itv_id > 0){
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->itv_id,'source_model'=>"Inventory Transfer Voucher",'source_row_id'=>$ItemSerialNumber->itv_row_id])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					if(@$ItemSerialNumber->iv_row_id > 0){
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_model'=>"Inventory Vouchers",'iv_row_id'=>$ItemSerialNumber->iv_row_id])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					
					if(@$ItemSerialNumber->is_opening_balance == "Yes"){
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_model'=>"Items",'company_id'=>$st_company_id])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
				}
			
		}
		}
		return $sumValue;
	}
	
	
	public function stockValuationWithDate($date=null){ 
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		//$date=date('Y-m-d');
		$date=$this->request->query('date');
		$date=date("Y-m-d",strtotime($date));
	//	pr($date); exit;
		$Items =$this->ItemLedgers->Items->find()->contain(['ItemCompanies'=>function($p) use($st_company_id){
		return $p->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
		}]);
		
		$stock=[];  $sumValue=0; $itemSerialRate=[]; $itemSerialQuantity=[];
		foreach($Items as $Item){
			if(@$Item->item_companies[0]->serial_number_enable==0){  
				$StockLedgers=$this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$Item->id,'ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.processed_on <='=>$date])->order(['ItemLedgers.processed_on'=>'ASC']);
				foreach($StockLedgers as $StockLedger){ 
					if($StockLedger->in_out=='In'){ 
						if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
							for($inc=0.01;$inc<$StockLedger->quantity;$inc+=0.01){
								$stock[$Item->id][]=$StockLedger->rate;
							}
						}
					}
				}
				foreach($StockLedgers as $StockLedger){
					if($StockLedger->in_out=='Out'){
						if(sizeof(@$stock[$Item->id])>0){
							$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity*100); 
							//$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity); 
						}
					}
				}
				if(sizeof(@$stock[$Item->id]) > 0){ 
					foreach(@$stock[$Item->id] as $stockRate){
						@$sumValue+=@$stockRate/100;
					}
				}
				
			}else if(@$Item->item_companies[0]->serial_number_enable==1){ 
				$ItemSerialNumbers=$this->ItemLedgers->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$Item->id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In'])->toArray();
				foreach($ItemSerialNumbers as $ItemSerialNumber){		
					if(@$ItemSerialNumber->grn_id > 0){ 
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]);
						if($outExist == 0){
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->grn_id,'source_model'=>"Grns",'source_row_id'=>$ItemSerialNumber->grn_row_id,'ItemLedgers.processed_on <='=>$date])->first();
						//	pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					if(@$ItemSerialNumber->sale_return_id > 0){ 
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]);
						if($outExist == 0){
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->sale_return_id,'source_model'=>"Sale Return",'source_row_id'=>$ItemSerialNumber->sales_return_row_id,'ItemLedgers.processed_on <='=>$date])->first();
						//	pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					if(@$ItemSerialNumber->itv_id > 0){
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->itv_id,'source_model'=>"Inventory Transfer Voucher",'source_row_id'=>$ItemSerialNumber->itv_row_id,'ItemLedgers.processed_on <='=>$date])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
					if(@$ItemSerialNumber->iv_row_id > 0){
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_model'=>"Inventory Vouchers",'iv_row_id'=>$ItemSerialNumber->iv_row_id,'ItemLedgers.processed_on <='=>$date])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					} 
					if(@$ItemSerialNumber->is_opening_balance == "Yes"){
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_model'=>"Items",'company_id'=>$st_company_id,'ItemLedgers.processed_on <='=>$date])->first();
							//pr($ItemLedgerData); 
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$sumValue+=@$ItemLedgerData['rate'];
							}
						}
					}
				}
			
		}
		}
		
		
		//pr($sumValue); exit;
	}
	
	public function stockSummery($stockstatus=null){ 
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout');
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$stockstatus=$this->request->query('stock');
		$item_name=$this->request->query('item_name');
		$item_category=$this->request->query('item_category');
		$item_group=$this->request->query('item_group_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$stock_status=$this->request->query('stock');
		$status=$this->request->query('status');
		$item_sub_group=$this->request->query('item_sub_group_id');
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->ItemLedgers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$date = $financial_year->date_from;
		$due_date= $financial_year->date_from;
		$where1=[];
		$where2=[];
		$where=[];
		/* if($from_date){
			$from_date=date("Y-m-d",strtotime($from_date));
            $where1['processed_on >=']=$from_date;  
		};
		 */
		if($to_date){
			$to_date=date("Y-m-d",strtotime($to_date));
            $where1['processed_on <=']=$to_date;
            $where2['transaction_date <=']=$to_date;
		}; //pr($where1); exit;
		
		if(!empty($item_name)){ 
			$where['Items.id']=$item_name;
			
		}
		if(!empty($item_category)){
			$where['Items.item_category_id']=$item_category;
		}
		if(!empty($item_group)){
			$where['Items.item_group_id ']=$item_group;
		}
		if(!empty($item_sub_group)){
			$where['Items.item_sub_group_id ']=$item_sub_group;
		}
		//pr($where);exit;
		$this->set(compact('item_category','item_group','item_sub_group','stock','item_name'));

		
		//pr($results); exit;
		
		$item_stocks =[];$items_names =[];
		$query = $this->ItemLedgers->find()->where(['ItemLedgers.processed_on <=' =>date("Y-m-d",strtotime($to_date))]);
				
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['in_out' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['in_out' => 'Out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);

			
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->contain(['Items'=>function ($q) use($where) { 
				return $q->where($where)->contain(['Units']);
				}])
		->where(['company_id'=>$st_company_id])
		->group('item_id')
		->autoFields(true)
		->where($where)
		
		->order(['Items.name'=>'ASC']);
		
		$results =$query->toArray();
		//pr($results); exit;
		if($stock_status == "Negative"){ //exit;
			foreach($results as $result){
				if($result->total_in - $result->total_out < 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				}
			}
		}elseif($stock_status == "Zero"){
			foreach($results as $result){
				if($result->total_in - $result->total_out == 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				}
			}
		}elseif($stock_status == "Positive"){ 
			foreach($results as $result){
				if($result->total_in - $result->total_out > 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
					//pr($result->total_in - $result->total_out); 
					
				}
				 
			}
		}elseif($stockstatus == "Positive"){ 
			foreach($results as $result){
				if($result->total_in - $result->total_out > 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
					//pr($result);
				}
			}
		}else{
			foreach($results as $result){
				
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				
				}
			}
		
	//	pr($item_stocks);
		///exit;
		//
		$ItemLedgers = $this->ItemLedgers->find()->contain(['Items'=>function ($q) use($where){
			return $q->where($where);
		}])->where(['ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.rate >'=>0]);
		
		$item_rate=[];
		$in_qty=[];
		foreach($ItemLedgers as $ItemLedger){
				if($ItemLedger->in_out == 'In'){
					@$item_rate[$ItemLedger->item_id] += ($ItemLedger->quantity*$ItemLedger->rate);
					@$in_qty[$ItemLedger->item_id] += $ItemLedger->quantity;
				}
		}

		 $ItemDatas=[];
		 $ItemUnits=[]; 

		 $Items =$this->ItemLedgers->Items->find()->contain(['Units','ItemCompanies'=>function($p) use($st_company_id){
						return $p->where(['ItemCompanies.company_id' => $st_company_id]);
		}])->where($where)->order(['Items.name'=>'ASC']);
		
		if($stock_status == "All"){
			$itemSerialNumberStatus=[];
			foreach($Items as $Item){ 
				if(!empty($Item->item_companies)){
					$ItemLedgersexists = $this->ItemLedgers->exists(['item_id' => $Item->id,'company_id'=>$st_company_id]);
					$itemSerialNumberStatus[$Item->id]=@$Item->item_companies[0]->serial_number_enable;
					if(empty($ItemLedgersexists)){
						$item_stocks[$Item->id] = 0;
						$items_names[$Item->id] = $Item->name;
						$items_unit_names[$Item->id] =$Item->unit->name;
						
					}
				}
			}
			asort($items_names); 
		}
		//pr($items_names);exit;
	
		//Stock valuation Start// disable   fhgdf
		$stockNew=[];
		$stock=[];  $sumValue=[]; $itemSerialRate=[]; $itemSerialQuantity=[];
		foreach($Items as $Item){ 
		
			if(@$Item->item_companies[0]->serial_number_enable==0){
				$StockLedgers=$this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$Item->id,'ItemLedgers.company_id'=>$st_company_id])->order(['ItemLedgers.processed_on'=>'ASC'])->where($where1);
				
				foreach($StockLedgers as $StockLedger){ 
					if($StockLedger->in_out=='In'){
						/* if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
							
							for($inc=0;$inc<=$StockLedger->quantity;$inc+=0.01){
								$stock[$Item->id][]=$StockLedger->rate;
							}
							
						} */
						$stockNew[$Item->id][]=['qty'=>$StockLedger->quantity, 'rate'=>$StockLedger->rate];
					}
				}
				
					
					
					foreach($StockLedgers as $StockLedger){
						$processed_on=date('Y-m-d',strtotime($StockLedger->processed_on));
						if($StockLedger->in_out=='Out' and $processed_on<=$to_date){
							/* if(sizeof(@$stock[$Item->id])>0){
								$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity*100); 
							} */
							
							if(sizeof(@$stockNew[$Item->id])==0){
							break;
							}
						
							$outQty=$StockLedger->quantity;
							a:
							if(sizeof(@$stockNew[$Item->id])==0){
								break;
							}
							$R=@$stockNew[$Item->id][0]['qty']-$outQty;
							if($R>0){
								$stockNew[$Item->id][0]['qty']=$R;
							}
							else if($R<0){
								unset($stockNew[$Item->id][0]);
								@$stockNew[$Item->id]=array_values(@$stockNew[$Item->id]);
								$outQty=abs($R);
								goto a;
							}
							else{
								unset($stockNew[$Item->id][0]);
								$stockNew[$Item->id]=array_values($stockNew[$Item->id]);
							}
						}
					}
					if(sizeof(@$stockNew[$Item->id]) > 0){ 
					foreach(@$stockNew[$Item->id] as $stockRate){
						//pr($stockRate['qty']);
						//pr($stockRate['rate']); 
						//@$sumValue[$Item->id]+=$stockRate['qty']*$stockRate['rate'];
						//@$sumValue+=@$stockRate;
					}
				}
				//exit;
				/* if(sizeof(@$stock[$Item->id]) > 0){ 
					foreach(@$stock[$Item->id] as $stockRate){
						@$sumValue[$Item->id]+=@$stockRate/100;
					}
				} */
		}else if(@$Item->item_companies[0]->serial_number_enable==1){
				//$ItemSerialNumbers=$this->ItemLedgers->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$Item->id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In','SerialNumbers.transaction_date <='=>$to_date])->toArray();
			
			if(strtotime($to_date)==strtotime('2017-04-01')){
					$ItemSerialNumbers=$this->ItemLedgers->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$Item->id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In','transaction_date <= '=>$to_date])->toArray();
				}else{
					$ItemSerialNumbers=$this->ItemLedgers->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$Item->id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In','transaction_date <= '=>$to_date])->toArray();
				}
			
			//	pr($ItemSerialNumbers); exit;	
			foreach($ItemSerialNumbers as $ItemSerialNumber){	// pr($ItemSerialNumber); 	
				if(@$ItemSerialNumber->grn_id > 0){ 
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <='=>$to_date]);
					if($outExist == 0){
						$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->grn_id,'source_model'=>"Grns",'source_row_id'=>$ItemSerialNumber->grn_row_id])->where($where1)->first();
					//	pr($ItemLedgerData); 
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						$stockNew[$Item->id][]=['qty'=>1, 'rate'=>@$ItemLedgerData['rate']];
						}
					}
				}
				if(@$ItemSerialNumber->sales_return_id > 0){ 
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <='=>$to_date]);
					if($outExist == 0){
						$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->sales_return_id,'source_model'=>"Sale Return",'source_row_id'=>$ItemSerialNumber->sales_return_row_id])->where($where1)->first();
					/* if($Item->id==1148){
						pr($ItemLedgerData); exit;
					} */
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						$stockNew[$Item->id][]=['qty'=>1, 'rate'=>@$ItemLedgerData['rate']];
						}
					}
				}
				if(@$ItemSerialNumber->itv_id > 0){
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <='=>$to_date]); 
					if($outExist == 0){  
						$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->itv_id,'source_model'=>"Inventory Transfer Voucher",'source_row_id'=>$ItemSerialNumber->itv_row_id])->where($where1)->first();
						//pr($ItemLedgerData); 
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						$stockNew[$Item->id][]=['qty'=>1, 'rate'=>@$ItemLedgerData['rate']];
						}
					}
				}
				if(@$ItemSerialNumber->iv_row_id > 0){ //
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'transaction_date <='=>$to_date]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_model'=>"Inventory Vouchers",'iv_row_id'=>$ItemSerialNumber->iv_row_id])->where($where1)->first();
							
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
							$stockNew[$Item->id][]=['qty'=>1, 'rate'=>@$ItemLedgerData['rate']];
							//@$sumValue+=@$ItemLedgerData->rate;
							}
						}
					
					}
				if(@$ItemSerialNumber->is_opening_balance == "Yes"){ 	
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id,'SerialNumbers.transaction_date <='=>$to_date]); 
					if($outExist == 0){  
						$ItemLedgerData =$this->ItemLedgers->find()->where(['ItemLedgers.source_model'=>"Items",'ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.item_id' => $ItemSerialNumber->item_id])->where($where1)->first();
						//pr($ItemLedgerData); 
						if($ItemLedgerData){ //pr($ItemSerialNumber); 
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						$stockNew[$Item->id][]=['qty'=>1, 'rate'=>@$ItemLedgerData['rate']];
						}
					}
				}
				
			}
			
		}
	}
	//pr(@$stockNew);  exit;
	$closingValue=0; $itmQty=[]; $itemRate=[];
		foreach($stockNew as $key=>$qw){ 
			foreach($qw as $rt){
				@$itmQty[@$key]+=@$rt['qty'];
				@$itemRate[@$key]+=@$rt['qty']*@$rt['rate'];
				$closingValue+=@$rt['qty']*@$rt['rate'];
				
			}
		}
		
		$total_amt=0;	$unit_rrate=0;
		foreach($Items as $Item){ //pr($Item->id);
			if(@$itmQty[@$Item->id] > 0){ //pr($Item->id);
				//echo @$Item->id.'<br/>';
				$unit_rrate=@$itemRate[@$Item->id]/@$itmQty[@$Item->id];
				$total_amt+=@$unit_rrate*@$itmQty[@$Item->id];
			}
		}
	
		
		
//pr($closingValue); exit;
	$unitRate=[]; $totalRate=[];
		foreach ($item_stocks as $key=> $item_stock1){
			$r=@$itemSerialRate[$key];
			$q=@$itemSerialQuantity[$key];
			if($q > 0){
			$UR=$r/$q;
			$unitRate[$key]=$UR;
			$totalRate[$key]=$UR*$q;
			}
		}
		$total=0;
		foreach($sumValue as $rr){
			$total+=$rr;
		}
		
		

		$ItemCategories = $this->ItemLedgers->Items->ItemCategories->find('list')->order(['ItemCategories.name' => 'ASC']);
		if(!empty($item_category)){
			$ItemGroups = $this->ItemLedgers->Items->ItemGroups->find('list')->order(['ItemGroups.name' => 'ASC'])->where(['item_category_id'=>$item_category]);
		}else{
			$ItemGroups = $this->ItemLedgers->Items->ItemGroups->find('list')->order(['ItemGroups.name' => 'ASC']);
		}	
		if(!empty($item_group)){
			$ItemSubGroups = $this->ItemLedgers->Items->ItemSubGroups->find('list')->order(['ItemSubGroups.name' => 'ASC'])->where(['item_group_id'=>$item_group]);	
		}else{
			$ItemSubGroups = $this->ItemLedgers->Items->ItemSubGroups->find('list')->order(['ItemSubGroups.name' => 'ASC']);
		}
		
		$Items = $this->ItemLedgers->Items->find('list')->order(['Items.name' => 'ASC']);
        $this->set(compact('itemLedgers', 'item_name','item_stocks','items_names','ItemCategories','ItemGroups','ItemSubGroups','item_rate','in_qty','Items','from_date','to_date','ItemDatas','items_unit_names','ItemUnits','url','stockstatus', 'stock_status', 'sumValue','itemSerialNumberStatus','unitRate','totalRate','stockNew','itemRate','itmQty'));
		$this->set('_serialize', ['itemLedgers']); 
    }
	
	public function excelStock(){
		
		$this->viewBuilder()->layout('');
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$stockstatus=$this->request->query('stockstatus');
		$item_name=$this->request->query('item_name');
		$item_category=$this->request->query('item_category');
		$item_group=$this->request->query('item_group_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$stock_status=$this->request->query('stock');
		$status=$this->request->query('status');
		$item_sub_group=$this->request->query('item_sub_group_id');
		$st_year_id = $session->read('st_year_id');
		$financial_year = $this->ItemLedgers->FinancialYears->find()->where(['id'=>$st_year_id])->first();
		$date = $financial_year->date_from;
		$due_date= $financial_year->date_from;
		$where1=[];
		$where2=[];
		$where=[];
		/* if($from_date){
			$from_date=date("Y-m-d",strtotime($from_date));
            $where1['processed_on >=']=$from_date;  
		};
		 */
		if($to_date){
			$to_date=date("Y-m-d",strtotime($to_date));
            $where1['processed_on <=']=$to_date;
            $where2['transaction_date <=']=$to_date;
		}; //pr($where1); exit;
		
		if(!empty($item_name)){ 
			$where['Items.id']=$item_name;
			
		}
		if(!empty($item_category)){
			$where['Items.item_category_id']=$item_category;
		}
		if(!empty($item_group)){
			$where['Items.item_group_id ']=$item_group;
		}
		if(!empty($item_sub_group)){
			$where['Items.item_sub_group_id ']=$item_sub_group;
		}
		
		$this->set(compact('item_category','item_group','item_sub_group','stock','item_name'));

		
		//pr($results); exit;
		
		$item_stocks =[];$items_names =[];
		$query = $this->ItemLedgers->find()->where(['ItemLedgers.processed_on <=' =>date("Y-m-d",strtotime($to_date))]);
				
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['in_out' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['in_out' => 'Out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);

			
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->contain(['Items'=>function ($q) use($where) { 
				return $q->where($where)->contain(['Units']);
				}])
		->where(['company_id'=>$st_company_id])
		->group('item_id')
		->autoFields(true)
		->where($where)
		
		->order(['Items.name'=>'ASC']);
		
		$results =$query->toArray();
		//pr($results); exit;
		if($stock_status == "Negative"){ //exit;
			foreach($results as $result){
				if($result->total_in - $result->total_out < 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				}
			}
		}elseif($stock_status == "Zero"){
			foreach($results as $result){
				if($result->total_in - $result->total_out == 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				}
			}
		}elseif($stock_status == "Positive"){ 
			foreach($results as $result){
				if($result->total_in - $result->total_out > 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
					
				}
				 
			}
		}elseif($stockstatus == "Positive"){ 
			foreach($results as $result){
				if($result->total_in - $result->total_out > 0){
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
					
				}
			}
		}else{
			foreach($results as $result){
				
					$item_stocks[$result->item_id] = $result->total_in - $result->total_out;
					$items_names[$result->item_id] = $result->item->name;
					$items_unit_names[$result->item_id] = $result->item->unit->name;
				
				}
			}
		
		//pr($item_stocks); exit;
		
		$ItemLedgers = $this->ItemLedgers->find()->contain(['Items'=>function ($q) use($where){
			return $q->where($where);
		}])->where(['ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.rate >'=>0]);
		
		$item_rate=[];
		$in_qty=[];
		foreach($ItemLedgers as $ItemLedger){
				if($ItemLedger->in_out == 'In'){
					@$item_rate[$ItemLedger->item_id] += ($ItemLedger->quantity*$ItemLedger->rate);
					@$in_qty[$ItemLedger->item_id] += $ItemLedger->quantity;
				}
		}

		 $ItemDatas=[];
		 $ItemUnits=[]; 

		 $Items =$this->ItemLedgers->Items->find()->contain(['Units','ItemCompanies'=>function($p) use($st_company_id){
						return $p->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
		}])->where($where);
		
		$itemSerialNumberStatus=[];
		foreach($Items as $Item){ 
			$ItemLedgersexists = $this->ItemLedgers->exists(['item_id' => $Item->id,'company_id'=>$st_company_id]);
			$itemSerialNumberStatus[$Item->id]=@$Item->item_companies[0]->serial_number_enable;
			
			if(empty($ItemLedgersexists)){
				$ItemDatas[$Item->id]=$Item->name;
				$ItemUnits[$Item->id]=$Item->unit->name;
			}
		}

		//pr($Items->toArray());
	
		//Stock valuation Start// disable   fhgdf
		$stock=[];  $sumValue=[]; $itemSerialRate=[]; $itemSerialQuantity=[];
		foreach($Items as $Item){ //pr(@$Item->item_companies[0]->serial_number_enable);
			if(@$Item->item_companies[0]->serial_number_enable==0){ 
				$StockLedgers=$this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$Item->id,'ItemLedgers.company_id'=>$st_company_id])->order(['ItemLedgers.processed_on'=>'ASC'])->where($where1);
				foreach($StockLedgers as $StockLedger){ 
					if($StockLedger->in_out=='In'){
						if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
							for($inc=0.01;$inc<$StockLedger->quantity;$inc+=0.01){
								$stock[$Item->id][]=$StockLedger->rate;
							}
						}
					}
				}
				foreach($StockLedgers as $StockLedger){
					if($StockLedger->in_out=='Out'){
						if(sizeof(@$stock[$Item->id])>0){
							$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity*100); 
						}
					}
				}
				if(sizeof(@$stock[$Item->id]) > 0){ 
					foreach(@$stock[$Item->id] as $stockRate){
						@$sumValue[$Item->id]+=@$stockRate/100;
					}
				}
		}else if(@$Item->item_companies[0]->serial_number_enable==1){
				$ItemSerialNumbers=$this->ItemLedgers->SerialNumbers->find()->where(['SerialNumbers.item_id'=>$Item->id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In'])->toArray();
			foreach($ItemSerialNumbers as $ItemSerialNumber){	// pr($ItemSerialNumber); 	
				if(@$ItemSerialNumber->grn_id > 0){ 
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]);
					if($outExist == 0){
						$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->grn_id,'source_model'=>"Grns",'source_row_id'=>$ItemSerialNumber->grn_row_id])->where($where1)->first();
					//	pr($ItemLedgerData); 
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						}
					}
				}
				if(@$ItemSerialNumber->sale_return_id > 0){ 
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]);
					if($outExist == 0){
						$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->sale_return_id,'source_model'=>"Sale Return",'source_row_id'=>$ItemSerialNumber->sales_return_row_id])->where($where1)->first();
					//	pr($ItemLedgerData); 
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						}
					}
				}
				if(@$ItemSerialNumber->itv_id > 0){
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
					if($outExist == 0){  
						$ItemLedgerData =$this->ItemLedgers->find()->where(['source_id'=>$ItemSerialNumber->itv_id,'source_model'=>"Inventory Transfer Voucher",'source_row_id'=>$ItemSerialNumber->itv_row_id])->where($where1)->first();
						//pr($ItemLedgerData); 
						if($ItemLedgerData){
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						}
					}
				}
				if(@$ItemSerialNumber->iv_row_id > 0){ //pr($ItemSerialNumber); 
					$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
						if($outExist == 0){  
							$ItemLedgerData =$this->ItemLedgers->find()->where(['source_model'=>"Inventory Vouchers",'iv_row_id'=>$ItemSerialNumber->iv_row_id])->where($where1)->first();
							
							if($ItemLedgerData){
							@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
							@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
							//@$sumValue+=@$ItemLedgerData->rate;
							}
						}
					
					}
				if(@$ItemSerialNumber->is_opening_balance == "Yes"){
				$outExist = $this->ItemLedgers->Items->SerialNumbers->exists(['SerialNumbers.parent_id' => $ItemSerialNumber->id]); 
					if($outExist == 0){  
						$ItemLedgerData =$this->ItemLedgers->find()->where(['ItemLedgers.source_model'=>"Items",'ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.item_id' => $ItemSerialNumber->item_id])->where($where1)->first();
						//pr($ItemLedgerData); 
						if($ItemLedgerData){ //pr($ItemSerialNumber); 
						@$itemSerialQuantity[@$ItemSerialNumber->item_id]=$itemSerialQuantity[@$ItemSerialNumber->item_id]+1;
						@$itemSerialRate[@$ItemSerialNumber->item_id]+=@$ItemLedgerData['rate'];
						}
					}
				}
				
			}
			
		}
	}
	$to_date=date("d-m-Y",strtotime($to_date));
	
	
	$unitRate=[]; $totalRate=[];
		foreach ($item_stocks as $key=> $item_stock1){
			$r=@$itemSerialRate[$key];
			$q=@$itemSerialQuantity[$key];
			if($q > 0){
			$UR=$r/$q;
			$unitRate[$key]=$UR;
			$totalRate[$key]=$UR*$q;
			}
		}
		$this->set(compact('itemLedgers', 'item_name','item_stocks','items_names','ItemCategories','ItemGroups','ItemSubGroups','item_rate','in_qty','Items','from_date','to_date','ItemDatas','items_unit_names','ItemUnits','url','stockstatus', 'stock_status', 'sumValue','itemSerialNumberStatus','unitRate','totalRate'));
		$this->set('_serialize', ['itemLedgers']); 
    
	}
	public function redirectStock(){
		//exit;
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$Itemdatas = $this->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$st_company_id,'in_out'=>'In','rate >'=>0]);
		
		foreach($Itemdatas as $Itemdata){
				$Itemledger_rate=0;
				$Itemledger_qty=0;
				$Itemledgers = $this->ItemLedgers->find()->where(['item_id'=>$Itemdata['item_id'],'in_out'=>'In','processed_on <='=>$Itemdata['processed_on'],'rate >'=>0]);
				//pr($Itemledgers->toArray()); 
				if($Itemledgers){ 
					$j=0; $qty_total=0; $total_amount=0;
						foreach($Itemledgers as $Itemledger){
							$Itemledger_qty = $Itemledger_qty+$Itemledger['quantity'];
							$Itemledger_rate = $Itemledger_rate+($Itemledger['rate']*$Itemledger['quantity']);
						}
						$per_unit_cost=$Itemledger_rate/$Itemledger_qty;
				}
				else{
					$per_unit_cost=0;
				}
				
				$query2 = $this->ItemLedgers->query();
						$query2->update()
							->set(['rate' => $per_unit_cost,'in_out' => 'In'])
							->where(['id' => $Itemdata['id']])
							->execute();
			}
			 return $this->redirect(['action'=>'stockReport?status=completed']);
			
	}
	
	public function materialIndent($id=null,$status=null){
		$this->viewBuilder()->layout('index_layout'); 
		$status = $this->request->query('status');
		$id = $this->request->query('id');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$qty_ids = explode(',',$id);
		$qty_ids = array_filter($qty_ids);
		$salesOrders=[];
		foreach($qty_ids as $id){
			if($status == 'salesorder'){
				$salesOrders[]=$this->ItemLedgers->SalesOrders->find()->where(['SalesOrders.id'=>$id])->first();
			}
			else if($status == 'jobcard'){
				$salesOrders[]=$this->ItemLedgers->JobCards->find()->where(['JobCards.id'=>$id])->first();
			}
			else if($status == 'purchaseorder'){
				$salesOrders[]=$this->ItemLedgers->PurchaseOrders->find()->where(['PurchaseOrders.id'=>$id])->first();
			}
			else if($status == 'quotation'){
				$salesOrders[]=$this->ItemLedgers->Quotations->find()->where(['Quotations.id'=>$id])->first();
			}
			else if($status == 'mi'){
				$salesOrders[]=$this->ItemLedgers->MaterialIndents->find()->where(['MaterialIndents.id'=>$id])->first();
			}
		}
		
		$this->set(compact('salesOrders','status','st_company_id'));
		
	}
	
	
	 public function materialindentreport($stockstatus=null){ 
		 
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout'); 
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$item_name=$this->request->query('item_name');
		$item_category=$this->request->query('item_category');
		$item_group=$this->request->query('item_group_id');
		$item_sub_group=$this->request->query('item_sub_group_id');
		$company_name[]=$this->request->query('company_name');
		$stock=$this->request->query('stock');
		$stockstatus=$this->request->query('stockstatus');
		//pr($company_name); exit;
		$where=[];
		$whereItem=[];
		
		
		$this->set(compact('item_category','item_group','item_sub_group','item_name','company_name','stock'));
		if(!empty($item_name)){ 
			$where['Item_id']=$item_name;
			$whereItem['id']=$item_name;
		}
		
		if(!empty($item_category)){
			$where['Items.item_category_id']=$item_category;
			$whereItem['Items.item_category_id']=$item_category;
		}
		if(!empty($item_group)){
			$where['Items.item_group_id ']=$item_group;
			$whereItem['Items.item_group_id ']=$item_group;
		}
		if(!empty($item_sub_group)){
			$where['Items.item_sub_group_id ']=$item_sub_group;
			$whereItem['Items.item_sub_group_id ']=$item_sub_group;
		}

		$mit=$this->ItemLedgers->newEntity();
		
		if ($this->request->is(['post'])) {
			$check=$this->request->data['check']; 
			$suggestindent=$this->request->data['suggestindent']; 
			$to_send=[];
			foreach($check as $item_id){
				$to_send[$item_id]=$suggestindent[$item_id];
			}
			$to=json_encode($to_send); 
			$this->redirect(['controller'=>'MaterialIndents','action' => 'add/'.$to.'']);
		}
		$where1=[];$where2=[];$where3=[];
		$where4=[];$where5=[];$where6=[];
		$where7=[];
		$dr=sizeof(@$company_name[0]); 
		$selected_company=[];
		if($dr > 1){
		 
		$company_names=array_filter($company_name[0]);
			//pr($company_names); exit;
			foreach(@$company_names as $names){ 
					$where1['SalesOrders.company_id IN'][]=$names;
					$where2['JobCards.company_id IN'][]=$names;
					$where3['PurchaseOrders.company_id IN'][]=$names;
					$where4['MaterialIndents.company_id IN'][]=$names;
					$where5['Quotations.company_id IN'][]=$names;
					$where6['ItemLedgers.company_id IN'][]=$names;
					$where7['ItemCompanies.company_id IN'][]=$names;
					$selected_company[]=$names;
			}
		}else if(@$company_name[0][0]==$st_company_id){ $names=$st_company_id;
				$where1['SalesOrders.company_id IN'][]=$names;
				$where2['JobCards.company_id IN'][]=$names;
				$where3['PurchaseOrders.company_id IN'][]=$names;
				$where4['MaterialIndents.company_id IN'][]=$names;
				$where5['Quotations.company_id IN'][]=$names;
				$where6['ItemLedgers.company_id IN'][]=$names;
				$where7['ItemCompanies.company_id IN'][]=$names;
				$selected_company[]=$names;
		}else if($company_name[0]==$st_company_id){ $names=$st_company_id;
				$where1['SalesOrders.company_id IN'][]=$names;
				$where2['JobCards.company_id IN'][]=$names;
				$where3['PurchaseOrders.company_id IN'][]=$names;
				$where4['MaterialIndents.company_id IN'][]=$names;
				$where5['Quotations.company_id IN'][]=$names;
				$where6['ItemLedgers.company_id IN'][]=$names;
				$where7['ItemCompanies.company_id IN'][]=$names;
				$selected_company[]=$names;
		
		}else{ 
				$names=array_filter($company_name[0]); 
				$where1['SalesOrders.company_id IN'][]=$names[0];
				$where2['JobCards.company_id IN'][]=$names[0];
				$where3['PurchaseOrders.company_id IN'][]=$names[0];
				$where4['MaterialIndents.company_id IN'][]=$names[0];
				$where5['Quotations.company_id IN'][]=$names[0];
				$where6['ItemLedgers.company_id IN'][]=$names[0];
				$where7['ItemCompanies.company_id IN'][]=$names[0];
				$selected_company[]=$names[0];
		}
	//	pr($selected_company); exit;
	$JobCards = $this->ItemLedgers->JobCards->find()->contain(['JobCardRows'])->where($where2)->where(['JobCards.status'=>'Pending'])->toArray();
	
	$job_card_qty=[];
	$job_id=[];
	
	if(!empty($JobCards)){
	foreach($JobCards as $JobCard){
		
		foreach($JobCard->job_card_rows as $job_card_row){
			$sales_order_row_id=$job_card_row->sales_order_row_id;
			$SalesOrderRows = $this->ItemLedgers->SalesOrders->SalesOrderRows->get($job_card_row->sales_order_row_id);
			$Invoices = $this->ItemLedgers->SalesOrders->Invoices->find()->contain(['InvoiceRows' => function($q) use($sales_order_row_id) {
				return $q->select(['invoice_id','sales_order_row_id','item_id','total_qty' => $q->func()->sum('InvoiceRows.quantity')])->group('InvoiceRows.sales_order_row_id')->where(['InvoiceRows.sales_order_row_id'=>$sales_order_row_id]);
					},'Ivs'])->toArray();
			
			
			foreach($Invoices as $Invoice){
				foreach($Invoice->invoice_rows as $invoice_row){
					$Invoices_quantity[$invoice_row->sales_order_row_id]=$invoice_row->total_qty;
				}
			}
			
			if(@$Invoices_quantity[@$invoice_row->sales_order_row_id]){
				$invoice_qty=@$SalesOrderRows->quantity-@$Invoices_quantity[@$sales_order_row_id];
			}else{
				$invoice_qty=@$SalesOrderRows->quantity;
			}
			
			$SalesOrderQty=$SalesOrderRows->quantity;
			$job_card_Qt=$job_card_row->quantity;
			$jciq=(@$invoice_qty*@$job_card_Qt)/@$SalesOrderQty;
			
			if(@$jciq > 0){
				@$job_card_qty[@$job_card_row->item_id]+=$jciq;
				@$job_id[$job_card_row->item_id].=@$job_card_row->job_card_id.',';
			}
		}
		}
	}  
	
	$SalesOrders = $this->ItemLedgers->SalesOrders->find()->contain(['SalesOrderRows'=>['InvoiceRows' => function($q) {
				return $q->select(['invoice_id','sales_order_row_id','item_id','total_qty' => $q->func()->sum('InvoiceRows.quantity')])->group('InvoiceRows.sales_order_row_id');
	}]])->where($where1)->where(['SalesOrders.sales_order_status !='=>"Close"]);
	
	$sales_order_qty=[];
	 $invoice_qty=[];
	$sales_id=[];
	
		foreach($SalesOrders as $SalesOrder){ $sales_qty=[]; $inc_qty=[]; 
			foreach($SalesOrder->sales_order_rows as $sales_order_row){ 
				foreach($sales_order_row->invoice_rows as $invoice_row){ //pr($invoice_row); exit;
					@$invoice_qty[$invoice_row['item_id']]+=$invoice_row['total_qty'];
					@$inc_qty[$invoice_row['item_id']]+=$invoice_row['total_qty'];
				}
				@$sales_order_qty[$sales_order_row['item_id']]+=$sales_order_row['quantity'];
				@$sales_qty[$sales_order_row['item_id']]+=$sales_order_row['quantity'];
			}
			
			
			foreach(@$sales_qty as $key=>$sales_order_qt){
					if(@$sales_order_qt > @$inc_qty[$key] ){ 
						@$sales_id[$key].=@$SalesOrder->id.',';
					}
			}
		}
	//pr($sales_order_qty); exit;
	$PurchaseOrders = $this->ItemLedgers->PurchaseOrders->find()->contain(['PurchaseOrderRows'=>['GrnRows' => function($q) {
				return $q->select(['grn_id','purchase_order_row_id','item_id','total_qty' => $q->func()->sum('GrnRows.quantity')])->group('GrnRows.purchase_order_row_id');
	}]])->where($where3);

	$purchase_order_qty=[];
	$grn_qty=[];
	$purchase_id=[];
		foreach($PurchaseOrders as $PurchaseOrder){ $sales_qty=[]; $g_qty=[];
			foreach($PurchaseOrder->purchase_order_rows as $purchase_order_row){
				foreach($purchase_order_row->grn_rows as $grn_row){
					@$grn_qty[$grn_row['item_id']]+=$grn_row['total_qty'];
					@$g_qty[$grn_row['item_id']]+=$grn_row['total_qty'];
				}
				@$purchase_order_qty[$purchase_order_row['item_id']]+=$purchase_order_row['quantity'];
				@$sales_qty[$purchase_order_row['item_id']]+=$purchase_order_row['quantity'];
				
			}
			
			foreach(@$sales_qty as $key=>$sales_order_qt){
					if(@$sales_order_qt > @$g_qty[$key] ){ 
						@$purchase_id[$key].=@$PurchaseOrder->id.',';
					}
				
			}
		}
	
	$Quotations = $this->ItemLedgers->Quotations->find()->contain(['QuotationRows'=>['SalesOrderRows' => function($q) {
				return $q->select(['sales_order_id','quotation_row_id','item_id','total_qty' => $q->func()->sum('SalesOrderRows.quantity')])->group('SalesOrderRows.quotation_row_id');
	}]])->where($where5)->where(['Quotations.status !='=>'Closed']);
	$qo_qty=[];
	$so_qty=[];
	$qotation_id=[];
		foreach($Quotations as $Quotation){ $sales_qty=[]; $s_qt=[];
			foreach($Quotation->quotation_rows as $quotation_row){
				foreach($quotation_row->sales_order_rows as $sales_order_row){
					@$so_qty[$sales_order_row['item_id']]+=$sales_order_row['total_qty'];
					@$s_qt[$sales_order_row['item_id']]+=$sales_order_row['total_qty'];
				}
				@$qo_qty[$quotation_row['item_id']]+=$quotation_row['quantity'];
				@$sales_qty[$quotation_row['item_id']]+=$quotation_row['quantity'];
			}
			
			foreach(@$sales_qty as $key=>$sales_order_qt){
					if(@$sales_order_qt > @$s_qt[$key] ){  
						@$qotation_id[$key].=@$Quotation->id.',';
					}
				
			}
		}
	$MaterialIndents = $this->ItemLedgers->MaterialIndents->find()->contain(['MaterialIndentRows','PurchaseOrders'=>['PurchaseOrderRows' => function($q) {
				return $q->select(['purchase_order_id','material_indent_row_id','item_id','total_qty' => $q->func()->sum('PurchaseOrderRows.quantity')])->group('PurchaseOrderRows.material_indent_row_id');
	}]])->where($where4);
		
	$mi_qty=[];
	$po_qty=[];
	$mi_id=[];
		foreach($MaterialIndents as $MaterialIndent){ $sales_qty=[];
			foreach($MaterialIndent->purchase_orders as $purchase_order){
				foreach($purchase_order->purchase_order_rows as $purchase_order_row){ 
					if($purchase_order_row->material_indent_row_id){
						@$po_qty[$purchase_order_row['item_id']]+=$purchase_order_row['total_qty'];
					}
				}
			}
			foreach(@$MaterialIndent->material_indent_rows as $material_indent_row){  
				@$mi_qty[$material_indent_row['item_id']]+=$material_indent_row['required_quantity'];
				@$sales_qty[$material_indent_row['item_id']]+=$material_indent_row['required_quantity'];
			}
			foreach(@$sales_qty as $key=>$sales_order_qt){
					if(@$sales_order_qt > @$po_qty[$key] ){ 
						@$mi_id[$key].=@$MaterialIndent->id.',';
					}
				
			}
		}
		if(!empty($company_name)){ 
		$ItemLedgers = $this->ItemLedgers->find();
				$totalInCase = $ItemLedgers->newExpr()
					->addCase(
						$ItemLedgers->newExpr()->add(['in_out' => 'In']),
						$ItemLedgers->newExpr()->add(['quantity']),
						'integer'
					);
				$totalOutCase = $ItemLedgers->newExpr()
					->addCase(
						$ItemLedgers->newExpr()->add(['in_out' => 'Out']),
						$ItemLedgers->newExpr()->add(['quantity']),
						'integer'
					);

				$ItemLedgers->select([
					'total_in' => $ItemLedgers->func()->sum($totalInCase),
					'total_out' => $ItemLedgers->func()->sum($totalOutCase),'id','item_id'
				])
				->group('item_id')
				->autoFields(true)
				->where($where)
				->where($where6)
				->contain(['Items' => function($q) use($where7,$where){
					return $q->where($where)->where(['Items.source'=>'Purchessed/Manufactured'])->orWhere(['Items.source'=>'Purchessed'])->contain(['ItemCompanies'=>function($p) use($where7){
						return $p->where($where7)->where(['ItemCompanies.freeze' => 0]);
					}]);
				}]);
		}else{
			$ItemLedgers = $this->ItemLedgers->find();
				$totalInCase = $ItemLedgers->newExpr()
					->addCase(
						$ItemLedgers->newExpr()->add(['in_out' => 'In']),
						$ItemLedgers->newExpr()->add(['quantity']),
						'integer'
					);
				$totalOutCase = $ItemLedgers->newExpr()
					->addCase(
						$ItemLedgers->newExpr()->add(['in_out' => 'Out']),
						$ItemLedgers->newExpr()->add(['quantity']),
						'integer'
					);

				$ItemLedgers->select([
					'total_in' => $ItemLedgers->func()->sum($totalInCase),
					'total_out' => $ItemLedgers->func()->sum($totalOutCase),'id','item_id'
				])
				->group('item_id')
				->autoFields(true)
				->where($where)
				->where(['company_id'=>$st_company_id])
				->contain(['Items' => function($q) use($where,$st_company_id){
					return $q->where($where)->where(['Items.source'=>'Purchessed/Manufactured'])->orWhere(['Items.source'=>'Purchessed'])->contain(['ItemCompanies'=>function($p) use($st_company_id){
						return $p->where(['company_id'=>$st_company_id,'ItemCompanies.freeze' => 0]);
					}]);
				}]);
		}	
		
		$ItemMiniStock=[];
		$Items =$this->ItemLedgers->Items->ItemCompanies->find()->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
			foreach($Items as $Item){ 
					$ItemMiniStock[$Item->item_id]=$Item->minimum_stock;
				
			}
		
		$material_report=[];
		$ledger_item=[];
		$Items_data =$this->ItemLedgers->Items->find();
		foreach($Items_data as $d){
			$material_report[$d->id]=array('item_name'=>$d->name,'item_id'=>$d->id,'Current_Stock'=>'0','minimum_stock'=>'0');
		}
		
		//asort($ItemLedgers);
		foreach ($ItemLedgers as $itemLedger){ 
			$ledger_item[]=$itemLedger->item->id;
			$item_name=$itemLedger->item->name;
			$item_id=$itemLedger->item->id;
			$Current_Stock=$itemLedger->total_in-$itemLedger->total_out;
			$material_report[$item_id]=array('item_name'=>$item_name,'item_id'=>$item_id,'Current_Stock'=>$Current_Stock,'minimum_stock'=>@$itemLedger->item->item_companies[0]->minimum_stock);
			
		}
		//pr($material_report); exit;
		asort($material_report);
		//pr($material_reports); exit;
		/*
		$remaing_items=$this->ItemLedgers->Items->ItemCompanies->find()->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0,'Items.id NOT IN'=>$ledger_item]);
		foreach ($remaing_items as $remaing_item){ 
		$item_name=$itemLedger->item->name;
		$item_id=$itemLedger->item->id;
		$Current_Stock=0;
		$material_report[$item_id]=array('item_name'=>$item_name,'item_id'=>$item_id,'Current_Stock'=>$Current_Stock,'minimum_stock'=>@$itemLedger->item->item_companies[0]->minimum_stock);
		}
		*/
		//pr($material_report); exit;
		$Items = $this->ItemLedgers->Items->find();
		$ItemDatas=[];
		$ItemUnits=[];
		foreach($Items as $Item){  
			$ItemLedgersexists = $this->ItemLedgers->exists(['item_id' => $Item->id,'company_id'=>$st_company_id]);
			if(empty($ItemLedgersexists)){
				$ItemDatas[$Item->id]=$Item->name;
			}
		}
		
		//pr($material_report);  pr($ItemDatas); exit;
		$ItemCategories = $this->ItemLedgers->Items->ItemCategories->find('list')->order(['ItemCategories.name' => 'ASC']);
		$ItemGroups = $this->ItemLedgers->Items->ItemGroups->find('list')->order(['ItemGroups.name' => 'ASC']);
		$ItemSubGroups = $this->ItemLedgers->Items->ItemSubGroups->find('list')->order(['ItemSubGroups.name' => 'ASC']);
		$Items = $this->ItemLedgers->Items->find('list')->order(['Items.name' => 'ASC']);
		$Companies = $this->ItemLedgers->Companies->find('list')->order(['Companies.name' => 'ASC']);
			
		$this->set(compact('material_report','mit','url','ItemCategories','ItemGroups','ItemSubGroups','Items','Companies','st_company_id','total_indent','stockstatus','jobCardQty','ItemDatas','stock','ItemMiniStock','invoice_qty','sales_order_qty','sales_id','purchase_order_qty','grn_qty','purchase_id','qotation_id','qo_qty','so_qty','mi_qty','po_qty','mi_id','job_id','job_card_qty','selected_company'));
			
	 }
	
	
	public function excelMetarialExport(){
		$this->viewBuilder()->layout(''); 
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		//$Items = $this->ItemLedgers->Items->find()->where(['source'=>'Purchessed/Manufactured'])->orWhere(['source'=>'Purchessed']); 
		/* $material_items_for_purchase=[];
		$material_items_for_purchase[]=array('item_name'=>'Kgn212','item_id'=>'144','quantity'=>'25','company_id'=>'25','employee_name'=>'Gopal','company_name'=>'STL','material_indent_id'=>'2');
		
		$to=json_encode($material_items_for_purchase);
		//pr($to); exit;
		$this->redirect(['controller'=>'PurchaseOrders','action' => 'add/'.$to.'']); */
		$mit=$this->ItemLedgers->newEntity();
		
		if ($this->request->is(['post'])) {
			$check=$this->request->data['check']; 
			$suggestindent=$this->request->data['suggestindent']; 
			$to_send=[];
			foreach($check as $item_id){
				$to_send[$item_id]=$suggestindent[$item_id];
			}

			$to=json_encode($to_send); 
			//rwjihf dfgdf?3qrrg
			//$this->redirect(['controller'=>'PurchaseOrders','action' => 'add/'.$to.'']);
			$this->redirect(['controller'=>'MaterialIndents','action' => 'add/'.$to.'']);
		}
		
		$salesOrders=$this->ItemLedgers->SalesOrders->find()
			->select(['total_rows'=>$this->ItemLedgers->SalesOrders->find()->func()->count('SalesOrderRows.id')])
			->leftJoinWith('SalesOrderRows', function ($q) {
				return $q->where(['SalesOrderRows.processed_quantity < SalesOrderRows.quantity']);
			})
			->where(['company_id'=>$st_company_id])
			->group(['SalesOrders.id'])
			->autoFields(true)
			->having(['total_rows >' => 0])
			->contain(['SalesOrderRows'])
			->toArray();
			//pr($salesOrders); exit; 
			
			$sales=[];
			foreach($salesOrders as $data){
				foreach($data->sales_order_rows as $row){ 
				//pr($row->quantity);
				$item_id=$row->item_id;
				$quantity=$row->quantity;
				$processed_quantity=$row->processed_quantity;
				$Sales_Order_stock=$quantity-$processed_quantity;
				$sales[$row->item_id]=@$sales[$row->item_id]+$Sales_Order_stock;
				}
				//$sales[$item_id]=@$sales[$item_id]+$Sales_Order_stock;
			}
			//pr($sales);exit;
		$JobCards=$this->ItemLedgers->JobCards->find()->where(['status'=>'Pending','company_id'=>$st_company_id])->contain(['JobCardRows']);
		
		$job_card_items=[];
		foreach($JobCards as $JobCard){
			foreach($JobCard->job_card_rows as $job_card_row){
				$job_card_items[$job_card_row->item_id]=@$job_card_items[$job_card_row->item_id]+$job_card_row->quantity;
			}
		}		
		//pr($job_card_items); exit;
		
		$ItemLedgers = $this->ItemLedgers->find();
				$totalInCase = $ItemLedgers->newExpr()
					->addCase(
						$ItemLedgers->newExpr()->add(['in_out' => 'In']),
						$ItemLedgers->newExpr()->add(['quantity']),
						'integer'
					);
				$totalOutCase = $ItemLedgers->newExpr()
					->addCase(
						$ItemLedgers->newExpr()->add(['in_out' => 'Out']),
						$ItemLedgers->newExpr()->add(['quantity']),
						'integer'
					);

				$ItemLedgers->select([
					'total_in' => $ItemLedgers->func()->sum($totalInCase),
					'total_out' => $ItemLedgers->func()->sum($totalOutCase),'id','item_id'
				])
				->group('item_id')
				->autoFields(true)
				->contain(['Items' => function($q) use($st_company_id){
					return $q->where(['Items.source'=>'Purchessed/Manufactured'])->orWhere(['Items.source'=>'Purchessed'])->contain(['ItemCompanies'=>function($p) use($st_company_id){
						return $p->where(['ItemCompanies.company_id' => $st_company_id,'ItemCompanies.freeze' => 0]);
					}]);
				}]);
				//pr($ItemLedgers->toArray()); exit;
		foreach ($ItemLedgers as $itemLedger){
			if($itemLedger->company_id==$st_company_id){
			$item_name=$itemLedger->item->name;
			$item_id=$itemLedger->item->id;
			$Current_Stock=$itemLedger->total_in-$itemLedger->total_out;
			
			
			$material_report[]=array('item_name'=>$item_name,'item_id'=>$item_id,'Current_Stock'=>$Current_Stock,'sales_order'=>@$sales[$item_id],'job_card_qty'=>@$job_card_items[$item_id]);
			}
		} 
		
		$this->set(compact('material_report','mit'));
			
	}
	public function fetchLedger($item_id=null,$from_date=null,$to_date=null)
    {
		//$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
        $this->paginate = [
            'contain' => ['Items']
        ];
		$where =[];
		$where['item_id']=$item_id;
		$where['company_id']=$st_company_id;
		if(!empty($from_date)){
			$From=date("Y-m-d",strtotime($from_date));
			$where['processed_on >=']=$From;
		}
		if(!empty($to_date)){
			$To=date("Y-m-d",strtotime($to_date));
			$where['processed_on <=']=$To;
		}
        $itemLedgers2 = $this->ItemLedgers->find()->where($where)->order(['processed_on'=>'DESC']);
		$itemLedgers=[];
		foreach($itemLedgers2 as $itemLedger){
			if($itemLedger->source_model =='Items'){
				$itemLedger->voucher_info='-';
				$itemLedger->party_type='Item';
				$itemLedger->party_info='-'; 
			}else{
				$result=$this->GetVoucherParty($itemLedger->source_model,$itemLedger->source_id); 
				$itemLedger->voucher_info=$result['voucher_info'];
				$itemLedger->party_type=$result['party_type'];
				$itemLedger->party_info=$result['party_info']; 	
			}
			$itemLedgers[]=$itemLedger;
		}
		$this->set(compact('itemLedgers'));
	}
	
	public function inventoryDailyReport(){ 
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$from_date=$this->request->query('From');
		$to_date=$this->request->query('To');
		$where =[];
		if(!empty($from_date)){
			$From=date("Y-m-d",strtotime($from_date));
			$where['processed_on >=']=$From;
		}
		if(!empty($to_date)){
			$To=date("Y-m-d",strtotime($to_date));
			$where['processed_on <=']=$To;
		}
		$itemLedgers = $this->ItemLedgers->find()
						->where($where)
						->order(['processed_on'=>'DESC'])
						->contain(['Items'])
						->where(['ItemLedgers.company_id' => $st_company_id]); 
		
		$itemDatas=[];
		foreach($itemLedgers as $itemLedger){
			$itemDatas[$itemLedger['source_model'].$itemLedger['source_id']][]=$itemLedger;
			
		}
		
		$serial_nos=[];
		$voucher_no=[];
		$link=[];
		$AllDatas=[];
		$AllDatas=[];
	
			$invoice=$this->ItemLedgers->Invoices->find()->contain(['InvoiceRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
						return $exp->between('date_created',$From ,$To, 'date');
					})->where(['Invoices.company_id' => $st_company_id])->toArray();
					//pr($invoice->toArray()); exit;
			if(!empty($invoice)){
				$AllDatas[$To]['Invoice']=$invoice;
			}
			
			$Grns=$this->ItemLedgers->Grns->find()->contain(['GrnRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
						return $exp->between('date_created',$From ,$To, 'date');
					})->where(['Grns.company_id' => $st_company_id])->toArray();
			if(!empty($Grns)){
				$AllDatas[$To]['Grns']=$Grns;
			}
			
			$InventoryTransferVouchers=$this->ItemLedgers->InventoryTransferVouchers->find()->contain(['InventoryTransferVoucherRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
						return $exp->between('transaction_date',$From ,$To, 'date');
					})->where(['InventoryTransferVouchers.company_id' => $st_company_id])->toArray();
			if(!empty($InventoryTransferVouchers)){
				$AllDatas[$To]['InventoryTransferVouchers']=$InventoryTransferVouchers;
			}
			
			$Ivs=$this->ItemLedgers->Ivs->find()->contain(['IvRows'=>['Items','SerialNumbers','IvRowItems'=>['Items','SerialNumbers']]])->where(function($exp) use($From ,$To) {
						return $exp->between('transaction_date',$From ,$To, 'date');
					})->where(['Ivs.company_id' => $st_company_id])->toArray();
			if(!empty($Ivs)){
				$AllDatas[$To]['InventoryVouchers']=$Ivs;
			}
			
			$SaleReturns=$this->ItemLedgers->SaleReturns->find()->contain(['SaleReturnRows'=>['Items','SerialNumbers'],'FinancialYears'])->where(function($exp) use($From ,$To) {
						return $exp->between('date_created',$From ,$To, 'date');
					})->where(['SaleReturns.company_id' => $st_company_id])->toArray();
			if(!empty($SaleReturns)){
				$AllDatas[$To]['SaleReturns']=$SaleReturns;
			}
			
			$PurchaseReturns=$this->ItemLedgers->PurchaseReturns->find()->contain(['PurchaseReturnRows'=>['Items','SerialNumbers'],'FinancialYears'])->where(function($exp) use($From ,$To) {
						return $exp->between('created_on',$From ,$To, 'date');
					})->where(['PurchaseReturns.company_id' => $st_company_id])->toArray();
					//pr($PurchaseReturns);exit;
			if(!empty($PurchaseReturns)){
				$AllDatas[$To]['PurchaseReturns']=$PurchaseReturns;
				//pr($AllDatas);exit;
			}
			
	
		$this->set(compact('AllDatas','serial_nos','voucher_no','From','To','link','url'));
	}
	
	public function excelInventory(){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$from_date=$this->request->query('From');
		$to_date=$this->request->query('To');
		$where =[];
		if(!empty($from_date)){
			$From=date("Y-m-d",strtotime($from_date));
			$where['processed_on >=']=$From;
		}
		if(!empty($to_date)){
			$To=date("Y-m-d",strtotime($to_date));
			$where['processed_on <=']=$To;
		}
		$itemLedgers = $this->ItemLedgers->find()
					->where($where)
					->order(['processed_on'=>'DESC'])
					->contain(['Items'])
					->where(['ItemLedgers.company_id' => $st_company_id]); 
	
	$itemDatas=[];
	foreach($itemLedgers as $itemLedger){
		$itemDatas[$itemLedger['source_model'].$itemLedger['source_id']][]=$itemLedger;
		
	}
	//pr($itemLedgers->toArray());exit;
	$serial_nos=[];
	$voucher_no=[];
	$link=[];
	$AllDatas=[];
	$AllDatas=[];

		$invoice=$this->ItemLedgers->Invoices->find()->contain(['InvoiceRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
					return $exp->between('date_created',$From ,$To, 'date');
				})->toArray();
				//pr($invoice->toArray()); exit;
		if(!empty($invoice)){
			$AllDatas[$To]['Invoice']=$invoice;
		}
		
		$Grns=$this->ItemLedgers->Grns->find()->contain(['GrnRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
					return $exp->between('date_created',$From ,$To, 'date');
				})->toArray();
		if(!empty($Grns)){
			$AllDatas[$To]['Grns']=$Grns;
		}
		
		$InventoryTransferVouchers=$this->ItemLedgers->InventoryTransferVouchers->find()->contain(['InventoryTransferVoucherRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
					return $exp->between('transaction_date',$From ,$To, 'date');
				})->toArray();
		if(!empty($InventoryTransferVouchers)){
			$AllDatas[$To]['InventoryTransferVouchers']=$InventoryTransferVouchers;
		}
		
		$Ivs=$this->ItemLedgers->Ivs->find()->contain(['IvRows'=>['Items','SerialNumbers','IvRowItems'=>['Items','SerialNumbers']]])->where(function($exp) use($From ,$To) {
					return $exp->between('transaction_date',$From ,$To, 'date');
				})->toArray();
		if(!empty($Ivs)){
			$AllDatas[$To]['InventoryVouchers']=$Ivs;
		}
		
		$SaleReturns=$this->ItemLedgers->SaleReturns->find()->contain(['SaleReturnRows'=>['Items','SerialNumbers']])->where(function($exp) use($From ,$To) {
					return $exp->between('date_created',$From ,$To, 'date');
				})->toArray();
		if(!empty($SaleReturns)){
			$AllDatas[$To]['SaleReturns']=$SaleReturns;
		}
		
		$PurchaseReturns=$this->ItemLedgers->PurchaseReturns->find()->contain(['PurchaseReturnRows'=>['Items','SerialNumbers'],'FinancialYears'])->where(function($exp) use($From ,$To) {
					return $exp->between('created_on',$From ,$To, 'date');
				})->toArray();
				//pr($PurchaseReturns);exit;
		if(!empty($PurchaseReturns)){
			$AllDatas[$To]['PurchaseReturns']=$PurchaseReturns;
			//pr($AllDatas);exit;
		}
      
	
	//pr($AllDatas);exit;
		$this->set(compact('itemDatas','serial_nos','voucher_no','From','To','link','from_date','to_date','AllDatas','sourceData'));
	}
	
	
	
	public function addToBucket($item_id=null,$qty=null){
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$ItemBuckets = $this->ItemLedgers->ItemBuckets->newEntity();
		$ItemBuckets->item_id=$item_id;
		$ItemBuckets->quantity=$qty;
		
		//$ItemBuckets->mi_number=$qty;
		//pr($ItemBuckets);exit;
		$this->ItemLedgers->ItemBuckets->save($ItemBuckets);

		$this->set(compact('ItemBuckets','item_id','qty'));
		
	}

	public function	openingStock(){
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		pr($st_company_id);
		exit;
	}
	

	
	public function ItemBucket()
	{
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$materialIndent = $this->MaterialIndents->newEntity();
		
		$ItemBuckets = $this->ItemLedgers->ItemBuckets->find()->contain(['Items'])->toArray();
		
		$this->set(compact('ItemBuckets'));
	}
	public function entryCount(){
		$this->viewBuilder()->layout('');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');

		$ItemLedgers=$this->ItemLedgers->find();
		$Items=$this->ItemLedgers->Items->find(); ?>
		<table border="1">
			<tr>
				<th>S.N</th>
				<th>Item Id</th>
				<th>Quantity</th>
				<th>Rate</th>
				
			</tr>
		<?php $i=1;
		  foreach($ItemLedgers as $ItemLedger){ 
			$AccountGroupsexists = $this->ItemLedgers->Items->exists(['Items.id' => $ItemLedger->item_id]);
				if(!$AccountGroupsexists){ ?>
			<tr>
				<td><?php echo $i++; ?></td>
				<td><?php echo $ItemLedger->item_id; ?></td>
				<td><?php echo $ItemLedger->quantity; ?></td>
				<td><?php echo $ItemLedger->rate; ?></td>
			</tr>
		<?php }
			} exit;
	}
	

	public function weightedAvgCostIvs($item_id=null){
			$this->viewBuilder()->layout('');
			$session = $this->request->session();
			$st_company_id = $session->read('st_company_id');
			
			$Items = $this->ItemLedgers->Items->get($item_id, [
				'contain' => ['ItemCompanies'=>function($q) use($st_company_id){
					return $q->where(['company_id'=>$st_company_id]);
				}]
			]);
			
			if($Items->item_companies[0]->serial_number_enable == '0'){  
				$stock=[];  $sumValue=[];
				foreach($Items as $Item){
					$StockLedgers=$this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$Item->id,'ItemLedgers.company_id'=>$st_company_id])->order(['ItemLedgers.processed_on'=>'ASC']);
					foreach($StockLedgers as $StockLedger){ 
						if($StockLedger->in_out=='In'){
							if(($StockLedger->source_model=='Grns' and $StockLedger->rate_updated=='Yes') or ($StockLedger->source_model!='Grns')){
								for($inc=0.01;$inc<$StockLedger->quantity;$inc+=0.01){
									$stock[$Item->id][]=$StockLedger->rate;
								}
							}
						}
					}
//pr($stock); exit;
					foreach($StockLedgers as $StockLedger){
						if($StockLedger->in_out=='Out'){
							if(sizeof(@$stock[$Item->id])>0){
								$stock[$Item->id] = array_slice($stock[$Item->id], $StockLedger->quantity*100); 
							}
						}
					}
					//echo "hello"; exit;
					if(sizeof(@$stock[$Item->id]) > 0){ 
						foreach(@$stock[$Item->id] as $stockRate){
							@$sumValue[$Item->id]+=@$stockRate/100;
						}
					}
				}
			
			}else{
				
			}
		exit;	
	}
	
	public function inventoryDailyReports(){ 
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout');
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		$from_date=$this->request->query('From');
		$to_date=$this->request->query('To');
		$where =[];
		if(!empty($from_date)){
			$From=date("Y-m-d",strtotime($from_date));
			$where['processed_on >=']=$From;
		}
		if(!empty($to_date)){
			$To=date("Y-m-d",strtotime($to_date));
			$where['processed_on <=']=$To;
		}
		$itemLedgers = $this->ItemLedgers->find()
						->where($where)
						->order(['processed_on'=>'DESC'])
						->contain(['Items'])
						->where(['ItemLedgers.company_id' => $st_company_id]); 
		
		$itemDatas=[];
		$qty=[];
		foreach($itemLedgers as $itemLedger){
			$itemDatas[$itemLedger['source_model'].$itemLedger['source_id']][]=$itemLedger;
			
		}
		//pr($itemDatas);exit;
		$serial_nos=[];
		$voucher_no=[];
		$sourceData=[];
		$link=[];
		$extra = [];
		foreach($itemDatas as $key=>$itemData)
		{ 
			foreach($itemData as $itemDetail)
			{
				if($itemDetail['source_model']=='Invoices')
				{
					$invoice=$this->ItemLedgers->Invoices->find()->where(['Invoices.id'=>$itemDetail['source_id']])->contain(['InvoiceRows'=>['Items','SerialNumbers']])->first();
					$sourceData[$key] = array_merge($itemData,$invoice->invoice_rows);
					$extra[$key] =$itemDetail['processed_on'].','.$itemDetail['source_id'].','.$itemDetail['in_out'].','.$itemDetail['quantity']; 
					$invoice=$this->ItemLedgers->Invoices->find()->where(['Invoices.id'=>$itemDetail['source_id']])->first();
					@$invoice1=($invoice->in1.'/IN-'.str_pad($invoice->in2, 3, '0', STR_PAD_LEFT).'/'.$invoice->in3.'/'.$invoice->in4);
					
					$voucher_no[$key][]=$invoice1;
					
					if($invoice['invoice_type']=="GST"){
						$link1 = ['controller'=>'Invoices','action' => 'gst-confirm'];
					}else{ 
						$link1 = ['controller'=>'Invoices','action' => 'confirm'];
					}
					$link[$key]=$link1;
				}
			}
			
		}
		//pr($sourceData);
		//exit;
	
		$this->set(compact('itemDatas','serial_nos','voucher_no','From','To','link','url','sourceData'));
	}
	
	public function SerialMisMatchList(){
		$this->viewBuilder()->layout('index_layout');
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
	
		$Items =$this->ItemLedgers->Items->find()->contain(['Units','ItemCompanies'=>function($p) use($st_company_id){
						return $p->where(['ItemCompanies.company_id' => $st_company_id]);
		}])->toArray();
		foreach($Items as $Item){ 
		if(@$Item->item_companies[0]->serial_number_enable==1){
				$ItemLedgersQty=$this->ItemLedgers->find()
				->select(['item_id','quantity'=>$this->ItemLedgers->find()->func()->sum('ItemLedgers.quantity')])
				->group(['ItemLedgers.item_id'])
				->where(['item_id'=>$Item->id,'in_out'=>'Out','company_id'=>$st_company_id])->first();
				
				$ItemSerialQty=$this->ItemLedgers->SerialNumbers->find()
				->select(['item_id'=>$this->ItemLedgers->SerialNumbers->find()->func()->count('SerialNumbers.item_id')])
				->group(['SerialNumbers.item_id'])
				->where(['item_id'=>$Item->id,'status'=>'Out','company_id'=>$st_company_id])->first();
				
				
				
				if(@$ItemLedgersQty->quantity != @$ItemSerialQty->item_id){
					pr(@$Item->name);
					pr(@$ItemLedgersQty->item_id);
					echo "<br>";
				}
			}
		}
		//pr($Items); exit;
		
		exit;
	
	}
	
	public function refresh(){
		$this->viewBuilder()->layout('index_layout');
        $session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		
		$ItemLedgers =	$this->ItemLedgers->find()
						->where(['company_id'=>$st_company_id, 'source_model'=>'Inventory Transfer Voucher', 'in_out'=>'Out'])
						->OrWhere(['company_id'=>$st_company_id, 'source_model'=>'Inventory Vouchers', 'in_out'=>'Out'])
						->order(['ItemLedgers.processed_on'=>'ASC']);
		$this->set(compact('ItemLedgers'));
	}
	
	public function updateRate(){
		$session = $this->request->session();
        $st_company_id = $session->read('st_company_id');
		
		$item_id=$this->request->query('item_id');
		$date=$this->request->query('date');
			
		echo $unit_rate = $this->weightedAvgCost($item_id,$date);
		exit;
		
	}
	
	public function weightedAvgCost($item_id=null,$transaction_date)
	{
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
			
		$Items = $this->ItemLedgers->Items->get($item_id, [
			'contain' => ['ItemCompanies'=>function($q) use($st_company_id){
				return $q->where(['company_id'=>$st_company_id]);
			}]
		]);
		$to_date = date('Y-m-d');
		$unit_rate=0;
		if($Items->item_companies[0]->serial_number_enable == '0'){

			$stock=[];  $sumValue=0; $where=[];   $stockNew=[]; 
				
			if(!empty($transaction_date)){
				$where['ItemLedgers.processed_on <']=$transaction_date;
				$where['ItemLedgers.item_id']=$item_id;
				$where['ItemLedgers.company_id']=$st_company_id;
			}
				
			$StockLedgers=$this->ItemLedgers->find()->where($where)->order(['ItemLedgers.processed_on'=>'ASC']);
			
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
			return $unit_rate; 	
		}else{
			$InSerialNumbers=	$this->ItemLedgers->SerialNumbers->find()
								->where(['SerialNumbers.item_id'=>$item_id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'In','SerialNumbers.transaction_date <'=>$transaction_date]);
			$sr=[];
			foreach($InSerialNumbers as $InSerialNumber){
				$sr[$InSerialNumber->id]=$InSerialNumber->id;
			}
			
			$OutSerialNumbers=	$this->ItemLedgers->SerialNumbers->find()
								->where(['SerialNumbers.item_id'=>$item_id,'SerialNumbers.company_id'=>$st_company_id,'status'=>'Out','SerialNumbers.transaction_date <'=>$transaction_date]);
			
			foreach($OutSerialNumbers as $OutSerialNumber){
				unset($sr[$OutSerialNumber->parent_id]);
			}		
			
			$rates=[];
			foreach($sr as $srId){ //echo 'hello<br/>';
				$SerialNumber=$this->ItemLedgers->SerialNumbers->get($srId);
				if($SerialNumber->grn_id>0){ //echo 'grn';
					$ItemLedgerRow=	$this->ItemLedgers->find()
									->where(['source_model'=>'Grns','source_id'=>$SerialNumber->grn_id, 'source_row_id'=>$SerialNumber->grn_row_id])->first();
					$rates[]=$ItemLedgerRow->rate;
				}
				else if($SerialNumber->sale_return_id>0){ //echo 'sale_return';
					$ItemLedgerRow=	$this->ItemLedgers->find()
									->where(['source_model'=>'Sale Return','source_id'=>$SerialNumber->sale_return_id, 'source_row_id'=>$SerialNumber->sales_return_row_id])->first();
					$rates[]=$ItemLedgerRow->rate;
				}
				else if($SerialNumber->itv_id>0){ //echo 'itv';
					$ItemLedgerRow=	$this->ItemLedgers->find()
									->where(['source_model'=>'Inventory Transfer Voucher','source_id'=>$SerialNumber->itv_id, 'source_row_id'=>$SerialNumber->itv_row_id])->first();
					$rates[]=$ItemLedgerRow->rate;
				}
				else if($SerialNumber->iv_row_id>0){ //echo 'iv';
					$ItemLedgerRow=	$this->ItemLedgers->find()
									->where(['source_model'=>'Inventory Vouchers','iv_row_id'=>$SerialNumber->iv_row_id])->first();
					$rates[]=$ItemLedgerRow->rate;
				}
				else if($SerialNumber->is_opening_balance  == "Yes"){ //echo 'ob';
					$ItemLedgerRow=	$this->ItemLedgers->find()
									->where(['source_model'=>'Items','ItemLedgers.company_id'=>$st_company_id,'ItemLedgers.item_id' => $SerialNumber->item_id])->first();
					$rates[]=$ItemLedgerRow->rate;
				}
			}
			if(sizeof($sr)>0){
				$unit_rate=array_sum($rates)/sizeof($sr);
			}else{
				$unit_rate=0;
			}
			echo $unit_rate; exit;
			
		}
		
	}
	
	
	
	public function qwerty(){
		$session = $this->request->session();
		$st_company_id = $session->read('st_company_id');
		
		$itemLedgers=	$this->ItemLedgers->find()
						->where([
							'company_id'=>$st_company_id, 
							'source_model IN'=>['Inventory Vouchers', 'Inventory Transfer Voucher'], 
							'in_out'=>'Out',
							'tamp_feild'=>'no'
							])
						->order(['itemLedgers.processed_on'=>'ASC'])
						->contain(['Items'=>['ItemCompanies'=>function($q) use($st_company_id){
							return $q->where(['company_id'=>$st_company_id]);
						}]])
						->limit(100);
						
		foreach($itemLedgers as $itemLedger)
		{
			if($itemLedger->item->item_companies[0]->serial_number_enable==1)
			{
				if($itemLedger->source_model=='Inventory Vouchers')
				{
					$SerialNumbers=	$this->ItemLedgers->SerialNumbers->find()
									->where(['iv_row_items'=>$itemLedger->iv_row_item_id]);
					if($SerialNumbers)
					{
						$Rate=[];
						foreach($SerialNumbers as $SerialNumber)
						{
							$inSr=$this->ItemLedgers->SerialNumbers->get($SerialNumber->parent_id);
							if($inSr->is_opening_balance=='Yes')
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Items', 'item_id'=>$inSr->item_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->grn_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Grns', 'source_id'=>$inSr->grn_id, 'source_row_id'=>$inSr->grn_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->sales_return_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Sale Return', 'source_id'=>$inSr->sales_return_id, 'source_row_id'=>$inSr->sales_return_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->itv_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Inventory Transfer Voucher', 'source_id'=>$inSr->itv_id, 'source_row_id'=>$inSr->itv_row_id])->first();
								$Rate[]=@$Row->rate;
							}
						}
						$sumOfRate=array_sum($Rate);
						$TotalSr=sizeof($SerialNumbers->toArray());
						if($sumOfRate>0 && $TotalSr>0)
						{
							$unitRate=$sumOfRate/$TotalSr;
						}else
						{
							$unitRate=0;
						}
					}
				}
				else if($itemLedger->source_model=='Invoices')
				{
					$SerialNumbers=	$this->ItemLedgers->SerialNumbers->find()
									->where(['invoice_row_id'=>$itemLedger->source_row_id]);
					if($SerialNumbers)
					{
						$Rate=[];
						foreach($SerialNumbers as $SerialNumber)
						{
							$inSr=$this->ItemLedgers->SerialNumbers->get($SerialNumber->parent_id);
							if($inSr->is_opening_balance=='Yes')
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Items', 'item_id'=>$inSr->item_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->grn_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Grns', 'source_id'=>$inSr->grn_id, 'source_row_id'=>$inSr->grn_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->sales_return_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Sale Return', 'source_id'=>$inSr->sales_return_id, 'source_row_id'=>$inSr->sales_return_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->itv_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Inventory Transfer Voucher', 'source_id'=>$inSr->itv_id, 'source_row_id'=>$inSr->itv_row_id])->first();
								$Rate[]=@$Row->rate;
							}
						}
						$sumOfRate=array_sum($Rate);
						$TotalSr=sizeof($SerialNumbers->toArray());
						if($sumOfRate>0 && $TotalSr>0)
						{
							$unitRate=$sumOfRate/$TotalSr;
						}else
						{
							$unitRate=0;
						}
					}
				}
				else if($itemLedger->source_model=='Purchase Return')
				{
					$SerialNumbers=	$this->ItemLedgers->SerialNumbers->find()
									->where(['purchase_return_row_id'=>$itemLedger->source_row_id]);
					if($SerialNumbers)
					{
						$Rate=[];
						foreach($SerialNumbers as $SerialNumber)
						{
							$inSr=$this->ItemLedgers->SerialNumbers->get($SerialNumber->parent_id);
							if($inSr->is_opening_balance=='Yes')
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Items', 'item_id'=>$inSr->item_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->grn_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Grns', 'source_id'=>$inSr->grn_id, 'source_row_id'=>$inSr->grn_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->sales_return_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Sale Return', 'source_id'=>$inSr->sales_return_id, 'source_row_id'=>$inSr->sales_return_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->itv_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Inventory Transfer Voucher', 'source_id'=>$inSr->itv_id, 'source_row_id'=>$inSr->itv_row_id])->first();
								$Rate[]=@$Row->rate;
							}
						}
						$sumOfRate=array_sum($Rate);
						$TotalSr=sizeof($SerialNumbers->toArray());
						if($sumOfRate>0 && $TotalSr>0)
						{
							$unitRate=$sumOfRate/$TotalSr;
						}else
						{
							$unitRate=0;
						}
					}
				}
				else if($itemLedger->source_model=='Inventory Transfer Voucher')
				{
					$SerialNumbers=	$this->ItemLedgers->SerialNumbers->find()
									->where(['itv_row_id'=>$itemLedger->source_row_id]);
					if($SerialNumbers)
					{
						$Rate=[];
						foreach($SerialNumbers as $SerialNumber)
						{
							$inSr=$this->ItemLedgers->SerialNumbers->get($SerialNumber->parent_id);
							if($inSr->is_opening_balance=='Yes')
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Items', 'item_id'=>$inSr->item_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->grn_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Grns', 'source_id'=>$inSr->grn_id, 'source_row_id'=>$inSr->grn_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->sales_return_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Sale Return', 'source_id'=>$inSr->sales_return_id, 'source_row_id'=>$inSr->sales_return_row_id])->first();
								$Rate[]=@$Row->rate;
							}
							else if($inSr->itv_row_id>0)
							{
								$Row=$this->ItemLedgers->find()->where(['company_id'=>$st_company_id, 'source_model'=>'Inventory Transfer Voucher', 'source_id'=>$inSr->itv_id, 'source_row_id'=>$inSr->itv_row_id])->first();
								$Rate[]=@$Row->rate;
							}
						}
						$sumOfRate=array_sum($Rate);
						$TotalSr=sizeof($SerialNumbers->toArray());
						if($sumOfRate>0 && $TotalSr>0)
						{
							$unitRate=$sumOfRate/$TotalSr;
						}else
						{
							$unitRate=0;
						}
					}
				}
				$il=$this->ItemLedgers->get($itemLedger->id);
				$il->rate=round($unitRate,2);
				$this->ItemLedgers->save($il);
				echo $itemLedger->id.' - '.round($unitRate,2);
				echo '<hr/>';
			}
			else if($itemLedger->item->item_companies[0]->serial_number_enable==0)
			{
				$transaction_date=$itemLedger->processed_on;
				$item_id=$itemLedger->item_id;
				$stock=[];  $sumValue=0; $where=[];   $stockNew=[]; 
				if(!empty($transaction_date)){
					$where['ItemLedgers.processed_on <=']=$transaction_date;
					$where['ItemLedgers.item_id']=$item_id;
					$where['ItemLedgers.company_id']=$st_company_id;
					$where['ItemLedgers.id !=']=$itemLedger->id;
				}
					
				$StockLedgers=$this->ItemLedgers->find()->where($where)->order(['ItemLedgers.processed_on'=>'ASC']);
				
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
				$il=$this->ItemLedgers->get($itemLedger->id);
				$il->rate=round($unit_rate,2);
				$this->ItemLedgers->save($il);
				echo $itemLedger->id.' - '.round($unit_rate,2);
				echo '<hr/>';
			}
			
			$query2=$this->ItemLedgers->query();
			$query2->update()
			->set(['tamp_feild' => 'yes'])
			->where(['id' => $itemLedger->id])
			->execute();
		}
		exit;
	}
	
}

