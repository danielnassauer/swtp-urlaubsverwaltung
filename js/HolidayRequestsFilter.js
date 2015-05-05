/**
 * Filtert HolidayRequests anhand von Filterfunktionen.
 * 
 * @param requests
 *            Array von HolidayRequests, die gefiltert werden sollen.
 * @param filters
 *            Array von Dictionaries mit Filterfunktionen und Attachments. Eine
 *            Filterfunktion bekommt einen HolidayRequest und ein Attachment
 *            übergeben und gibt true zurück, wenn dieser übernommen werden
 *            soll. Bei einem leeren Array werden alle HolidayRequests
 *            übernommen. In Attachments stehen zusätzliche Informationen für
 *            die Filterfunktionen. Jeder Filterfunktion wird zusätzlich zum
 *            HolidayRequest das Attachment übergeben. Ist das Attachment null,
 *            wird nur der HolidayRequest übergeben.
 *            Beispiel: [{"filter": isSubstituteFilter, "attachment": 42}, ...]
 * @return Array mit gefilterten HolidayRequests.
 */
function filterHolidayRequests(requests, filters) {
	var requ = [];
	for (var i = 0; i < requests.length; i++) {
		var accept = true;
		for (var j in filters) {
			var filter = filters[j]["filter"];
			var attachment = filters[j]["attachment"];
			if (attachment == null) {
				accept = filter(requests[i]);
			} else {
				accept = filter(requests[i], attachment);
			}
			if (!accept) {
				break;
			}
		}
		if (accept) {
			requ.push(requests[i]);
		}
	}
	return requ;
}

/**
 * Filtert HolidayRequests anhand des Departments.
 * 
 * @param request
 *            HolidayRequest
 * @param attachment
 *            Dictionary: "department": Name der Abteilung, "persons": Array von
 *            allen Personen
 * @returns true, wenn das Department übereinstimmt.
 */
function departmentFilter(request, attachment) {
	department = attachment["department"];
	persons = attachment["persons"];
	for (var i = 0; i < persons.length; i++) {
		if (persons[i].id == request.person) {
			return persons[i].department == department;
		}
	}
}

/**
 * Filtert HolidayRequests, deren Status = waiting ist.
 * 
 * @param request
 * @returns {Boolean} true, wenn der Status = waiting ist.
 */
function waitingStatusFilter(request) {
	return request.status == 2;
}

/**
 * Filtert HolidayRequests, deren Status = accepted ist.
 * 
 * @param request
 * @returns {Boolean} true, wenn der Status = accepted ist.
 */
function acceptedStatusFilter(request) {
	return request.status == 1;
}

/**
 * Filtert HolidayRequests, deren Status = declined ist.
 * 
 * @param request
 * @returns {Boolean} true, wenn der Status = declined ist.
 */
function declinedStatusFilter(request) {
	return request.status == 3;
}

/**
 * Filtert HolidayRequests anhand der Vertretungen.
 * 
 * @param request
 *            HolidayRequest
 * @param person_id
 *            ID der Vertetung
 * @returns {Boolean} true, wenn Person als Vertretung vorkommt.
 */
function isSubstituteFilter(request, person_id) {
	return person_id in request.substitutes;
}

/**
 * Filtert HolidayRequests anhand des Antragstellers
 * 
 * @param request
 *            HolidayRequest
 * @param person_id
 *            ID des Antragstellers
 * @returns {Boolean} true, wenn die Person der Antragsteller des Requests ist.
 */
function isRequesterFilter(request, person_id) {
	return request.person == person_id;
}

/**
 * Filtert HolidayRequests anhand des Antragstellers
 * 
 * @param request
 *            HolidayRequest
 * @param person_id
 *            ID des Antragstellers
 * @returns {Boolean} true, wenn die Person nicht der Antragsteller des Requests ist.
 */
function withoutMeFilter(request, person_id){
		return request.person != person_id;
}

/**
 * Filtert HolidayRequests nach Mitarbeitern
 * 
 * @param request
 *            HolidayRequest
 * @param attachment
 *            Dictionary: "persons": Array von allen Personen
 * @returns {Boolean} true, wenn die Person ein Mitarbeiter ist.
 */
function employeeFilter(request, attachment) {
			persons = attachment["persons"];
			for(var i = 0; i < persons.length; i++){
				if(request.person == persons[i].id){
					return persons[i].role == 1;
				}
			}
}



/**
 * Filtert HolidayRequests nach Abteilungsleitern
 * 
 * @param request
 *            HolidayRequest
 * @param attachment
 *            Dictionary: "persons": Array von allen Personen
 * @returns {Boolean} true, wenn die Person ein Abteilungsleiter ist.
 */
function abteilungsleiterFilter(request, attachment) {
			persons = attachment["persons"];
			for(var i = 0; i < persons.length; i++){
				if(request.person == persons[i].id){
					return (persons[i].role == 2 || persons[i].role == 3);
				}
			}
}


/**
 * Filtert HolidayRequests, deren Status = wartend oder angenommen ist.
 * 
 * @param request
 * @returns {Boolean} true, wenn der Status = wartend oder angenommen ist.
 */
function readyStatusFilter(request) {
	return (request.status == 1 || request.status == 2);
}


/**
 * Filtert HolidayRequests, in denen der Status der Vertretungen = angenommen ist.
 * 
 * @param request
 * @returns {Boolean} true, wenn der Status der Vertretungen = angenommen ist, oder keine Vertretung angegeben wurde.
 */
function substituteAcceptedFilter(request){
	if(Object.keys(request.substitutes).length > 0){
		for(var substitute in request.substitutes){
			return (request.substitutes[substitute] == 2);
		}
	} else return Object.keys(request.substitutes).length == 0;
}

function substituteDeclinedFilter(request, person_id){
	if(Object.keys(request.substitutes).length > 0){
		for(person_id in request.substitutes){
		return (request.substitutes[person_id] == 1 || request.substitutes[person_id] == 2);
		}

	}
}
