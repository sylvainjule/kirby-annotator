# Kirby 3 – Annotator

This plugin allows you to add notes to images by pinning them to specific coordinates / areas.

![screenshot-lemons](https://user-images.githubusercontent.com/14079751/46471969-e9424600-c7db-11e8-93e3-9f2596423ad9.jpg)

## 1. Installation

Download and copy this repository to ```/site/plugins/annotator```

Alternatively, you can install it with composer: ```composer require sylvainjule/annotator```

## 2. Blueprint usage

The annotator is a section which doesn't store any information itself. It provides an interface to manipulate content from other fields. Here's a basic setup of the plugin within your blueprint:

#### 2.1. Basic example

```yaml
columns:
  - width: 2/3
    sections:
      annotator:
        type: annotator
        storage:
          src: src
          markers: markers

  - width: 1/3
    sections:
      myfields:
        type: fields
        fields:
          src:
            type: files
            max: 1
          markers:
            type: structure
            fields:
              (...)
```

#### 2.2. Complete example

With all the default options explicitely set:

```yaml
columns:
  - width: 2/3
    sections:
      annotator:
        type: annotator
        buttons:
          - pin
          - rect
          - circle
        colors:
          - orange
          - yellow
          - green
          - blue
          - purple
          - pink
        theme: light
        debug: false
        storage:
          color: color
          src: src
          markers: markers

  - width: 1/3
    sections:
      myfields:
        type: fields
        fields:
          color:
            type: text
            disabled: true
          src:
            type: files
            max: 1
          markers:
            type: structure
            fields:
              (...)
```

#### 2.3. Usage within a file page

You can use this plugin within a file page by setting it like stated above, but skipping the `src` option within the `storage` settings. The plugin will automatically detect the image of the given page.

## 3. Options

### 3.1. Display options

See to the complete example above to see how to use them.

##### • Tools

> type: `array`, default: all tools listed above

You have 3 tools available : `pin`, `rect`and `circle`. All of them are visible by default, at least one should be provided.

##### • Colors

> type: `array`, default: all colors listed above

You have 6 predefined colors available to choose from. All of them are visible by default, at least one should be provided. If there's no color storage specified, the first color of the list will be used on load.

##### • Theme

> type: `string`, default: `light`

You have two themes available, a dark and a light one.

![screenshot-themes](https://user-images.githubusercontent.com/14079751/46482917-cd986900-c7f6-11e8-806b-3e794e4caabd.jpg)

##### • Debug

> type: `boolean`, default: `false`

When set to `true`, mouse coordinates will be shown in real-time in the toolbar. Not needed unless you're trying to extend some functionality.

![screenshot-coordinates](https://user-images.githubusercontent.com/14079751/46483228-71821480-c7f7-11e8-9ab3-27ee53ab4670.jpg)



### 3.2. Storage options

##### • Image file

The section needs to be synced with a field returning an image url to work with. 

In theory, using a ```select``` field might work, but I strongly recommend using a ```files``` field and limiting it to a single file. Not only does it look nicer, but most importantly it returns an absolute url of the file:

```yaml
# annotator section
storage:
  src: src

# fields section
src:
  type: files
  max: 1
```

> Note: You don’t need to explicitly set a ```max``` value, though it may look clearer. When confronted to a files field containing multiple files, the plugin will always use the first one.

##### • Markers structure

The plugin needs an associated structure field to store the markers informations. It has 5 reserved fields that shouldn't be used for any other purpose: `type`, `x`, `y`,`w` and `h`. Those will be automatically set and don't need to be explicitely specified unless you want to show them within the panel:

![screenshots-typexywh](https://user-images.githubusercontent.com/14079751/46482920-ce30ff80-c7f6-11e8-9f01-6d212c85a1c4.jpg)

```yaml
# annotator section
storage:
  markers: markers

# fields section
markers:
  type: structure
  fields:
    type:
      label: 'Type'
      type: text
    x:
      label: 'x'
      type: text
    y:
      label: 'y'
      type: text
    w:
      label: 'w'
      type: text
    h:
      label: 'h'
      type: text
```

Otherwise, you can directly start adding fields you'd like to sync content with:

![screenshot-notes](https://user-images.githubusercontent.com/14079751/46482916-cd986900-c7f6-11e8-951a-16a94b9d0927.jpg)

```yaml
# annotator section
storage:
  markers: markers

# fields section
markers:
  type: structure
  fields:
    mynote:
      label: 'Note'
      type: text
```

##### • Color

Without any associated `color` field, the plugin won't remember the last color used within the editor, and will always fallback to the first one when loading the component. Setting a color storage is pretty straightforward:

```yaml
# annotator section
storage:
  color: color

# fields section
color:
  type: text
  disabled: true
```

> Note that the plugin needs to have access to the field element within the panel view to update the color on the fly, therefore it cannot be of `type: hidden`. If you want to hide it visually, you'll have to work your way there with a custom panel css.

## 4. Template usage

Markers are stored in a structure field, which means we need to create a collection with the `toStructure()` method. I will refer to a variable named `$marker` in the examples below, this is how we get it:

```php
foreach($page->markers()->toStructure() as $marker) {
    // now we have a $marker variable
}
```

### 4.1. How are the informations stored ?

Each marker has a set of coordinates, **proportional to the image**.

These coordinates are limited to 4 decimals, and return a value between `0` and `1`. Kirby might return them as strings, so remember to always make sure that you're getting a number before working with them : 

```php
echo $marker->x()->toFloat()
```

Each marker also has its type specified as a string, either `pin`, `rect` or `circle`.

##### • Pin

This is the kind of output to expect:

```php
type: 'pin'
x: 0.50 #(if 50% from the left)
y: 0.50 #(if 50% from the top)
w: 0
y: 0
```

##### • Rectangles

This is the kind of output to expect:

```php
type: 'rect'
x: 0.50 #(if 50% from the left)
y: 0.50 #(if 50% from the top)
w: 0.25 #(if 25% of the width)
y: 0.25 #(if 25% of the height)
```

##### • Circles

Please note two things: 

- the `x`and `y` coordinates are the ones **of the circle's center**. This means you'll have to move the marker element with:

  ```css
  transform: translate(-50%, -50%);
  ```

- `w` and `h` are calculated for the ellipse to be a perfect circle. This means that they will match if the image is squared, but will differ if it is not, in order to compensate for the proportional difference between width and height. Therefore you can set on the element:

  ```css
  border-radius: 50%;
  ```

This is the kind of output to expect:

```php
type: 'circle'
x: 0.50 // center is 50% from the left
y: 0.50 // center is 50% from the top
w: 0.25 // diameter is 25% of the width
y: 0.3275 // diameter is still the same, but adjusted to match the image ratio
```

### 4.2. Methods and functions

##### • Check the marker's type

```php
// Check if the marker is a [pin / rect / circle]. 
// Returns true or false.
$marker->type()->isPin() 
$marker->type()->isRect()
$marker->type()->isCircle()

// Check if the marker is not a [pin / rect / circle]. 
// Returns true or false.
$marker->type()->isNotPin()
$marker->type()->isNotRect()
$marker->type()->isNotCircle()
```

##### • Working with percentages

```php
// Convert the value to a float and multiply it by 100.
// Returns a number.
$marker->x()->toPercent() // returns 50

// Convert the value to a float, multiply it by 100 and append a '%'. 
// Returns a string.
$marker->x()->toPercentString() // returns '50%'
```

##### • Formated inline styles

Returns a properly formated inline style, according to the marker's type.

```php
echo markerStyle($marker);

// if $marker is a pin
returns 'left:50%; top:50%;'
// if $marker is a rectangle
returns 'left:50%; top:50%; width:25%; height:25%;'
// if $marker is a circle
returns 'left:50%; top:50%; width:25%; height:32.75%; border-radius:50%; transform:translate(-50%, -50%);'
```

### 4.3. Basic usage example

Make sure your `.marker` class has a `position: absolute`.

```php
<?php if($image = $page->src()->toFile()): ?>
<div>
    <?php foreach($page->markers()->toStructure() as $marker): ?>
        <div class="marker" style="<?php echo markerStyle($marker); ?>"></div>
    <?php endforeach; ?>
    <img src="<?php echo $image->url() ?>">
</div>
<?php endif; ?>
```


## 6. License

MIT

## 7. Credits

- The fields synchronization has been taken from [@rasteiner](https://github.com/rasteiner/kn-map-section)'s map section. 🙏
