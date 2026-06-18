// ============================================================
// LPG Point POS — Shared JS (localStorage)
// ============================================================

// ---- AUTH ----
const USERS = {
  'owner1': { id:'U1', username:'owner1', password:'1234', name:'Bilal', role:'Owner' },
  'owner2': { id:'U2', username:'owner2', password:'5678', name:'Bhai Jan',   role:'Manager' },
};
function getSession(){ try{ return JSON.parse(sessionStorage.getItem('lpg_sess')); }catch{ return null; } }
function setSession(u){ sessionStorage.setItem('lpg_sess', JSON.stringify(u)); }
function requireAuth(){
  if(!getSession()){ window.location.href='login.html'; }
}
function logout(){ sessionStorage.removeItem('lpg_sess'); window.location.href='login.html'; }

// ---- DB ----
const DB = {
  g(k,d={}){ try{ return JSON.parse(localStorage.getItem('lpg_'+k))??d; }catch{ return d; } },
  s(k,v){ localStorage.setItem('lpg_'+k, JSON.stringify(v)); },
  txns(){ return this.g('txns',[]); },
  setTxns(v){ this.s('txns',v); },
  addTxn(t){ const a=this.txns(); a.unshift(t); this.setTxns(a); },
  custs(){ return this.g('custs',{}); },
  setCusts(v){ this.s('custs',v); },
  ledger(){ return this.g('ledger',[]); },
  setLedger(v){ this.s('ledger',v); },
  addLedgerEntry(e){ const a=this.ledger(); a.unshift(e); this.setLedger(a); },
  prods(){ return this.g('prods',{}); },
  setProds(v){ this.s('prods',v); },
  inv(){ return this.g('inv',{}); },
  setInv(v){ this.s('inv',v); },
  nextId(pfx){ const k='ctr_'+pfx; let n=this.g(k,1000); n++; this.s(k,n); return pfx.toUpperCase()+n; }
};

// ---- SEED ----
function seedIfEmpty(){
  // Products
  if(!Object.keys(DB.prods()).length){
    DB.setProds({
      'P001':{id:'P001',name:'11 KG LPG Cylinder',cat:'Cylinder',salePrice:2800,refillCharge:800,returnDeposit:500,unit:'pcs'},
      'P002':{id:'P002',name:'5 KG Small Cylinder',cat:'Cylinder',salePrice:1400,refillCharge:500,returnDeposit:300,unit:'pcs'},
      'P003':{id:'P003',name:'45 KG Commercial',cat:'Cylinder',salePrice:9500,refillCharge:3500,returnDeposit:2000,unit:'pcs'},
      'P004':{id:'P004',name:'HOB Mini Cylinder',cat:'Cylinder',salePrice:600,refillCharge:250,returnDeposit:150,unit:'pcs'},
    });
  }
  // Inventory
  if(!Object.keys(DB.inv()).length){
    DB.setInv({
      'P001':{prodId:'P001',qty:24,minQty:5,maxQty:50},
      'P002':{prodId:'P002',qty:8,minQty:3,maxQty:30},
      'P003':{prodId:'P003',qty:6,minQty:2,maxQty:20},
      'P004':{prodId:'P004',qty:3,minQty:2,maxQty:20},
    });
  }
  // Customers + Txns
  if(!Object.keys(DB.custs()).length){
    const custs = {
      'C0001':{id:'C0001',name:'Ahmed Khan',phone:'0301-1234567',addr:'Block A, Gulshan',note:'Regular customer',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''},
      'C0002':{id:'C0002',name:'Bilal Raza',phone:'0312-9876543',addr:'Latifabad Unit 6',note:'',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''},
      'C0003':{id:'C0003',name:'Sara Bibi',phone:'0333-5544332',addr:'Qasimabad',note:'',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''},
      'C0004':{id:'C0004',name:'Usman Mir',phone:'0345-1122334',addr:'Hyderabad City',note:'Commercial client',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''},
      'C0005':{id:'C0005',name:'Farhan Shaikh',phone:'0321-7788990',addr:'PECHS Block 2',note:'',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''},
    };
    DB.setCusts(custs);
    _seedDemoTxns(custs);
  }
}

