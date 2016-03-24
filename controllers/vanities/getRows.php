<?php

require_once 'azure\WindowsAzure\WindowsAzure.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Table\Models\QueryEntitiesOptions;
use WindowsAzure\Table\Models\Filters\Filter;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=vanman;AccountKey=QdWBBF/0E+rYpBrk5YC0kyV7CxRgnP9CP0AhQG4Q9R8cDIFIbIyHHwoK3I+GgAlfOb4V7ifiDZ6BRBDsGvefIQ==";


// Create table REST proxy.
$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

$filter = "";
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

// echo "Methods:<br/>";
// var_dump(get_class_methods($result));

// echo "Next Partition:<br/>";
// var_dump($result->getNextPartitionKey());

$entities = $result->getEntities();
$nextPK=$result->getNextPartitionKey();
$nextRK=$result->getNextRowKey();

// echo "Count:".count($entities)." Next PK:".$nextPK." RK:".$nextRK."<br>\n";

while ($nextPK <> NULL && $nextPK <> "" ) {
    $options = new QueryEntitiesOptions();
    $options->setFilter(Filter::applyQueryString($filter));
    $options->setNextPartitionKey($nextPK);
    $options->setNextRowKey($nextRK);
    $result = $tableRestProxy->queryEntities($scale, $options);        
    $nextPK=$result->getNextPartitionKey();
    $nextRK=$result->getNextRowKey();
    $newentities=$result->getEntities();       

    // echo "Count:".count($newentities)." Next PK:".$nextPK." RK:".$nextRK."<br>\n";
    $entities=array_merge($newentities, $entities);    
}

$i=0;
$columns = ["Row Number"];
$data = [];


foreach($entities as $entity){
	
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
		
		if($column == "Row Number"){
			$value = $i+1;
		}
		else if ($value instanceof DateTime) {
			$value = $value->format("d-m-Y h:i:s a");
		}
		elseif($column == "RowKey")
		{
			$value = base64_decode($value);
		}
		
		$data[] =[ "$column" => "$value" ];
	}
	
	// echo "Methods:<br/>";
	// var_dump(get_class_methods($entity));
	
	$i++;
}

echo var_dump($data);
?>