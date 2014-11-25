/**
 * Sendet einen GET-Request an den angegebenen Pfad und parset die JSON-Antwort.
 * 
 * @param path
 *            Pfad
 * @returns JavaScript-Wert des JSON-Strings
 */
function GET(path) {
	var response = $.ajax({
		type : "GET",
		url : path,
		async : false
	}).responseText;
	return $.parseJSON(response);
}

/**
 * Sendet einen PUT-Request an den angegebenen Pfad .
 * 
 * @param path
 *            Pfad
 * @param content
 *            der HTTP-Body, der gesendet wird
 */
function PUT(path, content) {
	$.ajax({
		type : "PUT",
		url : path,
		data : JSON.stringify(content),
		async : false
	});
}

/**
 * Sendet einen POST-Request an den angegebenen Pfad und parset die
 * JSON-Antwort.
 * 
 * @param path
 *            Pfad
 * @returns JavaScript-Wert des JSON-Strings
 */
function POST(path, content) {
	var response = $.ajax({
		type : "POST",
		url : path,
		data : JSON.stringify(content),
		async : false
	}).responseText;
	return $.parseJSON(response);
}