function _seedDemoTxns(custs){
  const prods = DB.getProds ? DB.getProds() : DB.prods();
  const inv = DB.inv();
  const ids = Object.keys(custs);
  const demos=[
    {ci:0,type:'SALE',  prodId:'P001',qty:2,pay:'cash'},
    {ci:1,type:'SALE',  prodId:'P001',qty:1,pay:'credit'},
    {ci:2,type:'SALE',  prodId:'P002',qty:1,pay:'cash'},
    {ci:3,type:'SALE',  prodId:'P003',qty:1,pay:'credit'},
    {ci:4,type:'SALE',  prodId:'P001',qty:3,pay:'cash'},
    {ci:1,type:'REFILL',prodId:'P001',qty:1,pay:'cash'},
    {ci:0,type:'RETURN',prodId:'P001',qty:1,pay:'cash'},
    {ci:3,type:'SALE',  prodId:'P002',qty:2,pay:'credit'},
  ];
  let txns=[], ledger=[];
  const ps = DB.prods();
  demos.forEach((d,i)=>{
    const c=custs[ids[d.ci]]; const p=ps[d.prodId];
    const amt = d.type==='SALE'?p.salePrice*d.qty : d.type==='REFILL'?p.refillCharge*d.qty : p.returnDeposit*d.qty;
    const now=new Date(); now.setHours(now.getHours()-(i*3+1));
    const txn={id:'TXN'+(1001+i),invNo:'INV'+(1001+i),custId:ids[d.ci],custName:c.name,custPhone:c.phone,
      type:d.type,prodId:d.prodId,prodName:p.name,qty:d.qty,
      unitPrice:d.type==='SALE'?p.salePrice:d.type==='REFILL'?p.refillCharge:p.returnDeposit,
      amount:amt,pay:d.pay,note:'',dt:now.toISOString(),addedBy:'U1'};
    txns.push(txn);
    // customer stats
    if(d.type==='SALE'){
      c.cylindersOut[d.prodId]=(c.cylindersOut[d.prodId]||0)+d.qty;
      if(d.pay==='credit'){c.creditSales+=amt;c.balance+=amt;}else c.cashSales+=amt;
    }else if(d.type==='REFILL'){
      if(d.pay==='credit'){c.creditSales+=amt;c.balance+=amt;}else c.cashSales+=amt;
    }else{
      c.cylindersOut[d.prodId]=Math.max(0,(c.cylindersOut[d.prodId]||0)-d.qty);
    }
    c.salesCount++; c.lastVisit=now.toISOString();
    // ledger entries
    if(d.type!=='RETURN'){
      const runBal = c.balance;
      ledger.push({id:'LED'+(2001+i),custId:ids[d.ci],dt:now.toISOString(),
        desc:`${p.name} x${d.qty} — ${d.type==='SALE'?'Sale':'Refill'}`,
        debit:d.pay==='credit'?amt:0,credit:d.pay!=='credit'?0:0,
        cashAmt:d.pay!=='credit'?amt:0,pay:d.pay,txnRef:txn.id,balance:runBal});
    }
    // inv
    if(d.type==='SALE' && inv[d.prodId]) inv[d.prodId].qty-=d.qty;
    else if(d.type==='RETURN' && inv[d.prodId]) inv[d.prodId].qty+=d.qty;
  });
  DB.setTxns(txns); DB.setCusts(custs); DB.setInv(inv); DB.setLedger(ledger);
}

// ---- HELPERS ----
function rs(n){ return 'Rs '+(n||0).toLocaleString(); }
function fmtDt(iso){ const d=new Date(iso); return d.toLocaleDateString('en-PK',{day:'2-digit',month:'short',year:'numeric'})+' '+d.toLocaleTimeString('en-PK',{hour:'2-digit',minute:'2-digit'}); }
function fmtDate(iso){ const d=new Date(iso); return d.toLocaleDateString('en-PK',{day:'2-digit',month:'short',year:'numeric'}); }
function fmtDateInput(iso){ const d=new Date(iso); return d.toISOString().split('T')[0]; }
function todayStr(){ return new Date().toISOString().split('T')[0]; }
function totalOut(c){ return Object.values(c.cylindersOut||{}).reduce((s,v)=>s+v,0); }
function payLabel(p){ return p==='cash'?'💵 Cash':p==='easypaisa'?'📱 EasyPaisa':p==='jazzcash'?'📱 JazzCash':'📒 Udhaar'; }
function payBadge(p){ return p==='cash'?'bg-cash':p==='easypaisa'?'bg-ep':p==='jazzcash'?'bg-jc':'bg-credit'; }
function typeBadge(t){ return t==='SALE'?'bg-sale':t==='RETURN'?'bg-return':'bg-refill'; }
function typeLabel(t){ return t==='SALE'?'Sale':t==='RETURN'?'Return':'Refill'; }
function pct(qty,max){ return max?Math.min(100,Math.round(qty/max*100)):0; }
function progClass(p){ return p<25?'low':p<50?'mid':'ok'; }

