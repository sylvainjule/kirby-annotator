<?php require_once(__DIR__ . DS . 'translations' . DS . 'helper.php'); ?>

<style>
.imageannotator-marker,
[data-field="imageannotator"] .structure-entries span.index {
    background: #ff6645;
    color: white;
}
</style>


<div class="structure<?php e($field->readonly(), ' structure-readonly') ?>" 
  data-field="imageannotator" 
  data-api="<?php __($field->url('sort')) ?>" 
  data-sortable="<?php e($field->sortable() && $field->entries()->count(), 'true', 'false') ?>"
  data-style="<?php echo $field->style() ?>">

<?php echo $field->headline() ?>

  <?php $src = $field->src();
  		if($image = $field->page()->content()->get($src)->toFile()): ?>
  	<div class="imageannotator-ctn-img" href="<?php __($field->url('add')) ?>">
  		<?php if($field->entries()->count()): $n = 0; foreach($field->entries() as $entry): $n++; ?>
  			<div class="imageannotator-marker grabbable" 
  			     style="left:<?php echo $entry->x() * 100 . '%' ?>; 
  			            top:<?php echo $entry->y() * 100 . '%' ?>;"
  			     data-uid="<?php echo $field->page()->uid() ?>"
  			     data-fieldname="<?php echo $field->name() ?>"
  			     data-entryid="<?php echo $entry->id() ?>"
  			     data-id="<?php echo $entry->markerid() ?>">
  			     <span><?php echo $n ?></span>
  			</div>
  		<?php endforeach; endif; ?>
  		<img class="imageannotator-img" src="<?php echo $image->resize(800)->url() ?>" alt="">
  	</div>
  <?php endif; ?>

  <div class="structure-entries">

    <?php if(!$field->entries()->count()): ?>
    <div class="structure-empty">
      <?php echo imageannotatorTranslation('empty') ?>
    </div>
    <?php else: ?>
    <?php require(__DIR__ . DS . 'styles' . DS . $field->style() . '.php') ?>
    <?php endif ?>
  </div>

</div>

<script>
	$(document).ready(function() { initDraggableMarkers() });
</script>