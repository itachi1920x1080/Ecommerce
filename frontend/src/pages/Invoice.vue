<template>
  <div class="bg-white dark:bg-zinc-950 min-h-screen pt-20 pb-12 px-4">
    <div class="max-w-4xl mx-auto bg-white dark:bg-zinc-900 p-8 md:p-12 shadow-sm rounded-3xl border border-zinc-200/50 dark:border-zinc-800/50" id="invoice-container">
      
      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start border-b border-zinc-200 dark:border-zinc-800 pb-8 mb-8">
        <div class="mb-6 md:mb-0">
          <h1 class="text-4xl font-display font-medium text-zinc-900 dark:text-zinc-50 tracking-tight">RUPP <span class="text-primary-500">Shop</span></h1>
          <p class="text-zinc-500 dark:text-zinc-400 font-light mt-2 text-sm">សាកលវិទ្យាល័យភូមិន្ទភ្នំពេញ</p>
          <p class="text-zinc-500 dark:text-zinc-400 font-light mt-1 text-sm">មហាវិថីសហព័ន្ធរុស្ស៊ី, រាជធានីភ្នំពេញ</p>
        </div>
        <div class="text-left md:text-right">
          <h2 class="text-xl font-display font-medium text-zinc-900 dark:text-zinc-50 uppercase tracking-widest">វិក្កយបត្រ (INVOICE)</h2>
          <p class="text-zinc-500 dark:text-zinc-400 font-light mt-4 text-sm">លេខវិក្កយបត្រ: <span class="font-medium text-zinc-900 dark:text-zinc-50">#INV-893472</span></p>
          <p class="text-zinc-500 dark:text-zinc-400 font-light mt-1 text-sm">កាលបរិច្ឆេទ: <span class="font-medium text-zinc-900 dark:text-zinc-50">{{ currentDate }}</span></p>
          <div class="mt-2 flex md:justify-end items-center gap-2 text-sm">
            <span class="text-zinc-500 dark:text-zinc-400 font-light">ស្ថានភាព:</span>
            <span class="text-green-600 dark:text-green-400 font-medium bg-green-50 dark:bg-green-500/10 px-3 py-1 rounded-full text-[10px] uppercase tracking-widest border border-green-200 dark:border-green-500/20">បានបង់ប្រាក់</span>
          </div>
        </div>
      </div>

      <!-- Billed To -->
      <div class="mb-10">
        <h3 class="text-zinc-400 dark:text-zinc-500 text-[10px] font-semibold uppercase tracking-widest mb-4">ចេញជូនដល់ (Billed To):</h3>
        <p class="text-zinc-900 dark:text-zinc-50 font-medium text-lg font-display">Mao Visal</p>
        <p class="text-zinc-500 dark:text-zinc-400 font-light mt-1 text-sm">Computer Science Department</p>
        <p class="text-zinc-500 dark:text-zinc-400 font-light mt-1 text-sm">visal@example.com</p>
      </div>

      <!-- Table -->
      <div class="overflow-hidden mb-10 rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-900 dark:text-zinc-50 text-[10px] uppercase tracking-widest border-b border-zinc-200/50 dark:border-zinc-800/50">
                <th class="py-4 px-6 font-semibold">ល.រ</th>
                <th class="py-4 px-6 font-semibold">បរិយាយទំនិញ</th>
                <th class="py-4 px-6 font-semibold text-center">ចំនួន</th>
                <th class="py-4 px-6 font-semibold text-right">តម្លៃរាយ</th>
                <th class="py-4 px-6 font-semibold text-right">សរុប</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200/50 dark:divide-zinc-800/50">
              <tr v-for="(item, index) in order.items" :key="index" class="text-zinc-700 dark:text-zinc-300 font-light hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                <td class="py-5 px-6 text-sm">{{ index + 1 }}</td>
                <td class="py-5 px-6 font-medium text-zinc-900 dark:text-zinc-50 text-sm">{{ item.name }}</td>
                <td class="py-5 px-6 text-center text-sm">{{ item.qty }}</td>
                <td class="py-5 px-6 text-right text-sm">${{ item.price.toFixed(2) }}</td>
                <td class="py-5 px-6 text-right font-medium text-zinc-900 dark:text-zinc-50">${{ (item.price * item.qty).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Totals -->
      <div class="flex justify-end mb-12">
        <div class="w-full md:w-1/2 lg:w-1/3 space-y-4 bg-zinc-50/50 dark:bg-zinc-800/30 p-6 rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50">
          <div class="flex justify-between font-light text-sm text-zinc-600 dark:text-zinc-400">
            <span>សរុបរង (Subtotal):</span>
            <span class="font-medium text-zinc-900 dark:text-zinc-50">${{ subtotal.toFixed(2) }}</span>
          </div>
          <div v-if="order.discount_amount > 0" class="flex justify-between font-light text-sm text-zinc-600 dark:text-zinc-400">
            <span>បញ្ចុះតម្លៃ (Discount):</span>
            <span class="font-medium text-primary-500">-${{ order.discount_amount.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-xl font-display font-medium text-zinc-900 dark:text-zinc-50 pt-4 border-t border-zinc-200/50 dark:border-zinc-800/50 mt-2">
            <span>សរុបរួម (Total):</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Footer message -->
      <div class="border-t border-zinc-200/50 dark:border-zinc-800/50 pt-8 mt-8 text-center text-zinc-500 dark:text-zinc-400 text-sm font-light">
        <p class="font-medium text-zinc-900 dark:text-zinc-50 mb-2">សូមអរគុណសម្រាប់ការគាំទ្រ RUPP Shop!</p>
        <p>ទំនិញដែលទិញរួចមិនអាចប្តូរ ឬត្រឡប់ជាប្រាក់វិញបានឡើយ។</p>
      </div>
    </div>

    <!-- Actions -->
    <div class="max-w-4xl mx-auto mt-10 flex flex-wrap justify-end gap-4 print:hidden">
      <router-link to="/my-orders" class="px-8 py-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-50 text-[11px] font-bold uppercase tracking-widest rounded-full hover:bg-zinc-50 dark:hover:bg-zinc-800 transition shadow-sm">
        ត្រឡប់ក្រោយ
      </router-link>
      <button @click="printInvoice" class="btn-primary px-8 py-3.5 text-[11px] uppercase tracking-widest font-bold flex items-center gap-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
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
    border: none !important;
  }
}
</style>
