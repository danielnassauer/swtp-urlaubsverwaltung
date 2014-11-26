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
 *            Java-Wert. Wird automatisch in JSON umgewandelt
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
 * @param content
 *            Java-Wert. Wird automatisch in JSON umgewandelt
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

/**
 * Liefert die Person zu einer ID.
 * 
 * @param id
 *            ID
 * @returns {Person} Person als Person-Objekt
 */
function getPerson(id) {
	var data = GET("Person/" + id);
	return new Person(data["id"], data["forename"], data["surname"],
			data["department"], data["field_service"],
			data["remaining_holiday"], data["role"]);
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
		persons.push(new Person(data["id"], data["forename"], data["surname"],
				data["department"], data["field_service"],
				data["remaining_holiday"], data["role"]));
	});
	return persons;
}

/**
 * Liefert die Abteilung zu einer ID.
 * 
 * @param id
 *            ID
 * @returns {Department} Abteilung als Department-Objekt
 */
function getDepartment(id) {
	var data = GET("Department/" + id);
	return new Department(data["id"], data["name"]);
}

/**
 * Liefert alle Abteilungen als Liste.
 * 
 * @returns {Array} Liste von Abteilungen als Department-Objekt
 */
function getDepartments() {
	var recv = GET("Department");
	var departments = [];
	recv.forEach(function(data) {
		departments.push(new Department(data["id"], data["name"]));
	});
	return departments;
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

function createNewHolidayRequest(start, end, person, substitutes, type, status,
		comment) {
	var r = {
		start : start,
		end : end,
		person : person,
		substitutes : substitutes,
		type : type,
		status : status,
		comment : comment
	}
	var data = POST("HolidayRequest", r);
	return new HolidayRequest(data["id"], data["start"], data["end"],
			data["person"], data["substitutes"], data["type"], data["status"],
			data["comment"]);
}

function editHolidayRequest(holidayRequest) {
	PUT("HolidayRequest/" + holidayRequest.id, holidayRequest);
}
