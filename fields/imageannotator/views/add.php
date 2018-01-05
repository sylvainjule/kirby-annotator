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
	    // Delete pin on click on 'Cancel'
	    app.modal.root.on('remove', function() {
	    	$('.imageannotator-ctn-img').each(function() {
				var _this = $(this),
					_markers = _this.find('.imageannotator-marker'),
					_last = _markers.last(),
					_markersCount = _markers.length,
					_entries = _this.next('.structure-entries').find('tbody tr');
					_entriesCount = _entries.length;

				if (_markersCount > _entriesCount) {
					_last.remove();
				}

			});
	    });
	})();
</script>