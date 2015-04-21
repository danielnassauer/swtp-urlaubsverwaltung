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
	console.log(response);
	return $.parseJSON(response);
}

/**
 * Sendet einen PUT-Request an den angegebenen Pfad .
 * 
 * @param path
 *            Pfad
 * @param content
 *            Java-Wert. Wird automatisch in JSON umgewandelt
 */
function PUT(path, content) {
	console.log("PUT (" + path + "):" + content);
	var response = $.ajax({
		type : "PUT",
		url : path,
		data : JSON.stringify(content),
		async : false
	}).responseText;
	console.log(response);
}

/**
 * Sendet einen POST-Request an den angegebenen Pfad und parset die
 * JSON-Antwort.
 * 
 * @param path
 *            Pfad
 * @param content
 *            Java-Wert. Wird automatisch in JSON umgewandelt
 * @returns JavaScript-Wert des JSON-Strings
 */
function POST(path, content) {
	console.log("POST (" + path + "):" + content);
	var response = $.ajax({
		type : "POST",
		url : path,
		data : JSON.stringify(content),
		async : false
	}).responseText;
	console.log(response);
	return $.parseJSON(response);
}

/**
 * Liefert die Person zu einer ID.
 * 
 * @param id
 *            ID
 * @returns {Person} Person als Person-Objekt
 */
function getPerson(id) {
	var data = GET("Person/" + id);
	return new Person(data["id"], data["forename"], data["lastname"],
			data["department"], data["field_service"],
			data["remaining_holiday"], data["role"], data["is_admin"]);
}

/**
 * Liefert alle Personen als Liste.
 * 
 * @returns {Array} Liste von Personen als Person-Objekt
 */
function getPersons() {
	var recv = GET("Person");
	var persons = [];
	recv.forEach(function(data) {
		persons.push(new Person(data["id"], data["forename"], data["lastname"],
				data["department"], data["field_service"],
				data["remaining_holiday"], data["role"], data["is_admin"]));
	});
	return persons;
}

/**
 * Ändert eine Person.
 * 
 * @param id
 *            ID der Person, die geändert werden soll.
 * @param field_service
 *            true, wenn die Person im Außendienst tätig sein soll.
 * @param remaining_holiday
 *            neue Anzahl der verbleibenden Urlaubstage.
 * @param role
 *            neue Rolle der Person 1: Mitarbeiter, 2: Abteilungsleiter, 3:
 *            Geschäftsleitung
 * @param is_admin
 *            true, wenn die Person ein Admin sein soll
 */
function editPerson(id, field_service, remaining_holiday, role, is_admin) {
	var p = {
		id : id,
		field_service : field_service,
		remaining_holiday : remaining_holiday,
		role : role,
		is_admin : is_admin
	}
	PUT("Person/" + id, p);
}

/**
 * Liefert einen UrlaubsAntrag zu einer ID.
 * 
 * @param id
 *            ID
 * @returns {HolidayRequest} Urlaubsantrag als HolidayRequest-Objekt
 */
function getHolidayRequest(id) {
	var data = GET("HolidayRequest/" + id);
	return new HolidayRequest(data["id"], data["start"], data["end"],
			data["person"], data["substitutes"], data["type"], data["status"],
			data["comment"]);
}

/**
 * Liefert alle Urlaubsanträge als Liste.
 * 
 * @returns {Array} Liste von Urlaubsanträgen als HolidayRequest-Objekt
 */
function getHolidayRequests() {
	var recv = GET("HolidayRequest");
	var requests = [];
	recv.forEach(function(data) {
		requests.push(new HolidayRequest(data["id"], data["start"],
				data["end"], data["person"], data["substitutes"], data["type"],
				data["status"], data["comment"]));
	});
	return requests;
}

/**
 * Erzeugt einen neuen HolidayRequest.
 * 
 * @param start
 *            Start-Datum (Unix-Timestamp)
 * @param end
 *            End-Datum (Unix-Timestamp)
 * @param person
 *            ID des Antragstellers
 * @param substitutes
 *            Dictionary mit Personen-IDs der Vertretungen als Key und True als
 *            Wert, wenn die Vertretung zugestimmt hat, ansonsten False
 * @param type
 *            Art des Urlaubsantrags: "Urlaub", "Freizeit", oder eine
 *            Beschreibung für einen Sonderurlaub
 * @returns {HolidayRequest} neu erzeugter HolidayRequest mit neuer ID
 */
function createHolidayRequest(start, end, person, substitutes, type) {
	var r = {
		start : start,
		end : end,
		person : person,
		substitutes : substitutes,
		type : type
	}
	var data = POST("HolidayRequest", r);
	return new HolidayRequest(data["id"], data["start"], data["end"],
			data["person"], data["substitutes"], data["type"], data["status"],
			data["comment"]);
}

/**
 * Ändert einen bestehenden HolidayRequest ab.
 * 
 * @param id
 *            ID des HolidayRequests, der abgeändert werden soll
 * @param start
 *            neues Start-Datum (Unix-Timestamp)
 * @param end
 *            neues End-Datum (Unix-Timestamp)
 * @param substitutes
 *            Dictionary mit Personen-IDs der Vertretungen als Key und True als
 *            Wert, wenn die Vertretung zugestimmt hat, ansonsten False
 * @param status
 *            neuer Status des Urlaubsantrags 1: angenommen, 2: wartend, 3:
 *            abgelehnt, 4: storniert
 * @param comment
 *            neuer Kommentar
 */
function editHolidayRequest(id, start, end, substitutes, status, comment) {
	var r = {
		id : id,
		start : start,
		end : end,
		substitutes : substitutes,
		status : status,
		comment : comment
	}
	PUT("HolidayRequest/" + id, r);
}
