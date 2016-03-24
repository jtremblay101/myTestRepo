<?
if(isset($_POST["action"]))
{
	$action = $_POST["action"];
	if($action == "create")
	{
		echo json_encode([
			"data" => $_POST["data"]
			]);			
	}
	elseif($action == "edit")
	{
		echo '
			{"data":{"0":{"Row Number":"1","PartitionKey":"","RowKey":"44pestthrea","Timestamp":"06-12-2012 06:06:16 pm","Options":"0","Destination":"http:\/\/www.pestthreat.com"}}}
		';	
	}
	else
	{
		echo json_encode([]);
	}
}