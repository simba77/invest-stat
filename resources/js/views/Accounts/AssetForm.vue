<template>
  <page-component title="Add Asset">
    <div class="card">
      <form class="space-y-6 w-full md:w-2/3 mx-auto" action="#" method="POST" @submit.prevent="submitForm">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Asset</h3>
          <p class="mt-1 text-sm text-gray-600">Enter the ticker and other params of asset</p>
        </div>
        <div class="bg-red-500 inline-block text-white rounded px-4 py-2" v-if="errors && errors.message">{{ errors.message }}</div>
        <div class="w-full md:w-2/4">
          <checkbox-component
            label="Short"
            name="short"
            :key="componentKey"
            v-model="form.short"
          ></checkbox-component>
          <input-text
            v-model="form.ticker"
            :key="tickerComponentKey"
            :error="errors?.ticker"
            class="mt-3"
            name="name"
            label="Ticker"
            @update:modelValue="getTickerData"
            placeholder="Enter a ticker"
          />

          <div v-if="tickerData" class="mt-2">
            {{ tickerData.shortName }}
          </div>

          <input-select
            v-model="form.stock_market"
            :key="componentKey"
            :error="errors?.stock_market"
            :options="[{value: 'SPB', name: 'SPB'}, {value: 'MOEX', name: 'MOEX'}]"
            name="stock_market"
            class="mt-3"
            label="Stock Market"
          />
          <input-text
            v-model="form.quantity"
            :key="componentKey"
            :error="errors?.quantity"
            type="number"
            class="mt-3"
            name="quantity"
            label="Quantity"
            placeholder="Quantity"
          />
          <input-text
            v-model="form.buy_price"
            :key="componentKey"
            :error="errors?.buy_price"
            type="number"
            class="mt-3"
            name="buy_price"
            label="Buy Price"
            placeholder="Buy Price"
          />
          <input-text
            v-model="form.target_price"
            :key="componentKey"
            :error="errors?.target_price"
            type="number"
            class="mt-3"
            name="target_price"
            label="Target Price"
            placeholder="Target Price"
          />
          <input-select
            v-model="form.currency"
            :key="componentKey"
            :error="errors?.currency"
            :options="[{value: 'RUB', name: 'RUB'}, {value: 'USD', name: 'USD'}]"
            id="currency"
            class="mt-3"
            name="currency"
            label="Currency"
          />
        </div>
        <div class="border-b"></div>
        <button type="submit" class="btn btn-primary" :disabled="loading">Save</button>
        <router-link :to="{name: 'AccountDetail', params: {id: $route.params.account}}" class="btn btn-secondary ml-3">Back</router-link>
      </form>
    </div>
  </page-component>
</template>

<script setup lang="ts">
import PageComponent from "@/components/PageComponent.vue";
import InputText from "@/components/Forms/InputText.vue";
import axios from "axios";
import InputSelect from "@/components/Forms/InputSelect.vue";
import CheckboxComponent from "@/components/Forms/CheckboxComponent.vue";
import {onMounted, ref} from 'vue'
import useAsync from '@/utils/use-async'
import {useRoute} from 'vue-router'
import router from '@/router'
import {useDebounceFn, useFetch} from '@vueuse/core'

const {params: routeParams} = useRoute()
const form = ref({
  ticker: '',
  stock_market: 'SPB',
  quantity: 1,
  buy_price: '',
  currency: 'USD',
  short: false,
  target_price: 0,
})
const errors = ref(null)
const componentKey = ref(0)
const tickerComponentKey = ref(0)
const tickerData = ref(null)


const {loading, run: submitForm} = useAsync(() => {
  axios.post('/api/assets/store/' + routeParams.account, form.value)
    .then(() => {
      router.push({name: 'AccountDetail', params: {id: routeParams.account}});
    })
    .catch((error) => {
      if (error.response.data.errors) {
        errors.value = error.response.data.errors
        componentKey.value += 1
        tickerComponentKey.value += 1
      } else {
        throw error
      }
    })
})

const {run: getForm} = useAsync((id: any) => {
  axios.get('/api/assets/edit/' + id)
    .then((response) => {
      form.value = response.data.form
      componentKey.value += 1
      tickerComponentKey.value += 1
    })
})


const getTickerData = useDebounceFn(async () => {
  const {data} = await useFetch('/api/assets/get-by-ticker/' + form.value.ticker).json()

  tickerData.value = data.value

  form.value.currency = data.value.currency;
  form.value.stock_market = data.value.stockMarket;
  form.value.buy_price = data.value.price;
  form.value.quantity = data.value.lotSize;
  // Добавляем 5% к текущей стоимости
  form.value.target_price = Math.round(parseFloat(data.value.price) + (parseFloat(data.value.price) * 0.05));
  componentKey.value += 1

}, 300)

onMounted(() => {
  if (routeParams.id) {
    getForm(routeParams.id);
  }
})

</script>
