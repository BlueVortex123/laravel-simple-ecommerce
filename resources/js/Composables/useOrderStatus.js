import {
  ClockIcon,
  TruckIcon,
  CheckCircleIcon,
  XCircleIcon,
  CurrencyDollarIcon,
  ArrowUpCircleIcon,
} from "@heroicons/vue/24/solid";

export function useOrderStatus() {
  // Status badge classes
  const getStatusBadgeClass = (status) => {
    const statusClasses = {
      'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
      'processing': 'bg-blue-100 text-blue-800 border-blue-200',
      'shipped': 'bg-indigo-100 text-indigo-800 border-indigo-200',
      'delivered': 'bg-green-100 text-green-800 border-green-200',
      'cancelled': 'bg-red-100 text-red-800 border-red-200',
      'refunded': 'bg-gray-100 text-gray-800 border-gray-200'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800 border-gray-200';
  };

  // Status badge classes for DaisyUI (for Index page)
  const getStatusBadgeClassDaisy = (status) => {
    const statusClasses = {
      'pending': 'badge-warning',
      'processing': 'badge-info',
      'shipped': 'badge-primary',
      'delivered': 'badge-success',
      'cancelled': 'badge-error',
      'refunded': 'badge-secondary'
    };
    return statusClasses[status] || 'badge-neutral';
  };

  // Payment status badge classes
  const getPaymentStatusBadgeClass = (status) => {
    const statusClasses = {
      'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
      'paid': 'bg-green-100 text-green-800 border-green-200',
      'completed': 'bg-green-100 text-green-800 border-green-200',
      'failed': 'bg-red-100 text-red-800 border-red-200',
      'refunded': 'bg-gray-100 text-gray-800 border-gray-200'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800 border-gray-200';
  };

  // Payment status badge classes for DaisyUI (for Index page)
  const getPaymentStatusBadgeClassDaisy = (status) => {
    const statusClasses = {
      'pending': 'badge-warning',
      'completed': 'badge-success',
      'paid': 'badge-success',
      'failed': 'badge-error',
      'refunded': 'badge-secondary'
    };
    return statusClasses[status] || 'badge-neutral';
  };

  // Status icons
  const getStatusIcon = (status) => {
    const icons = {
      'pending': ClockIcon,
      'processing': TruckIcon,
      'shipped': TruckIcon,
      'delivered': CheckCircleIcon,
      'cancelled': XCircleIcon,
      'refunded': CurrencyDollarIcon
    };
    return icons[status] || ClockIcon;
  };

  // Format date
  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
  };

  // Format date for Index page
  const formatDateIndex = (dateString) => {
    const order_date = new Date(dateString);
    return `${order_date.getDate()}-${order_date.getMonth() + 1
      }-${order_date.getFullYear()} ${order_date.getHours()}:${order_date.getMinutes()}:${order_date.getSeconds()}`;
  };

  // Format currency
  const formatCurrency = (amount) => {
    return `$${parseFloat(amount).toFixed(2)}`;
  };

  return {
    getStatusBadgeClass,
    getStatusBadgeClassDaisy,
    getPaymentStatusBadgeClass,
    getPaymentStatusBadgeClassDaisy,
    getStatusIcon,
    formatDate,
    formatDateIndex,
    formatCurrency
  };
}
