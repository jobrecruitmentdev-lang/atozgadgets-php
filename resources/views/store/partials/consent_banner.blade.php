<div id="cookie-consent-banner" class="cookie-consent-container" style="display: none;">
    <div class="cookie-consent-card">
        <div class="cookie-consent-content">
            <div class="cookie-consent-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"></path>
                    <path d="M8.5 8.5v.01"></path>
                    <path d="M7.5 15.5v.01"></path>
                    <path d="M12 12v.01"></path>
                    <path d="M11 17v.01"></path>
                    <path d="M16 16v.01"></path>
                </svg>
            </div>
            <div class="cookie-consent-text">
                <h4 class="cookie-consent-title">Cookie & Privacy Preferences</h4>
                <p class="cookie-consent-desc">
                    We use cookies and analytics to enhance your browsing experience, provide secure checkouts, and analyze store traffic. See our <a href="{{ url('/privacy') }}" class="cookie-privacy-link">Privacy Policy</a>.
                </p>
            </div>
        </div>
        <div class="cookie-consent-actions">
            <button type="button" id="btn-cookie-reject" class="btn-cookie-secondary">Essential Only</button>
            <button type="button" id="btn-cookie-accept" class="btn-cookie-primary">Accept All</button>
        </div>
    </div>
</div>

<style>
.cookie-consent-container {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 40px);
    max-width: 820px;
    z-index: 99999;
    animation: cookieSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes cookieSlideUp {
    from {
        opacity: 0;
        transform: translate(-50%, 30px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

.cookie-consent-card {
    background: rgba(18, 18, 18, 0.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(201, 169, 98, 0.25);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6), 0 0 20px rgba(201, 169, 98, 0.1);
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}

.cookie-consent-content {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
}

.cookie-consent-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(201, 169, 98, 0.12);
    color: #c9a962;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cookie-consent-title {
    font-size: 15px;
    font-weight: 600;
    color: #fafaf9;
    margin-bottom: 4px;
    letter-spacing: -0.01em;
}

.cookie-consent-desc {
    font-size: 13px;
    color: #a1a1aa;
    line-height: 1.45;
    margin: 0;
}

.cookie-privacy-link {
    color: #c9a962;
    text-decoration: underline;
    text-underline-offset: 3px;
    transition: color 0.2s;
}

.cookie-privacy-link:hover {
    color: #dfbf78;
}

.cookie-consent-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.btn-cookie-primary {
    background: linear-gradient(135deg, #c9a962 0%, #b89851 100%);
    color: #0a0a0a;
    font-weight: 600;
    font-size: 13px;
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
    white-space: nowrap;
}

.btn-cookie-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(201, 169, 98, 0.35);
}

.btn-cookie-secondary {
    background: rgba(255, 255, 255, 0.05);
    color: #d4d4d8;
    font-weight: 500;
    font-size: 13px;
    padding: 10px 18px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-cookie-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fafaf9;
    border-color: rgba(255, 255, 255, 0.2);
}

@media (max-width: 768px) {
    .cookie-consent-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 20px;
    }
    .cookie-consent-actions {
        width: 100%;
        justify-content: flex-end;
    }
    .btn-cookie-primary, .btn-cookie-secondary {
        flex: 1;
        text-align: center;
    }
}
</style>

<script>
(function() {
    function initConsentBanner() {
        var consent = localStorage.getItem('atoz_consent_status');
        var banner = document.getElementById('cookie-consent-banner');
        if (!banner) return;

        if (!consent) {
            banner.style.display = 'block';
        }

        var btnAccept = document.getElementById('btn-cookie-accept');
        var btnReject = document.getElementById('btn-cookie-reject');

        if (btnAccept) {
            btnAccept.addEventListener('click', function() {
                localStorage.setItem('atoz_consent_status', 'granted');
                if (typeof gtag === 'function') {
                    gtag('consent', 'update', {
                        'analytics_storage': 'granted',
                        'ad_storage': 'granted',
                        'ad_user_data': 'granted',
                        'ad_personalization': 'granted'
                    });
                }
                banner.style.display = 'none';
            });
        }

        if (btnReject) {
            btnReject.addEventListener('click', function() {
                localStorage.setItem('atoz_consent_status', 'essential');
                if (typeof gtag === 'function') {
                    gtag('consent', 'update', {
                        'analytics_storage': 'denied',
                        'ad_storage': 'denied',
                        'ad_user_data': 'denied',
                        'ad_personalization': 'denied'
                    });
                }
                banner.style.display = 'none';
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConsentBanner);
    } else {
        initConsentBanner();
    }
})();
</script>
