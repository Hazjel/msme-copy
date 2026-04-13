<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string $satuan
 * @property numeric $harga_pokok
 * @property numeric $harga_jual
 * @property int $stok
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereHargaJual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereHargaPokok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereStok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barang whereUpdatedAt($value)
 */
	class Barang extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nomor
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int $supplier_id
 * @property int|null $user_id
 * @property numeric $total
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PembelianDetail> $details
 * @property-read int|null $details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReturPembelian> $returs
 * @property-read int|null $returs_count
 * @property-read \App\Models\Supplier $supplier
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembelian whereUserId($value)
 */
	class Pembelian extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $pembelian_id
 * @property int $barang_id
 * @property int $qty
 * @property numeric $harga
 * @property numeric $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Barang $barang
 * @property-read \App\Models\Pembelian $pembelian
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereBarangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail wherePembelianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PembelianDetail whereUpdatedAt($value)
 */
	class PembelianDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nomor
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int $pembelian_id
 * @property int $supplier_id
 * @property int|null $user_id
 * @property numeric $total
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReturPembelianDetail> $details
 * @property-read int|null $details_count
 * @property-read \App\Models\Pembelian $pembelian
 * @property-read \App\Models\Supplier $supplier
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian wherePembelianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelian whereUserId($value)
 */
	class ReturPembelian extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $retur_pembelian_id
 * @property int $pembelian_detail_id
 * @property int $barang_id
 * @property int $qty
 * @property numeric $harga
 * @property numeric $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Barang $barang
 * @property-read \App\Models\PembelianDetail $pembelianDetail
 * @property-read \App\Models\ReturPembelian $returPembelian
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereBarangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail wherePembelianDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereReturPembelianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReturPembelianDetail whereUpdatedAt($value)
 */
	class ReturPembelianDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $telepon
 * @property string|null $alamat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pembelian> $pembelians
 * @property-read int|null $pembelians_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

