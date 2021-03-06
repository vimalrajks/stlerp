<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class LoginsTable extends Table
{
    public function initialize(array $config)
    {
		$this->belongsTo('Employees', [
            'foreignKey' => 'employee_id',
            'joinType' => 'INNER'
        ]);
		$this->belongsTo('UserRights');
		$this->belongsTo('UserLogs');
		$this->belongsTo('RequestLeaves');
		$this->belongsTo('EmployeeCompanies');
		$this->belongsTo('Quotations');
		$this->belongsTo('SalesOrders');
		$this->belongsTo('Invoices');
		$this->belongsTo('PurchaseOrders');
		$this->belongsTo('LeaveApplications');
		$this->belongsTo('TravelRequests');
		$this->belongsTo('LoanApplications');
		$this->belongsTo('Grns');
		$this->belongsTo('FinancialYears');
		$this->belongsTo('FinancialMonths');
		
    }
	
	 public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('username', 'create')
            ->notEmpty('username');
		
		$validator->add(
				'username', 
				['unique' => [
					'rule' => 'validateUnique', 
					'provider' => 'table', 
					'message' => 'Not unique']
				]
			);

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');
			
		 $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        return $validator;
    }
	
}