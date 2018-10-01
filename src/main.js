import Annotator from './annotator.vue'
import Coordinator from './helpers/coordinator.js'

panel.plugin('sylvainjule/annotator', {
	use: [Coordinator],
    sections: {
        annotator: Annotator
    }
});