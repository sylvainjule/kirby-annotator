<template>
    <k-field :name="name" :type="type" :input="false" :label="false" :disabled="isDisabled" class="k-annotator-field">
        <div :class="['annotator', {'annotator-disabled': isDisabled}]" ref="container" :data-theme="theme" :data-color="currentColor" @mousemove="panImage">
            <div class="annotator-background"></div>
            <div v-if="!src" class="annotator-placeholder">
                <div class="annotator-placeholder-ctn">
                    Please select an image
                </div>
            </div>
            <div v-if="src" class="annotator-toolbar">
                <div class="annotator-toolbar-inner">
                    <div v-for="tool in tools" :key="tool" :class="['tool', tool, {'selected': currentTool == tool}]" @click="setTool(tool)">
                        <component :is="'icon-' + tool"></component>
                    </div>
                    <div v-if="zoom" :class="['tool', 'zoom-button', {selected: fullsize}]" @click="toggleFullsize">
                        <component is="icon-zoom"></component>
                    </div>
                    <div class="color">
                        <div class="color-selected blue" @click="toggleSelector">
                            <div class="circle"></div>
                            <svg viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M3 6l5 5 5-5-1-1-4 4-4-4z" />
                            </svg>
                        </div>
                        <div v-if="showSelector" class="color-selector">
                            <ul class="colors">
                                <li v-for="color in colors" :key="color" :class="['color-entry', color]" @click="setColor(color)">
                                    <div class="circle"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div v-if="debug" class="annotator-toolbar-debug">
                    <div class="coord x">{{ coords.x }}</div>
                    <div class="coord y">{{ coords.y }}</div>
                </div>
            </div>
            <div v-if="src" class="annotator-ctn" @mousemove="updateCoords">
                <div :class="['image', {fullsize: fullsize}]">
                    <div class="image-ctn" :style="customTransform">
                        <div ref="markers" class="markers" @mousedown="addMarker" @dblclick="addMarker">
                            <component v-for="(marker, index) in markers" :key="marker._id ?? `index-${index}`" :index="index" :current="index == drag.index" :marker="marker" v-bind="marker.type === 'circle' ? { rotate } : {}" :is="'marker-' + marker.type" @initDragResize="initDragResize"></component>
                        </div>
                        <img :src="src" ref="image">
                    </div>
                </div>
            </div>
        </div>
    </k-field>
</template>

<script>
import iconPin from './assets/svg/icon-pin.vue'
import iconRect from './assets/svg/icon-rect.vue'
import iconCircle from './assets/svg/icon-circle.vue'
import iconZoom from './assets/svg/icon-zoom.vue'

import markerPin from './components/pin.vue'
import markerRect from './components/rect.vue'
import markerCircle from './components/circle.vue'

