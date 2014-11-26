<html>
<head>
<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/model.js"></script>
<script type="text/javascript" src="js/client.js"></script>
</head>
<body>

	<script type="text/javascript">
	console.log(getPerson(1));
	console.log(getPersons());
	console.log(getDepartment(1));
	console.log(getDepartments());
	console.log(getHolidayRequest(1));
	console.log(getHolidayRequests());
	var r = createNewHolidayRequest("start", "end", 1, [2,3,4], 1, 1,"");
	console.log(r);
	r.start = "x";
	editHolidayRequest(r);	
	</script>

</body>
</html>