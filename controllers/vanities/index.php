<?
if(isset($_POST["action"]))
{
	echo json_encode([
		"data" => $_POST["data"]
		]);
}