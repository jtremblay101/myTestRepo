<?
if(isset($_POST["action"]))
{
	require_once 'azure\WindowsAzure\WindowsAzure.php';
	use WindowsAzure\Common\ServicesBuilder;
	use WindowsAzure\Common\ServiceException;
	use WindowsAzure\Table\Models\QueryEntitiesOptions;
	use WindowsAzure\Table\Models\Filters\Filter;

	$connectionString = "DefaultEndpointsProtocol=https;AccountName=vanman;AccountKey=QdWBBF/0E+rYpBrk5YC0kyV7CxRgnP9CP0AhQG4Q9R8cDIFIbIyHHwoK3I+GgAlfOb4V7ifiDZ6BRBDsGvefIQ==";


	// Create table REST proxy.
	$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);
	
	$action = $_POST["action"];
	$data = $_POST["data"];
	
	if($action == "create")
	{
		foreach($data as $row)
		{
			$RowKey = base64_encode( $row["RowKey"] );
			$filter = "RowKey eq '$RowKey'";					
		}
	}
	elseif($action == "edit")
	{
		echo json_encode([
			"data" => array_values($_POST["data"])
			]);		
	}
	else
	{
		echo json_encode([]);
	}
	
	
	$scale = "Vanities";
	try {
		$result = $tableRestProxy->queryEntities($scale, $filter);
	}
		catch(ServiceException $e){
		// Handle exception based on error codes and messages.
		// Error codes and messages are here:
		// http://msdn.microsoft.com/library/azure/dd179438.aspx
		$code = $e->getCode();
		$error_message = $e->getMessage();
		echo $code.": ".$error_message."<br />";
	}
	
	$entities = $result->getEntities();
	
	if($action == "create")
	{
		if(count($entities)>0)
		{
			echo json_encode([
				"error" => "There already exists a row for that url. Please just edit that one.";
				]);
		}
		else
		{
			
		}
	}
	elseif($action == "edit")
	{
		echo json_encode([
			"data" => array_values($_POST["data"])
			]);		
	}
	else
	{
		echo json_encode([]);
	}
	
}