@extends('layouts.app')

@section('title', 'Make a Donation')

@section('content')

@php
    $impact = $stats ?? [
        'schools_supported' => 0,
        'girls_enrolled' => 0,
        'packets_needed_monthly' => 0,
        'pads_needed_monthly' => 0,
    ];
@endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap');

    .public-page {
        font-family: 'Manrope', sans-serif;
    }

    .public-serif {
        font-family: 'Fraunces', serif;
    }

    .help-tabs {
        margin: 16px 0 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        padding: 6px;
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        background: #eef2ff;
    }

    .help-tab {
        border: 0;
        border-radius: 9px;
        padding: 11px 10px;
        font-weight: 800;
        font-size: 13px;
        color: #4338ca;
        background: transparent;
        cursor: pointer;
        transition: all .2s ease;
    }

    .help-tab.is-active {
        background: #312e81;
        color: #ffffff;
        box-shadow: 0 6px 14px rgba(49, 46, 129, .28);
    }

    .help-panel {
        margin-top: 12px;
        border: 1px solid #e0e7ff;
        border-radius: 12px;
        background: #f8faff;
        padding: 14px 14px 4px;
    }

    .help-panel-title {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 800;
        color: #312e81;
    }

    .help-panel-copy {
        margin: 0 0 10px;
        font-size: 12px;
        color: #4f46e5;
    }

    @media (max-width: 700px) {
        .help-tabs {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="public-page" style="margin: -4px;">
    <div style="background:linear-gradient(135deg, #eef2ff, #e0e7ff); border:1px solid #c7d2fe; color:#312e81; border-radius:16px; padding:24px 18px; box-shadow:0 10px 20px rgba(79,70,229,.12);">
        <p style="margin:0 0 10px; display:inline-block; padding:4px 10px; border-radius:999px; background:#eef2ff; border:1px solid #c7d2fe; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Monthly Program Need</p>
        <h2 class="public-serif" style="font-size:34px; line-height:1.15; margin:0;">Every Packet Keeps A Girl Learning</h2>
        <p style="margin:12px 0 0; max-width:640px; font-size:14px; color:#4338ca;">Your support helps us meet baseline monthly requirements before shortages disrupt school attendance.</p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-top:18px;">
            <div style="border:1px solid #dbeafe; background:#ffffff; border-radius:12px; padding:12px;">
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700;">Schools Supported</div>
                <div style="font-size:30px; color:#1f2937; font-weight:900; margin-top:4px;">{{ number_format($impact['schools_supported']) }}</div>
            </div>
            <div style="border:1px solid #dbeafe; background:#ffffff; border-radius:12px; padding:12px;">
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700;">Girls Supported</div>
                <div style="font-size:30px; color:#1f2937; font-weight:900; margin-top:4px;">{{ number_format($impact['girls_enrolled']) }}</div>
            </div>
            <div style="border:1px solid #c7d2fe; background:#eef2ff; border-radius:12px; padding:12px;">
                <div style="font-size:11px; text-transform:uppercase; color:#4338ca; font-weight:700;">Packets Needed Monthly</div>
                <div style="font-size:30px; font-weight:900; margin-top:4px; color:#4338ca;">{{ number_format($impact['packets_needed_monthly'] ?? $impact['pads_needed_monthly']) }}</div>
            </div>
        </div>
    </div>

    <div style="margin-top:16px; border:1px solid #e0e7ff; background:#ffffff; border-radius:16px; padding:18px; box-shadow:0 8px 16px rgba(15,23,42,.04);">
        <h3 class="public-serif" style="margin:0; font-size:28px; color:#312e81;">Choose Donation Type</h3>
        <p style="margin:8px 0 0; color:#4338ca; font-size:13px;">Select whether you want to donate pads or donate money via M-Pesa.</p>

        @if(session('success'))
            <div class="alert" style="margin-top:14px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert" style="margin-top:14px; background:#fef2f2; color:#991b1b; border-left-color:#ef4444;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('donate.store') }}" style="margin-top:14px;">
            @csrf

            @php
                $selectedContribution = old('contribution_type', 'Donate Pads');
            @endphp

            <input type="hidden" name="contribution_type" id="contribution-type" value="{{ $selectedContribution }}">

            <div class="help-tabs" role="tablist" aria-label="Contribution types">
                <button type="button" class="help-tab" data-contribution="Donate Pads" role="tab">Donate Pads</button>
                <button type="button" class="help-tab" data-contribution="Donate Money" role="tab">Donate Money</button>
            </div>

            <p>
                <label style="font-weight:700; color:#3730a3;">Name</label><br>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </p>

            <p>
                <label style="font-weight:700; color:#3730a3;">Email</label><br>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </p>

            <p id="phone-field">
                <label style="font-weight:700; color:#3730a3;">M-Pesa Phone</label><br>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="07XXXXXXXX or 2547XXXXXXXX">
                <small style="color:#6b7280; display:block; margin-top:4px;">Used to receive the M-Pesa STK prompt.</small>
            </p>

            <p id="donor-type-field">
                <label style="font-weight:700; color:#3730a3;">Donor Type</label><br>
                <select name="donor_type">
                    <option value="Individual" {{ old('donor_type') === 'Individual' ? 'selected' : '' }}>Individual</option>
                    <option value="Organization" {{ old('donor_type') === 'Organization' ? 'selected' : '' }}>Organization</option>
                </select>
                <small style="color:#6b7280; display:block; margin-top:4px;">If Organization, enter the organization name in the Name field.</small>
            </p>

            <div class="help-panel" id="pads-panel" role="tabpanel">
                <h4 class="help-panel-title">In-Kind Pads Pledge</h4>
                <p class="help-panel-copy">Pledge packets you can deliver physically to support the current cycle.</p>
                <p id="pads-field">
                    <label style="font-weight:700; color:#3730a3;">Packets Pledged</label><br>
                    <input type="number" name="quantity_pledged" value="{{ old('quantity_pledged') }}" min="1">
                </p>
            </div>

            <div class="help-panel" id="money-panel" role="tabpanel">
                <h4 class="help-panel-title">M-Pesa Money Donation</h4>
                <p class="help-panel-copy">Enter amount and phone number to receive an STK prompt instantly.</p>
                <p id="money-field">
                    <label style="font-weight:700; color:#3730a3;">Amount (KES)</label><br>
                    <input type="number" name="amount_kes" value="{{ old('amount_kes') }}" min="1" step="1">
                </p>
            </div>

            <button class="btn" type="submit" style="font-weight:800;" id="submit-label">Submit</button>

            <p style="margin-top:10px; font-size:13px; color:#6b7280;" id="helper-text"></p>
        </form>
    </div>
</div>

<script>
    (function () {
        const tabs = document.querySelectorAll('.help-tab');
        const contributionType = document.getElementById('contribution-type');
        const padsPanel = document.getElementById('pads-panel');
        const moneyPanel = document.getElementById('money-panel');
        const padsField = document.getElementById('pads-field');
        const moneyField = document.getElementById('money-field');
        const donorTypeField = document.getElementById('donor-type-field');
        const phoneField = document.getElementById('phone-field');
        const submitLabel = document.getElementById('submit-label');
        const helperText = document.getElementById('helper-text');

        function selectedValue() {
            return contributionType.value || 'Donate Pads';
        }

        function setActiveTab(value) {
            tabs.forEach(function (tab) {
                tab.classList.toggle('is-active', tab.dataset.contribution === value);
                tab.setAttribute('aria-selected', tab.dataset.contribution === value ? 'true' : 'false');
            });
        }

        function toggleFields() {
            const value = selectedValue();

            const isPads = value === 'Donate Pads';
            const isMoney = value === 'Donate Money';

            setActiveTab(value);

            padsField.style.display = isPads ? 'block' : 'none';
            moneyField.style.display = isMoney ? 'block' : 'none';
            donorTypeField.style.display = (isPads || isMoney) ? 'block' : 'none';
            phoneField.style.display = isMoney ? 'block' : 'none';

            padsPanel.style.display = isPads ? 'block' : 'none';
            moneyPanel.style.display = isMoney ? 'block' : 'none';

            if (isPads) {
                submitLabel.textContent = 'Submit Pads Pledge';
                helperText.textContent = 'Your in-kind pads pledge is recorded and your team can coordinate delivery.';
            }

            if (isMoney) {
                submitLabel.textContent = 'Pay With M-Pesa';
                helperText.textContent = 'Submit to receive an M-Pesa STK prompt on your phone and complete payment.';
            }
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                contributionType.value = tab.dataset.contribution;
                toggleFields();
            });
        });

        toggleFields();
    })();
</script>

@endsection