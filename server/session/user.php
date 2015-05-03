<?php
require_once dirname ( __FILE__ ) . '/Session.php';

$id = Session::getUserID ();

if ($id != null) {
	echo '<script type="text/javascript">
	var user = getPerson(' . $id . ');
</script>';
} else {
	echo '<script type="text/javascript">
	var user = null;
</script>';
}

?>