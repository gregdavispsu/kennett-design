<?php
/**
 * EspressoAPI
 *
 * RESTful API for Even tEspresso
 *
 * @ package			Espresso REST API
 * @ author				Mike Nelson
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			{@link http://eventespresso.com/support/terms-conditions/}   * see Plugin Licensing *
 * @ link					{@link http://www.eventespresso.com}
 * @ since		 		3.2.P
 *
 * ------------------------------------------------------------------------
 *
 * Events API Facade class
 *
 * @package			Espresso REST API
 * @subpackage	includes/APIFacades/Espresso_Events_Resource_Facade.class.php
 * @author				Mike Nelson
 *
 * ------------------------------------------------------------------------
 */
//require_once("EspressoAPI_Generic_Resource_Facade.class.php");
abstract class EspressoAPI_Events_Resource_Facade extends EspressoAPI_Generic_Resource_Facade{
	var $modelName="Event";
	var $modelNamePlural="Events";
	
	var $requiredFields = array(
		array('var'=>'id','type'=>'int'),
		array('var'=>'code','type'=>'string'),
		array('var'=>'name','type'=>'string'),
		array('var'=>'description','type'=>'string'),
		array('var'=>'status','type'=>'enum','allowedEnumValues'=>array(
				'secondary/waitlist',
				'expired',
				'active',
				'denied',
				'inactive',
				'ongoing',
				'pending',
				'draft')),
		array('var'=>'limit','type'=>'int'),
		array('var'=>'group_registrations_allowed','type'=>'bool'),
		array('var'=>'group_registrations_max','type'=>'int'),
		array('var'=>'active','type'=>'bool'),
		array('var'=>'member_only','type'=>'bool'),
		array('var'=>'virtual_url','type'=>'string'),
		array('var'=>'call_in_number','type'=>'string'),
		array('var'=>'phone','type'=>'string'),
		array('var'=>'metadata','type'=>'array'));
}