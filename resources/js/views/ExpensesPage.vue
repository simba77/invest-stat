<template>
  <page-component title="Expenses">
    <table class="simple-table white-header">
      <thead>
      <tr>
        <th>Name</th>
        <th>Sum</th>
      </tr>
      </thead>
      <tbody>
      <tr class="table-subtitle">
        <td colspan="2">Basic</td>
      </tr>
      <tr>
        <td>Item one</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td>Item two</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td>Item three</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td class="text-right">Subtotal:</td>
        <td>5 000,00</td>
      </tr>


      <tr class="table-subtitle">
        <td colspan="2">Payments</td>
      </tr>
      <tr>
        <td>Mobile</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td>Internet</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td class="text-right">Subtotal:</td>
        <td>5 000,00</td>
      </tr>


      <tr class="table-subtitle">
        <td colspan="2">Shops and Others</td>
      </tr>
      <tr>
        <td>Shop name</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td>Supermarket</td>
        <td>5 000,00</td>
      </tr>
      <tr>
        <td class="text-right">Subtotal:</td>
        <td>5 000,00</td>
      </tr>
      <tr class="font-bold">
        <td class="text-right">Total:</td>
        <td>5 000,00</td>
      </tr>
      </tbody>
    </table>
  </page-component>
</template>

<script>
import PageComponent from "../components/PageComponent";

export default {
  name: "ExpensesPage",
  components: {PageComponent}
}
</script>
