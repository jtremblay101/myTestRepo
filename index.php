<head>
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="assets\DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="assets\DataTables/Editor-PHP-1.5.5/css/editor.dataTables.css">
	<link rel="stylesheet" type="text/css" href="assets\js\jquery-ui-1.11.4/jquery-ui.css">

	<!-- JS -->
	<script type="text/javascript" src="assets\DataTables\jQuery-2.2.0\jquery-2.2.0.js"></script>
	<script type="text/javascript" src="assets\js\jquery-ui-1.11.4\jquery-ui.js"></script>
	
	
	<script type="text/javascript" src="assets\DataTables/datatables.js"></script>
	<script type="text/javascript" src="assets\DataTables/Editor-PHP-1.5.5/js/dataTables.editor.js"></script>
	
	<script type="text/javascript" src="assets\js/functions.js"></script>
	
	<script>
		var editor; // use a global for the submit and return data rendering in the examples
		$(function(){
			
 
			editor = new $.fn.dataTable.Editor( {
				ajax: "controllers/vanities/index.php",
				table: "#vanmanTable",
				idSrc: "Row Number",
				fields: [ {
						label: "Row Key:",
						name: "RowKey"
					}, {
						label: "Destination:",
						name: "Destination"
					}
				]
			} );
			
			jsonDataTable({
				id: "vanmanTable"
				,url: "controllers/vanities/getRows.php"
				,postObj :{}
				,dataTableOpts: {
					dom: 'Bflrtip'
					,buttons: [
						'colvis'
						,'excel'
						,'print'
						, { extend: "create", editor: editor },
						{ extend: "edit",   editor: editor },
						{ extend: "remove", editor: editor }
					]
					,select: true
				}
			});
			
			var editor = new $.fn.dataTable.Editor( {} );
		});
	</script>
</head>
<body>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Rollins Vanity Urls</h3>
		</div>
		  
		<div class="panel-body">
			<table id="vanmanTable" class="table table-striped table-bordered">
			
			</table>
		</div>
	</div>
</body>






