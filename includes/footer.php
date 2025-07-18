<?php
// includes/footer.php - Estilo Boticário
?>
    </main>

    <!-- Footer Principal -->
    <?php $isEspecial = $loja_dados['is_especial'] ?? 'N'; ?>
    <footer class="footer"<?php if ($isEspecial === 'S'): ?> style="background: <?php echo $corPrincipal; ?>;"<?php endif; ?>>
        <div class="container">           
            <!-- Informações do Distribuidor -->
            <div class="distributor-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="distributor-info">
                            <div class="d-flex align-items-center">
                                <div class="distributor-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="distributor-details">
                                    <h6 class="distributor-title">Seu Distribuidor Oficial</h6>
                                    <p class="distributor-name"><?php echo ucfirst(getAfiliado()); ?></p>
                                    <small class="distributor-description">
                                        Atendimento personalizado e suporte especializado para suas necessidades de saúde
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="distributor-actions">
                            <?php
                            $whatsapp = $loja_dados_response['data']['data']['whatsapp'] ?? '';
                            $whatsapp_link = $whatsapp ? 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp) : '#';
                            ?>
                            <a href="<?php echo $whatsapp_link; ?>" class="btn btn-distributor" target="_blank">
                                <i class="fab fa-whatsapp me-2"></i>Falar com Distribuidor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Métodos de Pagamento e Segurança -->
            <div class="payment-security-section">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="section-title">Formas de Pagamento</h6>
                        <div class="payment-methods">
                            <div class="payment-item">
                                <i class="fab fa-cc-visa"></i>
                            </div>
                            <div class="payment-item">
                                <i class="fab fa-cc-mastercard"></i>
                            </div>
                            <div class="payment-item">
                                <i class="fab fa-cc-amex"></i>
                            </div>
                            <div class="payment-item">
                                <i class="fas fa-barcode"></i>
                                <small>Boleto</small>
                            </div>
                            <div class="payment-item">
                                <i class="fab fa-pix"></i>
                                <small>PIX</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="section-title">Segurança e Certificações</h6>
                        <div class="security-badges">
                            <div class="security-item">
                                <i class="fas fa-shield-alt text-success"></i>
                                <small>Site Seguro SSL</small>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-award text-warning"></i>
                                <small>Anvisa Certificado</small>
                            </div>
                            <!--
                            <div class="security-item">
                                <i class="fas fa-medal text-info"></i>
                                <small>ISO 9001</small>
                            </div>
                            -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Newsletter Footer 
            <div class="newsletter-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5>Receba ofertas exclusivas</h5>
                        <p class="mb-0">Cadastre-se e ganhe 10% de desconto na primeira compra</p>
                    </div>
                    <div class="col-md-6">
                        <form class="newsletter-form-footer d-flex">
                            <input type="email" class="form-control me-2" placeholder="Seu e-mail" required>
                            <button type="submit" class="btn btn-newsletter-footer">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            -->
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright mb-0">
                            &copy; 2025 VitaTop - Encapsulados Naturais. Todos os direitos reservados.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="footer-legal">
                            <a href="#">Política de Privacidade</a>
                            <span class="separator">|</span>
                            <a href="#">Termos de Uso</a>
                            <span class="separator">|</span>
                            <a href="#">Cookies</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- CSS Adicional para o Footer -->
    <style>
    /* Footer Styles */
    .footer {
        background: linear-gradient(135deg, var(--boticario-green), var(--boticario-light-green));
        color: white;
        padding: 60px 0 0;
        margin-top: 80px;
        position: relative;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }

    /* Footer Main */
    .footer-main {
        padding-bottom: 40px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 30px;
    }

    /* Footer Logo */
    .footer-brand {
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .footer-tagline {
        color: rgba(255,255,255,0.8);
        font-style: italic;
    }

    .footer-description {
        color: rgba(255,255,255,0.9);
        line-height: 1.6;
        margin-bottom: 25px;
        font-size: 0.95rem;
    }

    /* Footer Titles */
    .footer-title {
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 20px;
        position: relative;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 30px;
        height: 2px;
        background: var(--boticario-orange);
        border-radius: 2px;
    }

    /* Footer Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        display: inline-block;
    }

    .footer-links a:hover {
        color: white;
        text-decoration: none;
        transform: translateX(5px);
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }

    .social-link:hover {
        background: rgba(255,255,255,0.2);
        color: white;
        text-decoration: none;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Contact Info */
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .contact-item i {
        width: 20px;
        font-size: 1.1rem;
    }

    .contact-item div {
        display: flex;
        flex-direction: column;
    }

    .contact-item strong {
        color: white;
        font-size: 0.85rem;
        margin-bottom: 2px;
    }

    .contact-item span {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
    }

    /* Distributor Section */
    .distributor-section {
        background: rgba(255,255,255,0.1);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .distributor-icon {
        width: 60px;
        height: 60px;
        background: var(--boticario-orange);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .distributor-title {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .distributor-name {
        color: white;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .distributor-description {
        color: rgba(255,255,255,0.8);
        font-size: 0.85rem;
        line-height: 1.4;
        margin: 0;
    }

    .btn-distributor {
        background: #25d366;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-distributor:hover {
        background: #20ba5a;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }

    /* Payment and Security */
    .payment-security-section {
        padding: 30px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 30px;
    }

    .section-title {
        color: white;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .payment-methods,
    .security-badges {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .payment-item,
    .security-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        min-width: 80px;
        transition: all 0.3s ease;
    }

    .payment-item:hover,
    .security-item:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }

    .payment-item i,
    .security-item i {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .payment-item small,
    .security-item small {
        color: rgba(255,255,255,0.9);
        font-size: 0.75rem;
        text-align: center;
    }

    /* Newsletter Footer */
    .newsletter-footer {
        background: rgba(255,255,255,0.1);
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .newsletter-footer h5 {
        color: white;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .newsletter-footer p {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
    }

    .newsletter-form-footer {
        gap: 10px;
    }

    .newsletter-form-footer input {
        border: none;
        border-radius: 25px;
        padding: 12px 20px;
        background: white;
        flex: 1;
    }

    .newsletter-form-footer input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
    }

    .btn-newsletter-footer {
        background: var(--boticario-orange);
        border: none;
        color: white;
        padding: 12px 20px;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-newsletter-footer:hover {
        background: #e55a2b;
        color: white;
        transform: scale(1.05);
    }

    /* Footer Bottom */
    .footer-bottom {
        padding: 25px 0;
        border-top: 1px solid rgba(255,255,255,0.2);
    }

    .copyright {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
    }

    .footer-legal {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .footer-legal a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .footer-legal a:hover {
        color: white;
        text-decoration: none;
    }

    .separator {
        color: rgba(255,255,255,0.5);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .footer {
            padding: 40px 0 0;
        }

        .distributor-section,
        .newsletter-footer {
            padding: 20px;
        }

        .distributor-info {
            margin-bottom: 20px;
        }

        .distributor-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            margin-right: 15px;
        }

        .distributor-name {
            font-size: 1.1rem;
        }

        .payment-methods,
        .security-badges {
            gap: 10px;
        }

        .payment-item,
        .security-item {
            min-width: 60px;
            padding: 10px;
        }

        .payment-item i,
        .security-item i {
            font-size: 1.2rem;
        }

        .newsletter-form-footer {
            flex-direction: column;
        }

        .footer-legal {
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .social-links {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .contact-info {
            gap: 10px;
        }

        .contact-item {
            gap: 8px;
        }

        .footer-main {
            text-align: center;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 20px;
        }

        .footer-links li {
            margin-bottom: 0;
        }
    }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/carrinho.js"></script>
    <script src="assets/js/carousel.js"></script>
    
    <!-- Script do Footer -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Newsletter form do footer
        const newsletterFooterForm = document.querySelector('.newsletter-form-footer');
        if (newsletterFooterForm) {
            newsletterFooterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                const button = this.querySelector('button');
                const originalContent = button.innerHTML;
                
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    this.querySelector('input').value = '';
                    
                    // Mostrar feedback
                    showFooterToast('E-mail cadastrado! Você receberá nossas ofertas.', 'success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    }, 2000);
                }, 1500);
            });
        }
        
        // Animação suave para links do footer
        document.querySelectorAll('.footer-links a, .footer-legal a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.color = 'white';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.color = 'rgba(255,255,255,0.8)';
            });
        });
        
        // Animação de hover para botões sociais
        document.querySelectorAll('.social-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.1)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });
    
    // Função para mostrar toast no footer
    function showFooterToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `footer-toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            ${message}
        `;
        
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            z-index: 9999;
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.4s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            max-width: 300px;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(50px)';
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 400);
        }, 4000);
    }
    </script>
</body>
</html>