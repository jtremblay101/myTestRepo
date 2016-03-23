<?php

require_once 'azure\WindowsAzure\WindowsAzure.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=vanman;AccountKey=QdWBBF/0E+rYpBrk5YC0kyV7CxRgnP9CP0AhQG4Q9R8cDIFIbIyHHwoK3I+GgAlfOb4V7ifiDZ6BRBDsGvefIQ==";


// Create table REST proxy.
$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

$filter = "";

try {
    $result = $tableRestProxy->queryEntities("Vanities", $filter);
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

$i=0;
$columns = [];
$body = "";

foreach($entities as $entity){
	$body .= "<tr>";
	
	// echo "Entity:<br/>";
	// var_dump($entity);
	
	// echo "Properties:<br/>";
	if($i ==0)
	{
		$properties = ($entity->getProperties());
		
		foreach($properties as $property => $propObject)
		{
			$columns[] = ($property);			
		}
	}
	
	foreach($columns as $column)
	{
		$value = $entity->getPropertyValue($column);
		
		if ($value instanceof DateTime) {
			$value = $value->format("d-m-Y @ h:i:s a");
		}
		elseif($column == "RowKey")
		{
			$value = base64_decode($value);
		}
		
		$body.="<td>$value</td>";
	}		
	
	// echo "Methods:<br/>";
	// var_dump(get_class_methods($entity));
	
		
	$body .= "</tr>";
	$i++;
}

$head = implode("</th><th>",$columns);

echo "
	<table>
		<thead>
			<th>
				$head
			</th>
		</thead>
		<tbody>
			$body
		</tbody>
	</table>
";
?>
<style>
table, th, td {
   border: 1px solid black;
}
</style>





