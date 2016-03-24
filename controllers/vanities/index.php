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
		$error = [
			"error" => "There was a problem editing your stuff."
		];
		echo json_encode($error);
	}
	else
	{
		echo json_encode([]);
	}
}