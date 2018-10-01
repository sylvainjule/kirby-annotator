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
          color: color
          src: src
          markers: markers

  - width: 2/3
    sections:
      myfields:
        type: fields
        fields:
          color:
            type: text
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
```


## Options

## Front-end usage

## Todo

- [ ] Add compatibility to use within a file page
- [ ] Write a proper Readme
- [X] Add composer support
- [X] Cross-browser
- [X] Keep track of the last color used
- [X] Add a placeholder when there's no image
- [X] Only show coordinates when ```debug: true``` is set in the blueprint

## License

MIT

## Credits

- The fields synchronization has been taken from [@rasteiner](https://github.com/rasteiner/kn-map-section)'s map section. 🙏
