<template>
  <div>
    <label :for="elementId" class="block text-sm font-medium text-gray-700">{{ label }}</label>
    <select
      :required="required"
      :disabled="disabled"
      :name="name"
      :class="[error ? 'border-red-500' : '', 'form-select rounded w-full mt-1']"
      :id="elementId"
      v-model="value"
      @change="updateModelValue"
    >
      <option v-for="(option, index) in options" :key="index" :value="option.value">{{ option.name }}</option>
    </select>
    <div class="mt-1 text-sm text-gray-500" v-if="help" v-html="help"></div>
    <div class="mt-1 text-sm text-red-500" v-if="error">{{ typeof error === 'object' ? error.join(',') : error }}</div>
  </div>
</template>

<script lang="ts">
export default {
  name: "InputSelect",
  emits: ['update:modelValue'],
  props: {
    modelValue: String,
    label: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: '',
    },
    name: {
      type: String,
      default: '',
    },
    id: {
      type: String,
      default: '',
    },
    error: {
      type: [String, Number, Object],
      default: null,
    },
    help: {
      type: [String, Number],
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    required: {
      type: Boolean,
      default: false,
    },
    options: {
      default: () => [],
    },
  },
  data() {
    return {
      value: this.modelValue,
      elementId: this.id ? this.id : this.name
    }
  },
  methods: {
    updateModelValue(event: { target: { value: any; }; }) {
      this.$emit('update:modelValue', event.target.value)
    },
  }
}
</script>
