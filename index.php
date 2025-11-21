<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>E-Commerce — MyWebSite (Corrected & Optimized)</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    :root{--main:#4f46e5;--muted:#6b7280;}
    html,body{font-family:'Inter',system-ui,Arial;}

    /* Dark mode helper (prefers + class toggle) */
    html.dark{background:#0b1220;color:#e6eef8}
    html.dark .card{background:#0f1724}

    /* subtle floating animation for hero */
    @keyframes floaty {0%{transform:translateY(0)}50%{transform:translateY(-6px)}100%{transform:translateY(0)}}
    .floaty{animation:floaty 4s ease-in-out infinite}

    /* accessible visually-hidden */
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

    /* small app styles */
    .cart-badge{min-width:1.15rem;height:1.15rem;padding:0 .25rem;font-size:.65rem}

    /* animations for navbar dropdown */
    .slide-down{transform-origin:top;animation:navDrop .28s cubic-bezier(.2,.9,.4,1) both}
    @keyframes navDrop{from{opacity:0;transform:translateY(-6px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}

    /* small shadow override for dark */
    .card{background:#fff}

    @media (prefers-color-scheme: dark){html:not(.light){background:#0b1220;color:#e6eef8}} 
  </style>
</head>
<body class="antialiased bg-gray-50 dark:bg-[#071025] transition-colors duration-300">

  <!-- Toast -->
  <div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-lg shadow opacity-0 pointer-events-none transition-opacity duration-300 z-50">Added to cart</div>

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white dark:bg-[#071025] shadow-sm">
    <div class="max-w-6xl mx-auto flex items-center gap-4 px-4 md:px-6 py-3">
      <button id="menu-toggle" class="md:hidden p-2 text-gray-700 dark:text-gray-200">
        <i class="fas fa-bars text-lg"></i>
        <span class="sr-only">Open menu</span>
      </button>

      <h1 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">MyWebSite</h1>

      <nav id="navbar" class="hidden md:flex gap-6 ml-6">
        <a href="#home" class="hover:text-indigo-600">Home</a>
        <a href="#products" class="hover:text-indigo-600">Products</a>
        <a href="#about" class="hover:text-indigo-600">About</a>
      </nav>

      <div class="ml-auto flex items-center gap-3">
        <!-- theme toggle -->
        <button id="theme-toggle" class="p-2 rounded-md text-gray-700 dark:text-gray-200" aria-label="Toggle theme">
          <i id="theme-icon" class="fas fa-moon"></i>
        </button>

        <!-- login/admin -->
        <button id="login-btn" class="px-3 py-1 border rounded-md text-sm hidden md:inline">Login</button>

        <!-- cart -->
        <button id="cart-btn" class="relative p-2 rounded-md bg-indigo-50 dark:bg-transparent">
          <i class="fas fa-shopping-cart text-xl text-indigo-600"></i>
          <span id="cart-count" class="absolute -top-2 -right-2 inline-flex items-center justify-center bg-red-600 text-white text-xs font-semibold rounded-full h-5 w-5">0</span>
          <span class="sr-only">Open cart</span>
        </button>
      </div>
    </div>

    <!-- mobile menu (hidden by default) -->
    <div id="mobile-nav" class="md:hidden bg-white dark:bg-[#071025] border-t border-gray-100 dark:border-gray-800 px-4 py-3 hidden">
      <a href="#home" class="block py-2">Home</a>
      <a href="#products" class="block py-2">Products</a>
      <a href="#about" class="block py-2">About</a>
      <div class="mt-3 flex gap-2">
        <button onclick="openLogin()" class="flex-1 py-2 bg-indigo-600 text-white rounded">Login</button>
        <button onclick="openAdmin()" class="flex-1 py-2 border rounded">Admin</button>
      </div>
    </div>
  </header>

  <!-- MAIN (single-file multi-section app) -->
  <main class="max-w-6xl mx-auto px-4 md:px-6 py-8">

    <!-- HERO -->
    <section id="home" class="text-center py-10">
      <div class="max-w-3xl mx-auto">
        <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-slate-100 mb-4">Discover Your Next Favorite Product</h2>
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">High quality, unbeatable prices. Your shopping journey starts here.</p>
        <div class="flex justify-center gap-3">
          <a href="#products" class="px-6 py-3 bg-yellow-400 text-indigo-900 font-bold rounded-full shadow hover:bg-yellow-300 transition floaty">Shop Now</a>
          <button onclick="openProducts()" class="px-6 py-3 border rounded">Browse Catalog</button>
        </div>
      </div>
    </section>

    <!-- PRODUCTS GRID (main interactive area) -->
    <section id="products" class="mt-8">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-bold">Featured Products</h3>
        <div class="flex gap-2">
          <button id="open-admin" class="hidden md:inline px-3 py-1 border rounded">Admin</button>
          <button id="clear-storage" class="px-3 py-1 border rounded">Reset Data</button>
        </div>
      </div>

      <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- products injected by JS -->
      </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="mt-14">
      <div class="card p-6 rounded-xl shadow">
        <h4 class="text-2xl font-bold mb-2">About Syfex</h4>
        <p class="text-gray-600 dark:text-gray-300">This demo includes a working cart (client-side), admin area (local), login modal, and persistent products using localStorage.
        It's a single-file app meant for development & learning — replace client-side auth with a proper backend for production.</p>
      </div>
    </section>

  </main>

  <!-- CART DRAWER -->
  <aside id="cart-drawer" class="fixed right-4 top-16 w-80 max-w-full bg-white dark:bg-[#071025] shadow-xl rounded-lg p-4 translate-x-6 opacity-0 pointer-events-none transition-all z-50">
    <h4 class="font-bold mb-3">Your Cart</h4>
    <div id="cart-items" class="space-y-3 max-h-64 overflow-auto pb-2"></div>
    <div class="mt-4 flex justify-between items-center">
      <strong>Total:</strong>
      <span id="cart-total">$0.00</span>
    </div>
    <div class="mt-4 flex gap-2">
      <button id="checkout-btn" class="flex-1 py-2 bg-indigo-600 text-white rounded">Checkout</button>
      <button id="close-cart" class="py-2 px-3 border rounded">Close</button>
    </div>
  </aside>

  <!-- LOGIN MODAL -->
  <div id="login-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-[#071025] rounded-lg p-6 w-96">
      <h4 class="text-xl font-bold mb-3">Login</h4>
      <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Use <code>admin</code> / <code>password</code> to access the admin panel (demo only).</p>
      <input id="username" placeholder="Username" class="w-full mb-2 p-2 border rounded" />
      <input id="password" type="password" placeholder="Password" class="w-full mb-4 p-2 border rounded" />
      <div class="flex gap-2">
        <button id="login-submit" class="flex-1 py-2 bg-indigo-600 text-white rounded">Login</button>
        <button onclick="closeLogin()" class="py-2 px-3 border rounded">Cancel</button>
      </div>
      <p id="login-error" class="text-red-500 text-sm mt-2 hidden"></p>
    </div>
  </div>

  <!-- ADMIN PANEL (simple single-file admin) -->
  <div id="admin-panel" class="fixed right-4 bottom-4 w-96 bg-white dark:bg-[#071025] shadow-lg rounded-lg p-4 hidden z-50">
    <div class="flex justify-between items-center mb-3">
      <h4 class="font-bold">Admin</h4>
      <button onclick="closeAdmin()" class="text-sm px-2 py-1 border rounded">Close</button>
    </div>

    <div>
      <h5 class="font-semibold">Add Product</h5>
      <input id="p-title" placeholder="Title" class="w-full p-2 border rounded mb-2" />
      <input id="p-price" placeholder="Price" class="w-full p-2 border rounded mb-2" />
      <input id="p-image" placeholder="Image URL" class="w-full p-2 border rounded mb-2" />
      <textarea id="p-desc" placeholder="Short description" class="w-full p-2 border rounded mb-2"></textarea>
      <div class="flex gap-2">
        <button id="add-product" class="flex-1 py-2 bg-indigo-600 text-white rounded">Add</button>
        <button id="import-sample" class="py-2 px-3 border rounded">Sample</button>
      </div>
    </div>

    <hr class="my-3">
    <h5 class="font-semibold mb-2">Products</h5>
    <div id="admin-products-list" class="space-y-2 max-h-48 overflow-auto"></div>
  </div>

  <!-- FOOTER -->
  <footer class="mt-16 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
    &copy; Syfex 2025. | Made with Tailwind CSS.
  </footer>

  <!-- SCRIPTS -->
  <script>
    /* ---------- App Data & Persistence (SERVER-BACKED) ---------- */
    const API_BASE = '/api';

    // Try to fetch products from server; fall back to localStorage or defaults
    const defaultProducts = [
      {id:'p1',title:'High-Performance Laptop',price:1299.99,image:'https://placehold.co/600x400/3b82f6/ffffff?text=Laptop+Pro',desc:'The ultimate machine for work and play.'},
      {id:'p2',title:'Noise-Cancelling Headphones',price:149.5,image:'https://placehold.co/600x400/10b981/ffffff?text=Wireless+Headphones',desc:'Crystal clear sound for every beat.'},
      {id:'p3',title:'Next-Gen Smartwatch',price:249.0,image:'https://placehold.co/600x400/f59e0b/ffffff?text=Smartwatch+X',desc:'Track fitness and stay connected.'},
      {id:'p4',title:'Ultra HD 4K Monitor',price:450.0,image:'https://placehold.co/600x400/ef4444/ffffff?text=4K+Monitor',desc:'See every detail in stunning clarity.'}
    ];

    // local helpers
    function formatPrice(n){ return '$' + Number(n).toFixed(2); }
    function getAuthHeaders(){ const token = localStorage.getItem('auth_token'); return token? { 'Authorization': 'Bearer '+token } : {}; }

    /* ---------- UI Elements ---------- */
    const productsGrid = document.getElementById('products-grid');
    const cartCountEl = document.getElementById('cart-count');
    const toast = document.getElementById('toast');
    const cartDrawer = document.getElementById('cart-drawer');
    const cartItemsWrap = document.getElementById('cart-items');
    const cartTotalEl = document.getElementById('cart-total');

    /* ---------- Server-backed product functions ---------- */
    async function fetchProductsFromServer(){
      try{
        const res = await fetch(`${API_BASE}/products`);
        if(!res.ok) throw new Error('Network');
        const data = await res.json();
        return data.products || defaultProducts.slice();
      }catch(e){ console.warn('Products API failed, using local data', e); const raw = localStorage.getItem('products'); return raw? JSON.parse(raw): defaultProducts.slice(); }
    }

    async function createProductOnServer(product){
      try{
        const res = await fetch(`${API_BASE}/products`, { method:'POST', headers: {'Content-Type':'application/json', ...getAuthHeaders()}, body: JSON.stringify(product) });
        if(!res.ok) throw new Error('Create failed');
        return await res.json();
      }catch(e){ console.error(e); throw e; }
    }

    async function deleteProductOnServer(id){
      try{
        const res = await fetch(`${API_BASE}/products/${id}`, { method:'DELETE', headers: getAuthHeaders() });
        if(!res.ok) throw new Error('Delete failed');
        return await res.json();
      }catch(e){ console.error(e); throw e; }
    }

    /* ---------- Cart (server-backed) ---------- */
    async function fetchCart(){
      try{
        const res = await fetch(`${API_BASE}/cart`, { headers: getAuthHeaders() });
        if(!res.ok) throw new Error('Cart fetch failed');
        const data = await res.json();
        return data.cart || {};
      }catch(e){ console.warn('Cart API failed; falling back to localStorage', e); const raw = localStorage.getItem('cart_items'); return raw? JSON.parse(raw): {}; }
    }

    async function addToCartServer(productId, qty = 1){
      try{
        const res = await fetch(`${API_BASE}/cart/add`, { method:'POST', headers: {'Content-Type':'application/json', ...getAuthHeaders()}, body: JSON.stringify({ productId, qty }) });
        if(!res.ok) throw new Error('Add to cart failed');
        const data = await res.json();
        return data.cart;
      }catch(e){ console.warn('Add to cart API failed', e); // fallback
        const cart = JSON.parse(localStorage.getItem('cart_items') || '{}'); cart[productId] = (cart[productId]||0)+qty; localStorage.setItem('cart_items', JSON.stringify(cart)); return cart; }
    }

    async function updateCartServer(productId, qty){
      try{
        const res = await fetch(`${API_BASE}/cart/update`, { method:'POST', headers: {'Content-Type':'application/json', ...getAuthHeaders()}, body: JSON.stringify({ productId, qty }) });
        if(!res.ok) throw new Error('Update failed');
        const data = await res.json();
        return data.cart;
      }catch(e){ console.warn('Update cart API failed', e); const cart = JSON.parse(localStorage.getItem('cart_items') || '{}'); if(qty<=0) delete cart[productId]; else cart[productId]=qty; localStorage.setItem('cart_items', JSON.stringify(cart)); return cart; }
    }

    async function removeFromCartServer(productId){
      try{
        const res = await fetch(`${API_BASE}/cart/remove`, { method:'POST', headers: {'Content-Type':'application/json', ...getAuthHeaders()}, body: JSON.stringify({ productId }) });
        if(!res.ok) throw new Error('Remove failed');
        const data = await res.json();
        return data.cart;
      }catch(e){ console.warn('Remove API failed', e); const cart = JSON.parse(localStorage.getItem('cart_items') || '{}'); delete cart[productId]; localStorage.setItem('cart_items', JSON.stringify(cart)); return cart; }
    }

    /* ---------- Auth (server-backed) ---------- */
    async function loginServer(username, password){
      try{
        const res = await fetch(`${API_BASE}/auth/login`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ username, password }) });
        if(!res.ok) throw new Error('Login failed');
        const data = await res.json();
        if(data.token) localStorage.setItem('auth_token', data.token);
        if(data.role) localStorage.setItem('user_role', data.role);
        return data;
      }catch(e){ throw e; }
    }

    function logoutLocal(){ localStorage.removeItem('auth_token'); localStorage.removeItem('user_role'); showToast('Logged out'); }

    /* ---------- Render ---------- */
    async function renderProducts(){
      const products = await fetchProductsFromServer();
      // keep a local copy for offline edits
      localStorage.setItem('products', JSON.stringify(products));

      productsGrid.innerHTML = '';
      products.forEach(p => {
        const card = document.createElement('article');
        card.className = 'card p-4 rounded-xl shadow';
        card.innerHTML = `
          <img src="${p.image}" alt="${p.title}" class="w-full h-40 object-cover rounded-lg mb-3">
          <h4 class="font-semibold">${p.title}</h4>
          <p class="text-sm text-gray-500 dark:text-gray-300 mb-2">${p.desc || ''}</p>
          <div class="flex items-center justify-between">
            <strong class="text-indigo-600">${formatPrice(p.price)}</strong>
            <button data-id="${p.id}" class="add-to-cart-btn px-3 py-1 bg-indigo-600 text-white rounded">Add to Cart</button>
          </div>
        `;
        productsGrid.appendChild(card);
      });

      // attach listeners
      document.querySelectorAll('.add-to-cart-btn').forEach(btn => btn.addEventListener('click', async e => {
        const id = e.currentTarget.dataset.id; await addToCart(id); showToast('Item added to cart'); renderCart();
      }));

      renderAdminProducts();
    }

    async function renderCart(){
      const cart = await fetchCart();
      const products = await fetchProductsFromServer();
      cartItemsWrap.innerHTML = '';
      let total = 0;
      Object.keys(cart).forEach(id => {
        const qty = cart[id];
        const p = products.find(x=>x.id===id);
        if(!p) return;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3';
        row.innerHTML = `
          <img src="${p.image}" class="w-12 h-12 object-cover rounded" />
          <div class="flex-1">
            <div class="text-sm font-medium">${p.title}</div>
            <div class="text-xs text-gray-500 dark:text-gray-300">${formatPrice(p.price)}</div>
          </div>
          <div class="flex items-center gap-2">
            <button class="px-2 py-1 border qty-decrease" data-id="${id}">-</button>
            <div class="w-6 text-center">${qty}</div>
            <button class="px-2 py-1 border qty-increase" data-id="${id}">+</button>
            <button class="ml-2 text-red-500 remove-item" data-id="${id}"><i class="fas fa-trash"></i></button>
          </div>
        `;
        cartItemsWrap.appendChild(row);
        total += p.price * qty;
      });
      cartTotalEl.textContent = formatPrice(total);

      // bind qty buttons
      cartItemsWrap.querySelectorAll('.qty-increase').forEach(b=>b.addEventListener('click', async e=>{
        const id=e.currentTarget.dataset.id; const current = (await fetchCart())[id]||0; await updateCartServer(id, current+1); renderCart();
      }));
      cartItemsWrap.querySelectorAll('.qty-decrease').forEach(b=>b.addEventListener('click', async e=>{
        const id=e.currentTarget.dataset.id; const current = (await fetchCart())[id]||0; await updateCartServer(id, current-1); renderCart();
      }));
      cartItemsWrap.querySelectorAll('.remove-item').forEach(b=>b.addEventListener('click', async e=>{ const id=e.currentTarget.dataset.id; await removeFromCartServer(id); renderCart(); }));

      updateCartBadge(cart);
    }

    function updateCartBadge(cartObj){
      const cart = cartObj || JSON.parse(localStorage.getItem('cart_items')||'{}');
      const count = Object.values(cart).reduce((s,n)=>s+n,0);
      cartCountEl.textContent = count;
    }

    /* ---------- Cart operations (UI-facing wrappers) ---------- */
    async function addToCart(productId){
      const cart = await addToCartServer(productId,1);
      // if server returns cart, sync to localStorage as fallback
      localStorage.setItem('cart_items', JSON.stringify(cart));
      updateCartBadge(cart);
      return cart;
    }

    async function removeFromCart(productId){
      const cart = await removeFromCartServer(productId);
      localStorage.setItem('cart_items', JSON.stringify(cart));
      updateCartBadge(cart);
      return cart;
    }

    /* ---------- Admin (server-backed) ---------- */
    async function renderAdminProducts(){
      const products = await fetchProductsFromServer();
      const wrap = document.getElementById('admin-products-list'); wrap.innerHTML='';
      products.forEach(p=>{
        const r = document.createElement('div'); r.className='flex justify-between items-center gap-2';
        r.innerHTML = `<div class="text-sm">${p.title} <div class="text-xs text-gray-500">${formatPrice(p.price)}</div></div><div class=\"flex gap-1\"><button data-id=\"${p.id}\" class=\"edit-prod px-2 py-1 border rounded\">Edit</button><button data-id=\"${p.id}\" class=\"del-prod px-2 py-1 border rounded\">Delete</button></div>`;
        wrap.appendChild(r);
      });

      wrap.querySelectorAll('.del-prod').forEach(b=>b.addEventListener('click', async e=>{
        const id=e.currentTarget.dataset.id; try{ await deleteProductOnServer(id); showToast('Deleted'); renderProducts(); }catch(err){ showToast('Delete failed'); }
      }));
    }

    document.getElementById('add-product').addEventListener('click', async ()=>{
      const t=document.getElementById('p-title').value.trim();
      const pr=parseFloat(document.getElementById('p-price').value) || 0;
      const img=document.getElementById('p-image').value.trim() || 'https://placehold.co/600x400/94a3b8/ffffff?text=Product';
      const desc=document.getElementById('p-desc').value.trim();
      if(!t) return showToast('Title required');
      const product = { title: t, price: pr, image: img, desc };
      try{
        await createProductOnServer(product);
        showToast('Product added');
        document.getElementById('p-title').value=''; document.getElementById('p-price').value=''; document.getElementById('p-image').value=''; document.getElementById('p-desc').value='';
        renderProducts();
      }catch(e){ showToast('Create failed'); }
    });

    document.getElementById('import-sample').addEventListener('click', async ()=>{ try{ for(const p of defaultProducts){ await createProductOnServer(p); } showToast('Sample imported'); renderProducts(); }catch(e){ showToast('Import failed'); } });

    document.getElementById('clear-storage').addEventListener('click', ()=>{ localStorage.clear(); location.reload(); });

    /* ---------- Login flow (server-backed) ---------- */
    function openLogin(){ document.getElementById('login-modal').classList.remove('hidden'); document.getElementById('login-modal').style.display='flex'; }
    function closeLogin(){ document.getElementById('login-modal').classList.add('hidden'); document.getElementById('login-modal').style.display='none'; }
    window.openLogin = openLogin; window.closeLogin = closeLogin;

    document.getElementById('login-btn').addEventListener('click', openLogin);

    document.getElementById('login-submit').addEventListener('click', async ()=>{
      const u=document.getElementById('username').value.trim(); const p=document.getElementById('password').value;
      try{
        const data = await loginServer(u,p);
        if(data && data.token){ closeLogin(); showToast('Logged in'); renderProducts(); } else { document.getElementById('login-error').classList.remove('hidden'); document.getElementById('login-error').textContent='Invalid credentials'; }
      }catch(e){ document.getElementById('login-error').classList.remove('hidden'); document.getElementById('login-error').textContent='Login failed'; }
    });

    function openAdmin(){ const role = localStorage.getItem('user_role'); if(role!=='admin'){ openLogin(); return; } document.getElementById('admin-panel').classList.remove('hidden'); }
    function closeAdmin(){ document.getElementById('admin-panel').classList.add('hidden'); }
    window.openAdmin = openAdmin; window.closeAdmin = closeAdmin;

    document.getElementById('open-admin').addEventListener('click', openAdmin);

    /* ---------- Mobile menu */
    document.getElementById('menu-toggle').addEventListener('click', ()=>{
      const m = document.getElementById('mobile-nav'); m.classList.toggle('hidden'); m.classList.toggle('slide-down');
    });

    /* ---------- Theme handling (auto prefers + persistent) */
    const html = document.documentElement;
    const themeIcon = document.getElementById('theme-icon');
    function applyTheme(t){ if(t==='dark'){ html.classList.add('dark'); themeIcon.className='fas fa-sun'; } else { html.classList.remove('dark'); themeIcon.className='fas fa-moon'; } }
    const savedTheme = localStorage.getItem('theme');
    if(savedTheme) applyTheme(savedTheme); else applyTheme(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches? 'dark':'light');
    document.getElementById('theme-toggle').addEventListener('click', ()=>{
      const isDark = html.classList.toggle('dark'); localStorage.setItem('theme', isDark? 'dark':'light'); applyTheme(isDark? 'dark':'light');
    });

    /* ---------- Cart drawer UI ---------- */
    document.getElementById('cart-btn').addEventListener('click', async ()=>{
      cartDrawer.classList.toggle('translate-x-6');
      const wasHidden = cartDrawer.classList.toggle('opacity-0');
      cartDrawer.classList.toggle('pointer-events-none');
      await renderCart();
    });
    document.getElementById('close-cart').addEventListener('click', ()=>{ cartDrawer.classList.add('opacity-0'); cartDrawer.classList.add('translate-x-6'); cartDrawer.classList.add('pointer-events-none'); });

    document.getElementById('checkout-btn').addEventListener('click', ()=>{ alert('Demo checkout — replace with server flow'); });

    /* ---------- Helpers & Init ---------- */
    async function openProducts(){ location.hash = '#products'; window.scrollTo({top:document.getElementById('products').offsetTop-60,behavior:'smooth'}); }

    // initialize app
    (async function init(){ await renderProducts(); const cart = await fetchCart(); localStorage.setItem('cart_items', JSON.stringify(cart)); updateCartBadge(cart); })();
  </script>
</body>
</html>
</body>
</html>
