
function setAllAuthors() {
    selected = jQuery('#default_author').val();
    jQuery("select[name^='post_author']").val(selected);
}

function setAllCategories() {
    selected = jQuery('#default_category').val();
    jQuery("select[name^='post_category']").val(selected);
}

function setAllStatus(status) {
    jQuery("select[name^='post_status']").val(status);
}

function setAllPostType() {
    selected = jQuery('.default_post_type').val();
	jQuery("select[name^='post_type']").val(selected);
}

jQuery(document).ready(function($) {
    $("#start_time").timepicker({ timeFormat: "h:mm TT" });
	$("#start_date").datepicker({ dateFormat: "dd M yy" });
});