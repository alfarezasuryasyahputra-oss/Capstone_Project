POSYANDU BINA WARGA - V3

Project ini mempertahankan fitur lama dan menambahkan modul pelayanan Posyandu.

Fitur lama yang dipertahankan:
- Website publik
- Ajukan janji temu
- Login staff
- Dashboard staff
- CRUD Data Ibu Hamil
- CRUD Data Bayi/Balita
- Status appointment
- Setup akun staff

Fitur tambahan:
- Jam pertemuan pada appointment
- NIK Data Ibu Hamil
- Jadwal Posyandu
- Pelayanan balita: BB, TB/PB, lingkar kepala, status gizi, catatan
- Imunisasi
- Vitamin balita
- KMS Digital + grafik berat badan
- Pemeriksaan ANC ibu hamil: usia kehamilan, LILA, BB, tekanan darah, keluhan
- Vitamin/suplemen ibu hamil
- Laporan dan rekapitulasi bulanan
- Cetak / Save as PDF untuk Data Ibu Hamil, Data Balita, dan KMS
- Role Orang Tua/Ibu dengan dashboard kesehatan
- Akun Orang Tua dapat dibuat oleh Admin/Kader

MIGRASI DATABASE:
1. Backup database posyandu terlebih dahulu.
2. Pastikan database posyandu lama sudah ada.
3. Import database/upgrade_v2.sql melalui phpMyAdmin.
4. Jangan mengimpor ulang database/posyandu.sql jika ingin mempertahankan data lama.
5. Setelah migrasi berhasil, fitur baru dapat digunakan.

LOGIN ORANG TUA:
- Admin/Kader membuat akun dari menu Data Master > Akun Orang Tua.
- Orang tua login melalui parent/login.php.

PDF:
- Tombol Cetak / PDF menggunakan fitur print browser.
- Pada dialog cetak pilih printer "Save as PDF" / "Microsoft Print to PDF".

Catatan:
Project ini masih ditujukan untuk pengembangan lokal/akademik. Sebelum dipakai dengan data kesehatan nyata, lakukan penguatan keamanan (HTTPS, CSRF protection, role permissions, audit log, backup, session security, dan kebijakan retensi data).

UPGRADE LOGIN ORANG TUA (MULTI-ANAK)
------------------------------------
1. Setelah upgrade_v2.sql, import database/upgrade_parent_multi.sql ke database posyandu.
2. Relasi lama parent_users.child_id dan mother_id otomatis dimigrasikan ke tabel relasi baru.
3. Staff > Data Master > Akun Orang Tua sekarang dapat memilih beberapa anak sekaligus.
4. Orang tua login dari /parent/login.php dan dapat memilih anak pada dashboard jika memiliki lebih dari satu anak.
5. Orang tua hanya melihat anak/ibu yang terhubung ke akun tersebut.


DESAIN ULANG DASHBOARD ORANG TUA - V3
------------------------------------
- Dashboard baru dengan ringkasan jumlah anak, data ibu, jadwal, dan imunisasi.
- Pemilihan anak multi-anak menggunakan akun yang sama.
- Profil anak dan data pengukuran terakhir.
- Grafik pertumbuhan berat berdasarkan data pelayanan_balita.
- Riwayat pelayanan anak.
- Ringkasan imunisasi dan vitamin.
- Ringkasan data ibu dan pemeriksaan ANC terakhir.
- Jadwal Posyandu berbentuk kartu.
- Edukasi kesehatan dasar.
- Responsive untuk desktop, tablet, dan HP.
- CSS khusus orang tua berada di css/parent.css.
