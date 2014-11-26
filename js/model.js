/**
 * Repräsentiert eine Person
 * @param id ID
 * @param forename Vorname
 * @param surname Nachname
 * @param department Abteilungs-ID
 * @param field_service Außendienst (als boolean)
 * @param remaining_holiday verbleibende Urlaubstage;
 * @param role Mitarbeiter-Typ 1: Mitarbeiter, 2: Abteilungsleiter, 3: Geschäftsleitung
 */
var Person = function(id, forename, surname, department, field_service,
		remaining_holiday, role) {

	this.id = id;
	this.forename = forename;
	this.surname = surname;
	this.department = department;
	this.field_service = field_service;
	this.remaining_holiday = remaining_holiday;
	this.role = role;
};

/**
 * Repräsentiert eine Abteilung
 * @param id ID
 * @param name Name der Abteilung
 */
var Department = function(id, name) {

	this.id = id;
	this.name = name;
};

/**
 * Repräsentiert einen Urlaubsantrag
 * @param id ID
 * @param start Start-Datum
 * @param end End-Datum
 * @param person Antragsteller
 * @param substitutes Liste von Personen-IDs der Vertretungen
 * @param type Art des Urlaubsantrags 1: Urlaub, 2: Freizeit, 3: Sonderurlaub
 * @param status Status des Urlaubsantrags 1: angenommen, 2: wartend, 3: abgelehnt
 * @param comment Kommentar bei Ablehnung
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