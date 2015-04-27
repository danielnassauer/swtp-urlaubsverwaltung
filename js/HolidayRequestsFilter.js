/* Filtert HolidayRequests anhand von Filterfunktionen.
 * @param requests Array von HolidayRequests, die gefiltert werden sollen.
 * @param filters Array von Dictionaries mit Filterfunktionen und Attachments. Eine Filterfunktion bekommt 
 *        einen HolidayRequest und ein Attachment übergeben und gibt true zurück, wenn dieser 
 *        übernommen werden soll. Bei einem leeren Array werden alle 
 *        HolidayRequests übernommen. In Attachments stehen zusätzliche Informationen für die Filterfunktionen.
 * @return Array mit gefilterten HolidayRequests.
 */
function filterHolidayRequests(requests, filters) {
	var requ = [];
	for (var i = 0; i < requests.length; i++) {
		var accept = true;
		for (filter in filters) {
			var attachment = filters[filter];
			if(attachment == null){
				accept = filter(requests[i]);
			}else{
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
 * 
 * @param request
 * @param attachment
 *            Dictionary. Keys: department (Name der Abteilung), persons (Array
 *            von allen Personen)
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

function waitingStatusFilter(request){
	return request.status == 2;
}