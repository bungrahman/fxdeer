@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<header class="flex justify-between items-center mb-8">
    <div>
        <h2>System Settings</h2>
        <div class="text-sm text-gray-500 dark:text-[#94A3B8]">Global configuration & API management</div>
    </div>
    <button type="submit" form="settings-form" class="btn-primary flex items-center gap-2 px-6 py-2 shadow-lg shadow-red-500/20">
        <i data-feather="save" class="w-4 h-4"></i> Save Settings
    </button>
</header>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 p-4 rounded-xl mb-8 border border-green-200 dark:border-green-500/20">
        {{ session('success') }}
    </div>
@endif

<form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- API Configuration -->
        <div class="section-card">
            <div class="section-title"><i data-feather="key"></i> API Configuration</div>
            
            <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: var(--text-base); font-size: 1rem;">Global Defaults</h4>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Default Payment Gateway</label>
                    <select name="default_payment_gateway" class="input-field">
                        <option value="stripe" {{ ($settings['default_payment_gateway'] ?? 'stripe') == 'stripe' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Stripe (Credit Card)</option>
                        <option value="duitku" {{ ($settings['default_payment_gateway'] ?? '') == 'duitku' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Duitku (VA / E-Wallet)</option>
                        <option value="paypal" {{ ($settings['default_payment_gateway'] ?? '') == 'paypal' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">PayPal</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">USD to IDR Exchange Rate (For Duitku)</label>
                    <input type="number" name="usd_to_idr_rate" value="{{ $settings['usd_to_idr_rate'] ?? '15500' }}" class="input-field" placeholder="15500">
                    <p style="color: var(--text-dim); font-size: 0.75rem; margin-top: 5px;">Example: 15500. Duitku transactions will be strictly charged in IDR.</p>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: var(--text-base); font-size: 1rem;">Stripe Config</h4>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Mode</label>
                    <select name="stripe_mode" class="input-field">
                        <option value="sandbox" {{ ($settings['stripe_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Sandbox</option>
                        <option value="production" {{ ($settings['stripe_mode'] ?? '') == 'production' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Production</option>
                    </select>
                </div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Stripe Secret Key</label>
                <input type="password" name="stripe_secret" value="{{ $settings['stripe_secret'] ?? '' }}" class="input-field" placeholder="sk_test_...">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Stripe Webhook Secret</label>
                <input type="password" name="stripe_webhook_secret" value="{{ $settings['stripe_webhook_secret'] ?? '' }}" class="input-field" placeholder="whsec_...">
            </div>

            <div style="padding-top: 1.5rem; margin-top: 1.5rem;">
                <div class="section-title" style="font-size: 1rem; margin-bottom: 1.5rem;"><i data-feather="credit-card"></i> Duitku Config</div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Mode</label>
                    <select name="duitku_mode" id="duitku_mode" class="input-field">
                        <option value="sandbox" {{ ($settings['duitku_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Sandbox</option>
                        <option value="production" {{ ($settings['duitku_mode'] ?? '') == 'production' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Production</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Merchant Code</label>
                    <input type="text" name="duitku_merchant_code" value="{{ $settings['duitku_merchant_code'] ?? '' }}" class="input-field" placeholder="DXXXX">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">API Key</label>
                    <input type="password" name="duitku_api_key" value="{{ $settings['duitku_api_key'] ?? '' }}" class="input-field" placeholder="Standard / Callback API Key">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">URL Callback (Copy to Duitku Dashboard)</label>
                    <input type="text" value="{{ url('/api/duitku/callback') }}" class="input-field bg-red-50/50 dark:bg-red-500/5 text-[#FF2D20] border-dashed border-[#FF2D20]/30 font-mono text-xs" readonly>
                </div>
                <div style="margin-top: 1.5rem;">
                    <button type="button" onclick="testDuitku()" class="btn-primary" style="padding: 0.6rem 1.2rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <i data-feather="zap" style="width: 16px; height: 16px;"></i> Test Duitku Connection
                    </button>
                </div>
            </div>

            <div style="border-top: 1px solid var(--glass-border); padding-top: 1.5rem; margin-top: 2rem;">
                <div class="section-title" style="font-size: 1rem; margin-bottom: 1.5rem;"><i data-feather="globe"></i> PayPal Config</div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Mode</label>
                    <select name="paypal_mode" class="input-field">
                        <option value="sandbox" {{ ($settings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Sandbox</option>
                        <option value="production" {{ ($settings['paypal_mode'] ?? '') == 'production' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Production</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Client ID</label>
                    <input type="text" name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}" class="input-field" placeholder="AXxxxx...">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Secret</label>
                    <input type="password" name="paypal_secret" value="{{ $settings['paypal_secret'] ?? '' }}" class="input-field" placeholder="EPxxxx...">
                </div>
            </div>
        </div>

        <!-- System behavior -->
        <div class="section-card">
            <div class="section-title"><i data-feather="sliders"></i> System Overrides</div>
            
            <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h5 style="font-size: 0.9rem; margin-bottom: 0.2rem;">Maintenance Mode</h5>
                    <p style="color: var(--text-dim); font-size: 0.75rem;">Restrict access to admin dashboard only.</p>
                </div>
                <label class="switch">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <!-- Global Kill-Switches -->
            <div style="border-top: 1px solid var(--glass-border); padding-top: 1.5rem; margin-top: 2rem;">
                <div class="section-title" style="font-size: 1rem; margin-bottom: 1.5rem;"><i data-feather="shield"></i> Global Kill-Switches</div>
                
                <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h5 style="font-size: 0.9rem; margin-bottom: 0.2rem;">Emergency Global Pause</h5>
                        <p style="color: var(--text-dim); font-size: 0.75rem;">Instantly stop ALL news distribution.</p>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="emergency_pause" value="0">
                        <input type="checkbox" name="emergency_pause" value="1" {{ ($settings['emergency_pause'] ?? '0') == '1' ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h5 style="font-size: 0.9rem; margin-bottom: 0.2rem;">Pipeline A (High Priority) Pause</h5>
                        <p style="color: var(--text-dim); font-size: 0.75rem;">Instantly pause Pipeline A for all users.</p>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="kill_switch_pipeline_a" value="0">
                        <input type="checkbox" name="kill_switch_pipeline_a" value="1" {{ ($settings['kill_switch_pipeline_a'] ?? '0') == '1' ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h5 style="font-size: 0.9rem; margin-bottom: 0.2rem;">Pipeline B (Standard) Pause</h5>
                        <p style="color: var(--text-dim); font-size: 0.75rem;">Instantly pause Pipeline B for all users.</p>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="kill_switch_pipeline_b" value="0">
                        <input type="checkbox" name="kill_switch_pipeline_b" value="1" {{ ($settings['kill_switch_pipeline_b'] ?? '0') == '1' ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <div class="section-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div class="section-title" style="margin-bottom: 0;"><i data-feather="globe"></i> Language Distribution Management</div>
                <button type="button" onclick="addLanguageRow()" class="status-pill" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); cursor: pointer; padding: 0.5rem 1rem;">+ Add Language</button>
            </div>
            
            <p style="color: var(--text-dim); font-size: 0.85rem; margin-bottom: 1.5rem;">Define supported languages and their dedicated Telegram bots for standardized pipelines.</p>

            <div id="language-container">
                @php
                    $supportedLangs = \App\Models\Setting::getSupportedLanguages();
                @endphp
                
                @if(count($supportedLangs) > 0)
                    @foreach($supportedLangs as $index => $lang)
                        <div class="lang-row border-b border-gray-200 dark:border-white/5 pb-6 mb-6 last:border-0 last:pb-0 grid grid-cols-1 md:grid-cols-[200px_1fr_1fr_50px] gap-4 items-end">
                            <div>
                                <label style="display: block; color: var(--text-dim); font-size: 0.7rem; margin-bottom: 0.3rem;">Language</label>
                                <select name="supported_languages[{{ $index }}][code]" onchange="updateLangName(this, {{ $index }})" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.6rem; border-radius: 8px; outline: none; font-size: 0.85rem;" required>
                                    @foreach(['en' => 'English', 'id' => 'Indonesian', 'ja' => 'Japanese', 'ko' => 'Korean', 'zh' => 'Chinese', 'es' => 'Spanish', 'fr' => 'French', 'de' => 'German', 'ru' => 'Russian', 'pt' => 'Portuguese', 'vi' => 'Vietnamese', 'th' => 'Thai'] as $code => $name)
                                        <option value="{{ $code }}" {{ ($lang['code'] ?? '') == $code ? 'selected' : '' }} data-name="{{ $name }}">{{ $name }} ({{ strtoupper($code) }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="supported_languages[{{ $index }}][name]" id="lang_name_{{ $index }}" value="{{ $lang['name'] ?? 'English' }}">
                            </div>
                            <div>
                                <label style="display: block; color: var(--text-dim); font-size: 0.7rem; margin-bottom: 0.3rem;">Bot Token</label>
                                <input type="text" name="supported_languages[{{ $index }}][bot_token]" value="{{ $lang['bot_token'] ?? '' }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.6rem; border-radius: 8px; outline: none; font-size: 0.85rem;" placeholder="Bot Token">
                            </div>
                            <div>
                                <label style="display: block; color: var(--text-dim); font-size: 0.7rem; margin-bottom: 0.3rem;">Chat ID</label>
                                <input type="text" name="supported_languages[{{ $index }}][chat_id]" value="{{ $lang['chat_id'] ?? '' }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.6rem; border-radius: 8px; outline: none; font-size: 0.85rem;" placeholder="Chat ID">
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" style="background: rgba(255,45,32,0.1); border: none; color: var(--primary); padding: 0.6rem; border-radius: 8px; cursor: pointer;"><i data-feather="trash-2" style="width: 16px; height: 16px;"></i></button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <script>
        let langIndex = {{ count($supportedLangs) }};
        const isoLangs = {'en': 'English', 'id': 'Indonesian', 'ja': 'Japanese', 'ko': 'Korean', 'zh': 'Chinese', 'es': 'Spanish', 'fr': 'French', 'de' : 'German', 'ru': 'Russian', 'pt': 'Portuguese', 'vi': 'Vietnamese', 'th': 'Thai'};

        function updateLangName(select, index) {
            const name = select.options[select.selectedIndex].getAttribute('data-name');
            document.getElementById(`lang_name_${index}`).value = name;
        }

        function addLanguageRow() {
            const container = document.getElementById('language-container');
            const row = document.createElement('div');
            row.className = 'lang-row border-b border-gray-200 dark:border-white/5 pb-6 mb-6 last:border-0 last:pb-0 grid grid-cols-1 md:grid-cols-[200px_1fr_1fr_50px] gap-4 items-end';
            
            let optionsHtml = '';
            for (const [code, name] of Object.entries(isoLangs)) {
                optionsHtml += `<option value="${code}" data-name="${name}">${name} (${code.toUpperCase()})</option>`;
            }

            row.innerHTML = `
                <div>
                    <label style="display: block; color: var(--text-dim); font-size: 0.7rem; margin-bottom: 0.3rem;">Language</label>
                    <select name="supported_languages[${langIndex}][code]" onchange="updateLangName(this, ${langIndex})" class="input-field text-sm py-2" required>
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="supported_languages[${langIndex}][name]" id="lang_name_${langIndex}" value="English">
                </div>
                <div>
                    <label style="display: block; color: var(--text-dim); font-size: 0.7rem; margin-bottom: 0.3rem;">Bot Token</label>
                    <input type="text" name="supported_languages[${langIndex}][bot_token]" class="input-field text-sm py-2" placeholder="Bot Token">
                </div>
                <div>
                    <label style="display: block; color: var(--text-dim); font-size: 0.7rem; margin-bottom: 0.3rem;">Chat ID</label>
                    <input type="text" name="supported_languages[${langIndex}][chat_id]" class="input-field text-sm py-2" placeholder="Chat ID">
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background: rgba(255,45,32,0.1); border: none; color: var(--primary); padding: 0.6rem; border-radius: 8px; cursor: pointer;"><i data-feather="trash-2" style="width: 16px; height: 16px;"></i></button>
            `;
            container.appendChild(row);
            feather.replace();
            langIndex++;
        }
    </script>

    <style>
        .switch { position: relative; display: inline-block; width: 44px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #FF2D20 !important; }
        input:checked + .slider:before { transform: translateX(22px); }
        input[name="maintenance_mode"]:checked + .slider { background-color: #f59e0b !important; }
    </style>

    <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-primary" style="padding: 1rem 4rem;">Save All Settings</button>
    </div>
</form>

<!-- Duitku Modal List -->
<div id="duitkuModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;" x-data="{ open: false }" x-show="open">
    <div class="bg-white dark:bg-[#17171a] p-6 rounded-2xl max-w-4xl w-full shadow-2xl relative max-h-[80vh] flex flex-col">
        <button type="button" onclick="closeDuitkuModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-900 dark:hover:text-white">
            <i data-feather="x"></i>
        </button>
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2"><i data-feather="check-circle" class="text-green-500"></i> Duitku Connected!</h3>
        <p class="text-sm text-gray-500 dark:text-[#94A3B8] mb-4">Available Payment Methods (<span id="duitkuModeDisplay"></span> mode):</p>
        
        <div id="duitkuMethods" class="overflow-y-auto pr-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 flex-1 content-start">
            <!-- Rendered by JS -->
        </div>
    </div>
</div>

<script>
    function testDuitku() {
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i data-feather="loader" class="animate-spin w-4 h-4"></i> Testing...';
        btn.disabled = true;
        feather.replace();

        const merchantCode = document.querySelector('input[name="duitku_merchant_code"]').value;
        const apiKey = document.querySelector('input[name="duitku_api_key"]').value;
        const mode = document.getElementById('duitku_mode').value;

        fetch('{{ route('admin.settings.test-duitku') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                duitku_merchant_code: merchantCode,
                duitku_api_key: apiKey,
                duitku_mode: mode
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            feather.replace();

            if (data.success) {
                document.getElementById('duitkuModeDisplay').innerText = mode.toUpperCase();
                
                const methodsContainer = document.getElementById('duitkuMethods');
                methodsContainer.innerHTML = '';
                
                data.methods.forEach(method => {
                    const el = document.createElement('div');
                    el.className = 'flex items-center gap-3 p-3 bg-white dark:bg-black/30 border border-gray-200 dark:border-white/10 rounded-xl hover:border-[#FF2D20]/30 transition-colors shadow-sm dark:shadow-none';
                    el.innerHTML = `
                        <img src="${method.paymentImage}" alt="${method.paymentName}" class="h-8 object-contain rounded bg-white p-1">
                        <div>
                            <div class="font-semibold text-sm">${method.paymentName}</div>
                            <div class="text-xs text-gray-500">Fee: Rp ${method.totalFee}</div>
                        </div>
                    `;
                    methodsContainer.appendChild(el);
                });

                document.getElementById('duitkuModal').style.display = 'flex';
            } else {
                alert('Connection Failed: ' + data.message);
            }
        })
        .catch(err => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            feather.replace();
            alert('An unexpected error occurred.');
            console.error(err);
        });
    }

    function closeDuitkuModal() {
        document.getElementById('duitkuModal').style.display = 'none';
    }
</script>

@endsection
