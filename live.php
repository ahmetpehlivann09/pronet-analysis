<?php
declare(strict_types=1);

/**
 * Pronet Sportsbook - single file, in-memory frontend.
 * No database, no SQL and no server-side event persistence.
 * Incoming WebSocket messages are parsed and rendered in browser memory.
 */
const WS_URL = 'wss://bragi-ws4.pronetgaming.eu/overview/1963betebet.com/1/0/d?X-Atmosphere-tracking-id=0&X-Atmosphere-Framework=4.0.1&X-Atmosphere-Transport=websocket&Content-Type=application/json';
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GoalZone | Sportsbook</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#0b0e13;--panel:#141922;--panel2:#1a202b;--line:#29313d;--muted:#8d98a8;--text:#edf1f7;--yellow:#ffd21f;--green:#70c58b;--red:#e45b66}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,Arial,sans-serif;font-size:13px}
a{color:inherit;text-decoration:none}.topbar{height:58px;background:#111720;border-bottom:1px solid var(--line);display:flex;align-items:center;padding:0 18px;gap:24px;position:sticky;top:0;z-index:20}
.brand{font-size:22px;font-weight:800;letter-spacing:-1px}.brand b{color:var(--yellow)}.topnav{display:flex;gap:20px;color:#b9c2ce;font-weight:600}.topnav a.active{color:var(--yellow)}
.top-actions{margin-left:auto;display:flex;align-items:center;gap:10px}.conn{font-size:11px;color:var(--muted)}.conn i{font-size:8px;color:#777;margin-right:6px}.conn.online i{color:var(--green)}
.btn-darkline{background:#202733;border:1px solid #36404e;color:#e9edf3;border-radius:6px;padding:7px 11px;font-size:12px}.btn-yellow{background:var(--yellow);color:#171717;border:0;border-radius:6px;font-weight:800;padding:8px 14px}
.layout{display:grid;grid-template-columns:230px minmax(420px,1fr) 300px;gap:12px;padding:12px;max-width:1800px;margin:auto;align-items:start}
.sidebar,.slip,.main-panel{background:var(--panel);border:1px solid var(--line);border-radius:9px;overflow:hidden}
.sidebar{position:sticky;top:70px;max-height:calc(100vh - 82px);overflow:auto}.side-head{padding:13px 14px;border-bottom:1px solid var(--line);font-weight:800}.search-wrap{padding:10px}.search-wrap input{width:100%;background:#0e131b;border:1px solid var(--line);border-radius:6px;color:white;padding:9px 10px;outline:none;font-size:12px}
.side-section{padding:10px 12px 5px;color:var(--muted);font-size:10px;text-transform:uppercase;letter-spacing:1px;font-weight:800}
.sport-item,.league-item{display:flex;align-items:center;gap:9px;padding:9px 13px;color:#cbd3de;cursor:pointer;border-left:3px solid transparent}.sport-item:hover,.league-item:hover{background:#1d2531}.sport-item.active,.league-item.active{background:#242b35;border-left-color:var(--yellow);color:white}.sport-item .count{margin-left:auto;color:var(--muted);font-size:10px}.league-item{padding:7px 13px 7px 30px;font-size:12px}
.center{min-width:0}.hero{background:linear-gradient(110deg,#202938,#171e29);border:1px solid #303b4b;border-radius:9px;padding:16px 18px;margin-bottom:12px;display:flex;align-items:center;gap:15px}.hero-icon{width:42px;height:42px;border-radius:10px;background:#313b4b;display:grid;place-items:center;color:var(--yellow);font-size:20px}.hero h1{font-size:17px;font-weight:800;margin:0 0 4px}.hero p{color:var(--muted);margin:0;font-size:11px}.hero .hero-right{margin-left:auto;text-align:right}.hero .hero-right strong{font-size:20px;display:block}
.toolbar{display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap}.filter{background:#1b222d;border:1px solid var(--line);border-radius:6px;padding:7px 11px;color:#c8d0da;font-size:11px}.filter.active{background:#3a3212;border-color:#74631a;color:var(--yellow)}
.league-block{background:var(--panel);border:1px solid var(--line);border-radius:8px;margin-bottom:10px;overflow:hidden}.league-title{display:flex;align-items:center;padding:11px 13px;background:#1b222c;border-bottom:1px solid var(--line);gap:9px;font-weight:700}.league-title .flag{font-size:15px}.league-title small{font-weight:400;color:var(--muted);margin-left:3px}.league-title .league-count{margin-left:auto;color:var(--muted);font-size:10px}
.match-head,.match-row{display:grid;grid-template-columns:minmax(170px,1.5fr) 76px 76px 76px;gap:6px;align-items:center;padding:9px 12px}.match-head{color:var(--muted);font-size:10px;text-align:center;border-bottom:1px solid #242c37}.match-head span:first-child{text-align:left}.match-row{border-bottom:1px solid #222a35;min-height:65px}.match-row:last-child{border-bottom:0}.match-row:hover{background:#181f29}
.match-info{min-width:0}.match-time{color:var(--muted);font-size:10px;margin-bottom:5px}.match-time.live{color:var(--red);font-weight:800}.teams{font-size:12px;font-weight:600;line-height:1.55}.teams .away{color:#b1bac7}.score{font-size:11px;color:var(--yellow);font-weight:800;margin-left:8px}.odds-cell{display:flex;justify-content:center}.odd{width:100%;background:#222b37;border:1px solid #303b49;color:#f1f3f6;border-radius:5px;padding:9px 3px;text-align:center;cursor:pointer;font-weight:700;font-size:12px}.odd:hover{border-color:var(--yellow);background:#302b18}.odd.selected{background:#5a4b10;border-color:var(--yellow);color:#fff}.odd:disabled{opacity:.35;cursor:not-allowed}.more-markets{color:var(--muted);font-size:10px;text-align:center;cursor:pointer;margin-top:5px}
.empty{padding:48px 18px;text-align:center;color:var(--muted)}.empty i{font-size:28px;color:#465264;margin-bottom:12px}.empty strong{display:block;color:#cbd3df;margin-bottom:5px}
.slip{position:sticky;top:70px}.slip-head{padding:13px 14px;border-bottom:1px solid var(--line);display:flex;align-items:center;font-weight:800}.slip-head span{margin-left:auto;color:var(--yellow);font-size:11px}.slip-tabs{display:flex;padding:9px;gap:6px}.slip-tab{flex:1;background:#1d2530;color:#aeb8c5;border:0;border-radius:5px;padding:8px;font-size:11px}.slip-tab.active{background:#3a3212;color:var(--yellow);font-weight:700}.slip-body{padding:10px}.slip-empty{text-align:center;color:var(--muted);padding:35px 10px}.slip-empty i{font-size:25px;display:block;margin-bottom:10px}.selection{background:#1d2530;border:1px solid #303b49;border-radius:6px;padding:10px;margin-bottom:8px}.selection-top{display:flex;gap:8px}.selection-name{font-weight:700;flex:1}.remove{border:0;background:none;color:#8d98a8}.selection-meta{font-size:10px;color:var(--muted);margin-top:5px}.selection-odd{color:var(--yellow);font-weight:800;font-size:14px}.stake{width:100%;background:#0d1219;border:1px solid var(--line);border-radius:5px;color:white;padding:9px;margin:8px 0}.summary-line{display:flex;justify-content:space-between;color:#aeb8c5;margin:8px 0;font-size:11px}.summary-line.total{font-size:14px;font-weight:800;color:white;border-top:1px solid var(--line);padding-top:12px}.place-bet{width:100%;padding:12px;background:#3a414b;border:0;border-radius:6px;color:#87909c;font-weight:800}.statusbar{background:#10151d;border:1px solid var(--line);border-radius:7px;padding:9px 12px;margin-bottom:10px;display:flex;align-items:center;gap:10px;font-size:11px;color:var(--muted)}.statusbar .status-dot{width:7px;height:7px;background:#737d89;border-radius:50%}.statusbar.connected .status-dot{background:var(--green);box-shadow:0 0 8px #70c58b80}.statusbar .status-text{flex:1}.tiny-btn{background:none;border:0;color:#aab4c1;font-size:11px}
@media(max-width:1150px){.layout{grid-template-columns:205px minmax(380px,1fr)}.slip{grid-column:1/-1;position:static}.slip-body{display:grid;grid-template-columns:1fr 1fr;gap:12px}.slip-empty{padding:10px}}
@media(max-width:760px){.topbar{padding:0 10px}.topnav{display:none}.layout{display:flex;flex-direction:column;padding:8px}.sidebar,.center,.slip{width:100%;position:static;max-height:none}.sidebar{order:2}.center{order:1}.slip{order:3}.match-head,.match-row{grid-template-columns:minmax(135px,1fr) repeat(3,58px);gap:4px;padding:8px}.odd{padding:9px 1px}.hero{padding:12px}.slip-body{display:block}}
</style>
</head>
<body>
<header class="topbar">
  <div class="brand">GOAL<b>ZONE</b></div>
  <nav class="topnav"><a class="active" href="#">SPOR</a><a href="#">CANLI</a><a href="#">BUGÜN</a><a href="#">SONUÇLAR</a></nav>
  <div class="top-actions">
    <span class="conn" id="connectionLabel"><i class="fa-solid fa-circle"></i> Bağlantı bekleniyor</span>
    <button class="btn-darkline" id="connectBtn"><i class="fa-solid fa-plug me-1"></i> Bağlan</button>
    <button class="btn-darkline" id="disconnectBtn" disabled><i class="fa-solid fa-power-off me-1"></i> Kes</button>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <div class="side-head"><i class="fa-solid fa-bars-staggered me-2"></i> Sporlar <span class="float-end text-secondary" id="sportsCount">0</span></div>
    <div class="search-wrap"><input id="searchInput" placeholder="Spor, ülke veya lig ara..."></div>
    <div class="side-section">Spor kategorileri</div>
    <div id="sportsList"><div class="empty py-4">Veri bekleniyor</div></div>
    <div class="side-section">Ligler</div>
    <div id="leaguesList"><div class="empty py-4">Henüz lig yok</div></div>
  </aside>

  <main class="center">
    <div class="statusbar" id="statusbar"><span class="status-dot"></span><span class="status-text" id="statusText">WebSocket bağlantısı kurulmadı. Bağlan butonuna basın.</span><button class="tiny-btn" id="clearDataBtn">Veriyi temizle</button></div>
    <section class="hero">
      <div class="hero-icon"><i class="fa-solid fa-trophy"></i></div>
      <div><h1 id="pageTitle">Tüm Sporlar</h1><p>Canlı karşılaşmalar ve yaklaşan etkinlikler</p></div>
      <div class="hero-right"><strong id="matchCount">0</strong><span class="text-secondary small">Karşılaşma</span></div>
    </section>
    <div class="toolbar">
      <button class="filter active" data-filter="all">TÜMÜ</button>
      <button class="filter" data-filter="live"><i class="fa-solid fa-circle-play me-1"></i> CANLI</button>
      <button class="filter" data-filter="upcoming">PROGRAM</button>
      <span class="ms-auto small text-secondary" id="lastUpdate">Henüz veri alınmadı</span>
    </div>
    <div id="matches">
      <div class="main-panel empty"><i class="fa-solid fa-satellite-dish"></i><strong>Karşılaşma verisi bekleniyor</strong>WebSocket üzerinden gelen add-fixture ve add-category mesajları burada gösterilecek.</div>
    </div>
  </main>

  <aside class="slip">
    <div class="slip-head"><i class="fa-solid fa-ticket me-2"></i> Bahis Kuponu <span id="slipCount">0 SEÇİM</span></div>
    <div class="slip-tabs"><button class="slip-tab active">Tekli</button><button class="slip-tab">Kombine</button><button class="slip-tab">Sistem</button></div>
    <div class="slip-body">
      <div id="slipSelections"><div class="slip-empty"><i class="fa-solid fa-ticket"></i>Kuponunuz boş.<br>Karşılaşmalardan oran seçin.</div></div>
      <div>
        <div class="summary-line"><span>Toplam oran</span><strong id="totalOdds">1.00</strong></div>
        <label class="small text-secondary" for="stakeInput">Bahis tutarı</label>
        <input class="stake" id="stakeInput" type="number" min="0" step="0.01" placeholder="Tutar girin">
        <div class="summary-line"><span>Olası kazanç</span><strong id="potentialWin">0.00</strong></div>
        <button class="place-bet" disabled>DEMO ARAYÜZÜ - BAHİS KABUL EDİLMEZ</button>
        <div class="text-secondary text-center mt-2" style="font-size:10px">Bu ekran yalnızca veri görüntüleme ve arayüz prototipidir.</div>
      </div>
    </div>
  </aside>
</div>

<script>
(() => {
  'use strict';
  const WS_URL = <?= json_encode(WS_URL, JSON_UNESCAPED_SLASHES) ?>;
  const $ = id => document.getElementById(id);
  const state = {
    socket:null, fixtures:new Map(), sports:new Map(), categories:new Map(),
    leagues:new Map(), selections:new Map(), filter:'all', selectedSport:null,
    selectedLeague:null, channelNullified:false, manualClose:false, lastUpdate:null
  };

  const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const number = value => Number.isFinite(Number(value)) ? Number(value) : 0;
  const fixtureId = f => String(f?.fId ?? f?.id ?? '');
  const sportName = id => state.sports.get(String(id))?.name || `Spor ${id}`;
  const categoryName = id => state.categories.get(String(id))?.name || `Kategori ${id}`;
  const leagueName = id => state.leagues.get(String(id))?.name || `Lig ${id}`;

  function setStatus(text, connected=false) {
    $('statusText').textContent = text;
    $('statusbar').classList.toggle('connected', connected);
    $('connectionLabel').classList.toggle('online', connected);
    $('connectionLabel').innerHTML = `<i class="fa-solid fa-circle"></i> ${connected ? 'Bağlandı' : 'Bağlantı yok'}`;
    $('connectBtn').disabled = connected;
    $('disconnectBtn').disabled = !state.socket || state.socket.readyState !== WebSocket.OPEN;
  }

  function logStatus(text) {
    setStatus(text, false);
    console.info('[GoalZone]', text);
  }

  function upsertSport(id, name) {
    if (id == null) return;
    state.sports.set(String(id), {id:String(id), name:name || sportName(id)});
  }
  function upsertCategory(id, sportId, name) {
    if (id == null) return;
    state.categories.set(String(id), {id:String(id), sportId:String(sportId ?? ''), name:name || categoryName(id)});
  }
  function upsertLeague(id, sportId, categoryId, name) {
    if (id == null) return;
    state.leagues.set(String(id), {id:String(id), sportId:String(sportId ?? ''), categoryId:String(categoryId ?? ''), name:name || leagueName(id)});
  }

  function normalizeFixture(f, ctx={}) {
    if (!f || typeof f !== 'object') return null;
    const id = fixtureId(f) || String(ctx.fId ?? '');
    if (!id) return null;
    const old = state.fixtures.get(id) || {};
    const m = f.mDat || old.mDat || {};
    const merged = {
      ...old, ...f, fId:id,
      stId:f.stId ?? ctx.stId ?? old.stId ?? '',
      cId:f.cId ?? ctx.cId ?? old.cId ?? '',
      seaId:f.seaId ?? ctx.seaId ?? old.seaId ?? '',
      mDat:{...old.mDat, ...m},
      btgs:Array.isArray(f.btgs) ? f.btgs : (old.btgs || [])
    };
    state.fixtures.set(id, merged);
    return merged;
  }

  function applyCategory(d) {
    const c = d.c || {};
    const stId = d.stId ?? '';
    const cId = c.cId ?? d.cId ?? '';
    upsertSport(stId, d.stN || sportName(stId));
    upsertCategory(cId, stId, c.cN || c.cName);
    (c.sns || []).forEach(season => {
      const leagueId = season.lId ?? season.sId;
      upsertLeague(leagueId, stId, cId, season.lName || season.seaN);
      (season.fs || []).forEach(f => normalizeFixture(f, {stId,cId,seaId:season.sId ?? leagueId}));
    });
  }

  function applyOdd(d) {
    const id = String(d.fId ?? '');
    const fixture = state.fixtures.get(id);
    const fo = d.fo || {};
    const foId = String(fo.foId ?? d.foId ?? '');
    if (!fixture || !foId) return;
    let found = false;
    fixture.btgs = (fixture.btgs || []).map(group => {
      if (String(group.btgId) !== String(d.btgId)) return group;
      const fos = (group.fos || []).map(option => {
        if (String(option.foId) === foId) { found=true; return {...option,...fo}; }
        return option;
      });
      if (!found) fos.push(fo);
      return {...group,fos};
    });
    if (!fixture.btgs.some(g => String(g.btgId) === String(d.btgId))) {
      fixture.btgs.push({btgId:d.btgId,btgN:d.btgN || d.mrkp || 'Market',fos:[fo]});
    }
    state.fixtures.set(id, fixture);
  }

  function applyMessage(message) {
    const items = Array.isArray(message?.ms) ? message.ms : [];
    items.forEach(item => {
      const type = item?.t, d = item?.d || {};
      if (type === 'add-category') applyCategory(d);
      if (type === 'add-fixture') {
        upsertSport(d.stId, sportName(d.stId));
        const f = normalizeFixture(d.f, d);
        if (f && !state.sports.has(String(d.stId))) upsertSport(d.stId, `Spor ${d.stId}`);
      }
      if (type === 'change-odd') applyOdd(d);
      if (type === 'remove-bet-type-group') {
        const f = state.fixtures.get(String(d.fId));
        if (f) f.btgs = (f.btgs || []).filter(g => String(g.btgId) !== String(d.btgId));
      }
      if (type === 'change-detail-size') {
        const f = state.fixtures.get(String(d.fId));
        if (f) f.fDS = d.fDS;
      }
    });
    if (items.length) {
      state.lastUpdate = new Date();
      $('lastUpdate').textContent = 'Güncellendi: ' + state.lastUpdate.toLocaleTimeString('tr-TR');
      renderAll();
    }
    return items.length;
  }

  function parseFrame(raw) {
    const text = String(raw || '').trim();
    if (!text) return null;
    if (text === 'CHANNEL_NULLIFIED') {
      state.channelNullified = true;
      logStatus('Sunucu CHANNEL_NULLIFIED gönderdi. Kanal geçersiz; yeniden bağlanma durduruldu.');
      return null;
    }
    if (text === 'X' || text === 'heartbeat' || text === 'HEARTBEAT') return null;
    let candidate = text;
    if (!(candidate.startsWith('{') || candidate.startsWith('['))) {
      const indexes = [candidate.indexOf('{'),candidate.indexOf('[')].filter(x => x >= 0);
      if (!indexes.length) { console.debug('Kontrol frame:', text); return null; }
      candidate = candidate.slice(Math.min(...indexes));
    }
    try { return JSON.parse(candidate); }
    catch (e) { console.warn('JSON frame ayrıştırılamadı', e, text.slice(0,500)); return null; }
  }

  function connect() {
    if (state.socket && [WebSocket.OPEN,WebSocket.CONNECTING].includes(state.socket.readyState)) return;
    state.manualClose = false;
    state.channelNullified = false;
    logStatus('WebSocket bağlantısı kuruluyor...');
    let ws;
    try { ws = new WebSocket(WS_URL); }
    catch (e) { logStatus('WebSocket başlatılamadı: ' + e.message); return; }
    state.socket = ws;
    ws.addEventListener('open', () => {
      setStatus('WebSocket açıldı. Veri mesajı bekleniyor...', true);
      console.info('WebSocket açık. Tarayıcı Origin başlığını otomatik belirler; özel Origin atanamaz.');
    });
    ws.addEventListener('message', event => {
      const parsed = parseFrame(event.data);
      if (parsed) {
        const count = applyMessage(parsed);
        if (count) setStatus(`${count} olay işlendi · ${state.fixtures.size} maç arayüzde`, true);
      }
    });
    ws.addEventListener('error', () => logStatus('WebSocket hatası. Kaynak sunucu bağlantıyı reddetmiş olabilir.'));
    ws.addEventListener('close', event => {
      state.socket = null;
      setStatus(`Bağlantı kapandı (${event.code})${event.reason ? ': '+event.reason : ''}`, false);
      if (state.channelNullified) logStatus('CHANNEL_NULLIFIED nedeniyle otomatik yeniden bağlanma yapılmadı.');
      else if (!state.manualClose) logStatus('Bağlantı kapandı. Yeniden denemek için Bağlan butonunu kullanın.');
    });
  }

  function disconnect() {
    state.manualClose = true;
    if (state.socket) state.socket.close(1000,'User disconnected');
    state.socket = null;
    setStatus('Bağlantı kullanıcı tarafından kapatıldı.',false);
  }

  function isLive(f) { return !!f.isLive; }
  function filteredFixtures() {
    return [...state.fixtures.values()].filter(f => {
      if (state.selectedSport && String(f.stId) !== state.selectedSport) return false;
      if (state.selectedLeague && String(f.seaId) !== state.selectedLeague) return false;
      if (state.filter === 'live' && !isLive(f)) return false;
      if (state.filter === 'upcoming' && isLive(f)) return false;
      return true;
    }).sort((a,b) => Number(!!b.isLive)-Number(!!a.isLive) || number(a.fsd)-number(b.fsd));
  }

  function renderSidebar() {
    $('sportsCount').textContent = state.sports.size;
    const sports = [...state.sports.values()].sort((a,b)=>a.name.localeCompare(b.name,'tr'));
    $('sportsList').innerHTML = sports.length ? sports.map(s => {
      const count = [...state.fixtures.values()].filter(f=>String(f.stId)===s.id).length;
      return `<div class="sport-item ${state.selectedSport===s.id?'active':''}" data-sport="${esc(s.id)}"><i class="fa-solid fa-medal"></i><span>${esc(s.name)}</span><span class="count">${count}</span></div>`;
    }).join('') : '<div class="text-secondary px-3 py-3">Spor verisi bekleniyor</div>';
    const leagues = [...state.leagues.values()].filter(l=>!state.selectedSport || l.sportId===state.selectedSport).sort((a,b)=>a.name.localeCompare(b.name,'tr'));
    $('leaguesList').innerHTML = leagues.length ? leagues.map(l=>`<div class="league-item ${state.selectedLeague===l.id?'active':''}" data-league="${esc(l.id)}"><i class="fa-regular fa-flag"></i>${esc(l.name)}</div>`).join('') : '<div class="text-secondary px-3 py-3">Lig verisi bekleniyor</div>';
    $('sportsList').querySelectorAll('[data-sport]').forEach(el=>el.addEventListener('click',()=>{state.selectedSport=state.selectedSport===el.dataset.sport?null:el.dataset.sport;state.selectedLeague=null;renderAll();}));
    $('leaguesList').querySelectorAll('[data-league]').forEach(el=>el.addEventListener('click',()=>{state.selectedLeague=state.selectedLeague===el.dataset.league?null:el.dataset.league;renderAll();}));
  }

  function renderMatches() {
    const fixtures = filteredFixtures();
    $('matchCount').textContent = fixtures.length;
    $('pageTitle').textContent = state.selectedLeague ? leagueName(state.selectedLeague) : state.selectedSport ? sportName(state.selectedSport) : 'Tüm Sporlar';
    if (!fixtures.length) {
      $('matches').innerHTML = `<div class="main-panel empty"><i class="fa-solid fa-satellite-dish"></i><strong>${state.fixtures.size?'Filtreye uygun maç bulunamadı':'Karşılaşma verisi bekleniyor'}</strong>${state.fixtures.size?'Farklı bir spor veya filtre seçin.':'WebSocket üzerinden gelen add-fixture ve add-category mesajları burada gösterilecek.'}</div>`;
      return;
    }
    const groups = new Map();
    fixtures.forEach(f=>{
      const key = String(f.seaId || f.cId || f.stId || 'other');
      if(!groups.has(key)) groups.set(key,[]);
      groups.get(key).push(f);
    });
    $('matches').innerHTML = [...groups.entries()].map(([key,list])=>{
      const first=list[0], name=state.leagues.has(key)?leagueName(key):state.categories.has(key)?categoryName(key):sportName(first.stId);
      return `<section class="league-block"><div class="league-title"><span class="flag">🏆</span><span>${esc(name)}</span><small>${esc(categoryName(first.cId))}</small><span class="league-count">${list.length} MAÇ</span></div>
      <div class="match-head"><span>Karşılaşma</span><span>1</span><span>X</span><span>2</span></div>
      ${list.map(renderFixture).join('')}</section>`;
    }).join('');
    $('matches').querySelectorAll('[data-odd]').forEach(btn=>btn.addEventListener('click',()=>selectOdd(btn.dataset.fixture,btn.dataset.group,btn.dataset.option)));
  }

  function renderFixture(f) {
    const id=fixtureId(f), live=isLive(f), md=f.mDat||{};
    const main=(f.btgs||[]).find(g=>String(g.btgN||g.btgNO||'').toLowerCase().includes('1x2')) || (f.btgs||[]).find(g=>(g.fos||[]).length>=2);
    const options=(main?.fos||[]).slice(0,3);
    const byLabel = label => options.find(o => {
      const s=String(o.hSh||o.pSh||o.oc||'').toLowerCase();
      if(label==='1') return s===String(f.hcN||'').toLowerCase() || s==='1';
      if(label==='x') return s.includes('beraber') || s==='draw' || s==='x';
      return s===String(f.acN||'').toLowerCase() || s==='2';
    });
    const odds=[byLabel('1'),byLabel('x'),byLabel('2')];
    const cells=odds.map(o=>o?`<button class="odd" data-odd data-fixture="${esc(id)}" data-group="${esc(main?.btgId)}" data-option="${esc(o.foId)}" ${!o.valid||o.freeze?'disabled':''}>${number(o.hO).toFixed(2)}</button>`:`<button class="odd" disabled>-</button>`).join('');
    const kickoff=live?(md.mT||md.st||'CANLI'):formatTime(f.fsd);
    return `<div class="match-row"><div class="match-info"><div class="match-time ${live?'live':''}">${live?'<i class="fa-solid fa-circle me-1"></i>':''}${esc(kickoff)} ${live&&md.st?'· '+esc(md.st):''}</div><div class="teams">${esc(f.hcN||'Ev sahibi')} <span class="score">${esc(md.sc||'')}</span><br><span class="away">${esc(f.acN||'Deplasman')}</span></div></div>${cells}</div>`;
  }

  function formatTime(ms) {
    const n=Number(ms); if(!n) return 'Saat bekleniyor';
    const d=new Date(n); return Number.isNaN(d.getTime())?'Saat bekleniyor':d.toLocaleString('tr-TR',{day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
  }

  function selectOdd(fixture, groupId, optionId) {
    const f=state.fixtures.get(String(fixture)); if(!f) return;
    const g=(f.btgs||[]).find(x=>String(x.btgId)===String(groupId)); if(!g) return;
    const o=(g.fos||[]).find(x=>String(x.foId)===String(optionId)); if(!o || !o.valid || o.freeze) return;
    const key=String(optionId);
    if(state.selections.has(key)) state.selections.delete(key);
    else state.selections.set(key,{key,fixtureId:String(fixture),home:f.hcN,away:f.acN,market:g.btgN||g.btgNO||'Market',selection:o.hSh||o.pSh||o.oc||'Seçim',odd:number(o.hO)});
    renderSlip();
    renderMatches();
  }

  function renderSlip() {
    const list=[...state.selections.values()];
    $('slipCount').textContent=list.length+' SEÇİM';
    if(!list.length) $('slipSelections').innerHTML='<div class="slip-empty"><i class="fa-solid fa-ticket"></i>Kuponunuz boş.<br>Karşılaşmalardan oran seçin.</div>';
    else $('slipSelections').innerHTML=list.map(s=>`<div class="selection"><div class="selection-top"><div class="selection-name">${esc(s.selection)}</div><strong class="selection-odd">${s.odd.toFixed(2)}</strong><button class="remove" data-remove="${esc(s.key)}"><i class="fa-solid fa-xmark"></i></button></div><div class="selection-meta">${esc(s.home)} - ${esc(s.away)}<br>${esc(s.market)}</div></div>`).join('');
    const total=list.reduce((v,s)=>v*s.odd,1);
    $('totalOdds').textContent=total.toFixed(2);
    const stake=number($('stakeInput').value);
    $('potentialWin').textContent=(stake*total).toFixed(2);
    $('slipSelections').querySelectorAll('[data-remove]').forEach(b=>b.addEventListener('click',()=>{state.selections.delete(b.dataset.remove);renderSlip();renderMatches();}));
  }

  function renderAll(){renderSidebar();renderMatches();renderSlip();}
  $('connectBtn').addEventListener('click',connect);
  $('disconnectBtn').addEventListener('click',disconnect);
  $('stakeInput').addEventListener('input',renderSlip);
  $('searchInput').addEventListener('input',()=>{
    const q=$('searchInput').value.toLocaleLowerCase('tr');
    document.querySelectorAll('.sport-item,.league-item').forEach(el=>el.style.display=el.textContent.toLocaleLowerCase('tr').includes(q)?'flex':'none');
  });
  document.querySelectorAll('[data-filter]').forEach(b=>b.addEventListener('click',()=>{
    state.filter=b.dataset.filter;
    document.querySelectorAll('[data-filter]').forEach(x=>x.classList.toggle('active',x===b));
    renderMatches();
  }));
  $('clearDataBtn').addEventListener('click',()=>{
    state.fixtures.clear();state.sports.clear();state.categories.clear();state.leagues.clear();state.selections.clear();state.selectedSport=null;state.selectedLeague=null;renderAll();logStatus('Bellekteki arayüz verileri temizlendi.');
  });
  renderAll();
})();
</script>
</body>
</html>
