<template>
  <page-component title="Investments and Results">
    <table class="simple-table">
      <thead>
      <tr>
        <th>Date</th>
        <th>Sum</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>07.07.2021</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td>08.07.2021</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td>19.07.2021</td>
        <td>5 000,00</td>
      </tr>
      </tbody>
    </table>
  </page-component>
</template>

<script>
import PageComponent from "../components/PageComponent";
export default {
  name: "InvestmentsPage",
  components: {PageComponent}
}
</script>
