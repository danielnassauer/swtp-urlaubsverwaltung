<?php
if (isset ( $_SESSION ['user'] )) {
	echo '<script type="text/javascript">
	var user = getPerson(' . $_SESSION ["user"] . ');		
</script>';
}
?>