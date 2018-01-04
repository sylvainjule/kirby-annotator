# Image annotator - Pin notes to images

<br>
This plugin is a tweaked structure field allowing you to add notes to images by pinning them to specific coordinates. Suggestions welcome.

<br><br>

![kirby-imageannotator](https://user-images.githubusercontent.com/14079751/34582368-dfeb79d8-f193-11e7-9360-3e71196a01fb.jpg)

<br>

## Overview

- Add a pin by clicking anywhere on the image. It acts as a trigger for adding a structure entry, and therefore opens a modal.
- Pins can be dragged once added (*Important : if a new pin has just been added, you'll need to  save the page in order to be able to drag the pin*).
- Pins are deleted when its associated structure entry is.
- Pins index is updated when structure entries are sorted.
- Pins background color can easily be changed


## Installation
Put the content of this repo in the `site/plugins` directory.  
The plugin folder must be named `imageannotator` :

```
|-- site/fields/
    |-- imageannotator/
        |-- fields/
        |-- routes/
        |-- imageannotator.php
```

## Blueprint usage

Basic usage in blueprint:

```yaml
  imagefield:
    label: The image that the annotator field will use
    type: image
  annotatorfield:
    label: Field label
    type: imageannotator
    src: imagefield
    fields: 
      markerid:
        type: hidden
      x:
        type: hidden
      y:
        type: hidden
      customfield:
        label: Note
        type: text
```

### Notes :

- The ```markerid```, ```x```and ```y```fields **must be specified** within the annotator fields. There currently cannot be renamed. They can be ```hidden``` or in ```readonly```mode if you wish to display them.
- Any field outputting a single image filename can be used as a source (ie. the ```select``` field, ```quickselect```, etc.)


## Front-end usage

The field can be dealt with as a regular structure field. Each entry will have a ```x``` and ```y``` value, formatted like this :

```yaml
  x: 0.50 #(if 50% from the left)
  y: 0.50 #(if 50% from the top)
```

A very basic example to get started :

```php
<?php if($image = $page->imagefield()->toFile()): ?>
<div class="image-container">
	<?php foreach($page->annotatorfield()->toStructure() as $pin): ?>
		<div class="pin" style="position: absolute; left: <?php echo $pin->x()->value() * 100 ?>%; top: <?php echo $pin->y()->value() * 100 ?>%;" data-note="<?php echo $pin->note() ?>"></div>
	<?php endforeach; ?>
	<img src="<?php echo $image->url() ?>">
</div>
<?php endif; ?>
```

## Options

### Structure field options

The default structure field options **should** be available, although many are not useful for the purpose of this field.
I haven't tested all of them so issues may occur, please test carefully before using in production. Any help with debugging is welcome !  

### Custom color background

You can specify a custom HEX color instead of the default one :

```yaml
  fieldname:
    label: Field label
    type: imageannotator
    src: imagetouse
    background: '#2b4795'
    fields: 
      ...
```

<div align="center">
    <img style="width: 500px; max-width: 100%" alt="background-change" src="https://user-images.githubusercontent.com/14079751/34582825-9f8eb0f6-f195-11e7-8c8d-1beb7c2a5b58.jpg"/>
</div>


### Translate the empty

If your language is missing, you can easily translate the *empty state* text by adding it to ```fields/imageannotator/translations/translations.php```.

## Credits

The field translation's method is a copy-paste of the [kirby-images](https://github.com/medienbaecker/kirby-images)' one by [medienbaecker](https://github.com/medienbaecker).
## License

MIT