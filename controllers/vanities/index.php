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
		echo json_encode([
			"data" => $_POST["data"]
			]);		
	}
	else
	{
		echo json_encode([]);
	}
}