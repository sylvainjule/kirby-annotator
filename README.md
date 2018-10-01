# Kirby 3 – Annotator

This plugins allows you to sync content with pins / zones defined on an image.

## Installation

Download and copy folder to ```/site/plugins/annotator```

Note: This plugin won't work without a few tweaks until [this issue](https://github.com/k-next/kirby/issues/1037) is fixed, details below.

## Usage

```yaml
columns:
  - width: 1/3
    sections:
      annotator:
        type: annotator
        storage:
          src: examplesrc
          markers: examplemarkers

  - width: 2/3
    sections:
      myfields:
        type: fields
        fields:
          examplesrc:
            type: select
            options: query
            query: page.images
          markers:
            type: structure
            fields:
              (...)
```

Current fix – add all options explicitely:

```yaml
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
    storage:
      src: src
      markers: markers
```


## Options

## Front-end usage

## Todo

- [ ] Add compatibility to ue within a file page
- [ ] Keep track of the last color used
- [ ] Test / fix in various browsers / tablets
- [ ] Add a placeholder when there's no image
- [ ] Only show coordinates when ```debug: true``` is set in the blueprint
- [ ] Add composer support

## License

MIT

## Credits

- The fields synchronization has been taken from [@rasteiner](https://github.com/rasteiner/kn-map-section)'s map section. 🙏