function toast(msg, type='s'){
  let wrap=document.getElementById('toast-wrap');
  if(!wrap){ wrap=document.createElement('div'); wrap.id='toast-wrap'; document.body.appendChild(wrap); }
  const el=document.createElement('div');
  const icon=type==='s'?'✅':type==='w'?'⚠️':'❌';
  el.className='toast-msg t'+type;
  el.innerHTML=icon+' '+msg;
  wrap.appendChild(el);
  setTimeout(()=>el.remove(),3500);
}

function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
document.addEventListener('click', e=>{
  document.querySelectorAll('.mo.show').forEach(m=>{ if(e.target===m) m.classList.remove('show'); });
  document.querySelectorAll('.ac-drop.show').forEach(d=>{ if(!d.parentElement.contains(e.target)) d.classList.remove('show'); });
});

// ---- SIDEBAR RENDER ----
function renderSidebar(activePage){
  const sess = getSession();
  const nav = [
    {section:'Main'},
    {id:'dashboard',label:'Dashboard',icon:'bi-grid-fill',href:'index.html'},
    {section:'Sales'},
    {id:'sale',label:'New Sale',icon:'bi-cart-plus-fill',href:'sale.html'},
    {id:'return',label:'Return',icon:'bi-arrow-return-left',href:'return.html'},
    {id:'refill',label:'Refill',icon:'bi-droplet-fill',href:'refill.html'},
    {section:'Records'},
    {id:'transactions',label:'Transactions',icon:'bi-receipt',href:'transactions.html'},
    {id:'customers',label:'Customers',icon:'bi-people-fill',href:'customers.html'},
    {section:'Stock'},
    {id:'inventory',label:'Inventory',icon:'bi-box-seam-fill',href:'inventory.html'},
    {id:'products',label:'Products',icon:'bi-tag-fill',href:'products.html'},
    {section:''},
    {id:'reports',label:'Reports',icon:'bi-bar-chart-fill',href:'reports.html'},
  ];
  let html='';
  nav.forEach(n=>{
    if(n.section!==undefined){
      if(n.section) html+=`<div class="sb-section">${n.section}</div>`;
    } else {
      const active = activePage===n.id?'active':'';
      html+=`<a class="sb-item ${active}" href="${n.href}" data-page="${n.id}"><i class="bi ${n.icon}"></i>${n.label}</a>`;
    }
  });
  const el=document.getElementById('sidebar-nav');
  if(el) el.innerHTML=html;
  // user
  const uel=document.getElementById('sb-user-name'); if(uel && sess) uel.textContent=sess.name;
  const url=document.getElementById('sb-user-role'); if(url && sess) url.textContent=sess.role;
  const uav=document.getElementById('sb-avatar'); if(uav && sess) uav.textContent=sess.name[0].toUpperCase();
}

// ---- AUTOCOMPLETE CUSTOMER ----
function setupCustAC(inputId, dropId, phoneId, custIdField){
  const inp=document.getElementById(inputId);
  const drop=document.getElementById(dropId);
  if(!inp||!drop) return;
  inp.addEventListener('input',()=>{
    const q=inp.value.toLowerCase().trim();
    const custs=DB.custs();
    if(!q){ drop.classList.remove('show'); return; }
    const matches=Object.values(custs).filter(c=>c.name.toLowerCase().includes(q)||c.phone.includes(q)).slice(0,6);
    if(!matches.length){ drop.classList.remove('show'); return; }
    drop.innerHTML=matches.map(c=>{
      const out=totalOut(c);
      return `<div class="ac-item" onclick="selectCust('${inputId}','${dropId}','${phoneId}','${custIdField}','${c.id}')">
        <div><div class="ain">${c.name}</div><div class="ais">${c.phone}${c.addr?' • '+c.addr:''}</div></div>
        ${out>0?`<span class="chip chip-o">${out} out</span>`:''}
      </div>`;
    }).join('');
    drop.classList.add('show');
  });
}
function selectCust(inpId,dropId,phoneId,custIdField,custId){
  const c=DB.custs()[custId];
  if(!c) return;
  const inp=document.getElementById(inpId); if(inp){ inp.value=c.name; inp.dataset.custId=custId; }
  const ph=document.getElementById(phoneId); if(ph) ph.value=c.phone;
  const cf=document.getElementById(custIdField); if(cf) cf.value=custId;
  document.getElementById(dropId).classList.remove('show');
  if(typeof onCustSelected==='function') onCustSelected(custId);
}

