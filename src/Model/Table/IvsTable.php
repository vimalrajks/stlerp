<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;
/**
 * Ivs Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Invoices
 * @property \Cake\ORM\Association\BelongsTo $Companies
 * @property \Cake\ORM\Association\HasMany $IvRows
 *
 * @method \App\Model\Entity\Iv get($primaryKey, $options = [])
 * @method \App\Model\Entity\Iv newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Iv[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Iv|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Iv patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Iv[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Iv findOrCreate($search, callable $callback = null)
 */
class IvsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('ivs');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER'
        ]);
		$this->belongsTo('InventoryVouchers');
		$this->belongsTo('FinancialYears');
		$this->belongsTo('FinancialMonths');
		$this->belongsTo('ItemLedgers');
		$this->belongsTo('JobCards');
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('IvRows', [
            'foreignKey' => 'iv_id',
			'saveStrategy'=>'replace'
        ]);
		
		$this->belongsTo('Creator', [
			'className' => 'Employees',
			'foreignKey' => 'created_by',
			'propertyName' => 'creator',
		]);
		
		$this->belongsTo('Companies', [
			'foreignKey' => 'company_id',
			'joinType' => 'INNER'
		]);
    }

	public function beforeMarshal(Event $event, ArrayObject $data)
    {
        @$data['transaction_date'] = trim(date('Y-m-d',strtotime(@$data['transaction_date'])));
    }
    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('voucher_no')
            ->requirePresence('voucher_no', 'create')
            ->notEmpty('voucher_no');

        $validator
            ->requirePresence('narration', 'create')
            ->notEmpty('narration');

        $validator
            ->date('transaction_date')
            ->requirePresence('transaction_date', 'create')
            ->notEmpty('transaction_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
