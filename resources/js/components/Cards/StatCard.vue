<template>
  <div class="bg-white shadow rounded p-6">
    <div class="text-gray-400 flex items-center">
      <div>{{ name }}</div>
      <div v-if="helpText" v-tooltip="helpText">
        <question-mark-circle-icon class="h-5 ml-1"></question-mark-circle-icon>
      </div>
    </div>
    <div class="flex justify-between items-center mt-1">
      <div class="text-3xl font-bold">{{ total }} {{ currency }}</div>
      <div
        :class="[percent > 0 ? 'bg-green-200 text-green-900' : 'bg-red-200 text-red-900', 'rounded-full px-2']"
        v-if="percent"
      >{{ percent > 0 ? '+' : '' }}{{ percent }}%
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {QuestionMarkCircleIcon} from "@heroicons/vue/solid";

export default {
  name: "StatCard",
  components: {QuestionMarkCircleIcon},
  props: {
    name: {
      type: String,
      default: '',
    },
    percent: {
      type: Number,
      default: null,
    },
    total: {
      type: Number,
      default: 0,
    },
    currency: {
      type: String,
      default: '₽',
    },
    helpText: {
      type: String,
      default: null,
    }
  }
}
</script>
