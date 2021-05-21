<?php

?>
<div class="wrap">

	<h1>PGP Sign & Verify</h1>
	<h2>Documents signed by PGP:</h2>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="pgpsign-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Now we can render the completed list table -->
		<?php
			$pgpsign_list_table->search_box( 'search', 'search_id' ); 
			$pgpsign_list_table->display()
		?>
	</form>
	
</div>