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

//////////////////////////////////////////////////
// All PHP style functions after this line!		//
//////////////////////////////////////////////////
function json_encode(mixed_val) {
  var retVal, json = this.window.JSON;
  try {
    if (typeof json === 'object' && typeof json.stringify === 'function') {
      retVal = json.stringify(mixed_val); // Errors will not be caught here if our own equivalent to resource
      //  (an instance of PHPJS_Resource) is used
      if (retVal === undefined) {
        throw new SyntaxError('json_encode');
      }
      return retVal;
    }

    var value = mixed_val;

    var quote = function(string) {
      var escapable =
        /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
      var meta = { // table of character substitutions
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"': '\\"',
        '\\': '\\\\'
      };

      escapable.lastIndex = 0;
      return escapable.test(string) ? '"' + string.replace(escapable, function(a) {
        var c = meta[a];
        return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0)
          .toString(16))
          .slice(-4);
      }) + '"' : '"' + string + '"';
    };

    var str = function(key, holder) {
      var gap = '';
      var indent = '    ';
      var i = 0; // The loop counter.
      var k = ''; // The member key.
      var v = ''; // The member value.
      var length = 0;
      var mind = gap;
      var partial = [];
      var value = holder[key];

      // If the value has a toJSON method, call it to obtain a replacement value.
      if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
        value = value.toJSON(key);
      }

      // What happens next depends on the value's type.
      switch (typeof value) {
        case 'string':
          return quote(value);

        case 'number':
          // JSON numbers must be finite. Encode non-finite numbers as null.
          return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':
          // If the value is a boolean or null, convert it to a string. Note:
          // typeof null does not produce 'null'. The case is included here in
          // the remote chance that this gets fixed someday.
          return String(value);

        case 'object':
          // If the type is 'object', we might be dealing with an object or an array or
          // null.
          // Due to a specification blunder in ECMAScript, typeof null is 'object',
          // so watch out for that case.
          if (!value) {
            return 'null';
          }
          if ((this.PHPJS_Resource && value instanceof this.PHPJS_Resource) || (window.PHPJS_Resource &&
            value instanceof window.PHPJS_Resource)) {
            throw new SyntaxError('json_encode');
          }

          // Make an array to hold the partial results of stringifying this object value.
          gap += indent;
          partial = [];

          // Is the value an array?
          if (Object.prototype.toString.apply(value) === '[object Array]') {
            // The value is an array. Stringify every element. Use null as a placeholder
            // for non-JSON values.
            length = value.length;
            for (i = 0; i < length; i += 1) {
              partial[i] = str(i, value) || 'null';
            }

            // Join all of the elements together, separated with commas, and wrap them in
            // brackets.
            v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind +
              ']' : '[' + partial.join(',') + ']';
            gap = mind;
            return v;
          }

          // Iterate through all of the keys in the object.
          for (k in value) {
            if (Object.hasOwnProperty.call(value, k)) {
              v = str(k, value);
              if (v) {
                partial.push(quote(k) + (gap ? ': ' : ':') + v);
              }
            }
          }

          // Join all of the member texts together, separated with commas,
          // and wrap them in braces.
          v = partial.length === 0 ? '{}' : gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' :
            '{' + partial.join(',') + '}';
          gap = mind;
          return v;
        case 'undefined':
          // Fall-through
        case 'function':
          // Fall-through
        default:
          throw new SyntaxError('json_encode');
      }
    };

    // Make a fake root object containing our value under the key of ''.
    // Return the result of stringifying the value.
    return str('', {
      '': value
    });

  } catch (err) { // Todo: ensure error handling above throws a SyntaxError in all cases where it could
    // (i.e., when the JSON global is not available and there is an error)
    if (!(err instanceof SyntaxError)) {
      throw new Error('Unexpected error type in json_encode()');
    }
    this.php_js = this.php_js || {};
    this.php_js.last_error_json = 4; // usable by json_last_error()
    return null;
  }
}

function json_decode(str_json) {
	var json = this.window.JSON;
	if (typeof json === 'object' && typeof json.parse === 'function') {
		try {
			return json.parse(str_json);
		} catch (err) {
			if (!(err instanceof SyntaxError)) {
				throw new Error('Unexpected error type in json_decode()');
			}
			this.php_js = this.php_js || {};
			this.php_js.last_error_json = 4; // usable by json_last_error()
			return null;
		}
	}
	
	var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
	var j;
	var text = str_json;

	cx.lastIndex = 0;
	if (cx.test(text)) {
		text = text.replace(cx, function(a) {
			return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
		});
	}

	if ((/^[\],:{}\s]*$/).test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
		j = eval('(' + text + ')');
		return j;
	}

	this.php_js = this.php_js || {};
	this.php_js.last_error_json = 4; // usable by json_last_error()
	return null;
}