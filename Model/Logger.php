<?php
App::uses('AuditableConfig', 'Auditable.Lib');
class Logger extends AppModel
{
	public $name = 'Logger';
	
	public $useTable = 'logs';
	
	public $displayField = 'id';
	
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'LogDetail' => array('className' => 'Auditable.LogDetail')
	);
	
	/**
	 * 
	 * 
	 * @param int $id
	 * @param  bool $loadResource
	 * @return array
	 */
	public function get($id, $loadResource = true)
	{
		$contain = array('LogDetail');

		if(!empty(AuditableConfig::$userModel))
		{
			$this->bindModel(array(
				'belongsTo' => array(
					'Responsible' => array(
						'className' => AuditableConfig::$userModel,
						'foreignKey' => 'user_id'
						)
					)
				)
			);

			$contain[] = 'Responsible';
		}

		$data = $this->find('first', array(
			'conditions' => array('Logger.id' => $id),
			'contain' => $contain
			)
		);
		
		$linked = null;
		
		if($loadResource)
		{
			$Resource = ClassRegistry::init($data[$this->name]['model_alias']);
			
			$linked = $Resource->find('first', array(
				'conditions' => array('id' => $data[$this->name]['model_id']),
				'recursive' => -1
				)
			);
		}
		
		if(!empty($linked))
		{
			$data[$Resource->name] = $linked[$Resource->name];
		}

		if(array_search('Responsible', $contain) === false)
		{
			$data['Responsible']['name'] = '';
		}
		
		return $data;
	}
}