<template>
	<div :class="['marker marker-pin', {dragging: current}]" :style="'top:'+ top +';left:'+ left +';'" @mousedown="initPinDrag">
		<div class="count">{{index + 1}}</div>
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
		index: Number,
	},
	computed: {
		top: function() {
			return this.marker.y * 100 + '%'
		},
		left: function() {
			return this.marker.x * 100 + '%'
		},
	},
	methods: {
		initPinDrag() {
			this.$emit('initDragResize', {
				index: this.index,
				type: this.marker.type,
				drag: true,
				resize: false
			})
		}
	},
}
</script>