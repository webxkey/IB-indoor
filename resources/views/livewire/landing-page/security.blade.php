<div>
      <style>
    /* ====== Theme ====== */
    :root {
      --indoor-green: #198754;
      --indoor-light: #f8f9fa;
      --indoor-dark: #212529;
    }

    body {
      background-color: var(--indoor-light);
      color: var(--indoor-dark);
      font-family: 'Poppins', sans-serif;
    }

    /* ====== Hero Section ====== */
    .security-hero {
      background: linear-gradient(rgba(25, 135, 84, 0.75), rgba(25, 135, 84, 0.75)),
                  url('https://images.unsplash.com/photo-1556742031-c6961e8560b0?auto=format&fit=crop&w=1600&q=80') center/cover;
      color: #fff;
      padding: 100px 0;
      text-align: center;
    }

    .security-hero h1 {
      font-weight: 700;
      font-size: 2.8rem;
    }

    .security-hero p {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    /* ====== Content Section ====== */
    .security-section {
      padding: 60px 0;
    }

    .security-card {
      background: #fff;
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      padding: 30px;
      transition: all 0.3s ease;
      height: 100%;
    }

    .security-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
    }

    .security-card h4 {
      color: var(--indoor-green);
      font-weight: 600;
    }

    .security-card p {
      color: #555;
      font-size: 0.95rem;
    }

    /* ====== Section Headings ====== */
    .section-title {
      font-weight: 700;
      text-align: center;
      margin-bottom: 2rem;
      color: var(--indoor-dark);
    }

  </style>
  <!-- Hero Section -->
  <section class="security-hero">
    <div class="container">
      <h1>Security & Data Protection</h1>
      <p>Your privacy and safety are our top priorities at Sportynix Hub</p>
    </div>
  </section>

  <!-- Main Security Info -->
  <section class="security-section">
    <div class="container">
      <h2 class="section-title">How We Keep Your Data Safe</h2>

      <div class="row row-cols-1 row-cols-md-3 g-4">

        <!-- Card 1 -->
        <div class="col">
          <div class="security-card">
            <h4>🔒 Data Encryption</h4>
            <p>All sensitive user data is protected using advanced SSL/TLS encryption, ensuring secure communication between your browser and our servers.</p>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col">
          <div class="security-card">
            <h4>🧩 Secure Authentication</h4>
            <p>We use secure password hashing and authentication protocols to safeguard user credentials from unauthorized access or misuse.</p>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col">
          <div class="security-card">
            <h4>🛡️ Firewall Protection</h4>
            <p>Our servers are protected by multi-layer firewalls and real-time intrusion detection systems to block any suspicious activity.</p>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="col">
          <div class="security-card">
            <h4>🔍 Regular Audits</h4>
            <p>We perform regular security checks and audits to identify vulnerabilities and maintain compliance with data protection standards.</p>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="col">
          <div class="security-card">
            <h4>👁️ User Privacy</h4>
            <p>Your data is never shared or sold to third parties. We only collect what’s necessary for smooth platform operation and improvement.</p>
          </div>
        </div>

        <!-- Card 6 -->
        <div class="col">
          <div class="security-card">
            <h4>📱 Secure Payments</h4>
            <p>All online transactions are handled by trusted, PCI-compliant payment gateways ensuring 100% safety for your financial information.</p>
          </div>
        </div>

      </div>
    </div>
  </section></div>