// ---- FIND OR CREATE CUSTOMER ----
function findOrCreateCust(name, phone){
  const custs=DB.custs();
  const found=Object.values(custs).find(c=>c.phone===phone||(c.name.toLowerCase()===name.toLowerCase()&&phone===''));
  if(found) return found.id;
  // create
  const id='C'+String(Object.keys(custs).length+1).padStart(4,'0');
  custs[id]={id,name,phone,addr:'',note:'',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''};
  DB.setCusts(custs);
  return id;
}

// ---- ADD CUSTOMER MODAL SUBMIT ----
function submitAddCust(){
  const name=document.getElementById('ac-name').value.trim();
  const phone=document.getElementById('ac-phone').value.trim();
  if(!name||!phone){ toast('Naam aur phone zaroori hai!','w'); return; }
  const custs=DB.custs();
  if(Object.values(custs).find(c=>c.phone===phone)){ toast('Is phone se customer pehle se mojood hai!','w'); return; }
  const id='C'+String(Object.keys(custs).length+1001).padStart(4,'0');
  custs[id]={id,name,phone,addr:document.getElementById('ac-addr')?.value.trim()||'',note:document.getElementById('ac-note')?.value.trim()||'',cylindersOut:{},balance:0,cashSales:0,creditSales:0,creditPaid:0,salesCount:0,lastVisit:''};
  DB.setCusts(custs);
  closeModal('addCustModal');
  toast(name+' add ho gaya! ✅','s');
  if(typeof onCustAdded==='function') onCustAdded(id, custs[id]);
}

// ---- RECEIPT PRINT ----
function printReceipt(modalId){
  const el=document.getElementById(modalId);
  const content=el.querySelector('.receipt-wrap').outerHTML;
  const win=window.open('','_blank','width=400,height=600');
  win.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Receipt</title>
  <style>body{margin:0;background:#fff;font-family:Inter,sans-serif;}
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
  .receipt-wrap{max-width:380px;margin:0 auto;}
  .rh{background:#0d1117;color:#fff;padding:18px 22px;display:flex;justify-content:space-between;}
  .rh .shop{font-size:16px;font-weight:800;} .rh small{font-size:10px;color:#999;display:block;}
  .rh .inv-meta{text-align:right;font-size:11px;color:#aaa;} .rh .inv-no{font-size:14px;font-weight:800;color:#f97316;}
  .rh .inv-type{font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#aaa;}
  .rb{padding:18px 22px;} .r2col{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
  .r-lbl{font-size:9px;font-weight:700;color:#888;letter-spacing:1px;text-transform:uppercase;margin-bottom:3px;}
  .r-val{font-size:13px;font-weight:600;} .r-val-sm{font-size:11px;color:#555;}
  .r-divider{border:none;border-top:1px dashed #ddd;margin:12px 0;}
  .rtbl{width:100%;border-collapse:collapse;margin-bottom:14px;}
  .rtbl th{background:#f5f5f5;padding:6px 8px;font-size:9px;font-weight:700;color:#666;text-transform:uppercase;text-align:left;}
  .rtbl td{padding:8px;border-bottom:1px solid #eee;font-size:12px;}
  .r-total-row{display:flex;justify-content:flex-end;}
  .r-total-box{background:#0d1117;color:#fff;padding:10px 16px;border-radius:6px;min-width:160px;text-align:right;}
  .r-total-box .tl{font-size:9px;color:#aaa;margin-bottom:2px;} .r-total-box .tv{font-size:18px;font-weight:800;color:#f97316;}
  .rf{background:#f9f9f9;padding:10px 22px;display:flex;justify-content:space-between;font-size:10px;color:#999;}
  </style></head><body>${content}</body></html>`);
  win.document.close(); win.focus(); setTimeout(()=>win.print(),400);
}

// Init
seedIfEmpty();