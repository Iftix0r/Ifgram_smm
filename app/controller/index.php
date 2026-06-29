<?php

require ('sql_connect.php');

function countRow($data){
    global $connect;
    $where    = "";
    if( $data["where"] ):
        $where    = "WHERE ";
        foreach ($data["where"] as $key => $value) {
        	$ar = $data["where"][$key];
            $where.=" $key = '$ar'";
            $execute[$key]=$value;
        }
        
    else:
        $execute[]= "";
    endif;
    $row  = mysqli_query($connect,"SELECT * FROM {$data['table']} $where ");
    
    $validate = mysqli_num_rows($row);
    return ($validate) ;
    
}

?>

<!DOCTYPE html>
<html lang="uz">
<head>
  <base href="https://<?=$_SERVER['HTTP_HOST']?>">
  <title><?=strtoupper($_SERVER['HTTP_HOST'])?> - Premium SMM Panel</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?=strtoupper($_SERVER['HTTP_HOST'])?> has the Cheapest SMM Panel and 100% High Quality for all social networks. Get the best panel today">
  <link rel="shortcut icon" type="image/ico" href="public/images/8df1bd5982b694d09ace0550ed9f0738fc91dc3e.png" />
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
     :root {
         --bg-color: #050505;
         --surface-color: #121212;
         --surface-color-light: #1e1e1e;
         --primary: #f33694;
         --secondary: #aa00ff;
         --text-main: #ffffff;
         --text-muted: #a0a0a0;
         --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
         --glow: 0 0 20px rgba(170, 0, 255, 0.4);
     }
     
     * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
     body { background-color: var(--bg-color); color: var(--text-main); overflow-x: hidden; line-height: 1.6; }
     
     /* Navbar */
     .navbar {
         display: flex; justify-content: space-between; align-items: center; 
         padding: 20px 5%; background: rgba(5, 5, 5, 0.8);
         backdrop-filter: blur(15px); position: fixed; width: 100%; top: 0; z-index: 1000;
         border-bottom: 1px solid rgba(255,255,255,0.05);
         transition: all 0.3s ease;
     }
     .brand { 
         font-size: 28px; font-weight: 800; background: var(--gradient); 
         -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
         text-decoration: none; letter-spacing: 1px;
     }
     .nav-links { display: flex; gap: 30px; }
     .nav-links a { 
         color: var(--text-main); text-decoration: none; font-weight: 600; font-size: 16px;
         display: flex; align-items: center; gap: 8px; transition: 0.3s ease;
         padding: 8px 16px; border-radius: 8px;
     }
     .nav-links a:hover { 
         color: var(--primary); background: rgba(255,255,255,0.03);
     }
     
     /* Hero Section */
     .hero {
         min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center;
         text-align: center; padding: 120px 5% 50px; position: relative;
     }
     .hero::before {
         content: ''; position: absolute; top: 20%; left: 50%; transform: translate(-50%, -50%);
         width: 60vw; height: 60vw; background: radial-gradient(circle, rgba(170,0,255,0.15) 0%, rgba(0,0,0,0) 70%);
         z-index: -1; pointer-events: none;
     }
     .hero h1 { font-size: 4.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 25px; max-width: 900px; }
     .hero h1 span { background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
     .hero p { font-size: 1.25rem; color: var(--text-muted); max-width: 700px; margin-bottom: 40px; }
     .hero-btn {
         padding: 16px 45px; font-size: 1.2rem; font-weight: 600; color: white; background: var(--gradient);
         border: none; border-radius: 50px; cursor: pointer; transition: 0.4s ease; text-decoration: none;
         box-shadow: var(--glow); position: relative; overflow: hidden;
     }
     .hero-btn::after {
         content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
         background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
         transition: 0.5s ease;
     }
     .hero-btn:hover::after { left: 100%; }
     .hero-btn:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(170, 0, 255, 0.6); }

     /* Stats Section */
     .stats-container { 
         display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
         gap: 30px; padding: 50px 5%; max-width: 1400px; margin: -50px auto 50px; position: relative; z-index: 10;
     }
     .stat-card {
         background: var(--surface-color); padding: 40px 30px; border-radius: 24px; text-align: center;
         border: 1px solid rgba(255,255,255,0.05); transition: 0.4s ease; position: relative; overflow: hidden;
         box-shadow: 0 10px 30px rgba(0,0,0,0.5);
     }
     .stat-card::before {
         content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
         background: var(--gradient); opacity: 0; transition: 0.4s ease;
     }
     .stat-card:hover::before { opacity: 1; }
     .stat-card:hover { transform: translateY(-10px); border-color: rgba(170,0,255,0.3); }
     .stat-icon { font-size: 45px; margin-bottom: 20px; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
     .stat-value { font-size: 3.2rem; font-weight: 800; margin-bottom: 5px; color: white; letter-spacing: -1px; }
     .stat-label { font-size: 1.1rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

     /* Features Section */
     .features { padding: 80px 5%; max-width: 1400px; margin: 0 auto; text-align: center; }
     .section-title { font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; }
     .section-title span { color: var(--primary); }
     .features-desc { color: var(--text-muted); font-size: 1.2rem; margin-bottom: 60px; max-width: 600px; margin-left: auto; margin-right: auto; }
     .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; }
     .feature-item {
         background: linear-gradient(145deg, var(--surface-color), var(--surface-color-light)); padding: 40px 30px;
         border-radius: 24px; text-align: left; transition: 0.4s ease;
         border-top: 1px solid rgba(255,255,255,0.05); border-left: 1px solid rgba(255,255,255,0.05);
     }
     .feature-item:hover { transform: translateY(-10px); box-shadow: var(--glow); border-color: rgba(170,0,255,0.3); }
     .feature-icon {
         width: 80px; height: 80px; border-radius: 20px; background: rgba(170, 0, 255, 0.05);
         display: flex; align-items: center; justify-content: center; font-size: 35px;
         color: var(--primary); margin-bottom: 25px; border: 1px solid rgba(170, 0, 255, 0.2);
     }
     .feature-title { font-size: 1.6rem; font-weight: 700; margin-bottom: 15px; color: #fff; }
     .feature-text { color: var(--text-muted); font-size: 1.05rem; line-height: 1.7; }

     /* Footer */
     footer { 
         text-align: center; padding: 40px 5%; border-top: 1px solid rgba(255,255,255,0.05); 
         margin-top: 80px; color: var(--text-muted); background: var(--surface-color);
     }
     footer p { display: flex; align-items: center; justify-content: center; gap: 10px; }
     
     /* Responsive */
     @media (max-width: 992px) {
         .hero h1 { font-size: 3.5rem; }
     }
     @media (max-width: 768px) {
         .hero h1 { font-size: 2.8rem; }
         .hero p { font-size: 1.1rem; }
         .nav-links { display: none; }
         .navbar { justify-content: center; }
         .section-title { font-size: 2.5rem; }
     }
     @media (max-width: 480px) {
         .hero h1 { font-size: 2.2rem; }
         .stat-value { font-size: 2.5rem; }
     }
     
     /* Animations */
     @keyframes fadeUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
     .animate-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
     .delay-1 { animation-delay: 0.2s; }
     .delay-2 { animation-delay: 0.4s; }
     .delay-3 { animation-delay: 0.6s; }
     .delay-4 { animation-delay: 0.8s; }
     .delay-5 { animation-delay: 1.0s; }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
      <a href="/" class="brand"><?=strtoupper($_SERVER['HTTP_HOST'])?></a>
      <div class="nav-links">
          <a href="/services"><i class="fas fa-list-ul"></i> Xizmatlar</a>
          <a href="/api"><i class="fas fa-code"></i> API</a>
          <a href="https://t.me/<?=$bot?>"><i class="fab fa-telegram-plane"></i> Bot</a>
      </div>
  </nav>

  <!-- Hero -->
  <section class="hero">
      <h1 class="animate-up">Ijtimoiy akkauntingizni <span>bir zumda</span> rivojlantiring</h1>
      <p class="animate-up delay-1">Ijtimoiy hisobingizni bitta panelda boshqarish uchun vaqtni tejang. Instagram, YouTube, Telegram, Twitter kabi tarmoqlar uchun eng tezkor SMM xizmatlari.</p>
      <a href="/services" class="hero-btn animate-up delay-2">Xizmatlarni Ko'rish</a>
  </section>

  <!-- Stats -->
  <div class="stats-container">
      <div class="stat-card animate-up">
          <i class="fas fa-layer-group stat-icon"></i>
          <div class="stat-value"><?=countRow(['table'=>"services"]) ?></div>
          <div class="stat-label">Barcha xizmatlar</div>
      </div>
      <div class="stat-card animate-up delay-1">
          <i class="fas fa-check-circle stat-icon"></i>
          <div class="stat-value"><?=countRow(['table'=>"orders","where"=>["status"=>"Completed"]]) ?></div>
          <div class="stat-label">Bajarilgan buyurtmalar</div>
      </div>
      <div class="stat-card animate-up delay-2">
          <i class="fas fa-users stat-icon"></i>
          <div class="stat-value"><?=countRow(['table'=>"users"]) ?></div>
          <div class="stat-label">Barcha obunachilar</div>
      </div>
      <div class="stat-card animate-up delay-3">
          <i class="fas fa-headset stat-icon"></i>
          <div class="stat-value">24/7</div>
          <div class="stat-label">Tezkor yordam markazi</div>
      </div>
      <div class="stat-card animate-up delay-4">
          <i class="fas fa-gift stat-icon"></i>
          <div class="stat-value"><?=countRow(['table'=>"services","where"=>["service_price"=>"0"]]) ?></div>
          <div class="stat-label">Bepul xizmatlar</div>
      </div>
      <div class="stat-card animate-up delay-5">
          <i class="fas fa-chart-line stat-icon"></i>
          <div class="stat-value"><?=countRow(['table'=>"orders"]) ?></div>
          <div class="stat-label">Barcha buyurtmalar</div>
      </div>
  </div>

  <!-- Features -->
  <section class="features">
      <h2 class="section-title animate-up">Nima uchun <span><?=strtoupper($_SERVER['HTTP_HOST'])?>?</span></h2>
      <p class="features-desc animate-up delay-1">Bizning panelda SMM xizmatlariga buyurtma berishdan qanday foyda olishingiz mumkinligini bilib oling.</p>
      
      <div class="features-grid">
          <div class="feature-item animate-up delay-2">
              <div class="feature-icon"><i class="fas fa-wallet"></i></div>
              <h3 class="feature-title">Eng arzon SMM panel</h3>
              <p class="feature-text">Bozordagi barcha mavjud panellar orasida eng arzon va ishonchli xizmatlarni taqdim etamiz.</p>
          </div>
          <div class="feature-item animate-up delay-3">
              <div class="feature-icon"><i class="fas fa-credit-card"></i></div>
              <h3 class="feature-title">Qulay to'lov tizimlari</h3>
              <p class="feature-text">Ko'plab mahalliy va xalqaro to'lov tizimlari orqali hisobni tez va oson to'ldirish imkoniyati.</p>
          </div>
          <div class="feature-item animate-up delay-4">
              <div class="feature-icon"><i class="fas fa-star"></i></div>
              <h3 class="feature-title">Premium xizmatlar</h3>
              <p class="feature-text">Sifat va kafolatga e'tibor qaratadigan eng yaxshi va organik obunachilarni jalb qiluvchi SMM panel.</p>
          </div>
          <div class="feature-item animate-up delay-5">
              <div class="feature-icon"><i class="fas fa-bolt"></i></div>
              <h3 class="feature-title">Tezkor yetkazish</h3>
              <p class="feature-text">Buyurtmani qabul qilishimiz bilanoq, uni sifatli va rekord darajadagi tezlikda yetkazib beramiz.</p>
          </div>
      </div>
  </section>

  <!-- Footer -->
  <footer>
      <p><i class="fas fa-shield-halved" style="color: var(--primary);"></i> &copy; <?=date('Y')?> <?=strtoupper($_SERVER['HTTP_HOST'])?>. Barcha huquqlar himoyalangan.</p>
  </footer>

  <!-- Scripts -->
  <script type="text/javascript" src="https://<?=$_SERVER['HTTP_HOST']?>/public/global/ch3915babussofa4.js"></script>
  <script type="text/javascript" src="https://<?=$_SERVER['HTTP_HOST']?>/public/global/cgtptn05b64bwcs4.js"></script>
  <script type="text/javascript" src="https://<?=$_SERVER['HTTP_HOST']?>/public/global/xcz59lmywkfdgsp4.js"></script>
  <script type="text/javascript" src="https://<?=$_SERVER['HTTP_HOST']?>/public/global/wnzsoolloslhfumj.js"></script>
  
  <script type="text/javascript">
     window.modules = window.modules || {};
     window.modules.signin = []; 
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.6/js/jquery.tablesorter.js"></script>
  <script src="https://cdn.rentalpanel.com/toolkit.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script type="text/javascript" src="https://<?=$_SERVER['HTTP_HOST']?>/public/<?=$theme?>/js/ajax.js"></script>

  <!-- Add animation observer -->
  <script>
      document.addEventListener("DOMContentLoaded", function() {
          const observer = new IntersectionObserver((entries) => {
              entries.forEach(entry => {
                  if (entry.isIntersecting) {
                      entry.target.style.animationPlayState = 'running';
                  }
              });
          });

          document.querySelectorAll('.animate-up').forEach((el) => {
              el.style.animationPlayState = 'paused';
              observer.observe(el);
          });
          
          // Add scrolling effect to navbar
          window.addEventListener('scroll', () => {
              const navbar = document.querySelector('.navbar');
              if (window.scrollY > 50) {
                  navbar.style.background = 'rgba(5, 5, 5, 0.95)';
                  navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.5)';
              } else {
                  navbar.style.background = 'rgba(5, 5, 5, 0.8)';
                  navbar.style.boxShadow = 'none';
              }
          });
      });
  </script>

</body>
</html>

  
