<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir Transaksi</title>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div x-data="kasirForm()" class="flex flex-col md:flex-row gap-6 max-w-7xl mx-auto p-6">
  <!-- LEFT: List Motor + Search -->
  <div class="md:w-2/3 w-full">
    <input type="text" x-model="searchQuery" placeholder="Cari motor..." class="w-full mb-4 border p-2 rounded shadow-sm">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 overflow-y-auto max-h-[80vh] pr-2">
      <template x-for="item in filteredBikes()" :key="item.id">
        <div class="border p-4 rounded shadow hover:bg-teal-100 cursor-pointer" @click="selectBike(item)">
          <img :src="'/storage/' + item.photo" alt="Gambar Motor" class="h-32 w-full object-cover rounded mb-2">
          <p class="font-semibold" x-text="item.bike_merk.name + ' - ' + item.license_plate"></p>
          <p class="text-sm text-gray-500">Harga: Rp <span x-text="formatRupiah(item.price)"></span></p>
        </div>
      </template>
    </div>
  </div>

  <!-- RIGHT: Sidebar Kasir -->
  <div class="md:w-1/3 w-full bg-white p-6 rounded-lg shadow">
    <template x-if="!selectedBike">
      <div class="text-center text-gray-500">
        <p class="text-lg font-semibold mb-2">Belum ada motor dipilih 🚵</p>
        <p class="text-sm">Silakan klik salah satu motor di sebelah kiri buat mulai transaksi.</p>
      </div>
    </template>

    <template x-if="selectedBike">
      <form action="{{ route('admin-vendor.transactions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="bike_id" :value="selectedBike.id">

        <div class="space-y-4">
          <h2 class="text-xl font-bold mb-2">Detail Transaksi</h2>

          <div class="bg-gray-100 p-3 rounded">
            <p class="font-medium" x-text="selectedBike.bike_merk.name + ' - ' + selectedBike.license_plate"></p>
            <p class="text-sm text-gray-500">Rp <span x-text="formatRupiah(selectedBike.price)"></span></p>
          </div>

          <div>
            <label class="block font-medium">Customer</label>
            <select name="customer_id" class="w-full border p-2 rounded">
              @foreach($customers as $cust)
                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block font-medium">Tanggal Mulai</label>
            <input type="datetime-local" name="start_date" x-model="startDate" class="w-full border p-2 rounded" required>
          </div>

          <div>
            <label class="block font-medium">Tanggal Selesai</label>
            <input type="datetime-local" name="end_date" x-model="endDate" class="w-full border p-2 rounded" required>
          </div>

          <div>
            <label class="block font-medium">Jenis Pengambilan</label>
            <select name="pickup_type" x-model="pickupType" class="w-full border p-2 rounded">
              <option value="pickup_self">Ambil Sendiri</option>
              <option value="delivery">Diantar</option>
            </select>
          </div>

          <template x-if="pickupType === 'delivery'">
            <div>
              <label class="block font-medium">Alamat Pengantaran</label>
              <textarea name="delivery_address" x-model="deliveryAddress" class="w-full border p-2 rounded"></textarea>
              <label class="block mt-2 font-medium">Biaya Pengantaran</label>
              <input type="number" name="delivery_fee" x-model="deliveryFee" class="w-full border p-2 rounded">
            </div>
          </template>

          <div>
            <label class="block font-medium">Add On</label>
            <template x-for="item in addons" :key="item.id">
              <div class="flex items-center gap-2">
                <input type="checkbox" :value="item.id" x-model="selectedAddons" :name="`addons[]`">
                <label x-text="`${item.name} (Rp ${formatRupiah(item.price)})`"></label>
              </div>
            </template>
          </div>

          <div>
            <label class="block font-medium">Metode Pembayaran</label>
            <select name="payment_method" x-model="paymentMethod" class="w-full border p-2 rounded">
              <option value="cash">Cash</option>
              <option value="transfer">Transfer Bank</option>
              <option value="ewallet">E-Wallet</option>
            </select>
          </div>

          <div class="text-lg font-bold mt-4">
            Total Harga: Rp <span x-text="formatRupiah(totalHarga())"></span>
          </div>

          <button type="submit" class="mt-4 bg-teal-600 text-white py-2 px-4 rounded hover:bg-teal-700 w-full">
            Simpan Transaksi
          </button>
        </div>
      </form>
    </template>
  </div>
</div>

<script>
  function kasirForm() {
    return {
      bikes: @json($bikes->load('bikeMerk', 'addOns')),
      selectedBike: null,
      addons: [],
      selectedAddons: [],
      startDate: '',
      endDate: '',
      pickupType: 'pickup_self',
      deliveryAddress: '',
      deliveryFee: 0,
      paymentMethod: 'cash',
      searchQuery: '',

      filteredBikes() {
        return this.bikes.filter(bike => {
          const keyword = this.searchQuery.toLowerCase();
          return (
            bike.bike_merk.name.toLowerCase().includes(keyword) ||
            bike.license_plate.toLowerCase().includes(keyword)
          );
        });
      },

      selectBike(bikeData) {
        this.selectedBike = bikeData;
        this.addons = bikeData.add_ons || [];
        this.selectedAddons = [];
      },

      totalHarga() {
        const base = this.selectedBike?.price || 0;
        const addonTotal = this.addons.filter(a => this.selectedAddons.includes(a.id)).reduce((sum, item) => sum + item.price, 0);
        const delivery = this.pickupType === 'delivery' ? parseInt(this.deliveryFee) || 0 : 0;
        return base + addonTotal + delivery;
      },

      formatRupiah(val) {
        return (val || 0).toLocaleString('id-ID');
      }
    }
  }
</script>

</body>
</html>
