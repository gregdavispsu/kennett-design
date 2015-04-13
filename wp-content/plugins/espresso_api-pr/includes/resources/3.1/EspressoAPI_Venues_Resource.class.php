<?php
/**
 *this file should actually exist in the Event Espresso Core Plugin 
 */
class EspressoAPI_Venues_Resource extends EspressoAPI_Venues_Resource_Facade{
	/**
	 * primary ID column for SELECT query when selecting ONLY the primary id
	 */
	protected $primaryIdColumn='Venue.id';
	var $APIqueryParamsToDbColumns=array(
		'id'=>'Venue.id',
		'name'=>'Venue.name',
		'identifier'=>'Venue.identifier',
		'address'=>'Venue.address',
		'address2'=>'Venue.address2',
		'city'=>'Venue.city',
		'state'=>'Venue.state',
		'zip'=>'Venue.zip',
		'country'=>'Venue.country',
		'user'=>'Venue.wp_user'
	);
	var $calculatedColumnsToFilterOn=array();
	var $selectFields="
		Venue.id AS 'Venue.id',
		Venue.name AS 'Venue.name',
		Venue.identifier AS 'Venue.identifier',
		Venue.address as 'Venue.address',
		Venue.address2 as 'Venue.address2',
		Venue.city AS 'Venue.city',
		Venue.state AS 'Venue.state',
		Venue.zip AS 'Venue.zip',
		Venue.country AS 'Venue.country',
		Venue.meta AS 'Venue.metas',
		Venue.wp_user AS 'Venue.user'";
	var $relatedModels=array();
	
	/**
	 * takes the results acquired from a DB selection, and extracts
	 * each instance of this model, and compiles into a nice array like
	 * array(12=>("id"=>12,"name"=>"mike party","description"=>"all your base"...)
	 * Also, if we're going to just be finding models that relate
	 * to a specific foreign_key on any table in the query, we can specify
	 * to only return those models using the $idKey and $idValue,
	 * for example if you have a bunch of results from a query like 
	 * "select * FROM events INNER JOIn attendees", and you just want
	 * all the attendees for event with id 13, then you'd call this as follows:
	 * $attendeesForEvent13=parseSQLREsultsForMyDate($results,'Event.id',13);
	 * @param array $sqlResults
	 * @param string/int $idKey
	 * @param string/int $idValue 
	 * @return array compatible with the required reutnr type for this model
	 */
	protected function _extractMyUniqueModelsFromSqlResults($sqlResult){
		$metas=unserialize($sqlResult['Venue.metas']);
		$venue=array(
		'id'=>$sqlResult['Venue.id'],
		'name'=>$sqlResult['Venue.name'],
		'identifier'=>$sqlResult['Venue.identifier'],
		'address'=>$sqlResult['Venue.address'],
		'address2'=>$sqlResult['Venue.address2'],
		'city'=>$sqlResult['Venue.city'],
		'state'=>$sqlResult['Venue.state'],
		'zip'=>$sqlResult['Venue.zip'],
		'country'=>$sqlResult['Venue.country'],
		'metas'=>$metas,
		'user'=>$sqlResult['Venue.user']
		);
		return $venue; 
	}
	function extractMyColumnsFromApiInput($apiInput,$dbEntries,$options=array()){
		return $dbEntries;
	}
}
//new Events_Controller();