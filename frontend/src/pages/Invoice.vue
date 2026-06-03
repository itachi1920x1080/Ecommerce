<template>
  <div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg" id="invoice-container">
      
      <div class="flex flex-col md:flex-row justify-between items-start border-b pb-6 mb-6">
        <div class="mb-4 md:mb-0">
          <h1 class="text-3xl font-extrabold text-blue-600 tracking-tight">RUPP Shop</h1>
          <p class="text-gray-500 mt-1">សាកលវិទ្យាល័យភូមិន្ទភ្នំពេញ</p>
          <p class="text-gray-500 text-sm">មហាវិថីសហព័ន្ធរុស្ស៊ី, រាជធានីភ្នំពេញ</p>
        </div>
        <div class="text-left md:text-right">
          <h2 class="text-2xl font-bold text-gray-800 uppercase">វិក្កយបត្រ (INVOICE)</h2>
          <p class="text-gray-600 mt-2">លេខវិក្កយបត្រ: <span class="font-semibold text-gray-900">#INV-893472</span></p>
          <p class="text-gray-600">កាលបរិច្ឆេទ: <span class="font-semibold text-gray-900">{{ currentDate }}</span></p>
          <p class="text-gray-600">ស្ថានភាព: <span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-sm">បានបង់ប្រាក់</span></p>
        </div>
      </div>

      <div class="mb-8">
        <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">ចេញជូនដល់ (Billed To):</h3>
        <p class="text-gray-800 font-bold text-lg">Mao Visal</p>
        <p class="text-gray-600">Computer Science Department</p>
        <p class="text-gray-600">visal@example.com</p>
      </div>

      <div class="overflow-x-auto mb-8">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-y border-gray-200">
              <th class="py-3 px-4 font-semibold">ល.រ</th>
              <th class="py-3 px-4 font-semibold">បរិយាយទំនិញ</th>
              <th class="py-3 px-4 font-semibold text-center">ចំនួន</th>
              <th class="py-3 px-4 font-semibold text-right">តម្លៃរាយ</th>
              <th class="py-3 px-4 font-semibold text-right">សរុប</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(item, index) in order.items" :key="index" class="text-gray-800 hover:bg-gray-50">
              <td class="py-4 px-4">{{ index + 1 }}</td>
              <td class="py-4 px-4 font-medium">{{ item.name }}</td>
              <td class="py-4 px-4 text-center">{{ item.qty }}</td>
              <td class="py-4 px-4 text-right">${{ item.price.toFixed(2) }}</td>
              <td class="py-4 px-4 text-right font-bold text-gray-900">${{ (item.price * item.qty).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-end mb-8">
        <div class="w-full md:w-1/2 lg:w-1/3 space-y-3">
          <div class="flex justify-between text-gray-600">
            <span>សរុបរង (Subtotal):</span>
            <span class="font-medium">${{ subtotal.toFixed(2) }}</span>
          </div>
          <div v-if="order.discount_amount > 0" class="flex justify-between text-gray-600">
            <span>បញ្ចុះតម្លៃ (Discount):</span>
            <span class="font-medium text-emerald-500">-${{ order.discount_amount.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-xl font-bold text-gray-900 pt-1 border-t border-gray-200 mt-2">
            <span>សរុបរួម (Total):</span>
            <span class="text-blue-600">${{ total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-200 pt-6 mt-8 text-center text-gray-500 text-sm">
        <p class="font-medium text-gray-600 mb-1">សូមអរគុណសម្រាប់ការគាំទ្រ RUPP Shop!</p>
        <p>ទំនិញដែលទិញរួចមិនអាចប្តូរ ឬត្រឡប់ជាប្រាក់វិញបានឡើយ។</p>
      </div>
    </div>

    <div class="max-w-3xl mx-auto mt-8 flex justify-end space-x-4 print:hidden">
      <router-link to="/my-orders" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
        ត្រឡប់ក្រោយ
      </router-link>
      <button @click="printInvoice" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm flex items-center gap-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        បោះពុម្ព (Print)
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// ទាញយកថ្ងៃខែឆ្នាំបច្ចុប្បន្ន
const currentDate = new Date().toLocaleDateString('en-GB');

// ទិន្នន័យសាកល្បង (អ្នកអាចប្តូរវាឱ្យទាញយកពី API របស់ Laravel តាមរយៈ Order ID បាននៅពេលក្រោយ)
const order = ref({
  subtotal: 66.00,
  discount_amount: 5.00,
  items: [
    { name: 'អាវយឺត RUPP CS ពណ៌ខៀវ', qty: 2, price: 12.50 },
    { name: 'កាតាបស្ពាយ Laptop (ASUS TUF Edition)', qty: 1, price: 35.00 },
    { name: 'សៀវភៅសរសេរ', qty: 5, price: 1.20 }
  ]
});

// គណនាតម្លៃសរុបដោយស្វ័យប្រវត្តិ
const subtotal = computed(() => {
  return order.value.subtotal || order.value.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
});

const total = computed(() => {
  return Math.max(0, subtotal.value - (order.value.discount_amount || 0));
});

// មុខងារហៅផ្ទាំង Print របស់កុំព្យូទ័រ
const printInvoice = () => {
  window.print();
};
</script>

<style>
/* CSS សម្រាប់រៀបចំទម្រង់ពេល Print កុំឱ្យជាប់ផ្ទៃពណ៌ប្រផេះ ឬប៊ូតុង */
@media print {
  body {
    background-color: white !important;
  }
  .print\:hidden {
    display: none !important;
  }
  #invoice-container {
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
    max-width: 100% !important;
  }
}
</style>
