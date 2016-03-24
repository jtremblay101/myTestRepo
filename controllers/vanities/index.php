<?
if(isset($_POST["action"]))
{
	echo json_encode($_POST["data"]);
}