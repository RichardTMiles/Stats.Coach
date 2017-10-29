function bootstrapAlert(message, level) {
    if(level === null) level = 'info';
    var container = document.getElementById('alert'),
        node = document.createElement("DIV"), text;

    text = level.charAt(0).toUpperCase() + level.slice(1);

    if (container === null)
        return false;

    node.innerHTML = '<div id="row"><div class="alert alert-' + level + ' alert-dismissible">' +
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
        '<h4><i class="icon fa fa-' + (level === "danger" ? "ban" : (level === "success" ? "check" : level)) + '"></i>'+text+'!</h4>' + message + '</div></div>';

    container.innerHTML = node.innerHTML + container.innerHTML;
}