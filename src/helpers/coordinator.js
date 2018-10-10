export default function(Vue) {

Vue.prototype.$annotator = new Vue({
    data() {
        return {
	        fields: [],
	        annotators: [],
	        updatingAnnotators: false,
	        updatingFields: false,
        }
    },
    methods: {
        getAcceptedType(field) {
	        let component = Vue.options.components['k-' + field.type + '-field']
	        if (!component) {
	          console.warn('Annotator could not update form: Unknown field type "' + field.type + '"')
	          return null
	        }
	        return component.options.props.value.type
        },
        updateFields(datapoint, fieldname, value) {
	        if(this.updatingAnnotators) return 
	        
	        this.updatingFields = true
	        //update fields
	        this.fields.forEach((section) => {
	            if(fieldname in section.fields) {            
	            	let type = this.getAcceptedType(section.fields[fieldname])
	            	if (!type) return

		            switch(datapoint) {
		              	case 'markers':
		                	//is this field structure like?
		                	let fields = section.fields[fieldname].fields
		                	if(fields) {
		                    	section.values[fieldname] = value.map((item) => {
			                    	item = Object.assign({}, item)
			                    	return item
			                    })
		                	} else { //if not, just send it raw data
		                    	section.values[fieldname] = value
		                	}
		                    break
		              	default:
		                    section.values[fieldname] = value
		                    break            
		            }
		            section.input(section.values)
	            }
	        })
	        this.$nextTick(() => this.updatingFields = false)
        },
        updateAnnotators(values) {
        	if(this.updatingFields) return 
        	this.updatingAnnotators = true
        
	        this.annotators.forEach((annotator) => {
	            for(let fieldname in values) {
	            	let value = values[fieldname]
	            	annotator.setValue(fieldname, value)
	            }
	        })
        	this.$nextTick(() => this.updatingAnnotators = false)
        },
        registerAnnotator(annotator) {
        	this.annotators.push(annotator)
        },
        unregisterAnnotator(annotator) {
        	const index = this.fields.indexOf(annotator);
        	if (index !== -1) {
          		this.annotators.splice(index, 1);
        	}
        },
        registerFields(section) {
        	this.fields.push(section)
        },
        unregisterFields(section) {
        	const index = this.fields.indexOf(section);
        	if (index !== -1) {
            	this.fields.splice(index, 1);
          	}
        }
    }
})

// replace fields section
const original = Vue.options.components["k-fields-section"]

// Extend section events
Vue.component('k-fields-section', {
    extends: original,
    created() {
        this.$annotator.registerFields(this)
    },
    destroyed() {
        this.$annotator.unregisterFields(this)
    },
    methods: {
        input(values) {
	        this.values = values
	        original.options.methods.input.call(this, values)
	        this.$annotator.updateAnnotators(values)
        }
    },
    watch: {
        values(newValues) {
        	this.$annotator.updateAnnotators(newValues)
        }
    }
})

}