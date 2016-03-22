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
	
	var_dump($entity);
	
	foreach($entity as $column => $value)
	{
		var_dump($column);
		var_dump($value);
		if($i==0)
		{
			$columns[] = $column;
		}
		
		$body.="<td>$value</td>";
	}
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





