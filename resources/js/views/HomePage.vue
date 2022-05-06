<template>
  <page-component title="Dashboard">
    <div class="text-xl mb-3">Investment Result</div>
    <div class="grid grid-cols-3 gap-4">
      <stat-card
        v-for="(card, i) in summary"
        :key="i"
        :name="card.name"
        :help-text="card.helpText ?? null"
        :percent="card.percent ?? null"
        :total="card.total"
      ></stat-card>
    </div>

    <div class="text-2xl font-extrabold mt-6 mb-3">Assets by Brokers</div>
    <div v-for="(broker, index) in brokers" :key="index">
      <div class="text-xl mb-3 mt-5">{{ broker.name }}</div>
      <div class="grid grid-cols-3 gap-4">
        <stat-card
          v-for="(card, i) in broker.cards"
          :key="i"
          :name="card.name"
          :percent="card.percent ?? null"
          :total="card.total"
        ></stat-card>
      </div>
    </div>
  </page-component>
</template>

<script lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import StatCard from "@/components/Cards/StatCard.vue";

export default {
  name: "HomePage",
  components: {StatCard, PageComponent},
  data() {
    return {
      summary: [
        {
          name: 'Profit',
          helpText: 'Assets for The Current Day - The Invested Amount',
          percent: 132,
          total: 9334,
        },
        {
          name: 'The Invested Amount',
          total: 9334,
        },
        {
          name: 'Savings and Investments',
          helpText: 'Saving + Assets for The Current Day',
          total: 9334,
        }
      ],
      brokers: [
        {
          name: 'Broker Credit Service',
          cards: [
            {
              name: 'Profit',
              percent: 132,
              total: 9334,
            },
            {
              name: 'Current Value',
              total: 9334,
            },
            {
              name: 'Initial Cost',
              total: 9334,
            }
          ]
        },
        {
          name: 'Alfa Capital',
          cards: [
            {
              name: 'Profit',
              percent: -32,
              total: -9334,
            },
            {
              name: 'Current Value',
              total: 9334,
            },
            {
              name: 'Initial Cost',
              total: 90334,
            }
          ]
        }
      ]
    }
  }
}
</script>