export default {
    components: {markerPin, markerRect, markerCircle, iconPin, iconRect, iconCircle, iconZoom},
    data() {
        return {
            showSelector: false,
            currentTool: '',
            manualColor: '',
            storedColor: '',
            rotate: 0,
            src: '',
            coords: {
                x: 0,
                y: 0,
                xabs: 0,
                yabs: 0,
            },
            drag: {
                index: null,
                type: null,
                isDragging: false,
                isResizing: false,
                handle: null,
                xref: 0,
                yref: 0,
                wref: 0,
                href: 0,
                maxRadius: 0,
            },
            markers: [],
            customTransform: undefined,
            fullsize: false,
            sourceRequest: 0,
            gestureContext: null,
        }
    },
    inheritAttrs: false,
    emits: [],
    props: {
        name: String,
        type: String,
        endpoints: Object,
        tools: { type: Array, default: () => ['pin', 'rect', 'circle'] },
        colors: { type: Array, default: () => ['orange', 'yellow', 'green', 'blue', 'purple', 'pink'] },
        theme: { type: String, default: null },
        debug: Boolean,
        disabled: Boolean,
        max: { type: [Number, Boolean], default: false },
        zoom: Boolean,
        translate: { type: Boolean, default: true },
        dblclick: Boolean,
        storage: { type: Object, default: () => ({}) },
        image: { type: [String, Boolean], default: false },
    },
    computed: {
        currentColor() {
            return this.storedColor != '' ? this.storedColor : this.manualColor
        },
        pageValues() {
            return this.$panel.view.props.versions ? (this.$panel.content.version('changes') || {}) : {}
        },
        contextKey() {
            return JSON.stringify([this.$panel.view.props.api, this.endpoints?.field, this.$panel.language.code])
        },
        markerValue() {
            return this.pageValues[this.storage.markers]
        },
        markerSnapshot() {
            return JSON.stringify([this.contextKey, this.storage.markers, this.markerValue])
        },
        colorValue() {
            return this.pageValues[this.storage.color] ?? ''
        },
        sourceSnapshot() {
            return JSON.stringify([this.contextKey, this.storage.src, this.image, this.pageValues[this.storage.src]])
        },
        isDisabled() {
            const language = this.$panel.language
            return this.disabled || this.$panel.content.isLocked() ||
                (!this.translate && language.code !== null && !language.default)
        }
    },
    watch: {
        markerSnapshot: {
            immediate: true,
            handler() {
                this.resetDrag()
                this.markers = Array.isArray(this.markerValue) ? this.clone(this.markerValue) : []
            }
        },
        contextKey() {
            this.fullsize = false
            this.customTransform = undefined
            this.showSelector = false
            this.currentTool = this.tools[0] ?? ''
            this.manualColor = this.colors[0] ?? ''
        },
        colorValue: {
            immediate: true,
            handler(value) { this.storedColor = value }
        },
        sourceSnapshot: {
            immediate: true,
            handler() {
                if(this.drag.isDragging || this.drag.isResizing) {
                    this.resetDrag()
                    this.markers = Array.isArray(this.markerValue) ? this.clone(this.markerValue) : []
                }
                this.updateSource()
            }
        },
        tools: {
            immediate: true,
            handler(value) {
                if(!value.includes(this.currentTool)) this.currentTool = value[0] ?? ''
            }
        },
        colors: {
            immediate: true,
            handler(value) {
                if(!value.includes(this.manualColor)) this.manualColor = value[0] ?? ''
            }
        },
        isDisabled(value) {
            if(value) {
                this.resetDrag()
                this.markers = Array.isArray(this.markerValue) ? this.clone(this.markerValue) : []
            }
        }
    },
    mounted() {
        document.addEventListener('mouseup', this.stopDragging)
    },
    beforeUnmount() {
        document.removeEventListener('mouseup', this.stopDragging)
        this.sourceRequest++
        this.resetDrag()
    },
    methods: {
        panImage(e) {
            if(!this.zoom || !this.fullsize || !this.$refs.image) return false

            let _container = this.$refs.container
            let _bounds    = _container.getBoundingClientRect()
            let _width     = _container.clientWidth
            let _height    = _container.clientHeight
            let xabs       = e.clientX - _bounds.left
            let yabs       = e.clientY - _bounds.top
            let x          = xabs / _width
            let y          = yabs / _height

            let _img       = this.$refs.image
            let _imgWidth  = _img.clientWidth
            let _imgHeight = _img.clientHeight

            let propX = _imgWidth  > _width  ? (_imgWidth - _width + 150) * x - 40 : 0
            let propY = _imgHeight > _height ? (_imgHeight - _height + 150) * y - 40 : 0

            this.customTransform = 'transform: translate('+ -propX +'px,'+ -propY +'px)'
        },
        toggleFullsize() {
            if(!this.zoom) return false

            if(this.fullsize) {
                this.fullsize = false
                this.customTransform = undefined
            }
            else {
                this.fullsize = true
            }
        },
        toggleSelector() {
            this.showSelector = !this.showSelector
        },
        setColor(color) {
            if(this.isDisabled) return
            if(this.storage.color) {
                this.storedColor = color
                this.updateColor()
            }
            else {
                this.manualColor = color
            }
            this.showSelector = false
        },
        setTool(tool) {
            this.currentTool = tool
        },
        updateCoords(e) {
            let _markers = this.$refs.markers
            if(!_markers || !_markers.clientWidth || !_markers.clientHeight) return

            let _bounds = _markers.getBoundingClientRect()
            let _width  = _markers.clientWidth
            let _height = _markers.clientHeight

            let xabs = e.clientX - _bounds.left
            let yabs = e.clientY - _bounds.top
            let x    = xabs / _width
            let y    = yabs / _height

            x = Math.max(0, Math.min(1, x.toFixed(4)))
            y = Math.max(0, Math.min(1, y.toFixed(4)))

            this.coords.x = x
            this.coords.y = y
            this.coords.xabs = xabs
            this.coords.yabs = yabs

            if(this.drag.isDragging) this.dragMarker(e)
            if(this.drag.isResizing) this.resizeMarker(e)
        },
        addMarker(e) {
            if(this.isDisabled || !this.currentTool || e.target !== this.$refs.markers) return false
            if(this.max && this.markers.length >= this.max) return false
            if(e.button !== 0) return false

            const isPin         = this.currentTool === 'pin'
            const expectedEvent = isPin && this.dblclick ? 'dblclick' : 'mousedown'
            if(e.type !== expectedEvent) return false
            if(this.drag.isDragging || this.drag.isResizing) return false

            if(!this.$refs.markers.clientWidth || !this.$refs.markers.clientHeight) return false
            this.updateCoords(e)
            const newMarker = {
                _id: this.$helper.uuid(),
                type: this.currentTool,
                x: this.coords.x,
                y: this.coords.y,
                w: 0,
                h: 0,
            };
            this.markers = [...this.markers, newMarker]

            if(isPin) {
                this.updateStructure()
                return
            }

            this.gestureContext    = this.contextKey
            this.drag.index        = this.markers.length - 1
            this.drag.isResizing   = true
            this.drag.type         = this.currentTool
            this.drag.handle       = 'bottom-right'
            this.drag.xref         = this.coords.x
            this.drag.yref         = this.coords.y
            this.drag.maxRadius    = this.getClosestSide(this.coords.xabs, this.coords.yabs)
        },
        getClosestSide(xabs, yabs) {
            let _markers = this.$refs.markers
            let _width  = _markers.clientWidth
            let _height = _markers.clientHeight

            let distanceTop = yabs
            let distanceBottom = _height - yabs
            let distanceLeft = xabs
            let distanceRight = _width - xabs

            return Math.min(distanceTop, distanceBottom, distanceLeft, distanceRight)
        },
        dragMarker(e) {
            let _marker = this.markers[this.drag.index]
            if(!_marker) return

            if(this.drag.type == 'pin') {
                _marker.x = this.coords.x
                _marker.y = this.coords.y
            }
            if(this.drag.type == 'circle') {
                let _markers = this.$refs.markers
                let minX = _marker.w / 2
                let maxX = 1 - minX
                let minY = _marker.h / 2
                let maxY = 1 - minY

                let x = Math.min(maxX, Math.max(minX, this.coords.x))
                let y = Math.min(maxY, Math.max(minY, this.coords.y))
                _marker.x = x
                _marker.y = y
            }
        },
        resizeMarker(e) {
            let _marker = this.markers[this.drag.index]
            if(!_marker) return

            if(this.drag.type == 'rect') {
                let x = parseFloat(_marker.x)
                let y = parseFloat(_marker.y)
                let w = parseFloat(_marker.w)
                let h = parseFloat(_marker.h)
                let xref = parseFloat(this.drag.xref)
                let yref = parseFloat(this.drag.yref)
                let wref = parseFloat(this.drag.wref)
                let href = parseFloat(this.drag.href)

                if(this.drag.handle == 'bottom-right') {
                    w = this.coords.x - xref
                    h = this.coords.y - yref
                }
                else if(this.drag.handle == 'top-left') {
                    x = Math.min(xref + wref, this.coords.x)
                    y = Math.min(yref + href, this.coords.y)

                    w = wref + (xref - x)
                    h = href + (yref - y)
                }
                else if(this.drag.handle == 'top-right') {
                    y = Math.min(yref + href, this.coords.y)

                    w = wref + (this.coords.x - (xref + wref))
                    h = href + (yref - y)
                }
                else if(this.drag.handle == 'bottom-left') {
                    x = Math.min(xref + wref, this.coords.x)

                    w = wref + (xref - x)
                    h = this.coords.y - yref
                }

                _marker.x = Math.max(0, Math.min(1, x.toFixed(4)))
                _marker.y = Math.max(0, Math.min(1, y.toFixed(4)))
                _marker.w = Math.max(0, Math.min(1, w.toFixed(4)))
                _marker.h = Math.max(0, Math.min(1, h.toFixed(4)))
            }
            else if(this.drag.type == 'circle') {
                let _markers = this.$refs.markers
                let _width  = _markers.clientWidth
                let _height = _markers.clientHeight

                // pythagore with absolute values
                let w = this.coords.xabs - (this.drag.xref * _width)
                let h = this.coords.yabs - (this.drag.yref * _height)
                let r = Math.sqrt(Math.pow(w, 2) + Math.pow(h, 2))

                // limit to max radius
                r = Math.min(this.drag.maxRadius, r)
                // back to percent
                r = r / _width

                // width is radius * 2
                w = r * 2
                // height is width * ratio
                h = w * _width / _height

                _marker.w = w.toFixed(4)
                _marker.h = h.toFixed(4)

                // set an angle
                let angleDeg = Math.atan2(this.coords.yabs - (this.drag.yref * _height), this.coords.xabs - (this.drag.xref * _width)) * 180 / Math.PI
                this.rotate = Number(angleDeg.toFixed(4))
            }
        },
        initDragResize(val) {
            if(this.isDisabled || !this.markers[val.index]) return false

            this.gestureContext = this.contextKey
            // enable resizing
            this.drag.index        = val.index
            this.drag.isDragging   = val.drag
            this.drag.isResizing   = val.resize
            this.drag.type         = val.type

            if(val.resize) {
                let _markers = this.$refs.markers
                let _marker  = this.markers[this.drag.index]

                if(val.type == 'circle') {
                    this.drag.xref         = _marker.x
                    this.drag.yref         = _marker.y
                    this.drag.maxRadius    = this.getClosestSide(_marker.x * _markers.clientWidth, _marker.y * _markers.clientHeight)
                }
                if(val.type == 'rect') {
                    this.drag.handle       = val.handle
                    this.drag.xref         = _marker.x
                    this.drag.yref         = _marker.y
                    this.drag.wref         = _marker.w
                    this.drag.href         = _marker.h
                }
            }
        },
        stopDragging() {
            if(!this.drag.isDragging && !this.drag.isResizing) return
            const canCommit = !this.isDisabled && this.gestureContext === this.contextKey
            this.resetDrag()
            if(canCommit) this.updateStructure()
        },
        resetDrag() {
            this.drag.index = null
            this.drag.isDragging = false
            this.drag.isResizing = false
            this.drag.type = null
            this.drag.handle = null
            this.drag.maxRadius = 0
            this.rotate = 0
            this.gestureContext = null
        },
        clone(value) {
            return JSON.parse(JSON.stringify(value))
        },
        async updateSource() {
            const request = ++this.sourceRequest
            const snapshot = this.sourceSnapshot
            const name = this.storage.src
            const value = this.pageValues[name]

            if(!name || !Object.hasOwn(this.pageValues, name)) {
                this.src = this.image || ''
                return
            }
            if(!Array.isArray(value)) {
                this.src = typeof value === 'string' ? value : ''
                return
            }

            const first = value[0]
            if(first && typeof first === 'object') {
                this.src = first.url || ''
                return
            }

            this.src = ''
            if(typeof first !== 'string' || !first || !this.endpoints?.field) return

            const base = this.endpoints.field.slice(0, this.endpoints.field.lastIndexOf('/'))
            try {
                const items = await this.$api.get(`${base}/${encodeURIComponent(name)}/items`, {
                    items: [first]
                })
                if(request !== this.sourceRequest || snapshot !== this.sourceSnapshot) return
                this.src = items?.[0]?.url || ''
            } 
            catch (error) {
                if(request === this.sourceRequest && snapshot === this.sourceSnapshot) {
                    console.warn('Annotator: unable to resolve image', error)
                }
            }
        },
        async updateField(name, value) {
            if(!name || this.isDisabled) return
            try {
                await this.$panel.content.update({ [name]: value })
            } 
            catch (error) {
                this.$panel.error(error)
            }
        },
        updateStructure() {
            return this.updateField(this.storage.markers, this.clone(this.markers))
        },
        updateColor() {
            return this.updateField(this.storage.color, this.storedColor)
        },
    },
};
</script>

<style lang="scss">
    @import './assets/css/styles.scss'
</style>