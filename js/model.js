/**
 * Repräsentiert eine Person
 * 
 * @param id
 *            ID
 * @param forename
 *            Vorname
 * @param lastname
 *            Nachname
 * @param department
 *            Abteilungs-ID
 * @param field_service
 *            Außendienst (als boolean)
 * @param remaining_holiday
 *            verbleibende Urlaubstage;
 * @param role
 *            Mitarbeiter-Typ 1: Mitarbeiter, 2: Abteilungsleiter, 3:
 *            Geschäftsleitung
 * @param is_admin
 *            Ist true, wenn die Person ein Admin ist
 */
var Person = function(id, forename, lastname, department, field_service,
		remaining_holiday, role, is_admin) {

	this.id = id;
	this.forename = forename;
	this.lastname = lastname;
	this.department = department;
	this.field_service = field_service;
	this.remaining_holiday = remaining_holiday;
	this.role = role;
	this.is_admin = is_admin;
};

/**
 * Repräsentiert einen Urlaubsantrag
 * 
 * @param id
 *            ID
 * @param start
 *            Start-Datum (Unix-Timestamp)
 * @param end
 *            End-Datum (Unix-Timestamp)
 * @param person
 *            ID des Antragstellers
 * @param substitutes
 *            Dictionary mit IDs der Vertretungen als Key und einem Integer als
 *            Wert (1: wartend, 2: angenommen, 3: abgelehnt).
 * @param type
 *            Art des Urlaubsantrags: "Urlaub", "Freizeit", oder eine
 *            Beschreibung für einen Sonderurlaub
 * @param status
 *            Status des Urlaubsantrags 1: angenommen, 2: wartend, 3: abgelehnt,
 *            4: storniert
 * @param comment
 *            Kommentar bei Ablehnung
 */
var HolidayRequest = function(id, start, end, person, substitutes, type,
		status, comment) {
	this.id = id;
	this.start = start;
	this.end = end;
	this.person = person;
	this.substitutes = substitutes;
	this.type = type;
	this.status = status;
	this.comment = comment;
};