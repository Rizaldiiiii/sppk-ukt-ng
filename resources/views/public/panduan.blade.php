@php
    $tahapanAlur = $kontenAlur ? (json_decode($kontenAlur->isi, true)['tahapan'] ?? []) : [];
@endphp
<x-layouts.guest title="Panduan Visual — Alur Penentuan UKT (KF-10)" :showScrollProgress="true">
    <style>
        .kf10-wrap {
            max-width: 920px;
            margin: 0 auto;
        }
        .kf10-badge {
            display: inline-block;
            background: #F5B301;
            color: #550b14;
            font-family: "Poppins", "Inter", sans-serif;
            font-weight: 800;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 14px;
        }
        .kf10-accent {
            width: 72px;
            height: 4px;
            border-radius: 4px;
            margin: 16px auto 18px;
            background: linear-gradient(90deg, #550b14, #F5B301);
        }
        .kf10-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 26px;
        }
        .kf10-legend span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            font-weight: 700;
            color: #550b14;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(85, 11, 20, 0.14);
            border-radius: 999px;
            padding: 5px 12px;
        }
        .kf10-legend i.sw {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            display: inline-block;
            flex-shrink: 0;
        }
        .kf10-legend i.mhs { background: #2b2826; border: 2px solid #F5B301; }
        .kf10-legend i.petugas { background: #ffffff; border: 2px solid #F5B301; }
        .kf10-legend i.pimpinan { background: #3d3a38; border: 2px solid #F5B301; }
        .kf10-legend i.sistem { background: #e8ebee; border: 2px solid #9aa3ab; }

        .kf10-road-wrap {
            position: relative;
            padding: 8px 0 4px;
        }
        .kf10-road {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        .kf10-stage {
            position: relative;
            z-index: 1;
            display: flex;
            margin-bottom: 28px;
        }
        .kf10-stage.left { justify-content: flex-start; }
        .kf10-stage.right { justify-content: flex-end; }
        .kf10-card {
            width: min(430px, 88%);
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(85, 11, 20, 0.10);
            padding: 20px 22px;
            position: relative;
        }
        .kf10-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
            background: linear-gradient(90deg, #550b14, #F5B301);
        }
        .kf10-num {
            position: absolute;
            top: -16px;
            left: 20px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #550b14;
            color: #ffffff;
            font-family: "Poppins", "Inter", sans-serif;
            font-weight: 800;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #F5B301;
            box-shadow: 0 4px 10px rgba(85, 11, 20, 0.25);
        }
        .kf10-actor {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 11px;
            border-radius: 999px;
            margin-top: 4px;
        }
        .kf10-actor.mhs { background: #2b2826; color: #ffffff; }
        .kf10-actor.petugas { background: #F5E8E9; color: #550b14; border: 1px solid rgba(85, 11, 20, 0.3); }
        .kf10-actor.pimpinan { background: #3d3a38; color: #ffffff; border: 1px solid #F5B301; }
        .kf10-art {
            background: linear-gradient(135deg, #F5E8E9, #FDF6E3);
            border: 1px solid rgba(85, 11, 20, 0.14);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            margin: 12px 0;
        }
        .kf10-art svg {
            width: 168px;
            height: 132px;
        }
        .kf10-card h2 {
            font-family: "Poppins", sans-serif;
            font-size: 19px;
            font-weight: 700;
            color: #550b14;
            line-height: 1.3;
        }
        .kf10-card p {
            font-size: 14px;
            color: #6f6259;
            margin-top: 6px;
            line-height: 1.6;
        }
        .kf10-card p b {
            color: #2b2118;
        }
        .kf10-see {
            margin-top: 10px;
            font-size: 13px;
            background: #F5E8E9;
            border-radius: 8px;
            padding: 8px 10px;
            color: #6f6259;
        }
        .kf10-see b {
            color: #550b14;
        }
        .kf10-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 12px;
        }
        .kf10-chip {
            font-size: 12px;
            font-weight: 700;
            color: #550b14;
            background: #ffffff;
            border: 1px solid rgba(85, 11, 20, 0.14);
            border-radius: 999px;
            padding: 4px 10px;
        }
        .kf10-chip.arrow {
            background: transparent;
            border: none;
            color: #6f6259;
            padding: 4px 0;
        }
        .kf10-chip.final {
            background: #F5B301;
            border-color: #F5B301;
            color: #550b14;
        }
        .kf10-finish {
            text-align: center;
            position: relative;
            z-index: 1;
            margin: 4px 0 30px;
        }
        .kf10-finish .flag {
            display: inline-block;
            background: #550b14;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 18px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(85, 11, 20, 0.22);
        }
        .kf10-text-alt {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(85, 11, 20, 0.14);
            border-radius: 16px;
            padding: 22px 24px;
            box-shadow: 0 8px 24px rgba(85, 11, 20, 0.08);
        }
        .kf10-text-alt h2 {
            font-family: "Poppins", sans-serif;
            font-size: 19px;
            font-weight: 700;
            color: #550b14;
            margin-bottom: 8px;
        }
        .kf10-text-alt ol {
            padding-left: 22px;
            font-size: 14px;
            color: #6f6259;
            list-style-type: decimal;
        }
        .kf10-text-alt li {
            margin-bottom: 8px;
            line-height: 1.6;
        }
        .kf10-text-alt li b {
            color: #2b2118;
        }
        .kf10-note {
            font-size: 13px;
            color: #6f6259;
            margin-top: 12px;
            border-top: 1px dashed rgba(85, 11, 20, 0.14);
            padding-top: 12px;
            line-height: 1.6;
        }

        @media (max-width: 640px) {
            .kf10-stage.left,
            .kf10-stage.right {
                justify-content: flex-start;
            }
            .kf10-card {
                width: 100%;
            }
            .kf10-road {
                display: none;
            }
        }
    </style>

    <div class="kf10-wrap space-y-6">
        @if($apakahKedaluwarsa)
            <div role="alert" class="glass-card border-l-4 border-l-warning p-5 bg-warning-bg/85 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-[8px] bg-warning text-white font-heading font-bold text-[11px] uppercase tracking-wider">
                            Label Kedaluwarsa
                        </span>
                        <span class="font-heading font-bold text-[15px] text-ink">
                            Penjelasan tertulis Alur / Prosedur Keberatan telah diperbarui
                        </span>
                    </div>
                    <p class="text-[13px] text-ink-secondary">
                        Terdapat pembaruan pada halaman penjelasan tertulis (KF-02 / KF-06) setelah panduan visual ini terakhir disinkronkan. Acuan utama tetap berada pada halaman penjelasan tertulis.
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <x-button :href="route('publik.alur')" variant="secondary" class="text-[12px] px-3.5 py-2">
                        Buka Alur Tertulis
                    </x-button>
                </div>
            </div>
        @endif

        <header class="text-center mb-2">
            <span class="kf10-badge">KF-10 &middot; Panduan Visual</span>
            <h1 class="font-heading font-extrabold text-[26px] sm:text-[32px] md:text-[36px] text-sekunder leading-tight">
                {{ $kontenPanduan?->judul ?? 'Alur Penentuan UKT, dari Data sampai Keputusan' }}
            </h1>
            <p class="text-[#6f6259] max-w-[640px] mx-auto mt-2.5 text-[15px]">
                {{ $kontenPanduan?->ringkasan ?? 'Ikuti jalannya dari atas ke bawah. Setiap tahap ada karakternya sendiri — kenali dari pakaiannya — supaya langsung kebayang siapa melakukan apa.' }}
            </p>
            <div class="kf10-accent" aria-hidden="true"></div>

            <div class="kf10-legend" aria-label="Legenda aktor">
                <span><i class="sw mhs" aria-hidden="true"></i>Mahasiswa — toga</span>
                <span><i class="sw petugas" aria-hidden="true"></i>Petugas — lanyard + kartu ID</span>
                <span><i class="sw pimpinan" aria-hidden="true"></i>Pimpinan — jas gelap + dasi emas</span>
                <span><i class="sw sistem" aria-hidden="true"></i>Sistem — roda gigi, otomatis</span>
            </div>
        </header>

        <div class="kf10-road-wrap">
            <svg class="kf10-road" viewBox="0 0 920 1480" preserveAspectRatio="none" aria-hidden="true">
                <path
                    d="M460 0 C 180 140, 180 260, 460 380 C 740 500, 740 620, 460 740 C 180 860, 180 980, 460 1100 C 740 1220, 740 1340, 460 1480"
                    fill="none"
                    stroke="#3d3a38"
                    stroke-width="26"
                    stroke-linecap="round"
                />
                <path
                    d="M460 0 C 180 140, 180 260, 460 380 C 740 500, 740 620, 460 740 C 180 860, 180 980, 460 1100 C 740 1220, 740 1340, 460 1480"
                    fill="none"
                    stroke="#ffffff"
                    stroke-width="3"
                    stroke-dasharray="14 16"
                    stroke-linecap="round"
                />
            </svg>

            <!-- Tahap 1 : Mahasiswa, duduk menulis di meja -->
            <article class="kf10-stage left">
                <div class="kf10-card">
                    <span class="kf10-num">1</span>
                    <span class="kf10-actor mhs">Mahasiswa &middot; toga</span>
                    <div class="kf10-art">
                        <svg viewBox="0 0 180 132" fill="none" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="Mahasiswa bertoga duduk menulis formulir di meja">
                            <ellipse cx="70" cy="120" rx="58" ry="5" fill="rgba(85,11,20,.10)" stroke="none"/>
                            <!-- meja + kertas -->
                            <rect x="98" y="76" width="64" height="8" rx="2" fill="#b9a48f" stroke="#8a6d5a" stroke-width="2"/>
                            <path d="M156 84 L156 116" stroke="#8a6d5a" stroke-width="5"/>
                            <rect x="104" y="56" width="38" height="20" rx="2" fill="#fff" stroke="#550b14" stroke-width="2.5"/>
                            <path d="M110 63 h26 M110 69 h18" stroke="#F5B301" stroke-width="2.5"/>
                            <!-- kursi -->
                            <rect x="26" y="38" width="7" height="44" rx="2" fill="#b9a48f" stroke="#8a6d5a" stroke-width="2"/>
                            <rect x="26" y="78" width="36" height="7" rx="2" fill="#b9a48f" stroke="#8a6d5a" stroke-width="2"/>
                            <path d="M30 85 L30 116 M58 85 L58 116" stroke="#8a6d5a" stroke-width="4"/>
                            <!-- kaki belakang (duduk, tertekuk, sampai lantai) -->
                            <path d="M54 82 L82 82 M82 82 L82 112 M82 112 L92 112" stroke="#1f1c1a" stroke-width="8"/>
                            <!-- badan berisi: jubah toga hitam + selempang emas -->
                            <rect x="42" y="42" width="30" height="38" rx="13" fill="#2b2826" stroke="#2b2826" stroke-width="3"/>
                            <path d="M48 44 L66 78" stroke="#F5B301" stroke-width="3"/>
                            <!-- kaki depan (duduk, tertekuk, sampai lantai) -->
                            <path d="M58 76 L88 76 M88 76 L88 114 M88 114 L99 114" stroke="#2b2826" stroke-width="9"/>
                            <!-- kepala berisi + topi toga -->
                            <circle cx="57" cy="27" r="15" fill="#fff" stroke="#550b14" stroke-width="4"/>
                            <path d="M40 14 L57 6 L74 14 L57 22 Z" fill="#2b2826" stroke="#2b2826" stroke-width="2"/>
                            <path d="M72 14 L72 22" stroke="#F5B301" stroke-width="2.5"/>
                            <circle cx="72" cy="24" r="2.5" fill="#F5B301" stroke="none"/>
                            <!-- lengan menulis -->
                            <path d="M68 54 L102 62" stroke="#2b2826" stroke-width="8"/>
                            <path d="M44 54 L38 72" stroke="#2b2826" stroke-width="8"/>
                        </svg>
                    </div>
                    <h2>Isi Data &amp; Unggah Bukti</h2>
                    <p><b>Mahasiswa</b> mengisi data ekonomi keluarga: penghasilan, jumlah tanggungan, daya listrik, kondisi hunian, dan aset — lalu mengunggah dokumen buktinya.</p>
                    <p class="kf10-see"><b>Yang kamu lihat nanti:</b> setiap data ini menjadi satu baris di tabel rincian hasilmu.</p>
                </div>
            </article>

            <!-- Tahap 2 : Petugas, berdiri memeriksa dokumen -->
            <article class="kf10-stage right">
                <div class="kf10-card">
                    <span class="kf10-num">2</span>
                    <span class="kf10-actor petugas">Petugas &middot; lanyard + kartu ID</span>
                    <div class="kf10-art">
                        <svg viewBox="0 0 180 132" fill="none" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="Petugas berlanyard memeriksa dokumen dengan kaca pembesar">
                            <ellipse cx="80" cy="120" rx="62" ry="5" fill="rgba(85,11,20,.10)" stroke="none"/>
                            <!-- dokumen -->
                            <rect x="106" y="22" width="48" height="64" rx="4" fill="#fff" stroke="#550b14" stroke-width="3"/>
                            <path d="M114 38 l5 5 9 -11" stroke="#F5B301" stroke-width="4"/>
                            <path d="M114 56 h28 M114 66 h20" stroke="#550b14" stroke-width="2.5"/>
                            <!-- kaca pembesar -->
                            <circle cx="130" cy="58" r="13" fill="rgba(245,179,1,.18)" stroke="#F5B301" stroke-width="4"/>
                            <path d="M140 68 L150 78" stroke="#F5B301" stroke-width="5"/>
                            <!-- kaki berdiri -->
                            <path d="M48 84 L42 114 M62 84 L70 114" stroke="#550b14" stroke-width="9"/>
                            <!-- badan berisi: kemeja + lanyard + kartu ID -->
                            <rect x="38" y="40" width="30" height="46" rx="13" fill="#fffdf8" stroke="#550b14" stroke-width="3"/>
                            <path d="M46 40 L60 64 M62 40 L60 64" stroke="#F5B301" stroke-width="3"/>
                            <rect x="52" y="62" width="16" height="13" rx="2" fill="#fff" stroke="#550b14" stroke-width="2.5"/>
                            <path d="M55 67 h10 M55 71 h7" stroke="#550b14" stroke-width="1.6"/>
                            <!-- kepala berisi -->
                            <circle cx="53" cy="25" r="15" fill="#fff" stroke="#550b14" stroke-width="4"/>
                            <!-- lengan memegang kaca pembesar -->
                            <path d="M66 52 L132 70" stroke="#550b14" stroke-width="8"/>
                            <path d="M40 52 L34 70" stroke="#550b14" stroke-width="8"/>
                        </svg>
                    </div>
                    <h2>Verifikasi Dokumen oleh Petugas</h2>
                    <p><b>Petugas UKT</b> memeriksa apakah angka yang diisi cocok dengan bukti resmi: slip gaji, Kartu Keluarga, tagihan listrik, dan foto hunian.</p>
                    <p class="kf10-see"><b>Tujuannya:</b> tidak ada komponen yang dihitung dari data yang keliru atau belum terverifikasi.</p>
                </div>
            </article>

            <!-- Tahap 3 : Sistem (tanpa karakter manusia & tanpa label aktor), mesin beroda gigi mengeluarkan diagram batang 5 komponen -->
            <article class="kf10-stage left">
                <div class="kf10-card">
                    <span class="kf10-num">3</span>
                    <div class="kf10-art">
                        <svg viewBox="0 0 200 132" fill="none" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="Mesin sistem menghitung komponen penilaian, digambarkan roda gigi dan diagram batang">
                            <ellipse cx="62" cy="116" rx="44" ry="5" fill="rgba(85,11,20,.10)" stroke="none"/>
                            <!-- mesin penilaian -->
                            <circle cx="62" cy="34" r="12" fill="#fff" stroke="#550b14" stroke-width="3"/>
                            <path d="M62 18 v6 M62 44 v6 M46 34 h6 M72 34 h6 M51 23 l4 4 M69 41 l4 4 M73 23 l-4 4 M55 41 l-4 4" stroke="#550b14" stroke-width="3"/>
                            <rect x="30" y="46" width="64" height="58" rx="8" fill="#fff" stroke="#550b14" stroke-width="3"/>
                            <rect x="40" y="56" width="44" height="20" rx="3" fill="#FDF6E3" stroke="#F5B301" stroke-width="2.5"/>
                            <path d="M46 63 h32 M46 69 h22" stroke="#550b14" stroke-width="2.5"/>
                            <circle cx="46" cy="88" r="4" fill="#F5B301" stroke="none"/>
                            <circle cx="62" cy="88" r="4" fill="#550b14" stroke="none"/>
                            <circle cx="78" cy="88" r="4" fill="#F5B301" stroke="none"/>
                            <!-- panah ke diagram -->
                            <path d="M96 74 L110 74 M104 68 L112 74 L104 80" stroke="#F5B301" stroke-width="4"/>
                            <!-- diagram batang 5 komponen -->
                            <path d="M116 108 H192" stroke="#550b14" stroke-width="3"/>
                            <rect x="120" y="86" width="11" height="22" rx="2" fill="#550b14"/>
                            <rect x="134" y="70" width="11" height="38" rx="2" fill="#F5B301"/>
                            <rect x="148" y="78" width="11" height="30" rx="2" fill="#550b14"/>
                            <rect x="162" y="60" width="11" height="48" rx="2" fill="#F5B301"/>
                            <rect x="176" y="72" width="11" height="36" rx="2" fill="#550b14"/>
                            <text x="116" y="126" font-size="11" font-weight="700" fill="#550b14" stroke="none" font-family="Inter, sans-serif">Total 3,20 / 5,00</text>
                            <path d="M188 30 l3 6 M194 26 l-2 7" stroke="#F5B301" stroke-width="2.5"/>
                        </svg>
                    </div>
                    <h2>Dihitung per Komponen</h2>
                    <p><b>Sistem</b> memetakan setiap data ke kategorinya, lalu menghitung poin tiap komponen beserta kalimat alasannya — semuanya bisa kamu telusuri. Di gambar, sistem digambarkan sebagai mesin beroda gigi yang mengeluarkan diagram batang hasil perhitungan.</p>
                    <p class="kf10-see"><b>Yang kamu lihat nanti:</b> tabel rincian komponen, langkah perhitungan, dan alasan tiap baris.</p>
                </div>
            </article>

            <!-- Tahap 4 : Pimpinan, jas + dasi emas, memberi stempel -->
            <article class="kf10-stage right">
                <div class="kf10-card">
                    <span class="kf10-num">4</span>
                    <span class="kf10-actor pimpinan">Pimpinan &middot; jas gelap + dasi emas</span>
                    <div class="kf10-art">
                        <svg viewBox="0 0 180 132" fill="none" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="Pimpinan berjas dan berdasi memberi stempel pada surat keputusan">
                            <ellipse cx="80" cy="120" rx="62" ry="5" fill="rgba(85,11,20,.10)" stroke="none"/>
                            <!-- meja + surat keputusan -->
                            <rect x="100" y="92" width="60" height="8" rx="2" fill="#b9a48f" stroke="#8a6d5a" stroke-width="2"/>
                            <rect x="106" y="54" width="46" height="38" rx="3" fill="#fff" stroke="#550b14" stroke-width="3"/>
                            <path d="M112 64 h34 M112 72 h24" stroke="#550b14" stroke-width="2.5"/>
                            <circle cx="138" cy="82" r="8" fill="#F5B301" stroke="#550b14" stroke-width="2"/>
                            <!-- stempel di tangan -->
                            <rect x="112" y="38" width="7" height="14" rx="2" fill="#550b14"/>
                            <rect x="106" y="50" width="19" height="6" rx="2" fill="#F5B301"/>
                            <!-- kaki berdiri -->
                            <path d="M48 84 L42 114 M62 84 L70 114" stroke="#3d3a38" stroke-width="9"/>
                            <!-- badan berisi: jas gelap + kemeja + dasi emas -->
                            <rect x="38" y="40" width="30" height="46" rx="13" fill="#3d3a38" stroke="#2b2826" stroke-width="3"/>
                            <path d="M45 40 L53 40 L49 52 L45 40 Z" fill="#fff" stroke="none"/>
                            <path d="M53 40 L61 40 L57 52 L53 40 Z" fill="#fff" stroke="none"/>
                            <path d="M51 40 L55 40 L56 50 L53 60 L50 50 Z" fill="#F5B301" stroke="none"/>
                            <!-- kepala berisi -->
                            <circle cx="53" cy="25" r="15" fill="#fff" stroke="#550b14" stroke-width="4"/>
                            <!-- lengan memegang stempel -->
                            <path d="M66 52 L112 46" stroke="#3d3a38" stroke-width="8"/>
                            <path d="M40 52 L34 70" stroke="#3d3a38" stroke-width="8"/>
                        </svg>
                    </div>
                    <h2>Ditetapkan oleh Kampus</h2>
                    <p><b>Pimpinan kampus yang berwenang</b> menetapkan golongan dan nominal UKT per semester melalui keputusan resmi. Sistem hanya menampilkan dan menjelaskan hasil itu.</p>
                    <p class="kf10-see"><b>Yang kamu lihat nanti:</b> golongan, nominal per semester, tanggal penetapan, dan sumbernya.</p>
                </div>
            </article>

            <!-- Tahap 5 : Mahasiswa + Petugas, serah terima surat keberatan -->
            <article class="kf10-stage left">
                <div class="kf10-card">
                    <span class="kf10-num">5</span>
                    <span class="kf10-actor mhs">Mahasiswa</span>
                    <span class="kf10-actor petugas">Petugas</span>
                    <div class="kf10-art">
                        <svg viewBox="0 0 220 132" fill="none" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="Mahasiswa bertoga menyerahkan surat keberatan kepada petugas berlanyard">
                            <ellipse cx="48" cy="120" rx="40" ry="5" fill="rgba(85,11,20,.10)" stroke="none"/>
                            <ellipse cx="172" cy="120" rx="40" ry="5" fill="rgba(85,11,20,.10)" stroke="none"/>
                            <!-- surat di tengah -->
                            <rect x="96" y="52" width="34" height="24" rx="3" fill="#fff" stroke="#F5B301" stroke-width="3"/>
                            <path d="M96 54 L113 66 L130 54" stroke="#F5B301" stroke-width="2.5"/>
                            <text x="146" y="46" font-size="24" font-weight="800" fill="#F5B301" stroke="none" font-family="Poppins, sans-serif">?</text>
                            <!-- mahasiswa kiri -->
                            <path d="M42 84 L36 114 M56 84 L62 114" stroke="#2b2826" stroke-width="9"/>
                            <rect x="34" y="40" width="30" height="46" rx="13" fill="#2b2826" stroke="#2b2826" stroke-width="3"/>
                            <path d="M40 42 L58 84" stroke="#F5B301" stroke-width="3"/>
                            <circle cx="49" cy="25" r="15" fill="#fff" stroke="#550b14" stroke-width="4"/>
                            <path d="M32 13 L49 5 L66 13 L49 21 Z" fill="#2b2826" stroke="#2b2826" stroke-width="2"/>
                            <path d="M64 13 L64 21" stroke="#F5B301" stroke-width="2.5"/>
                            <circle cx="64" cy="23" r="2.5" fill="#F5B301" stroke="none"/>
                            <path d="M62 52 L98 60" stroke="#2b2826" stroke-width="8"/>
                            <path d="M36 52 L30 70" stroke="#2b2826" stroke-width="8"/>
                            <!-- petugas kanan -->
                            <path d="M166 84 L160 114 M180 84 L186 114" stroke="#550b14" stroke-width="9"/>
                            <rect x="158" y="40" width="30" height="46" rx="13" fill="#fffdf8" stroke="#550b14" stroke-width="3"/>
                            <path d="M166 40 L180 64 M182 40 L180 64" stroke="#F5B301" stroke-width="3"/>
                            <rect x="172" y="62" width="16" height="13" rx="2" fill="#fff" stroke="#550b14" stroke-width="2.5"/>
                            <path d="M175 67 h10 M175 71 h7" stroke="#550b14" stroke-width="1.6"/>
                            <circle cx="173" cy="25" r="15" fill="#fff" stroke="#550b14" stroke-width="4"/>
                            <path d="M160 52 L130 60" stroke="#550b14" stroke-width="8"/>
                            <path d="M186 52 L192 70" stroke="#550b14" stroke-width="8"/>
                        </svg>
                    </div>
                    <h2>Keberatan, bila Hasil Tidak Sesuai</h2>
                    <p><b>Mahasiswa</b> yang merasa golongan belum mencerminkan kondisi ekonomi sebenarnya menyerahkan surat keberatan beserta bukti, lalu <b>petugas</b> meninjaunya.</p>
                    <div class="kf10-chips" aria-label="Tahapan status keberatan">
                        <span class="kf10-chip">Diajukan</span>
                        <span class="kf10-chip arrow" aria-hidden="true">&rarr;</span>
                        <span class="kf10-chip">Dalam Peninjauan</span>
                        <span class="kf10-chip arrow" aria-hidden="true">&rarr;</span>
                        <span class="kf10-chip">Perlu Perbaikan</span>
                        <span class="kf10-chip arrow" aria-hidden="true">&rarr;</span>
                        <span class="kf10-chip final">Disetujui / Ditolak</span>
                    </div>
                    <p class="kf10-see"><b>Keputusan</b> dibuat petugas berwenang berdasarkan bukti, dan tercatat di riwayat.</p>
                </div>
            </article>

            <div class="kf10-finish space-y-4">
                <div>
                    <span class="flag">Selesai — kamu bisa memantau statusnya di sistem</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 pt-1">
                    <x-button :href="route('publik.alur')" variant="secondary">
                        Baca Penjelasan Alur Tertulis (KF-02)
                    </x-button>
                    <x-button :href="route('publik.prosedur-keberatan')" variant="primary">
                        Pelajari Prosedur Keberatan (KF-06)
                    </x-button>
                </div>
            </div>
        </div>

        <section class="kf10-text-alt" aria-label="Versi teks dari diagram alur">
            <h2>Versi teks (alternatif dari diagram di atas)</h2>
            <ol>
                <li><b>Isi Data &amp; Unggah Bukti</b> — Aktor: Mahasiswa (toga). Mengisi data ekonomi keluarga dan mengunggah dokumen bukti.</li>
                <li><b>Verifikasi Dokumen oleh Petugas</b> — Aktor: Petugas (lanyard + kartu ID). Memeriksa kesesuaian angka dengan bukti resmi.</li>
                <li><b>Dihitung per Komponen</b> — Tanpa aktor manusia: sistem digambarkan sebagai mesin beroda gigi yang menghitung poin tiap komponen beserta kalimat alasannya, hasilnya berupa diagram batang.</li>
                <li><b>Ditetapkan oleh Kampus</b> — Aktor: Pimpinan (jas gelap + dasi emas). Menetapkan golongan dan nominal UKT lewat keputusan resmi.</li>
                <li><b>Keberatan, bila Hasil Tidak Sesuai</b> — Aktor: Mahasiswa + Petugas. Mahasiswa menyerahkan surat keberatan, petugas meninjau; statusnya Diajukan &rarr; Dalam Peninjauan &rarr; Perlu Perbaikan &rarr; Disetujui / Ditolak.</li>
            </ol>
            <p class="kf10-note">
                Diagram ini pelengkap penjelasan tertulis dan tabel (<a href="{{ route('publik.alur') }}" class="font-semibold text-sekunder underline">KF-02</a> dan <a href="{{ route('publik.prosedur-keberatan') }}" class="font-semibold text-sekunder underline">KF-06</a>), bukan penggantinya. Tahapannya sama dengan halaman Alur dan Prosedur Keberatan. Bila alur berubah dan diagram belum diperbarui, diagram diberi label kedaluwarsa.
            </p>
        </section>
    </div>
</x-layouts.guest>

