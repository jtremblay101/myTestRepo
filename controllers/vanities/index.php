<?
session_start();
		
if(!isset($_SESSION["logged"]))
{
	echo json_encode([
				"error" => "Please Log In. (Refresh with key.)"
				]);
	exit;
}

require_once 'azure\WindowsAzure\WindowsAzure.php';
	
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Table\Models\QueryEntitiesOptions;
use WindowsAzure\Table\Models\Filters\Filter;
use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

if(isset($_POST["action"]))
{
	$connectionString = "DefaultEndpointsProtocol=https;AccountName=vanman;AccountKey=QdWBBF/0E+rYpBrk5YC0kyV7CxRgnP9CP0AhQG4Q9R8cDIFIbIyHHwoK3I+GgAlfOb4V7ifiDZ6BRBDsGvefIQ==";


	// Create table REST proxy.
	$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);
	
	$action = $_POST["action"];
	$data = array_values($_POST["data"]);
	$table = "Vanities";
		
	$RowKey = "";
	$Destination = "";
	$error_message = "";
	
	if(isset($_POST["previousRowKey"]))
	{
		$previousRowKey = base64_encode($_POST["previousRowKey"]);		
	}
	
	if($action == "create")
	{
		foreach($data as $row)
		{
			$RowKey = base64_encode( $row["RowKey"] );
			$Destination = ( $row["Destination"] );
			$filter = "RowKey eq '$RowKey'";					
		}
	}
	elseif($action == "edit")
	{
		foreach($data as $row)
		{
			$RowKey = base64_encode( $row["RowKey"] );
			$Destination = ( $row["Destination"] );
			$filter = "RowKey eq '$previousRowKey'";			
		}		
	}
	else
	{
		foreach($data as $row)
		{
			$RowKey = base64_encode( $row["RowKey"] );
			$Destination = ( $row["Destination"] );
			$filter = "";			
		}
	}
	
	if($action == "create" || $action == "edit")
	{		
		try {
			$result = $tableRestProxy->queryEntities($table, $filter);
		}
			catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();			
		}
		
		if(strlen($error_message)>0)
		{
			echo json_encode([
				"error" => "There was a problem retrieving the entity. Please contact the Marketing Technology Team. $error_message"
			]);
			exit;
		}
		
		$entities = $result->getEntities();		
	}
	
	if($action == "create")
	{
		if(count($entities)>0)
		{
			echo json_encode([
				"error" => "There already exists a row for that url. Please just edit that one."
			]);
			exit;
		}
		else
		{
						
			$newEntity = new Entity();
			$newEntity->setPartitionKey("");
			$newEntity->setRowKey($RowKey);
			$newEntity->addProperty("Destination", null, $Destination);
			
			try{
				$inserted = $tableRestProxy->insertEntity($table, $newEntity);
			}
			catch(ServiceException $e){
				// Handle exception based on error codes and messages.
				// Error codes and messages are here:
				// http://msdn.microsoft.com/library/azure/dd179438.aspx
				$code = $e->getCode();
				$error_message = $e->getMessage();
			}
			
			if(strlen($error_message)>0)
			{
				echo json_encode([
				"error" => "Your new entry was not made. Please contact the Marketing Technology Team. $error_messag"
				]);
			}
			else
			{
				$data[0]["Row Number"] = "New";
				echo json_encode(["data"=>$data]);				
			}
			
		}
	}
	elseif($action == "edit")
	{
		if(!count($entities)>0)
		{
			echo json_encode([
				"error" => "There is no entry to edit. Please refresh your page to get the latest data. If issue persists, please contact the Marketing Technology Team."
				]);
			exit;
		}
		else
		{
			
			foreach($entities as $editEntity)
			{			
				if($RowKey != $previousRowKey)
				{
					$editEntity->setRowKey("$RowKey");
					try{
						$tableRestProxy->insertEntity($table, $editEntity);
						$tableRestProxy->deleteEntity($table, "", $previousRowKey);
					}
					catch(ServiceException $e){
						// Handle exception based on error codes and messages.
						// Error codes and messages are here:
						// http://msdn.microsoft.com/library/azure/dd179438.aspx
						$code = $e->getCode();
						$error_message = "On delete add: ".$e->getMessage();
					}
					if(strlen($error_message)>0)
					{
						echo json_encode([
							"error" => "Your entry was not edited correctly. Please contact the Marketing Technology Team. $error_message"
						]);
						exit;
					}
					$newResult = $tableRestProxy->getEntity($table, "", $RowKey);

					$editEntity = $newResult->getEntity();
				}
				
				$editEntity->setPropertyValue("Destination", $data[0]["Destination"]); //Modified Destination.
			
				
				try{
					$tableRestProxy->updateEntity($table, $editEntity);
				}
				catch(ServiceException $e){
					// Handle exception based on error codes and messages.
					// Error codes and messages are here:
					// http://msdn.microsoft.com/library/azure/dd179438.aspx
					$code = $e->getCode();
					$error_message = "On edit: ".$e->getMessage();
				}
				
				if(strlen($error_message)>0)
				{
					echo json_encode([
						"error" => "Your entry was not edited. Please contact the Marketing Technology Team. $error_message"
					]);
					exit;
				}
				else
				{
					echo json_encode(["data"=>$data]);				
				}
			}
		}
		
	}
	elseif($action == "remove")
	{
		try{			
			$tableRestProxy->deleteEntity($table, "", $RowKey);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = "On delete add: ".$e->getMessage();
		}
		
		if(strlen($error_message)>0)
		{
			echo json_encode([
				"error" => "Your was not deleted. Please contact the Marketing Technology Team. $error_message"
			]);
		}
		else
		{
			echo json_encode([]);				
		}
	}
	
}