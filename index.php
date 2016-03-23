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

echo "Methods:<br/>";
var_dump(get_class_methods($result));

echo "Next Partition:<br/>";
var_dump($result->getNextPartitionKey());

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

$table = "
	<table id='vanmanTable' class='table table-striped table-bordered'>
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
<head>
	<script type="text/javascript" src="assets\DataTables\jQuery-2.2.0\jquery-2.2.0.js"></script>
	<link rel="stylesheet" type="text/css" href="assets\DataTables/datatables.css"/>
 
	<script type="text/javascript" src="assets\DataTables/datatables.js"></script>
	<script>
		$(function(){
			$("#vanmanTable").DataTable({
				dom: 'Bfrtip',
				buttons: [
					'colvis',
					'excel',
					'print'
				]
			});
		});
	</script>
</head>
<body>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Rollins Vanity Urls</h3>
		</div>
		  
		<div class="panel-body">
			<?php echo $table; ?>
		</div>
	</div>
</body>






