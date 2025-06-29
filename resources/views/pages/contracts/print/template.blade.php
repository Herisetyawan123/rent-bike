<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kontrak Sewa Kendaraan</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }
    h2 { text-align: center; text-transform: uppercase; }
    table { width: 100%; }
    td { vertical-align: top; padding-bottom: 6px; }
    .signature { margin-top: 60px; text-align: center; }
    .signature td { padding: 40px; }
  </style>
</head>
<body>

  <h2>Surat Perjanjian Sewa Kendaraan Bermotor</h2>

  <p>Pada hari ini {{ $tanggal }}, tahun {{ $tahun }}, kami yang bertanda tangan di bawah ini:</p>

  <ol>
    <li>
      Nama: {{ $vendor->name }}<br>
      Alamat KTP: {{ $vendor->vendor->address ?? '-' }}<br>
      <br>Untuk selanjutnya disebut sebagai <strong>Pihak Pertama</strong> atau Pemilik
    </li>
    <li>
      Nama: {{ $customer->name }}<br>
      Alamat: {{ $customer->renter->address ?? '-' }}<br>
      <br>Untuk selanjutnya menjadi <strong>Pihak Kedua</strong> atau Penyewa
    </li>
  </ol>

  <p>Selanjutnya kedua belah Pihak setuju untuk melakukan transaksi sewa menyewa satu unit Kendaraan Bermotor dengan spesifikasi sebagai berikut:</p>

  <ol>
    <li>Merk/Jenis: {{ $bike->bikeMerk->name ?? '-' }} {{ $bike->bikeType->name ?? '' }}</li>
    <li>Tahun: {{ $bike->year }}</li>
    <li>No. Polisi: {{ $bike->license_plate }}</li>
  </ol>

  @if (!empty($clauses) && count($clauses))
    <p>Adapun syarat sewa-menyewa sebagai berikut:</p>
    <ol>
      @foreach ($clauses as $clause)
        <li>{{ $clause }}</li>
      @endforeach
    </ol>
  @endif

  <p>
    Demikian surat perjanjian sewa menyewa ini dibuat agar dapat dipergunakan sebagaimana mestinya dan dibuat rangkap dua dengan dilampirkan materai serta mempunyai kekuatan hukum yang sama.
  </p>

  <br><br>

  <table class="signature">
    <tr>
      <td>Pihak Pertama</td>
      <td>Pihak Kedua</td>
    </tr>
    <tr>
      <td style="padding-top: 60px;">{{ $vendor->name }}</td>
      <td style="padding-top: 60px;">{{ $customer->name }}</td>
    </tr>
  </table>

</body>
</html>
