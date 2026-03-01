@extends('layouts.auth')

@section('title', 'Select Payment')
@section('subtitle', "Hello " . Auth::user()->name . ", you've selected the {$plan->name} package.")

@section('content')

<div class="bg-gray-50 dark:bg-white/5 border border-dashed border-gray-300 dark:border-white/20 rounded-2xl p-6 mb-8 flex justify-between items-center">
    <span class="text-gray-500 dark:text-[#94A3B8]">Total Amount:</span>
    <span class="text-2xl font-bold text-[#FF2D20]">${{ number_format($plan->price, 2) }}</span>
</div>

<form action="{{ route('payment.process') }}" method="POST">
    @csrf
    
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-sm">
            <i data-feather="alert-circle" class="w-4 h-4 inline mr-1" style="vertical-align: text-bottom;"></i>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-sm">
            <ul class="list-disc ml-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
    <input type="hidden" name="payment_method" id="selected_payment_method" value="">
    
    <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-4">Choose Payment Method:</label>

    <div class="grid gap-4 mb-8">
        <!-- Stripe -->
        @if(!empty($settings['stripe_secret']))
        <label class="flex items-center gap-4 bg-gray-50 dark:bg-black/30 p-4 rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer transition-all duration-300 hover:border-[#FF2D20] has-[:checked]:border-[#FF2D20] has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-500/10">
            <input type="radio" name="gateway" value="stripe" {{ $settings['default_payment_gateway'] === 'stripe' ? 'checked' : '' }} class="w-5 h-5 text-[#FF2D20] focus:ring-[#FF2D20] dark:bg-black/50 dark:border-white/20">
            <div class="flex items-center gap-3 text-gray-900 dark:text-white font-medium">
                <i data-feather="credit-card" class="w-5 text-gray-400"></i>
                <span>Credit / Debit Card (Stripe)</span>
            </div>
        </label>
        @endif

        <!-- Duitku -->
        @if(!empty($settings['duitku_merchant_code']) && !empty($settings['duitku_api_key']))
        <div class="gateway-container">
            <label class="flex items-center gap-4 bg-gray-50 dark:bg-black/30 p-4 rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer transition-all duration-300 hover:border-[#FF2D20] has-[:checked]:border-[#FF2D20] has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-500/10 mb-2">
                <input type="radio" name="gateway" value="duitku" {{ $settings['default_payment_gateway'] === 'duitku' ? 'checked' : '' }} class="gateway-radio w-5 h-5 text-[#FF2D20] focus:ring-[#FF2D20] dark:bg-black/50 dark:border-white/20">
                <div class="flex items-center gap-3 text-gray-900 dark:text-white font-medium">
                    <i data-feather="smartphone" class="w-5 text-gray-400"></i>
                    <span>QRIS / Bank Transfer (Duitku)</span>
                </div>
            </label>
            
            <div id="duitku-methods" class="ml-9 mt-4 flex flex-col gap-2 {{ $settings['default_payment_gateway'] === 'duitku' ? '' : 'hidden' }}">
                @forelse($duitkuMethods as $method)
                    <div class="duitku-method-item group relative flex items-center gap-4 p-3 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl cursor-pointer hover:border-[#FF2D20] transition-all" 
                         onclick="selectDuitkuMethod('{{ $method['paymentMethod'] }}', this)">
                        <div class="flex-shrink-0 w-10 h-6 flex items-center justify-center bg-gray-50 dark:bg-white/5 rounded p-1 group-hover:bg-white/10 overflow-hidden">
                            <img src="{{ $method['paymentImage'] }}" alt="{{ $method['paymentName'] }}" class="max-h-full w-auto object-contain">
                        </div>
                        <div class="flex flex-col flex-grow">
                            <span class="text-xs font-bold text-gray-800 dark:text-white">{{ $method['paymentName'] }}</span>
                            <span class="text-[10px] text-gray-500">Fee: Rp {{ number_format($method['totalFee'], 0, ',', '.') }}</span>
                        </div>
                        <!-- Checkmark icon hidden by default -->
                        <div class="selected-icon hidden bg-[#FF2D20] text-white rounded-full p-0.5 shadow-sm">
                            <i data-feather="check" style="width: 10px; height: 10px;"></i>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 italic">No specific methods available, generic Duitku will be used.</p>
                @endforelse
            </div>
        </div>
        @endif

        <!-- PayPal -->
        @if(!empty($settings['paypal_client_id']) && !empty($settings['paypal_secret']))
        <label class="flex items-center gap-4 bg-gray-50 dark:bg-black/30 p-4 rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer transition-all duration-300 hover:border-[#FF2D20] has-[:checked]:border-[#FF2D20] has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-500/10">
            <input type="radio" name="gateway" value="paypal" {{ $settings['default_payment_gateway'] === 'paypal' ? 'checked' : '' }} class="w-5 h-5 text-[#FF2D20] focus:ring-[#FF2D20] dark:bg-black/50 dark:border-white/20">
            <div class="flex items-center gap-3 text-gray-900 dark:text-white font-medium">
                <i data-feather="globe" class="w-5 text-gray-400"></i>
                <span>PayPal (Global)</span>
            </div>
        </label>
        @endif
        
        @if(empty($settings['stripe_secret']) && empty($settings['duitku_merchant_code']) && empty($settings['paypal_client_id']))
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-xl text-sm">
            No payment gateways have been configured by the administrator yet.
        </div>
        @endif
    </div>

    <button type="submit" class="btn-primary w-full shadow-lg">Complete Payment</button>
</form>

<div class="text-center mt-8 text-sm">
    <a href="{{ route('home') }}" class="text-gray-500 dark:text-[#94A3B8] hover:text-[#FF2D20] transition-colors no-underline">Cancel and return home</a>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.gateway-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const duitkuMethods = document.getElementById('duitku-methods');
            if (this.value === 'duitku') {
                duitkuMethods.classList.remove('hidden');
            } else {
                duitkuMethods.classList.add('hidden');
                document.getElementById('selected_payment_method').value = '';
                document.querySelectorAll('.duitku-method-item').forEach(i => i.classList.remove('ring-2', 'ring-[#FF2D20]', 'border-[#FF2D20]'));
            }
        });
    });

    function selectDuitkuMethod(methodId, element) {
        document.getElementById('selected_payment_method').value = methodId;
        
        // Reset all
        document.querySelectorAll('.duitku-method-item').forEach(i => {
            i.classList.remove('ring-2', 'ring-[#FF2D20]', 'border-[#FF2D20]', 'bg-red-50', 'dark:bg-red-500/5');
            i.querySelector('.selected-icon').classList.add('hidden');
        });
        
        // Select current
        element.classList.add('ring-2', 'ring-[#FF2D20]', 'border-[#FF2D20]', 'bg-red-50', 'dark:bg-red-500/5');
        element.querySelector('.selected-icon').classList.remove('hidden');
        
        // Auto-select Duitku radio if not already selected
        const duitkuRadio = document.querySelector('input[name="gateway"][value="duitku"]');
        if (!duitkuRadio.checked) {
            duitkuRadio.checked = true;
            duitkuRadio.dispatchEvent(new Event('change'));
        }
    }
</script>
@endpush
