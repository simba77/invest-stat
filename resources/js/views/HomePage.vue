<template>
  <page-component title="Dashboard">
    <preloader-component v-if="loading"/>
    <template v-else>
      <div class="flex justify-between">
        <div class="text-xl mb-3">Investment Result</div>
        <div class="text-xl mb-3">1 USD = {{ dashboard.usd }}₽</div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4">
        <stat-card
          v-for="(card, i) in dashboard.summary"
          :key="i"
          :name="card.name"
          :help-text="card.helpText ?? null"
          :percent="card.percent ?? null"
          :total="card.total"
        ></stat-card>
      </div>

      <div class="text-2xl font-extrabold mt-6 mb-3">Assets by Brokers</div>
      <div v-for="(broker, index) in dashboard.brokers" :key="index">
        <div class="text-xl mb-3 mt-5">{{ broker.name }}</div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-4">
          <stat-card
            v-for="(card, i) in broker.cards"
            :key="i"
            :help-text="card.help"
            :currency="broker.currency"
            :name="card.name"
            :percent="card.percent ?? null"
            :total="card.total"
          ></stat-card>
        </div>
      </div>
    </template>
  </page-component>
</template>

<script setup lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import StatCard from "@/components/Cards/StatCard.vue";
import {useDashboard} from "@/composable/useDashboard";
import PreloaderComponent from "@/components/Common/PreloaderComponent.vue";

const {dashboard, getDashboard, loading} = useDashboard()

getDashboard()
</script>
