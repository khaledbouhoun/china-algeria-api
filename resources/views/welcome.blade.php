<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sino-Algeria Trade API</title>
    <meta name="description" content="Sino-Algeria Trade & Logistics Platform — REST API for order fulfillment, package tracking, wallets, visas and zone management.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root{
            --bg-0:#07090d;
            --bg-1:#0c1016;
            --bg-2:#11161e;
            --panel:rgba(255,255,255,0.035);
            --panel-border:rgba(255,255,255,0.08);
            --panel-border-hover:rgba(255,255,255,0.16);
            --text-hi:#f3f5f7;
            --text-mid:#a7afba;
            --text-low:#5f6a78;
            --brand-red:#e0342b;
            --brand-gold:#e8b64a;
            --brand-jade:#2fa77d;
            --radius:18px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{
            font-family:'Manrope',system-ui,-apple-system,sans-serif;
            background:
                radial-gradient(1100px 620px at 12% -8%, rgba(224,52,43,0.16), transparent 60%),
                radial-gradient(900px 560px at 88% 8%, rgba(232,182,74,0.10), transparent 55%),
                radial-gradient(1000px 700px at 50% 105%, rgba(47,167,125,0.10), transparent 60%),
                var(--bg-0);
            color:var(--text-hi);
            min-height:100vh;
            line-height:1.5;
            -webkit-font-smoothing:antialiased;
            overflow-x:hidden;
        }
        .mono{font-family:'JetBrains Mono',monospace}

        /* ---------- background grid ---------- */
        .grid-overlay{
            position:fixed;inset:0;z-index:0;pointer-events:none;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size:44px 44px;
            mask-image:radial-gradient(ellipse 80% 60% at 50% 20%, black 30%, transparent 80%);
        }

        .wrap{position:relative;z-index:1;max-width:1180px;margin:0 auto;padding:0 28px}

        /* ---------- nav ---------- */
        nav{
            display:flex;align-items:center;justify-content:space-between;
            padding:26px 0;
        }
        .brand{display:flex;align-items:center;gap:12px}
        .brand-mark{
            width:38px;height:38px;border-radius:10px;
            background:linear-gradient(135deg,var(--brand-red),#a91e17);
            display:flex;align-items:center;justify-content:center;
            font-weight:800;font-size:15px;color:#fff;
            box-shadow:0 6px 20px rgba(224,52,43,0.35);
        }
        .brand-name{font-weight:700;font-size:15px;letter-spacing:.2px}
        .brand-name span{color:var(--text-mid);font-weight:500}
        .nav-links{display:flex;gap:28px;align-items:center}
        .nav-links a{
            color:var(--text-mid);text-decoration:none;font-size:14px;font-weight:500;
            transition:color .2s ease;
        }
        .nav-links a:hover{color:var(--text-hi)}
        .nav-cta{
            background:var(--text-hi);color:#0c1016 !important;padding:9px 18px;border-radius:999px;
            font-weight:700 !important;font-size:13px !important;
        }

        /* ---------- hero ---------- */
        .hero{padding:64px 0 40px;text-align:center}
        .badge{
            display:inline-flex;align-items:center;gap:8px;
            background:var(--panel);border:1px solid var(--panel-border);
            padding:7px 16px;border-radius:999px;font-size:12.5px;color:var(--text-mid);
            margin-bottom:28px;
        }
        .dot{width:6px;height:6px;border-radius:50%;background:var(--brand-jade);box-shadow:0 0 8px var(--brand-jade)}
        h1.hero-title{
            font-size:clamp(2.2rem, 5vw, 3.6rem);
            font-weight:800;letter-spacing:-0.03em;line-height:1.08;
            max-width:820px;margin:0 auto;
        }
        h1.hero-title .accent{
            background:linear-gradient(90deg,var(--brand-red),var(--brand-gold));
            -webkit-background-clip:text;background-clip:text;color:transparent;
        }
        .hero-sub{
            max-width:600px;margin:22px auto 0;color:var(--text-mid);
            font-size:16.5px;line-height:1.65;
        }
        .hero-actions{display:flex;gap:14px;justify-content:center;margin-top:36px;flex-wrap:wrap}
        .btn{
            padding:13px 26px;border-radius:12px;font-weight:700;font-size:14.5px;
            text-decoration:none;transition:all .2s ease;display:inline-flex;align-items:center;gap:8px;
            border:1px solid transparent;
        }
        .btn-primary{background:var(--brand-red);color:#fff;box-shadow:0 10px 30px -8px rgba(224,52,43,0.55)}
        .btn-primary:hover{background:#c92b23;transform:translateY(-1px)}
        .btn-ghost{background:var(--panel);color:var(--text-hi);border-color:var(--panel-border)}
        .btn-ghost:hover{border-color:var(--panel-border-hover);background:rgba(255,255,255,0.06)}

        /* ---------- stats ---------- */
        .stats{
            display:grid;grid-template-columns:repeat(4,1fr);gap:1px;
            background:var(--panel-border);border:1px solid var(--panel-border);
            border-radius:var(--radius);overflow:hidden;margin-top:58px;
        }
        .stat{background:var(--bg-1);padding:26px 18px;text-align:center}
        .stat b{display:block;font-size:1.8rem;font-weight:800;letter-spacing:-0.02em}
        .stat span{color:var(--text-low);font-size:12.5px;text-transform:uppercase;letter-spacing:.06em}

        /* ---------- section heads ---------- */
        .section{padding:80px 0}
        .section-head{max-width:560px;margin:0 auto 46px;text-align:center}
        .kicker{
            color:var(--brand-gold);font-size:12.5px;font-weight:700;
            text-transform:uppercase;letter-spacing:.12em;margin-bottom:10px;
        }
        .section-head h2{font-size:clamp(1.6rem,3vw,2.1rem);font-weight:800;letter-spacing:-0.02em}
        .section-head p{color:var(--text-mid);margin-top:12px;font-size:15px;line-height:1.6}

        /* ---------- module cards ---------- */
        .modules{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
        .card{
            background:var(--panel);border:1px solid var(--panel-border);
            border-radius:var(--radius);padding:26px;
            transition:border-color .25s ease, transform .25s ease, background .25s ease;
            position:relative;overflow:hidden;
        }
        .card:hover{border-color:var(--panel-border-hover);transform:translateY(-3px);background:rgba(255,255,255,0.055)}
        .card-icon{
            width:44px;height:44px;border-radius:11px;
            display:flex;align-items:center;justify-content:center;margin-bottom:18px;
            font-size:19px;
        }
        .ic-red{background:rgba(224,52,43,0.14);color:#f2857d}
        .ic-gold{background:rgba(232,182,74,0.14);color:var(--brand-gold)}
        .ic-jade{background:rgba(47,167,125,0.14);color:#5fd3a8}
        .card h3{font-size:16.5px;font-weight:700;margin-bottom:8px}
        .card p{color:var(--text-mid);font-size:14px;line-height:1.6}
        .card .tag{
            display:inline-block;margin-top:14px;font-size:11.5px;color:var(--text-low);
            font-family:'JetBrains Mono',monospace;
        }

        /* ---------- endpoint preview ---------- */
        .panel-split{
            display:grid;grid-template-columns:1fr 1.15fr;gap:36px;align-items:center;
        }
        .terminal{
            background:var(--bg-2);border:1px solid var(--panel-border);border-radius:var(--radius);
            overflow:hidden;box-shadow:0 30px 60px -30px rgba(0,0,0,0.6);
        }
        .term-bar{
            display:flex;align-items:center;gap:7px;padding:12px 16px;
            border-bottom:1px solid var(--panel-border);background:rgba(255,255,255,0.02);
        }
        .term-dot{width:10px;height:10px;border-radius:50%}
        .term-body{padding:20px 22px;font-size:13px;line-height:2}
        .term-body .m{color:var(--brand-jade);font-weight:600}
        .term-body .path{color:var(--text-hi)}
        .term-body .cm{color:var(--text-low)}
        .term-body .str{color:#e8b64a}
        .term-body .code-line{white-space:pre}

        .feature-list{list-style:none}
        .feature-list li{
            display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--panel-border);
        }
        .feature-list li:last-child{border-bottom:none}
        .feature-list .num{
            font-family:'JetBrains Mono',monospace;color:var(--brand-red);font-weight:700;font-size:13px;
            width:26px;flex-shrink:0;padding-top:2px;
        }
        .feature-list h4{font-size:15px;font-weight:700;margin-bottom:4px}
        .feature-list p{color:var(--text-mid);font-size:13.5px;line-height:1.55}

        /* ---------- stack strip ---------- */
        .stack{
            display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:14px;
        }
        .chip{
            display:flex;align-items:center;gap:9px;
            background:var(--panel);border:1px solid var(--panel-border);
            padding:10px 18px;border-radius:999px;font-size:13.5px;font-weight:600;color:var(--text-mid);
        }
        .chip .sw{width:8px;height:8px;border-radius:50%}

        /* ---------- footer ---------- */
        footer{
            border-top:1px solid var(--panel-border);padding:34px 0;
            display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;
        }
        footer .fl{color:var(--text-low);font-size:13px}
        footer .fr{display:flex;gap:22px}
        footer .fr a{color:var(--text-mid);text-decoration:none;font-size:13px}
        footer .fr a:hover{color:var(--text-hi)}

        @media (max-width:860px){
            .stats{grid-template-columns:repeat(2,1fr)}
            .modules{grid-template-columns:1fr}
            .panel-split{grid-template-columns:1fr}
            .nav-links{display:none}
        }
    </style>
</head>
<body>
    <div class="grid-overlay"></div>

    <div class="wrap">
        <nav>
            <div class="brand">
                <div class="brand-mark">SA</div>
                <div class="brand-name">Sino-Algeria <span>Trade API</span></div>
            </div>
            <div class="nav-links">
                <a href="#modules">Modules</a>
                <a href="#endpoints">Endpoints</a>
                <a href="#stack">Stack</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="nav-cta">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-cta">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- HERO -->
        <section class="hero">
            <div class="badge"><span class="dot"></span> API v1 &middot; Status: Operational</div>
            <h1 class="hero-title">The backbone for <span class="accent">China&ndash;Algeria</span> trade &amp; logistics</h1>
            <p class="hero-sub">
                A single REST API powering order fulfillment, real-time package tracking, digital wallets,
                visa processing and zone management &mdash; built for import/export operations moving between
                two continents.
            </p>
            <div class="hero-actions">
                <a href="#endpoints" class="btn btn-primary">Explore the API →</a>
                <a href="#modules" class="btn btn-ghost">View modules</a>
            </div>

            <div class="stats">
                <div class="stat"><b>18</b><span>Database Tables</span></div>
                <div class="stat"><b>6</b><span>Core Modules</span></div>
                <div class="stat"><b>2</b><span>Countries Connected</span></div>
                <div class="stat"><b>99.9%</b><span>Uptime Target</span></div>
            </div>
        </section>

        <!-- MODULES -->
        <section class="section" id="modules">
            <div class="section-head">
                <div class="kicker">Platform Modules</div>
                <h2>Everything one trade platform needs</h2>
                <p>Every module is modeled, versioned, and exposed through consistent, predictable endpoints.</p>
            </div>
            <div class="modules">
                <div class="card">
                    <div class="card-icon ic-red">📦</div>
                    <h3>Order Fulfillment</h3>
                    <p>Create, track and manage orders end-to-end from purchase in China to delivery in Algeria.</p>
                    <span class="tag">/api/orders</span>
                </div>
                <div class="card">
                    <div class="card-icon ic-gold">🚚</div>
                    <h3>Package Tracking</h3>
                    <p>Live shipment status across freight, customs and last-mile stages with full history.</p>
                    <span class="tag">/api/packages</span>
                </div>
                <div class="card">
                    <div class="card-icon ic-jade">💳</div>
                    <h3>Wallets</h3>
                    <p>Multi-currency balances, top-ups and transaction ledgers for buyers and agents.</p>
                    <span class="tag">/api/wallets</span>
                </div>
                <div class="card">
                    <div class="card-icon ic-red">🛂</div>
                    <h3>Visa Management</h3>
                    <p>Application intake, document review and status tracking for business travel visas.</p>
                    <span class="tag">/api/visas</span>
                </div>
                <div class="card">
                    <div class="card-icon ic-gold">🗺️</div>
                    <h3>Zone Management</h3>
                    <p>Configurable shipping zones and rate rules mapped across regions and carriers.</p>
                    <span class="tag">/api/zones</span>
                </div>
                <div class="card">
                    <div class="card-icon ic-jade">🔐</div>
                    <h3>Authentication</h3>
                    <p>Secure identity and session management powered by Firebase Authentication.</p>
                    <span class="tag">/api/auth</span>
                </div>
            </div>
        </section>

        <!-- ENDPOINTS -->
        <section class="section" id="endpoints">
            <div class="section-head">
                <div class="kicker">Built for Developers</div>
                <h2>Clean, predictable, RESTful</h2>
                <p>Consistent resource naming, Form Request validation, and typed API Resources across every module.</p>
            </div>
            <div class="panel-split">
                <ul class="feature-list">
                    <li>
                        <div class="num">01</div>
                        <div>
                            <h4>Eloquent-backed resources</h4>
                            <p>Every table is fully modeled with relationships, casts and validated Form Requests.</p>
                        </div>
                    </li>
                    <li>
                        <div class="num">02</div>
                        <div>
                            <h4>Consistent API Resources</h4>
                            <p>Uniform JSON shape across all 18 tables, so client integration stays predictable.</p>
                        </div>
                    </li>
                    <li>
                        <div class="num">03</div>
                        <div>
                            <h4>Service-layer architecture</h4>
                            <p>Business logic lives in dedicated Service classes, keeping controllers thin and testable.</p>
                        </div>
                    </li>
                    <li>
                        <div class="num">04</div>
                        <div>
                            <h4>PostgreSQL on Neon</h4>
                            <p>Serverless Postgres with resolved foreign keys and clean indexing across the schema.</p>
                        </div>
                    </li>
                </ul>

                <div class="terminal">
                    <div class="term-bar">
                        <div class="term-dot" style="background:#ff5f57"></div>
                        <div class="term-dot" style="background:#febc2e"></div>
                        <div class="term-dot" style="background:#28c840"></div>
                        <span class="mono" style="color:var(--text-low);font-size:12px;margin-left:8px">GET /api/orders/8841</span>
                    </div>
                    <div class="term-body mono">
<span class="code-line"><span class="m">GET</span> <span class="path">/api/v1/orders/8841</span></span>
<span class="code-line"><span class="cm">Authorization: Bearer &lt;firebase_token&gt;</span></span>
<span class="code-line"></span>
<span class="code-line">{</span>
<span class="code-line">  "id": 8841,</span>
<span class="code-line">  "status": <span class="str">"in_transit"</span>,</span>
<span class="code-line">  "origin_zone": <span class="str">"Guangzhou"</span>,</span>
<span class="code-line">  "destination_zone": <span class="str">"Algiers"</span>,</span>
<span class="code-line">  "wallet_charge": 214.50,</span>
<span class="code-line">  "package": { "tracking_no": <span class="str">"SA-2291X"</span> }</span>
<span class="code-line">}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- STACK -->
        <section class="section" id="stack">
            <div class="section-head">
                <div class="kicker">Under the Hood</div>
                <h2>The stack powering the platform</h2>
            </div>
            <div class="stack">
                <div class="chip"><span class="sw" style="background:#FF2D20"></span> Laravel</div>
                <div class="chip"><span class="sw" style="background:#336791"></span> PostgreSQL (Neon)</div>
                <div class="chip"><span class="sw" style="background:#FFCA28"></span> Firebase Auth</div>
                <div class="chip"><span class="sw" style="background:#777BB4"></span> PHP {{ PHP_VERSION }}</div>
                <div class="chip"><span class="sw" style="background:#e0342b"></span> Laravel v{{ Illuminate\Foundation\Application::VERSION }}</div>
            </div>
        </section>

        <footer>
            <div class="fl">© {{ date('Y') }} Sino-Algeria Trade &amp; Logistics Platform</div>
            <div class="fr">
                <a href="https://laravel.com/docs">Docs</a>
                <a href="#modules">Modules</a>
                <a href="#endpoints">API</a>
            </div>
        </footer>
    </div>
</body>
</html>
