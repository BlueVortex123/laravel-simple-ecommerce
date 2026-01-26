<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavLink from "@/Components/NavLink.vue";
import { useOrderStatus } from '@/Composables/useOrderStatus.js';
import {
  HomeIcon,
  ClipboardDocumentListIcon,
  UserIcon,
  MapPinIcon,
  CreditCardIcon,
  ShoppingBagIcon,
  CalendarIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
  order: {
    type: Object,
    required: true
  }
});

// Use the order status composable in order to avoid DRY code
const {
  getStatusBadgeClass,
  getPaymentStatusBadgeClass,
  getStatusIcon,
  formatDate,
  formatCurrency
} = useOrderStatus();

// Computed properties
const subtotal = computed(() => {
  return props.order.order_items?.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0) || 0;
});

const orderTotal = computed(() => {
  return parseFloat(props.order.total_amount || 0);
});
</script>

<template>
  <Head :title="`Order ${order.order_number}`" />

  <AuthenticatedLayout>
    <!-- Breadcrumbs -->
    <div class="breadcrumbs text-sm mb-6">
      <ul class="flex items-center space-x-2 text-gray-600">
        <li>
          <NavLink :href="route('dashboard')" class="flex items-center hover:text-blue-600">
            <HomeIcon class="w-4 h-4" />
            <span class="ml-2">Dashboard</span>
          </NavLink>
        </li>
        <li class="text-gray-400 mx-2">&gt;</li>
        <li>
          <NavLink :href="route('orders.index')" class="flex items-center hover:text-blue-600">
            <ClipboardDocumentListIcon class="w-4 h-4" />
            <span class="ml-2">Orders</span>
          </NavLink>
        </li>
        <li class="text-gray-400 mx-2">&gt;</li>
        <li class="text-gray-900">{{ order.order_number }}</li>
      </ul>
    </div>

    <!-- Order Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Order {{ order.order_number }}</h1>
          <p class="text-sm text-gray-600">Placed on {{ formatDate(order.created_at) }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border"
                :class="getStatusBadgeClass(order.status)">
            <component :is="getStatusIcon(order.status)" class="w-4 h-4 mr-2" />
            {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
          </span>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border"
                :class="getPaymentStatusBadgeClass(order.payment_status)">
            <CreditCardIcon class="w-4 h-4 mr-2" />
            {{ order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) }}
          </span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
              <ShoppingBagIcon class="w-5 h-5 mr-2 text-gray-600" />
              Order Items ({{ order.order_items?.length || 0 }})
            </h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="item in order.order_items" :key="item.id" 
                   class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                  <img :src="`/storage/${item.product_snapshot?.image || item.product?.image || 'products/placeholder.jpg'}`" 
                       :alt="item.product_snapshot?.name || item.product?.name"
                       class="w-16 h-16 object-cover rounded-lg bg-gray-200">
                </div>
                <div class="flex-1">
                  <h3 class="font-medium text-gray-900">
                    {{ item.product_snapshot?.name || item.product?.name }}
                  </h3>
                  <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                    {{ item.product_snapshot?.description || item.product?.description }}
                  </p>
                  <div class="flex items-center justify-between mt-3">
                    <div class="text-sm text-gray-600">
                      <span>Qty: {{ item.quantity }}</span>
                      <span class="mx-2">×</span>
                      <span>{{ formatCurrency(item.unit_price) }}</span>
                    </div>
                    <div class="font-semibold text-gray-900">
                      {{ formatCurrency(item.total_price) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
              <UserIcon class="w-5 h-5 mr-2 text-gray-600" />
              Customer Information
            </h2>
          </div>
          <div class="p-6">
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Name:</span>
                <span class="font-medium text-gray-900">{{ order.user?.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium text-gray-900">{{ order.user?.email }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        <!-- Order Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
          </div>
          <div class="p-6">
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Shipping:</span>
                <span class="font-medium">Free</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Tax:</span>
                <span class="font-medium">{{ formatCurrency(orderTotal - subtotal) }}</span>
              </div>
              <div class="border-t pt-3">
                <div class="flex justify-between">
                  <span class="text-lg font-semibold text-gray-900">Total:</span>
                  <span class="text-lg font-bold text-gray-900">{{ formatCurrency(orderTotal) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
              <CreditCardIcon class="w-5 h-5 mr-2 text-gray-600" />
              Payment Information
            </h2>
          </div>
          <div class="p-6">
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Method:</span>
                <span class="font-medium capitalize">{{ order.payment_method?.replace('_', ' ') }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                      :class="getPaymentStatusBadgeClass(order.payment_status)">
                  {{ order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200" v-if="order.shipping_address">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
              <MapPinIcon class="w-5 h-5 mr-2 text-gray-600" />
              Shipping Address
            </h2>
          </div>
          <div class="p-6">
            <div class="space-y-1 text-sm">
              <p class="font-medium">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</p>
              <p v-if="order.shipping_address.company" class="text-gray-600">{{ order.shipping_address.company }}</p>
              <p class="text-gray-600">{{ order.shipping_address.address_line_1 }}</p>
              <p v-if="order.shipping_address.address_line_2" class="text-gray-600">{{ order.shipping_address.address_line_2 }}</p>
              <p class="text-gray-600">{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.postal_code }}</p>
              <p class="text-gray-600">{{ order.shipping_address.country }}</p>
              <p v-if="order.shipping_address.phone" class="text-gray-600">{{ order.shipping_address.phone }}</p>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
              <CalendarIcon class="w-5 h-5 mr-2 text-gray-600" />
              Order Timeline
            </h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">Order Placed</p>
                  <p class="text-xs text-gray-600">{{ formatDate(order.created_at) }}</p>
                </div>
              </div>
              <div v-if="order.shipped_at" class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">Shipped</p>
                  <p class="text-xs text-gray-600">{{ formatDate(order.shipped_at) }}</p>
                </div>
              </div>
              <div v-if="order.delivered_at" class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">Delivered</p>
                  <p class="text-xs text-gray-600">{{ formatDate(order.delivered_at) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>