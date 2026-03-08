<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login - {{ Site('nama_toko') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/font-awesome/css/font-awesome.min.css') }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}

        body{
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            font-family:"Segoe UI","Open Sans",sans-serif;
            background:#050709;
            overflow:hidden;
        }

        /* ─── CANVAS BG ─── */
        #wh{position:fixed;inset:0;width:100%;height:100%;z-index:0}

        /* ─── PARTICLES ─── */
        .particles{
            position:fixed;inset:0;z-index:1;pointer-events:none;
            background-image:
                radial-gradient(1.5px 1.5px at 12% 25%, rgba(255,200,60,.35) 0%,transparent 100%),
                radial-gradient(1px 1px at 30% 55%, rgba(255,200,60,.25) 0%,transparent 100%),
                radial-gradient(1.5px 1.5px at 48% 18%, rgba(255,200,60,.30) 0%,transparent 100%),
                radial-gradient(1px 1px at 62% 70%, rgba(255,200,60,.20) 0%,transparent 100%),
                radial-gradient(1.5px 1.5px at 75% 35%, rgba(255,200,60,.28) 0%,transparent 100%),
                radial-gradient(1px 1px at 88% 60%, rgba(255,200,60,.22) 0%,transparent 100%),
                radial-gradient(1px 1px at 22% 80%, rgba(255,200,60,.18) 0%,transparent 100%),
                radial-gradient(1.5px 1.5px at 55% 45%, rgba(255,200,60,.15) 0%,transparent 100%);
            animation:drift 12s ease-in-out infinite alternate;
        }
        @keyframes drift{
            0%{transform:translateY(0) translateX(0)}
            100%{transform:translateY(-14px) translateX(5px)}
        }

        /* ─── TOP BAR ─── */
        .top-bar{
            position:fixed;top:0;left:0;right:0;height:66px;z-index:30;
            background:rgba(4,6,12,.80);
            backdrop-filter:blur(14px);
            border-bottom:1px solid rgba(255,160,0,.18);
            display:flex;align-items:center;justify-content:space-between;
            padding:0 28px;
        }
        .logo-wrap{display:flex;align-items:center;gap:10px}
        .logo-wrap img{
            height:46px;width:auto;object-fit:contain;
            filter:drop-shadow(0 2px 8px rgba(0,0,0,.8));
        }
        .logo-txt{font-size:13px;font-weight:700;color:rgba(255,255,255,.88);line-height:1.35}
        .logo-txt small{
            display:block;font-weight:400;font-size:10px;
            color:rgba(255,165,0,.80);letter-spacing:1.2px;text-transform:uppercase;
        }
        .bar-center{
            font-size:16px;font-weight:800;letter-spacing:3.5px;
            color:rgba(255,165,0,.50);text-transform:uppercase;
        }

        /* ─── LOGIN CARD (dark glass) ─── */
        .card-login{
            position:relative;z-index:10;
            width:420px;
            background:rgba(6,9,18,.82);
            backdrop-filter:blur(22px) saturate(1.4);
            border:1px solid rgba(255,165,0,.22);
            border-radius:22px;
            padding:40px 42px 34px;
            box-shadow:
                0 0 0 1px rgba(255,165,0,.08),
                0 40px 90px rgba(0,0,0,.85),
                inset 0 1px 0 rgba(255,255,255,.06),
                inset 0 -1px 0 rgba(255,165,0,.08);
            animation:rise .6s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes rise{
            from{opacity:0;transform:translateY(32px) scale(.96)}
            to  {opacity:1;transform:translateY(0)    scale(1)}
        }
        /* Amber top accent */
        .card-login::before{
            content:'';position:absolute;
            top:0;left:10%;right:10%;height:2px;
            background:linear-gradient(90deg,transparent,#f5a623,#ffe066,#f5a623,transparent);
            border-radius:0 0 4px 4px;
        }
        /* Subtle glow pulse */
        .card-login::after{
            content:'';position:absolute;
            inset:-1px;border-radius:22px;z-index:-1;
            background:radial-gradient(ellipse at 50% 0%,rgba(245,166,35,.18),transparent 65%);
            animation:pulse 4s ease-in-out infinite alternate;
        }
        @keyframes pulse{
            from{opacity:.6}to{opacity:1}
        }

        /* Icon */
        .login-icon{
            width:68px;height:68px;
            background:linear-gradient(135deg,#f7b733,#c96800);
            border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            font-size:30px;
            margin:0 auto 18px;
            box-shadow:0 6px 28px rgba(200,100,0,.55),0 0 0 6px rgba(245,166,35,.10);
        }

        /* Typography */
        .login-title{
            font-weight:700;text-align:center;
            font-size:22px;color:#fff;margin-bottom:4px;
            letter-spacing:-.2px;
        }
        .login-appname{
            display:block;text-align:center;
            font-size:14px;font-weight:700;
            color:#f5a623;margin-bottom:5px;
            text-decoration:none;letter-spacing:.3px;
        }
        .login-sub{
            text-align:center;color:rgba(255,255,255,.38);
            font-size:12px;margin-bottom:26px;
        }

        /* Divider */
        .divider{
            border:none;
            border-top:1px solid rgba(255,255,255,.08);
            margin-bottom:22px;
        }

        /* Form */
        .f-label{
            font-size:11px;font-weight:700;
            color:rgba(255,255,255,.55);
            display:block;margin-bottom:6px;
            letter-spacing:.8px;text-transform:uppercase;
        }
        .f-input{
            width:100%;height:47px;
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.12);
            border-radius:11px;
            color:#fff;font-size:14px;
            padding:0 15px;
            transition:border-color .2s,background .2s;
            outline:none;
        }
        .f-input::placeholder{color:rgba(255,255,255,.22)}
        .f-input:focus{
            border-color:rgba(245,166,35,.60);
            background:rgba(255,255,255,.09);
            box-shadow:0 0 0 3px rgba(245,166,35,.12);
        }
        .f-input.is-invalid{border-color:rgba(220,80,80,.6)}

        /* Button */
        .btn-masuk{
            width:100%;height:50px;margin-top:14px;
            background:linear-gradient(135deg,#c96800 0%,#f5a623 50%,#ffcb57 100%);
            border:none;border-radius:12px;
            color:#fff;font-weight:800;font-size:15px;
            letter-spacing:.6px;cursor:pointer;
            box-shadow:0 6px 22px rgba(200,100,0,.50),0 2px 6px rgba(0,0,0,.4);
            transition:all .22s;position:relative;overflow:hidden;
        }
        .btn-masuk::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.18) 50%,transparent 100%);
            transform:translateX(-100%);
            transition:transform .5s;
        }
        .btn-masuk:hover::before{transform:translateX(100%)}
        .btn-masuk:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(200,100,0,.60)}
        .btn-masuk:active{transform:translateY(0)}

        /* Footer */
        .card-foot{
            text-align:center;margin-top:22px;
            color:rgba(255,255,255,.22);font-size:11px;
        }
        .card-foot span{color:rgba(245,166,35,.45)}

        /* Alert */
        .alert-dark-danger{
            background:rgba(180,40,40,.18);
            border:1px solid rgba(220,80,80,.30);
            border-radius:10px;padding:10px 14px;
            color:rgba(255,150,150,.9);font-size:13px;
            display:flex;justify-content:space-between;align-items:center;
            margin-bottom:16px;
        }
        .alert-dark-danger button{
            background:none;border:none;color:rgba(255,150,150,.7);
            cursor:pointer;font-size:16px;line-height:1;padding:0 4px;
        }

        @media(max-width:480px){
            .card-login{width:92vw;padding:30px 22px 24px}
            .top-bar{padding:0 14px}
            .bar-center,.logo-txt{display:none}
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════════════════════════
     WAREHOUSE BACKGROUND — long aisle perspective, industrial
══════════════════════════════════════════════════════════ -->
<svg id="wh" xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
<defs>
  <!-- Ambient ceiling glow per lamp -->
  <radialGradient id="a1" gradientUnits="userSpaceOnUse" cx="190" cy="0" r="520">
    <stop offset="0%" stop-color="#ffe07a" stop-opacity=".28"/>
    <stop offset="100%" stop-color="#050709" stop-opacity="0"/>
  </radialGradient>
  <radialGradient id="a2" gradientUnits="userSpaceOnUse" cx="530" cy="0" r="520">
    <stop offset="0%" stop-color="#ffe07a" stop-opacity=".22"/>
    <stop offset="100%" stop-color="#050709" stop-opacity="0"/>
  </radialGradient>
  <radialGradient id="a3" gradientUnits="userSpaceOnUse" cx="720" cy="0" r="520">
    <stop offset="0%" stop-color="#ffe07a" stop-opacity=".26"/>
    <stop offset="100%" stop-color="#050709" stop-opacity="0"/>
  </radialGradient>
  <radialGradient id="a4" gradientUnits="userSpaceOnUse" cx="910" cy="0" r="520">
    <stop offset="0%" stop-color="#ffe07a" stop-opacity=".22"/>
    <stop offset="100%" stop-color="#050709" stop-opacity="0"/>
  </radialGradient>
  <radialGradient id="a5" gradientUnits="userSpaceOnUse" cx="1250" cy="0" r="520">
    <stop offset="0%" stop-color="#ffe07a" stop-opacity=".28"/>
    <stop offset="100%" stop-color="#050709" stop-opacity="0"/>
  </radialGradient>
  <!-- Floor -->
  <linearGradient id="flr" x1="0" y1="0" x2="0" y2="1">
    <stop offset="0%"   stop-color="#1c1806"/>
    <stop offset="60%"  stop-color="#0e0c04"/>
    <stop offset="100%" stop-color="#020201"/>
  </linearGradient>
  <!-- Deep-centre fog -->
  <radialGradient id="fog" gradientUnits="userSpaceOnUse" cx="720" cy="370" r="380">
    <stop offset="0%"   stop-color="#0a0d18" stop-opacity=".0"/>
    <stop offset="70%"  stop-color="#0a0d18" stop-opacity=".55"/>
    <stop offset="100%" stop-color="#050709" stop-opacity=".85"/>
  </radialGradient>
  <!-- Full vignette -->
  <radialGradient id="vig" cx="50%" cy="50%" r="75%">
    <stop offset="0%"   stop-color="#050709" stop-opacity="0"/>
    <stop offset="100%" stop-color="#010203" stop-opacity=".82"/>
  </radialGradient>
  <!-- Card warm spot -->
  <radialGradient id="cspot" gradientUnits="userSpaceOnUse" cx="720" cy="450" r="380">
    <stop offset="0%"   stop-color="#f5a623" stop-opacity=".09"/>
    <stop offset="100%" stop-color="#f5a623" stop-opacity="0"/>
  </radialGradient>
  <!-- Shelf steel gradient -->
  <linearGradient id="stl" x1="0" y1="0" x2="0" y2="1">
    <stop offset="0%"   stop-color="#2e3450"/>
    <stop offset="100%" stop-color="#1a1d2e"/>
  </linearGradient>
</defs>

<!-- BASE -->
<rect width="1440" height="900" fill="#050709"/>

<!-- ══ CEILING & STRUCTURE ══ -->
<rect width="1440" height="76" fill="#090c15"/>
<rect y="68" width="1440" height="8" fill="#111420"/>
<!-- Truss beams across ceiling -->
<rect y="74" width="1440" height="3" fill="#181b2a"/>
<rect x="0"    y="62" width="6" height="18" fill="#1e2235"/>
<rect x="285"  y="62" width="6" height="18" fill="#1e2235"/>
<rect x="570"  y="62" width="6" height="18" fill="#1e2235"/>
<rect x="855"  y="62" width="6" height="18" fill="#1e2235"/>
<rect x="1140" y="62" width="6" height="18" fill="#1e2235"/>
<rect x="1434" y="62" width="6" height="18" fill="#1e2235"/>
<!-- Horizontal truss bar -->
<line x1="0" y1="66" x2="1440" y2="66" stroke="#1a1d2e" stroke-width="2"/>

<!-- ══ LIGHT FIXTURES ══ -->
<!-- Conduit wires -->
<line x1="190"  y1="0" x2="190"  y2="46" stroke="#1e2235" stroke-width="2"/>
<line x1="530"  y1="0" x2="530"  y2="46" stroke="#1e2235" stroke-width="2"/>
<line x1="720"  y1="0" x2="720"  y2="46" stroke="#1e2235" stroke-width="2"/>
<line x1="910"  y1="0" x2="910"  y2="46" stroke="#1e2235" stroke-width="2"/>
<line x1="1250" y1="0" x2="1250" y2="46" stroke="#1e2235" stroke-width="2"/>
<!-- Fixture housings -->
<rect x="148"  y="44" width="84" height="18" rx="4" fill="#1e2235"/>
<rect x="488"  y="44" width="84" height="18" rx="4" fill="#1e2235"/>
<rect x="678"  y="44" width="84" height="18" rx="4" fill="#1e2235"/>
<rect x="868"  y="44" width="84" height="18" rx="4" fill="#1e2235"/>
<rect x="1208" y="44" width="84" height="18" rx="4" fill="#1e2235"/>
<!-- Tube bulbs -->
<rect x="150"  y="57" width="80" height="6" rx="3" fill="#ffe566" opacity=".95"/>
<rect x="490"  y="57" width="80" height="6" rx="3" fill="#ffe566" opacity=".90"/>
<rect x="680"  y="57" width="80" height="6" rx="3" fill="#ffe566" opacity=".95"/>
<rect x="870"  y="57" width="80" height="6" rx="3" fill="#ffe566" opacity=".90"/>
<rect x="1210" y="57" width="80" height="6" rx="3" fill="#ffe566" opacity=".95"/>
<!-- Inner bright core -->
<rect x="160"  y="58" width="60" height="4" rx="2" fill="#fff8cc" opacity=".7"/>
<rect x="500"  y="58" width="60" height="4" rx="2" fill="#fff8cc" opacity=".65"/>
<rect x="690"  y="58" width="60" height="4" rx="2" fill="#fff8cc" opacity=".7"/>
<rect x="880"  y="58" width="60" height="4" rx="2" fill="#fff8cc" opacity=".65"/>
<rect x="1220" y="58" width="60" height="4" rx="2" fill="#fff8cc" opacity=".7"/>

<!-- ══ DRAMATIC LIGHT CONES ══ -->
<polygon points="150,63  30,680  355,680"  fill="rgba(255,224,80,.052)"/>
<polygon points="162,63  65,680  325,680"  fill="rgba(255,224,80,.038)"/>
<polygon points="490,63 365,680  670,680"  fill="rgba(255,224,80,.044)"/>
<polygon points="502,63 395,680  640,680"  fill="rgba(255,224,80,.030)"/>
<polygon points="680,63 555,680  895,680"  fill="rgba(255,224,80,.050)"/>
<polygon points="692,63 575,680  875,680"  fill="rgba(255,224,80,.032)"/>
<polygon points="870,63 750,680 1040,680"  fill="rgba(255,224,80,.044)"/>
<polygon points="882,63 768,680 1010,680"  fill="rgba(255,224,80,.030)"/>
<polygon points="1210,63 1085,680 1420,680" fill="rgba(255,224,80,.052)"/>
<polygon points="1222,63 1100,680 1400,680" fill="rgba(255,224,80,.038)"/>

<!-- Ambient glow layers -->
<rect width="1440" height="900" fill="url(#a1)"/>
<rect width="1440" height="900" fill="url(#a2)"/>
<rect width="1440" height="900" fill="url(#a3)"/>
<rect width="1440" height="900" fill="url(#a4)"/>
<rect width="1440" height="900" fill="url(#a5)"/>

<!-- ══ AISLE PERSPECTIVE LINES (converge to VP 720,390) ══ -->
<!-- These define the aisle floor and ceiling perspective -->
<line x1="0"    y1="76"  x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".035"/>
<line x1="220"  y1="76"  x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".03"/>
<line x1="0"    y1="660" x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".03"/>
<line x1="220"  y1="660" x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".025"/>
<line x1="1440" y1="76"  x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".035"/>
<line x1="1220" y1="76"  x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".03"/>
<line x1="1440" y1="660" x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".03"/>
<line x1="1220" y1="660" x2="720" y2="390" stroke="#ffffff" stroke-width=".8" stroke-opacity=".025"/>

<!-- ══ LEFT RACK — NEAR (foreground) ══ -->
<!-- Post L -->
<rect x="8"   y="76" width="16" height="600" fill="url(#stl)"/>
<rect x="8"   y="630" width="16" height="46" fill="#c96800" opacity=".40"/>
<rect x="8"   y="76"  width="3"  height="600" fill="rgba(255,255,255,.04)"/>
<!-- Post R -->
<rect x="224" y="76" width="16" height="600" fill="url(#stl)"/>
<rect x="224" y="630" width="16" height="46" fill="#c96800" opacity=".40"/>
<!-- Diagonal brace -->
<line x1="24"  y1="76"  x2="224" y2="310" stroke="#181b2e" stroke-width="5"/>
<line x1="224" y1="76"  x2="24"  y2="310" stroke="#181b2e" stroke-width="5"/>
<!-- Shelf beams + surfaces -->
<rect x="8"  y="146" width="232" height="10" rx="2" fill="#252840"/><rect x="8"  y="138" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="8"  y="246" width="232" height="10" rx="2" fill="#252840"/><rect x="8"  y="238" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="8"  y="346" width="232" height="10" rx="2" fill="#252840"/><rect x="8"  y="338" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="8"  y="446" width="232" height="10" rx="2" fill="#252840"/><rect x="8"  y="438" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="8"  y="546" width="232" height="10" rx="2" fill="#252840"/><rect x="8"  y="538" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>

<!-- Boxes L shelf 1 -->
<rect x="16" y="104" width="48" height="34" rx="2" fill="#7a500e"/><rect x="18" y="106" width="44" height="3" fill="#9a6818" opacity=".7"/><rect x="40" y="106" width="3" height="30" fill="#9a6818" opacity=".5"/>
<rect x="72" y="108" width="42" height="30" rx="2" fill="#5e3c0c"/><rect x="74" y="110" width="38" height="2" fill="#7a5014" opacity=".6"/>
<rect x="122" y="104" width="46" height="34" rx="2" fill="#8c6214"/><rect x="124" y="106" width="42" height="3" fill="#a87a1c" opacity=".7"/>
<rect x="178" y="108" width="38" height="30" rx="2" fill="#6a4810"/>
<!-- Boxes L shelf 2 -->
<rect x="16" y="204" width="54" height="42" rx="2" fill="#503c14"/><rect x="18" y="206" width="50" height="3" fill="#6a5020" opacity=".7"/><rect x="43" y="206" width="3" height="38" fill="#6a5020" opacity=".4"/>
<rect x="78" y="206" width="42" height="40" rx="2" fill="#7c5228"/>
<rect x="128" y="204" width="50" height="42" rx="2" fill="#5e4014"/><rect x="130" y="206" width="46" height="3" fill="#7a5820" opacity=".6"/>
<rect x="188" y="208" width="30" height="38" rx="2" fill="#3e2c0c"/>
<!-- Boxes L shelf 3 -->
<rect x="16" y="302" width="40" height="44" rx="2" fill="#7c4c14"/><rect x="18" y="304" width="36" height="3" fill="#9a6020" opacity=".65"/>
<rect x="64" y="304" width="50" height="42" rx="2" fill="#503414"/>
<rect x="122" y="302" width="44" height="44" rx="2" fill="#6c4414"/><rect x="124" y="304" width="40" height="3" fill="#885820" opacity=".6"/>
<rect x="174" y="306" width="36" height="40" rx="2" fill="#8c5c1c"/>
<!-- Boxes L shelf 4 -->
<rect x="16" y="404" width="58" height="42" rx="2" fill="#5e4014"/><rect x="18" y="406" width="54" height="3" fill="#7a5420" opacity=".7"/><rect x="47" y="406" width="3" height="38" fill="#7a5420" opacity=".4"/>
<rect x="82" y="404" width="46" height="42" rx="2" fill="#7c5018"/>
<rect x="136" y="402" width="52" height="44" rx="2" fill="#4e3210"/><rect x="138" y="404" width="48" height="3" fill="#664418" opacity=".7"/>
<!-- Boxes L shelf 5 -->
<rect x="16" y="490" width="66" height="56" rx="2" fill="#3e2c0c"/><rect x="18" y="492" width="62" height="4" fill="#50381a" opacity=".9"/><rect x="49" y="492" width="4" height="52" fill="#50381a" opacity=".5"/>
<rect x="90" y="494" width="56" height="52" rx="2" fill="#5e4014"/>
<rect x="154" y="490" width="58" height="56" rx="2" fill="#322408"/><rect x="156" y="492" width="54" height="4" fill="#443418" opacity=".8"/>

<!-- Pallet L -->
<rect x="12"  y="632" width="226" height="14" rx="2" fill="#362810"/>
<rect x="28"  y="620" width="24"  height="14" fill="#362810"/>
<rect x="108" y="620" width="24"  height="14" fill="#362810"/>
<rect x="196" y="620" width="24"  height="14" fill="#362810"/>
<rect x="12"  y="646" width="226" height="8"  rx="1" fill="#241c08"/>
<!-- Boxes on pallet L -->
<rect x="20"  y="562" width="88" height="60" rx="2" fill="#6c5018"/><rect x="22" y="564" width="84" height="4" fill="#8a6622" opacity=".9"/><rect x="64" y="564" width="4" height="56" fill="#8a6622" opacity=".5"/>
<rect x="116" y="566" width="72" height="56" rx="2" fill="#4e3810"/><rect x="118" y="568" width="68" height="3" fill="#664e1c" opacity=".7"/>

<!-- ══ RIGHT RACK — NEAR (foreground) ══ -->
<rect x="1200" y="76" width="16" height="600" fill="url(#stl)"/>
<rect x="1200" y="630" width="16" height="46" fill="#c96800" opacity=".40"/>
<rect x="1200" y="76" width="3"  height="600" fill="rgba(255,255,255,.04)"/>
<rect x="1416" y="76" width="16" height="600" fill="url(#stl)"/>
<rect x="1416" y="630" width="16" height="46" fill="#c96800" opacity=".40"/>
<line x1="1216" y1="76"  x2="1416" y2="310" stroke="#181b2e" stroke-width="5"/>
<line x1="1416" y1="76"  x2="1216" y2="310" stroke="#181b2e" stroke-width="5"/>
<!-- Shelf beams R -->
<rect x="1200" y="146" width="232" height="10" rx="2" fill="#252840"/><rect x="1200" y="138" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="1200" y="246" width="232" height="10" rx="2" fill="#252840"/><rect x="1200" y="238" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="1200" y="346" width="232" height="10" rx="2" fill="#252840"/><rect x="1200" y="338" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="1200" y="446" width="232" height="10" rx="2" fill="#252840"/><rect x="1200" y="438" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<rect x="1200" y="546" width="232" height="10" rx="2" fill="#252840"/><rect x="1200" y="538" width="232" height="9"  rx="1" fill="#2e3250" opacity=".7"/>
<!-- Boxes R shelf 1 -->
<rect x="1210" y="104" width="50" height="34" rx="2" fill="#7a500e"/><rect x="1212" y="106" width="46" height="3" fill="#9a6818" opacity=".7"/>
<rect x="1268" y="108" width="44" height="30" rx="2" fill="#5e3c0c"/>
<rect x="1320" y="104" width="48" height="34" rx="2" fill="#8c6214"/><rect x="1322" y="106" width="44" height="3" fill="#a87a1c" opacity=".7"/>
<rect x="1376" y="108" width="40" height="30" rx="2" fill="#6a4810"/>
<!-- Boxes R shelf 2 -->
<rect x="1210" y="204" width="56" height="42" rx="2" fill="#503c14"/>
<rect x="1274" y="206" width="44" height="40" rx="2" fill="#7c5228"/>
<rect x="1326" y="204" width="52" height="42" rx="2" fill="#5e4014"/>
<rect x="1386" y="208" width="28" height="38" rx="2" fill="#3e2c0c"/>
<!-- Boxes R shelf 3 -->
<rect x="1210" y="302" width="42" height="44" rx="2" fill="#7c4c14"/>
<rect x="1260" y="304" width="52" height="42" rx="2" fill="#503414"/>
<rect x="1320" y="302" width="46" height="44" rx="2" fill="#6c4414"/>
<rect x="1374" y="306" width="38" height="40" rx="2" fill="#8c5c1c"/>
<!-- Boxes R shelf 4 -->
<rect x="1210" y="404" width="60" height="42" rx="2" fill="#5e4014"/>
<rect x="1278" y="404" width="48" height="42" rx="2" fill="#7c5018"/>
<rect x="1334" y="402" width="54" height="44" rx="2" fill="#4e3210"/>
<!-- Boxes R shelf 5 -->
<rect x="1210" y="490" width="68" height="56" rx="2" fill="#3e2c0c"/><rect x="1212" y="492" width="64" height="4" fill="#50381a" opacity=".9"/>
<rect x="1286" y="494" width="58" height="52" rx="2" fill="#5e4014"/>
<rect x="1352" y="490" width="60" height="56" rx="2" fill="#322408"/>
<!-- Pallet R -->
<rect x="1200" y="632" width="232" height="14" rx="2" fill="#362810"/>
<rect x="1216" y="620" width="24"  height="14" fill="#362810"/>
<rect x="1296" y="620" width="24"  height="14" fill="#362810"/>
<rect x="1408" y="620" width="24"  height="14" fill="#362810"/>
<rect x="1200" y="646" width="232" height="8"  rx="1" fill="#241c08"/>
<rect x="1208" y="562" width="90" height="60" rx="2" fill="#6c5018"/>
<rect x="1306" y="566" width="74" height="56" rx="2" fill="#4e3810"/>

<!-- ══ MID-DEPTH RACKS (L) — smaller, softer ══ -->
<rect x="250" y="100" width="9"   height="520" fill="#181b28" opacity=".95"/>
<rect x="415" y="100" width="9"   height="520" fill="#181b28" opacity=".95"/>
<rect x="250" y="150" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="250" y="230" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="250" y="310" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="250" y="390" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="250" y="470" width="174" height="6"   fill="#20223c" opacity=".85"/>
<!-- Mid-L boxes -->
<rect x="258" y="115" width="30" height="35" rx="1" fill="#503808" opacity=".85"/>
<rect x="294" y="118" width="26" height="32" rx="1" fill="#3e2c08" opacity=".80"/>
<rect x="328" y="115" width="32" height="35" rx="1" fill="#503808" opacity=".85"/>
<rect x="370" y="118" width="28" height="32" rx="1" fill="#3e2c08" opacity=".78"/>
<rect x="258" y="195" width="34" height="35" rx="1" fill="#3e2c08" opacity=".78"/>
<rect x="300" y="198" width="28" height="32" rx="1" fill="#503808" opacity=".75"/>
<rect x="336" y="195" width="32" height="35" rx="1" fill="#3e2c08" opacity=".78"/>
<rect x="258" y="275" width="32" height="35" rx="1" fill="#503808" opacity=".68"/>
<rect x="298" y="278" width="26" height="32" rx="1" fill="#3a2808" opacity=".62"/>
<rect x="332" y="275" width="30" height="35" rx="1" fill="#3e2c08" opacity=".65"/>
<rect x="370" y="278" width="28" height="32" rx="1" fill="#503808" opacity=".62"/>
<rect x="258" y="355" width="36" height="35" rx="1" fill="#3a2808" opacity=".55"/>
<rect x="302" y="358" width="28" height="32" rx="1" fill="#3e2c08" opacity=".52"/>
<rect x="338" y="355" width="30" height="35" rx="1" fill="#3a2808" opacity=".55"/>

<!-- ══ MID-DEPTH RACKS (R) ══ -->
<rect x="1016" y="100" width="9"   height="520" fill="#181b28" opacity=".95"/>
<rect x="1181" y="100" width="9"   height="520" fill="#181b28" opacity=".95"/>
<rect x="1016" y="150" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="1016" y="230" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="1016" y="310" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="1016" y="390" width="174" height="6"   fill="#20223c" opacity=".85"/>
<rect x="1016" y="470" width="174" height="6"   fill="#20223c" opacity=".85"/>
<!-- Mid-R boxes -->
<rect x="1025" y="115" width="32" height="35" rx="1" fill="#503808" opacity=".85"/>
<rect x="1065" y="118" width="28" height="32" rx="1" fill="#3e2c08" opacity=".80"/>
<rect x="1101" y="115" width="30" height="35" rx="1" fill="#503808" opacity=".85"/>
<rect x="1142" y="118" width="26" height="32" rx="1" fill="#3e2c08" opacity=".78"/>
<rect x="1025" y="195" width="36" height="35" rx="1" fill="#3e2c08" opacity=".78"/>
<rect x="1069" y="198" width="30" height="32" rx="1" fill="#503808" opacity=".75"/>
<rect x="1108" y="195" width="32" height="35" rx="1" fill="#3e2c08" opacity=".78"/>
<rect x="1025" y="275" width="34" height="35" rx="1" fill="#503808" opacity=".68"/>
<rect x="1067" y="278" width="28" height="32" rx="1" fill="#3a2808" opacity=".62"/>
<rect x="1103" y="275" width="30" height="35" rx="1" fill="#3e2c08" opacity=".65"/>
<rect x="1025" y="355" width="38" height="35" rx="1" fill="#3a2808" opacity=".55"/>
<rect x="1071" y="358" width="30" height="32" rx="1" fill="#3e2c08" opacity=".52"/>

<!-- ══ FAR RACKS — deep aisle (tiny, desaturated) ══ -->
<!-- Left far -->
<rect x="426" y="110" width="5"   height="450" fill="#141620" opacity=".9"/>
<rect x="568" y="110" width="5"   height="450" fill="#141620" opacity=".9"/>
<rect x="426" y="155" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="426" y="225" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="426" y="295" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="426" y="365" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="426" y="435" width="147" height="4"   fill="#181a28" opacity=".7"/>
<!-- Right far -->
<rect x="867" y="110" width="5"   height="450" fill="#141620" opacity=".9"/>
<rect x="1009" y="110" width="5"  height="450" fill="#141620" opacity=".9"/>
<rect x="867" y="155" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="867" y="225" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="867" y="295" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="867" y="365" width="147" height="4"   fill="#181a28" opacity=".7"/>
<rect x="867" y="435" width="147" height="4"   fill="#181a28" opacity=".7"/>
<!-- Far boxes L -->
<rect x="433" y="122" width="22" height="33" rx="1" fill="#3a2806" opacity=".55"/>
<rect x="460" y="125" width="18" height="30" rx="1" fill="#2e2006" opacity=".50"/>
<rect x="500" y="122" width="22" height="33" rx="1" fill="#3a2806" opacity=".55"/>
<rect x="533" y="125" width="20" height="30" rx="1" fill="#2e2006" opacity=".50"/>
<rect x="433" y="192" width="24" height="33" rx="1" fill="#2e2006" opacity=".45"/>
<rect x="464" y="195" width="20" height="30" rx="1" fill="#3a2806" opacity=".45"/>
<rect x="506" y="192" width="22" height="33" rx="1" fill="#2e2006" opacity=".45"/>
<rect x="535" y="195" width="20" height="30" rx="1" fill="#3a2806" opacity=".42"/>
<!-- Far boxes R -->
<rect x="875" y="122" width="22" height="33" rx="1" fill="#3a2806" opacity=".55"/>
<rect x="904" y="125" width="20" height="30" rx="1" fill="#2e2006" opacity=".50"/>
<rect x="940" y="122" width="22" height="33" rx="1" fill="#3a2806" opacity=".55"/>
<rect x="968" y="125" width="22" height="30" rx="1" fill="#2e2006" opacity=".50"/>
<rect x="875" y="192" width="26" height="33" rx="1" fill="#2e2006" opacity=".45"/>
<rect x="908" y="195" width="20" height="30" rx="1" fill="#3a2806" opacity=".45"/>
<rect x="942" y="192" width="22" height="33" rx="1" fill="#2e2006" opacity=".45"/>

<!-- ══ VERY FAR CENTRE AISLE (vanishing point area) ══ -->
<rect x="574" y="76"  width="292" height="560" fill="#07090f" opacity=".7"/>
<rect x="575" y="76"  width="5"   height="560" fill="#111420" opacity=".8"/>
<rect x="640" y="76"  width="5"   height="560" fill="#111420" opacity=".8"/>
<rect x="705" y="76"  width="5"   height="560" fill="#111420" opacity=".8"/>
<rect x="770" y="76"  width="5"   height="560" fill="#111420" opacity=".8"/>
<rect x="835" y="76"  width="5"   height="560" fill="#111420" opacity=".8"/>
<rect x="575" y="160" width="290" height="3"   fill="#131524" opacity=".8"/>
<rect x="575" y="230" width="290" height="3"   fill="#131524" opacity=".8"/>
<rect x="575" y="300" width="290" height="3"   fill="#131524" opacity=".8"/>
<rect x="575" y="370" width="290" height="3"   fill="#131524" opacity=".8"/>
<rect x="575" y="440" width="290" height="3"   fill="#131524" opacity=".8"/>
<!-- Tiny far boxes -->
<rect x="580" y="128" width="18" height="32" rx="1" fill="#2e2006" opacity=".42"/>
<rect x="604" y="130" width="16" height="30" rx="1" fill="#241c05" opacity=".38"/>
<rect x="645" y="128" width="18" height="32" rx="1" fill="#2e2006" opacity=".42"/>
<rect x="670" y="130" width="16" height="30" rx="1" fill="#241c05" opacity=".38"/>
<rect x="710" y="128" width="18" height="32" rx="1" fill="#2e2006" opacity=".42"/>
<rect x="736" y="130" width="16" height="30" rx="1" fill="#241c05" opacity=".38"/>
<rect x="776" y="128" width="18" height="32" rx="1" fill="#2e2006" opacity=".42"/>
<rect x="802" y="130" width="16" height="30" rx="1" fill="#241c05" opacity=".38"/>
<rect x="840" y="128" width="16" height="32" rx="1" fill="#2e2006" opacity=".42"/>
<rect x="580" y="198" width="18" height="32" rx="1" fill="#241c05" opacity=".35"/>
<rect x="646" y="198" width="18" height="32" rx="1" fill="#2e2006" opacity=".35"/>
<rect x="712" y="198" width="18" height="32" rx="1" fill="#241c05" opacity=".35"/>
<rect x="778" y="198" width="18" height="32" rx="1" fill="#2e2006" opacity=".35"/>
<rect x="580" y="268" width="18" height="32" rx="1" fill="#2e2006" opacity=".28"/>
<rect x="648" y="268" width="16" height="32" rx="1" fill="#241c05" opacity=".28"/>
<rect x="714" y="268" width="18" height="32" rx="1" fill="#2e2006" opacity=".28"/>

<!-- ══ FORKLIFT SILHOUETTE (right mid) ══ -->
<!-- Mast outer -->
<rect x="1058" y="460" width="12" height="200" fill="#0c0f1a"/>
<rect x="1072" y="460" width="9"  height="200" fill="#0e1120"/>
<!-- Mast inner channel -->
<rect x="1062" y="460" width="4"  height="200" fill="#080a14"/>
<!-- Carriage -->
<rect x="1038" y="520" width="40" height="22" rx="2" fill="#0e1120"/>
<!-- Forks -->
<rect x="1012" y="535" width="98" height="8"  rx="2" fill="#0a0d18"/>
<rect x="1012" y="548" width="98" height="8"  rx="2" fill="#0a0d18"/>
<!-- Body -->
<rect x="1060" y="530" width="96" height="90" rx="5" fill="#0c0f1a"/>
<rect x="1062" y="532" width="94" height="88" rx="4" fill="#0e1220" opacity=".9"/>
<!-- Hood -->
<rect x="1062" y="530" width="94" height="32" rx="3" fill="#0a0c18"/>
<!-- Exhaust stack -->
<rect x="1100" y="502" width="8"  height="30" rx="2" fill="#0a0c18"/>
<rect x="1098" y="498" width="12" height="6"  rx="2" fill="#0e1020"/>
<!-- Cab -->
<rect x="1100" y="488" width="52" height="44" rx="3" fill="#0a0c18"/>
<rect x="1103" y="491" width="46" height="38" rx="2" fill="#0e1220" opacity=".8"/>
<!-- Seat hint -->
<rect x="1112" y="518" width="28" height="8"  rx="2" fill="#080a14" opacity=".7"/>
<!-- Counterweight -->
<rect x="1140" y="550" width="40" height="60" rx="3" fill="#080a14"/>
<!-- Wheels -->
<circle cx="1078" cy="625" r="26" fill="#080a14"/>
<circle cx="1078" cy="625" r="16" fill="#0c0f1a"/>
<circle cx="1078" cy="625" r="6"  fill="#080a14"/>
<circle cx="1140" cy="625" r="20" fill="#080a14"/>
<circle cx="1140" cy="625" r="12" fill="#0c0f1a"/>
<circle cx="1140" cy="625" r="4"  fill="#080a14"/>
<!-- Headlight beam -->
<rect x="1018" y="535" width="14" height="8"  rx="3" fill="#ffe566" opacity=".55"/>
<polygon points="1018,536 960,548 960,530" fill="rgba(255,224,80,.05)"/>

<!-- ══ FLOOR ══ -->
<rect x="0" y="656" width="1440" height="244" fill="url(#flr)"/>
<rect x="0" y="654" width="1440" height="4"   fill="#201800" opacity=".7"/>
<!-- Safety tape strip (yellow) -->
<rect x="0"   y="654" width="1440" height="9" fill="#e09000" opacity=".25"/>
<rect x="0"   y="654" width="1440" height="3" fill="#ffe066" opacity=".18"/>
<!-- Floor perspective lines (aisle) -->
<line x1="0"    y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".04"/>
<line x1="180"  y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".04"/>
<line x1="360"  y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".035"/>
<line x1="540"  y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".03"/>
<line x1="720"  y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".04"/>
<line x1="900"  y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".03"/>
<line x1="1080" y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".035"/>
<line x1="1260" y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".04"/>
<line x1="1440" y1="660" x2="720" y2="800" stroke="#ffffff" stroke-width="1" stroke-opacity=".04"/>
<!-- Floor tile lines -->
<rect x="0" y="700" width="1440" height="1" fill="#ffffff" opacity=".04"/>
<rect x="0" y="745" width="1440" height="1" fill="#ffffff" opacity=".032"/>
<rect x="0" y="790" width="1440" height="1" fill="#ffffff" opacity=".025"/>
<rect x="0" y="835" width="1440" height="1" fill="#ffffff" opacity=".018"/>
<!-- Floor light pools under each lamp -->
<ellipse cx="190"  cy="672" rx="100" ry="22" fill="#ffe566" opacity=".08"/>
<ellipse cx="530"  cy="672" rx="100" ry="22" fill="#ffe566" opacity=".065"/>
<ellipse cx="720"  cy="672" rx="110" ry="24" fill="#ffe566" opacity=".075"/>
<ellipse cx="910"  cy="672" rx="100" ry="22" fill="#ffe566" opacity=".065"/>
<ellipse cx="1250" cy="672" rx="100" ry="22" fill="#ffe566" opacity=".08"/>

<!-- ══ DEPTH FOG (centre recedes into darkness) ══ -->
<rect width="1440" height="900" fill="url(#fog)"/>

<!-- ══ WARM GLOW SPOT (behind card position) ══ -->
<rect width="1440" height="900" fill="url(#cspot)"/>

<!-- ══ VIGNETTE ══ -->
<rect width="1440" height="900" fill="url(#vig)"/>
</svg>

<div class="particles"></div>

<!-- ══ TOP BAR ══ -->
<div class="top-bar">
    <div class="logo-wrap">
        <img src="{{ asset('logo.png') }}" alt="RS Royal Prima">
        <div class="logo-txt">RS Royal Prima Jambi<small>Jambi, Jl. Raden Wijaya RT 35</small></div>
    </div>
    <div class="bar-center">Sistem Manajemen Inventori</div>
    <div class="logo-wrap">
        <div class="logo-txt" style="text-align:right;">Terakreditasi<small>Paripurna</small></div>
        <img src="{{ asset('akre.png') }}" alt="Akreditasi">
    </div>
</div>

<!-- ══ LOGIN CARD ══ -->
<div class="card-login">

    <div class="login-icon">&#128230;</div>

    <h3 class="login-title">Selamat Datang</h3>
    <a href="{{ route('login') }}" class="login-appname">{{ Site('nama_toko') }}</a>
    <p class="login-sub">Masukkan kredensial Anda untuk melanjutkan</p>
    <hr class="divider">

    @if (session('error'))
        <div class="alert-dark-danger">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()">&#215;</button>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <label class="f-label">Username</label>
        <input type="text" name="username"
               value="{{ old('username') }}"
               class="f-input mb-1 @error('username') is-invalid @enderror"
               placeholder="Masukkan username" autofocus>
        @error('username')
            <small class="text-danger d-block mb-2" style="font-size:12px;">{{ $message }}</small>
        @enderror

        <div style="margin-top:16px"></div>

        <label class="f-label">Password</label>
        <input type="password" name="password"
               class="f-input mb-1 @error('password') is-invalid @enderror"
               placeholder="Masukkan password">
        @error('password')
            <small class="text-danger d-block mb-2" style="font-size:12px;">{{ $message }}</small>
        @enderror

        <button type="submit" class="btn-masuk">
            <i class="fa fa-sign-in" style="margin-right:8px;"></i>MASUK
        </button>
    </form>

    <p class="card-foot">
        &copy; {{ date('Y') }} <span>RS Royal Prima</span> &mdash; Sistem Inventori Internal
    </p>
</div>

<script src="{{ asset('sufee-admin/vendors/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('sufee-admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html>
