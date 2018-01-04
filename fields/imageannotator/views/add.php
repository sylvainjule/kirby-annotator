<div class="modal-content modal-content-<?php echo $modalsize ?>">
  <?php echo $form ?>
</div>


<script>
	(function() {
	    app.modal.root.on('setup', function () {
		    var form = $('.modal .form');
		    var idField = form.find('input[name=markerid]');
		    var xField = form.find('input[name=x]');
		    var yField = form.find('input[name=y]');

		    var _field = form.attr('data-field'),
		    	_field = $('.field-name-'+ _field),
		    	_marker = _field.find('.imageannotator-marker').last(),
		    	_x = _marker.attr('data-x'),
		    	_y = _marker.attr('data-y'),
		    	_id= _marker.attr('data-id');

		    idField.val(_id);
		    xField.val(_x);
		    yField.val(_y);
	  });
	})();
</script>