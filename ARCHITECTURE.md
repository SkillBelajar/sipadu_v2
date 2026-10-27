# ARCHITECTURE.md — sipadu_v2

## Tech Stack
- Laravel 13, Livewire 3, TailwindCSS, MySQL 8, Redis, Pest PHP, Nginx, Docker
- Laravel Octane ditambah di slice akhir (belum sekarang)

## Roles
- admin, sekolah, verifikator, kabid

## Core Features
- Pengajuan layanan (multi-upload PDF)
- Workflow verifikasi
- Pelaporan Sarpras (foto)
- Activity logging

## Database Schema Draft
users, layanan, syarat_layanan, pengajuan, dokumen_pengajuan, laporan_sarpras, activity_logs

## Current Phase
Slice 8 — High Concurrency Optimization & Laravel Octane
