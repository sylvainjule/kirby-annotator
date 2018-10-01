<template>
	<div :class="['marker marker-circle', {dragging: current}]" :style="'top:'+ top +';left:'+ left +';width:'+ width +';height:'+ height +';'">
		<div class="count">{{index + 1}}</div>
		<div class="resize-helpers" :style="'transform: rotate('+ computedRotate +'deg);'">
			<div class="handle top" @mousedown="initCircleResize"></div>
			<div class="handle center" @mousedown="initCircleDrag"></div>
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
		index: Number,
		rotate: Number
	},
	computed: {
		computedRotate: function() {
			return this.rotate * 1 + 90
		},
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
		initCircleResize() {
			this.$emit('initDragResize', {
				index: this.index,
				type: this.marker.type,
				drag: false,
				resize: true,
			})
		},
		initCircleDrag() {
			this.$emit('initDragResize', {
				index: this.index,
				type: this.marker.type,
				drag: true,
				resize: false,
			})
		}
	},
}
</script>