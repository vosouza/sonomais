<?php

declare(strict_types=1);

namespace Vosouza\Sonomais\controller;

use Vosouza\Sonomais\model\Product;

class HomeViewController implements Controller{

    public function __construct()
    {
    }

    public function processaRequisicao(): void{
        $product = new Product('Base Sommie Marrom - Reconflex','produto',100.0,'cama','/images/produto1.png', false);
        /** HTML include */
        ?>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo">Sono+</div>
              <ul class="nav-links" id="navLinks">
                <li><a href="#">Estofados</a></li>
                <li><a href="#">Colchões</a></li>
                <li><a href="#">Sobre</a></li>
              </ul>
              <div class="menu-icon" id="menuIcon" onclick="toggleMenu()">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
              </div>
            </div>
          </nav>
          
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Melhores produtos escolhidos para seu lar.</h1>
                <p>Seu sono, nossa prioridade!</p>
            </div>
        </div>
    </section>
    <section class="products">
        <div class="container">
            <div class="container-title">
                <h2>Os melhores produtos para seu lar.</h2>
                <p>Functional handbags made of luxurious and honest materials.</p>
            </div>
            <div class="product-grid">
                <?php 
                    for($i = 0; $i < 10; $i++){
                        $this->makeProduct($product);
                    }
                ?>
            </div>
        </div>
    </section>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <!-- Endereço -->
                <div class="footer-section">
                    <h3>Nosso Endereço</h3>
                    <p>Rua Exemplo, 123 - Centro</p>
                    <p>São Paulo, SP - Brasil</p>
                    <p>CEP: 01000-000</p>
                </div>
    
                <!-- Redes Sociais -->
                <div class="footer-section">
                    <h3>Nos siga</h3>
                    <div class="social-icons">
                        <a href="#"><span class="material-icons">facebook</span></a>
                        <a href="#"><span class="material-icons">photo_camera</span></a>
                        <a href="#"><span class="material-icons">smart_display </span></a>
                    </div>
                </div>
            </div>
    
            <!-- Direitos autorais -->
            <p class="copyright">&copy; 2024 Dawn - Todos os direitos reservados.</p>
        </div>
    </footer>
    
</body>
        <?php
        /** END HTML include */
    }

    public function makeProduct(Product $product): void{
        ?>
        <div class="product-item">
            <img src=<?php echo $product->image ?>  alt="Bag 1">
            <p><?php echo $product->name ?></p>
        </div>
        <?php
    }

    public function setHead(): void{
        require_once __DIR__.'/../../home/HomeHeader.php';
    }

    public function setFooter(): void{
        require_once __DIR__.'/../../home/HomeHeader.php';
    }
}