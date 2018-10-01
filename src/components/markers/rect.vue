<template>
	<div :class="['marker marker-rect', {dragging: current}]" :style="'top:'+ top +';left:'+ left +';width:'+ width +';height:'+ height +';'">
		<div class="count">{{index + 1}}</div>
		<div class="resize-helpers">
			<div class="handle top-left" @mousedown="initResize('top-left')"></div>
			<div class="handle top-right" @mousedown="initResize('top-right')"></div>
			<div class="handle bottom-left" @mousedown="initResize('bottom-left')"></div>
			<div class="handle bottom-right" @mousedown="initResize('bottom-right')"></div>
		</div>
	</div>
</template>

<script>
export default {
	data() { 
		return {
		}
	},
	props: {
		marker: Object,
		current: Boolean,
		index: Number
	},
	computed: {
		top: function() {
			return this.marker.y * 100 + '%'
		},
		left: function() {
			return this.marker.x * 100 + '%'
		},
		width: function() {
			return this.marker.w * 100 + '%'
		},
		height: function() {
			return this.marker.h * 100 + '%'
		},
	},
	methods: {
		initResize(handle) {
			this.$emit('initDragResize', {
				index: this.index,
				type: this.marker.type,
				drag: false,
				resize: true,
				handle: handle
			})
		},
	},
}
</script>