<?php


/**
 * Description of EspressoAPI_Generic_Resource_Facade_Base_Functions
 *
 * @author mnelson4
 */
abstract class EspressoAPI_Generic_Resource_Facade_Base_Functions {
	/**
	 * array for converting between object parameters the API expects and DB columns (modified DB columns, see each API's $selectFields).
	 * keys are the API-expected parameters, and values are teh DB columns
	 * eg.: array("event_id"=>'id','niceName'=>'uglyDbName')
	 * sometimes these may seem repetitious, but they're mostly handy for enumerating a specific list of allowed api params
	 * @var array 
	 */
	var $APIqueryParamsToDbColumns=array();
	/**
	 * 
	 * @var type 
	 */
	protected $validator;
	/**
	 * primary ID column for SELECT query when selecting ONLY the primary id
	 */
	protected $primaryIdColumn;
	
	function __construct(){
		$this->validator=new EspressoAPI_Validator($this);
	}
	/**
	 * returns whether or not $modelName is the name (singular or plural)
	 * of the current model
	 * @param string $modelName 
	 */
	function isARelatedModel($modelName){
		foreach($this->relatedModels as $relatedModel){
			if($relatedModel['modelName']==$modelName || $relatedModel['modelNamePlural']==$modelName){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * gets the class of the related model specified by $modelName.
	 * eg: Attendees are related models to Registrations. So on a 
	 * EspressoAPI_Registration_Resource you can call getRelatedModel('Attendee')
	 * and you'll get the EspressoAPI_Attendee_Resource.
	 * Automatically makes sure the related models' classes have been instantiated.
	 * @param string $modelName
	 * @return child of EspressoAPI_Generic_Resource_Facade 
	 */
	/*function getRelatedModel($modelName){
		$relatedModels=$this->getFullRelatedModels();
		return $relatedModels[$modelName]['class'];
	}*/
	/**
	 * finds the model's field info.
	 * eg, on the Event Resource call getRequiredFieldInfo('name') and you
	 * should get array('var'=>'name', 'type'=>'string')
	 * @param string $fieldName
	 * @return array with keys 'var', 'type', and possibly 'allowedEnumValues' 
	 */
	function getRequiredFieldInfo($fieldName){
		$requiredFields=$this->getRequiredFields();
		$fieldIndicated=null;
		foreach($requiredFields as $requiredField){
			if($requiredField['var']==$fieldName){
				$fieldIndicated=$requiredField;
				break;
			}
		}
		if($fieldIndicated==null){
			return array('var'=>null,'type'=>null,'allowedEnumValues'=>null);
		}else{
			if(!array_key_exists('allowedEnumValues',$fieldIndicated)){
				$fieldIndicated['allowedEnumValues']=null;
			}
			return $fieldIndicated;
		}
	}
	/**
	 * returns the list of fields on teh current model that are required in a response
	 * and should eb acceptable for querying on
	 * @return type 
	 */
	function getRequiredFields(){
		return $this->requiredFields;
	}
	
	protected function convertApiParamToDBColumn($apiParam){
		$apiParamParts=explode(".",$apiParam,2);
		if(count($apiParamParts)!=2){
			throw new EspressoAPI_BadRequestException(__("Illegal get parameter passed!:","event_espresso").$apiParam);
		}else if($apiParamParts[0]==$this->modelName && array_key_exists($apiParamParts[1], $this->APIqueryParamsToDbColumns)){
			return $this->APIqueryParamsToDbColumns[$apiParamParts[1]];
		}elseif(count($apiParamParts)==2 && array_key_exists($apiParamParts[0],$this->relatedModels)){
			$otherFacade=EspressoAPI_ClassLoader::load($this->relatedModels[$apiParamParts[0]]['modelNamePlural'],'Resource');
			$columnName=$otherFacade->convertApiParamToDBColumn($apiParamParts[1]);
			return $columnName;
		//}elseif(count($apiParamParts)==1){//th
		//	return $this->APIqueryParamsToDbColumns[$apiParam];
		}else{
			throw new EspressoAPI_BadRequestException(__("Illegal get parameter passed!:","event_espresso").$apiParam);
		}
	}
	
	/**
	 * takes the api param value and produces a db value for using in a mysql WHERE clause.
	 * also takes an option $mappingFromApiToDbColumn and $key, which, if value is 
	 * a key in the array, convert the db value into the associated value in $mappingFromApiToDbColumn
	 * @param string $valueInput
	 * @param array $mappingFromApiToDbColumn eg array('true'=>'Y','false'=>'N')
	 * @param string $apiKey
	 * @return string representing the db col value in mySQL
	 * @throws EspressoAPI_BadRequestException 
	 */
	protected function constructSimpleValueInWhereClause($valueInput,$mappingFromApiToDbColumn=null,$apiKey=null){
		//first: validate that the input is acceptable
		//if($this->validator->valueIs($valueInput, , $allowedEnumValues)
		if(isset($mappingFromApiToDbColumn)){
			if(array_key_exists($valueInput,$mappingFromApiToDbColumn)){
				$valueInput=$mappingFromApiToDbColumn[$valueInput];
			}else{
				$validInputs=implode(",",array_keys($mappingFromApiToDbColumn));
				throw new EspressoAPI_BadRequestException(__("The key/value pair you specified in your query is invalid:","event_espresso").$apiKey."/".$valueInput.__(". Valid inputs would be :","event_espresso").$validInputs);
			}
		}
		if(is_numeric($valueInput) || in_array($valueInput,array('true','false'))){
			return $valueInput;
		}else{
			return "'$valueInput'";
		}
	}
	
		/**
	 *gets the API Facade classes for each related model and puts in an array with keys like the following:
	 * array('Event'=>array('modelName'=>'Event','modelNamePlural'=>'Events','hasMany'=>true,'class'=>EspressoAPI_events_Resource),
	 *		'Datetime'=>...)
	 * @return array as described above 
	 */
	function getFullRelatedModels(){
		//if we've already called this function and assigned the classes in the relatedModels array, just use it
		if(array_key_exists('class',array_shift(array_values($this->relatedModels)))){
			return $this->relatedModels;
		}
		$relatedModels=array();
		foreach($this->relatedModels as $modelName=>$relatedModel){
			$relatedModels[$modelName]['modelName']=$modelName;
			$relatedModels[$modelName]['modelNamePlural']=$relatedModel['modelNamePlural'];
			$relatedModels[$modelName]['hasMany']=$relatedModel['hasMany'];
			$relatedModels[$modelName]['class']=EspressoAPI_ClassLoader::load($relatedModel['modelNamePlural'],'Resource');
		}
		$this->relatedModels=$relatedModels;
		return $relatedModels;
	}
}