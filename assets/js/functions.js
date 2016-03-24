//////////////////////////////////////////////////
// All the best functions after this line!		//
//////////////////////////////////////////////////
/**
* @title jsonDataTable
*
* @description This function will send an ajax request to the "url"
* 					you specify, which should echo a json_encode() array
* 					of results, ie: from a SQL runQuery; and auto populate
*					a table with the "id" specified with the column headers
*					being the associated keys of the json object, and the 
*					data being loaded from the json object to the rows of 
*					the table.
* @params 
*		param(id) -> Either the 'id' of the table to use,
*					or the 'param' object with all available
*					options, including 'id' and all other 
*					parameters.
*			*id -> The id of the table you want to apply 
*					everything to. 
*			*url -> The url of the ajax page which will echo
*					the associated json_encode() object.
*			*postObj -> The object that is passed to the ajax
*					page.
*			*dataTableOpts -> The datatable custom options after it
*					passes the "data" and "columns" options.
*/
function jsonDataTable(param,url,postObj,dataTableOpts)
{
	var tableId= "";
	
	if(typeof param === 'object')
	{
		if('id' in param && 'url' in param)
		{
			tableId=param.id;
			url=param.url;
			postObj=('postObj' in param ? param.postObj : {} );
			dataTableOpts=('dataTableOpts' in param ? param.dataTableOpts : {} );
			tables=('tables' in param ? param.tables : {} );
		}
		else
		{
			console.log("%c Missing Parameters for genTable() ",'background: #222; color: #bada55');
			return false;
		}
	}
	else
	{
		tableId=param;
	}
	var poundId = tableId;
	if(tableId.substring(0,1) !== "#")
		poundId = "#" + tableId;
	else
		tableId = tableId.substring(1);
		
	if(!$(poundId).is("table"))
	{
		tableId=tableId+"Table";
		
		if(!$("#"+tableId).length)
		{
			$(poundId).append("<table id='"+tableId+"' class='table table-bordered table-condensed hover no-footer dontFuckWithMyColumn'></table>");
		}
		poundId = poundId+"Table";
		
		
		
	}
	
	var $table = $(poundId);
	if(typeof window["dataTable_"+tableId] === 'undefined')
	{
		$table.html("<thead><tr><th>Loading...</th></tr></thead>");
		window["dataTable_"+tableId] = $("#"+tableId+"").DataTable({});
		// console.log("making datatable");
	}
	else
	{
		
		window["dataTable_"+tableId] = $("#"+tableId+"").DataTable({});
	}
	
	var datatableObj = {};
	
	
	
	$table.find('tbody').html("<tr><td>Loading...</td></tr>");
	
	var postTest = $.post(url, postObj, function(response) {
		datatableObj = json_decode(response);
		
		$table.find('td, tbody').remove();
		
		if(datatableObj!=null && Object.keys(datatableObj).length>0)
		{
			var datatableColumns = [
				
			];
			
			window["dataTable_"+tableId].destroy();
			$table.find('thead tr').empty();
			// thead tr was just clear so no th's to loop through?
			// $.each($(poundId+" thead th"), function(i,val){
				
				// datatableColumns.push({data:$(this).html()});
			// });
			
			$.each(datatableObj[0], function(index, value){
				
				if($table.find("thead th:contains('" + index + "')").html()==index)
				{
					datatableColumns.splice($table.find("thead th:contains('" + index + "')").index(),1,{data:index});
				}
				else
				{
					$table.find("thead tr").append("<th>"+index+"</th>");
					datatableColumns.push({data:index});
				}
			});
			
			var dataTableValues = {
				data:datatableObj,
				columns:datatableColumns
			}
			$.extend(dataTableValues,dataTableOpts)
			
			window["dataTable_"+tableId] = $table.DataTable(dataTableValues);
		}
		else if(datatableObj==null && response=="null")
		{
			$table.html("<thead><tr><th>No Results Found</th></tr></thead>");
		}
		else
		{
			window["dataTable_"+tableId].draw();
			showError(response,"Mistakes were made....");
		}
	});	
}