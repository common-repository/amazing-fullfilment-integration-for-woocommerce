jQuery(document).ready(function($) {
	/**
	 * Logs data table
	 */
	$('#logList').DataTable({
		"processing" : true,
		"serverSide" : true,
		"columns" : [ 
			{ "data" : "time",    "orderable" : false, "searchable" : true, "width" : "150px" },
			{ "data" : "message", "orderable" : false, "searchable" : true}
		],
		"order": [[ 0, "desc" ]],
		"pagingType" : "simple_numbers",
		"lengthMenu" : [ [ 15, 50, 200 ], [ 15, 50, 200 ] ],
		"ajax" : {
			"url" : amzFulfillmentLogs.ajax_url,
			"type" : "POST",
			"data" : {
				'action' : 'amzFulfillmentLogs'
			}
		}
	});
});
