jQuery(document).ready(function ($) {
	/**
	 * Tooltips
	 */
	jQuery('.amzTooltip').tooltip({
		classes: {
			"ui-tooltip": "highlight"
		}
	});

	/**
	 * Show amazon listings
	 */
	jQuery("#amzfulfillment-listings-button").click(function() {
		$("#amzfulfillment-listings").fadeToggle("slow");
	});

	/**
	 * Show package details
	 */
	jQuery(".amzfulfillment-package-button").click(function() {
		var packageNumber = $(this).data("packagenumber");
		var url = window.location.href.replace("#", "");
		window.location.href = url + "&showPackage=" + packageNumber;
	});

	/**
	 * Hide rule item template
	 */
	jQuery("#amzfulfillment-rule-template").hide();

	/**
	 * Add rule item
	 */
	jQuery("#amzfulfillment-rule-add, #amzfulfillment-rule-add-label").click(function() {
		var newItem = $("#amzfulfillment-rule-template").clone();
		newItem.appendTo("#amzfulfillment-rule-container")
		newItem.show();
		newItem.find(".amzfulfillment-remove").click(function(){
			$(this).parent().remove();
		});
		jQuery("#amzfulfillment-rule-empty").hide();
	});

	/**
	 * Deactivate license confirmation dialog
	 */
	$(function() {
		$("#deactivate-confirm").dialog({
			autoOpen: false,
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: {
				"Deactivate": function() {
					window.location.href = window.location.href.replace("#", "") + "&licenseDeactivate";
				}
			}
		});
	});
	$("#deactivate").on("click", function() {
		$("#deactivate-confirm").dialog("open");
	});
});
