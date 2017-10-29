<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IvRowItems Model
 *
 * @property \Cake\ORM\Association\BelongsTo $IvRows
 * @property \Cake\ORM\Association\BelongsTo $Items
 *
 * @method \App\Model\Entity\IvRowItem get($primaryKey, $options = [])
 * @method \App\Model\Entity\IvRowItem newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\IvRowItem[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\IvRowItem|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\IvRowItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\IvRowItem[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\IvRowItem findOrCreate($search, callable $callback = null)
 */
class IvRowItemsTable extends Table
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

        $this->table('iv_row_items');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('IvRows', [
            'foreignKey' => 'iv_row_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Items', [
            'foreignKey' => 'item_id',
            'joinType' => 'INNER'
        ]);
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
            ->decimal('quantity')
            ->requirePresence('quantity', 'create')
            ->notEmpty('quantity');

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
        $rules->add($rules->existsIn(['iv_row_id'], 'IvRows'));
        $rules->add($rules->existsIn(['item_id'], 'Items'));

        return $rules;
    }
}
