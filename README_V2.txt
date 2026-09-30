POSYANDU BINA WARGA - V2

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
