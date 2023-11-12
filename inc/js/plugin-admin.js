document.addEventListener("DOMContentLoaded", function() {
    var select = document.querySelector("[name=\'rp_post_type\']");
    var selectedOption = select.options[select.selectedIndex];
    selectedOption.selected = true;
});