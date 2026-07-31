@extends('layouts.auth')

@push('styles')
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #3730a3;
            --bg: #f1f5f9;
            --card-radius: 16px;
        }

        body {
            background: var(--bg);
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
        }

        .sub-header {
            text-align: center;
            padding: 1.5rem 0 0.5rem;
        }

        .sub-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
        }

        .sub-header p {
            color: #64748b;
            font-size: 1.05rem;
        }

        .billing-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .billing-toggle .toggle-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #94a3b8;
            transition: color 0.2s;
            cursor: pointer;
            user-select: none;
        }

        .billing-toggle .toggle-label.active {
            color: #1e293b;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
            margin-bottom: 0;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        .switch input:checked+.slider {
            background-color: var(--primary);
        }

        .switch input:checked+.slider:before {
            transform: translateX(24px);
        }

        .plan-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .plan-card .card-body {
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .plan-card.current-plan {
            border: 2px solid var(--primary) !important;
        }

        .popular-badge {
            position: absolute;
            top: 16px;
            right: -36px;
            background: var(--primary);
            color: white;
            padding: 4px 42px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transform: rotate(45deg);
            z-index: 2;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
        }

        .current-badge {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: var(--primary);
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .plan-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .plan-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .plan-price {
            margin: 0.75rem 0;
        }

        .plan-price .amount {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .plan-price .period {
            color: #64748b;
            font-size: 0.9rem;
        }

        .plan-price .original-price {
            font-size: 1.1rem;
            color: #94a3b8;
            text-decoration: line-through;
            margin-right: 0.5rem;
        }

        .plan-price .save-badge {
            display: inline-block;
            background: #dcfce7;
            color: #16a34a;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: 0.5rem;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 1rem 0 1.5rem;
            flex: 1;
        }

        .plan-features li {
            padding: 0.55rem 0;
            color: #475569;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        .plan-features li i {
            width: 18px;
            text-align: center;
            color: #22c55e;
            font-size: 0.85rem;
        }

        .plan-btn {
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        .plan-btn-primary {
            background: var(--primary);
            color: white;
        }

        .plan-btn-primary:hover {
            background: var(--primary-dark);
            color: white;
        }

        .plan-btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .plan-btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        .plan-btn-success {
            background: #16a34a;
            color: white;
        }

        .plan-btn-success:hover {
            background: #15803d;
            color: white;
        }

        .section-title {
            text-align: center;
            margin: 3rem 0 1.5rem;
        }

        .section-title h3 {
            font-weight: 700;
            color: #1e293b;
        }

        .section-title p {
            color: #64748b;
        }

        .comparison-table-wrap {
            background: white;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 0.85rem 1rem;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .comparison-table th {
            background: #f8fafc;
            font-weight: 700;
            color: #1e293b;
        }

        .comparison-table th:first-child,
        .comparison-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: #334155;
        }

        .comparison-table tbody tr:hover {
            background: #f8fafc;
        }

        .faq-section .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 0.75rem;
        }

        .faq-section .card-header {
            background: white;
            border: none;
            padding: 0;
        }

        .faq-section .card-header .btn {
            width: 100%;
            text-align: left;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-section .card-header .btn:hover {
            background: #f8fafc;
        }

        .trust-footer {
            text-align: center;
            padding: 3rem 0 1rem;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .trust-icons {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            margin-bottom: 1rem;
        }

        .trust-icons .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #64748b;
        }
    </style>
@endpush

@section('body_class', 'hold-transition')

@section('content')
    <div class="container py-4">
        <div class="sub-header">
            <h1>
                <span style="background: linear-gradient(135deg, #4f46e5, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <i class="fas fa-crown mr-2" style="-webkit-text-fill-color: #4f46e5;"></i>Choose Your Plan
                </span>
            </h1>
            <p>Select the perfect plan for your school. Upgrade or change anytime.</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($currentSubscription)
            @if ($currentSubscription->isInGracePeriod())
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Your subscription expired on
                    <strong>{{ $currentSubscription->expires_at->format('M d, Y') }}</strong>.
                    You are in a <strong>7-day grace period</strong>. Renew now to avoid losing access.
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Current plan: <strong>{{ $currentSubscription->plan->name }}</strong> &mdash;
                    Expires: <strong>{{ $currentSubscription->expires_at->format('M d, Y') }}</strong>
                    ({{ $currentSubscription->daysUntilExpiry() }} days remaining)
                </div>
            @endif
        @endif

        @php
            $discountRate = 0.15;
            $popularPlanName = 'Academy';
            $planConfigs = [
                'starter' => ['icon' => 'fa-seedling', 'gradient' => 'linear-gradient(135deg, #10b981, #34d399)'],
                'growth' => ['icon' => 'fa-chart-line', 'gradient' => 'linear-gradient(135deg, #3b82f6, #60a5fa)'],
                'academy' => ['icon' => 'fa-graduation-cap', 'gradient' => 'linear-gradient(135deg, #8b5cf6, #a78bfa)'],
                'enterprise' => ['icon' => 'fa-crown', 'gradient' => 'linear-gradient(135deg, #f59e0b, #fbbf24)'],
            ];
            $featureTemplate = [
                'starter' => [
                    'Up to %s students',
                    'Student records management',
                    'Grade management',
                    'Basic reports',
                    'Email support',
                ],
                'growth' => [
                    'Up to %s students',
                    'Everything in Starter',
                    'Fee & payment management',
                    'Class & section management',
                    'Email support',
                ],
                'academy' => [
                    'Up to %s students',
                    'Everything in Growth',
                    'SMS notifications',
                    'Custom reports & analytics',
                    'Priority email support',
                ],
                'enterprise' => [
                    'Unlimited students',
                    'Everything in Academy',
                    'API access',
                    'Dedicated account manager',
                    'Phone & priority support',
                ],
            ];
            $comparisonFeatures = [
                'Student Management' => ['icon' => 'fa-users', 'starter' => true, 'growth' => true, 'academy' => true, 'enterprise' => true],
                'Grade Management' => ['icon' => 'fa-star', 'starter' => true, 'growth' => true, 'academy' => true, 'enterprise' => true],
                'Fee & Payment Management' => ['icon' => 'fa-money-bill-wave', 'starter' => false, 'growth' => true, 'academy' => true, 'enterprise' => true],
                'SMS Notifications' => ['icon' => 'fa-sms', 'starter' => false, 'growth' => false, 'academy' => true, 'enterprise' => true],
                'Custom Reports' => ['icon' => 'fa-chart-bar', 'starter' => false, 'growth' => false, 'academy' => true, 'enterprise' => true],
                'API Access' => ['icon' => 'fa-code', 'starter' => false, 'growth' => false, 'academy' => false, 'enterprise' => true],
                'Bulk Import/Export' => ['icon' => 'fa-file-import', 'starter' => false, 'growth' => true, 'academy' => true, 'enterprise' => true],
                'Parent Portal' => ['icon' => 'fa-user-friends', 'starter' => false, 'growth' => false, 'academy' => true, 'enterprise' => true],
                'Priority Support' => ['icon' => 'fa-headset', 'starter' => false, 'growth' => false, 'academy' => false, 'enterprise' => true],
                'Dedicated Manager' => ['icon' => 'fa-handshake', 'starter' => false, 'growth' => false, 'academy' => false, 'enterprise' => true],
            ];
        @endphp

        <div class="billing-toggle">
            <span class="toggle-label active" id="labelTerm">Pay per Term</span>
            <label class="switch">
                <input type="checkbox" id="billingSwitch">
                <span class="slider"></span>
            </label>
            <span class="toggle-label" id="labelAnnual">
                Pay per Year
                <span class="badge badge-success ml-1">Save {{ $discountRate * 100 }}%</span>
            </span>
        </div>

        <div class="row justify-content-center">
            @foreach ($plans as $plan)
                @php
                    $slug = Str::slug($plan->name);
                    $cfg = $planConfigs[$slug] ?? $planConfigs['starter'];
                    $isPopular = strtolower($plan->name) === strtolower($popularPlanName);
                    $isCurrentPlan = $currentSubscription?->plan_id === $plan->id && $currentSubscription->isActive();
                    $exceedsLimit = $studentCount > $plan->max_students && $plan->max_students < 999999;
                    $annualPrice = $plan->price * 4 * (1 - $discountRate);
                    $annualSavings = $plan->price * 4 - $annualPrice;
                    $template = $featureTemplate[$slug] ?? $featureTemplate['starter'];
                    $features = collect($template)->map(fn($f) =>
                        str_contains($f, '%s')
                            ? sprintf($f, $plan->max_students >= 999999 ? 'Unlimited' : number_format($plan->max_students))
                            : $f
                    )->values()->all();
                @endphp
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="plan-card card {{ $isCurrentPlan ? 'current-plan' : '' }}">
                        @if ($isPopular && !$isCurrentPlan)
                            <div class="popular-badge">Most Popular</div>
                        @endif
                        @if ($isCurrentPlan)
                            <div class="current-badge">
                                <i class="fas fa-check-circle mr-1"></i> Current Plan
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="plan-icon" style="background: {{ $cfg['gradient'] }};">
                                <i class="fas {{ $cfg['icon'] }}"></i>
                            </div>

                            <div class="plan-name">{{ $plan->name }}</div>

                            <div class="plan-price">
                                <div>
                                    <span class="price-term">
                                        <span class="amount">₦{{ number_format($plan->price) }}</span>
                                    </span>
                                    <span class="price-annual d-none">
                                        <span class="original-price">₦{{ number_format($plan->price * 4) }}</span>
                                        <span class="amount">₦{{ number_format($annualPrice) }}</span>
                                        <span class="save-badge">Save ₦{{ number_format($annualSavings) }}</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="period period-term">/term (90 days)</span>
                                    <span class="period period-annual d-none">/year</span>
                                </div>
                            </div>

                            @if ($exceedsLimit)
                                <div class="alert alert-warning py-2 text-sm mb-3">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    You have {{ $studentCount }} students. Consider upgrading.
                                </div>
                            @endif

                            <ul class="plan-features">
                                @foreach ($features as $feature)
                                    <li>
                                        <i class="fas fa-check"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('subscription.initiate') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit"
                                    class="plan-btn
                                    @if ($isCurrentPlan) plan-btn-success
                                    @elseif($isPopular) plan-btn-primary
                                    @else plan-btn-outline
                                    @endif">
                                    @if ($isCurrentPlan)
                                        <i class="fas fa-sync mr-1"></i> Renew Plan
                                    @else
                                        <i class="fas fa-arrow-right mr-1"></i> Subscribe
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="section-title">
            <h3>Compare Plans Side by Side</h3>
            <p>See exactly what each plan includes</p>
        </div>

        <div class="text-center mb-3">
            <button class="btn btn-outline-primary" id="toggleComparison">
                <i class="fas fa-chevron-down mr-1"></i> Show Full Comparison
            </button>
        </div>

        <div id="comparisonTable" style="display: none;">
            <div class="comparison-table-wrap">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Features</th>
                            @foreach ($plans as $plan)
                                <th>{{ $plan->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <i class="fas fa-users text-primary mr-2"></i>
                                Student Capacity
                            </td>
                            @foreach ($plans as $plan)
                                <td class="font-weight-bold">
                                    {{ $plan->max_students >= 999999 ? 'Unlimited' : number_format($plan->max_students) }}
                                </td>
                            @endforeach
                        </tr>
                        @foreach ($comparisonFeatures as $feature => $data)
                            <tr>
                                <td>
                                    <i class="fas {{ $data['icon'] }} text-primary mr-2"></i>
                                    {{ $feature }}
                                </td>
                                @foreach ($plans as $plan)
                                    @php
                                        $planSlug = Str::slug($plan->name);
                                        $has = $data[$planSlug] ?? false;
                                    @endphp
                                    <td>
                                        @if ($has)
                                            <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-muted" style="font-size: 1.2rem;"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-title">
            <h3>Frequently Asked Questions</h3>
            <p>Everything you need to know about our subscription plans</p>
        </div>

        <div class="faq-section" style="max-width: 700px; margin: 0 auto;" id="faqAccordion">
            <div class="card">
                <div class="card-header" id="faq1">
                    <button class="btn" data-toggle="collapse" data-target="#faqCollapse1">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                </div>
                <div id="faqCollapse1" class="collapse" data-parent="#faqAccordion">
                    <div class="card-body text-muted">
                        We accept all major debit/credit cards, bank transfers, USSD, and mobile money
                        through Paystack. All payments are processed securely.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="faq2">
                    <button class="btn collapsed" data-toggle="collapse" data-target="#faqCollapse2">
                        <span>Can I upgrade or downgrade my plan?</span>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                </div>
                <div id="faqCollapse2" class="collapse" data-parent="#faqAccordion">
                    <div class="card-body text-muted">
                        Yes, you can upgrade at any time. Downgrades take effect at the end of your
                        current billing period. Contact support for assistance with plan changes.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="faq3">
                    <button class="btn collapsed" data-toggle="collapse" data-target="#faqCollapse3">
                        <span>Is there a refund policy?</span>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                </div>
                <div id="faqCollapse3" class="collapse" data-parent="#faqAccordion">
                    <div class="card-body text-muted">
                        We offer a <strong>7-day money-back guarantee</strong>. If you are not satisfied within the
                        first week, contact us for a full refund. No questions asked.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="faq4">
                    <button class="btn collapsed" data-toggle="collapse" data-target="#faqCollapse4">
                        <span>What happens when my subscription expires?</span>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                </div>
                <div id="faqCollapse4" class="collapse" data-parent="#faqAccordion">
                    <div class="card-body text-muted">
                        You have a <strong>7-day grace period</strong> after expiry. During this time, you
                        can renew to continue uninterrupted access. After the grace period,
                        access to your dashboard will be restricted until you renew.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="faq5">
                    <button class="btn collapsed" data-toggle="collapse" data-target="#faqCollapse5">
                        <span>Can I switch from annual to term billing?</span>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                </div>
                <div id="faqCollapse5" class="collapse" data-parent="#faqAccordion">
                    <div class="card-body text-muted">
                        Yes, you can switch between billing cycles. Contact our support team
                        and we will help you make the transition smoothly.
                    </div>
                </div>
            </div>
        </div>

        <div class="trust-footer">
            <div class="trust-icons">
                <div class="icon-circle"><i class="fas fa-lock"></i></div>
                <div class="icon-circle"><i class="fas fa-shield-alt"></i></div>
                <div class="icon-circle"><i class="fas fa-credit-card"></i></div>
            </div>
            <p class="mb-1">Secured by Paystack. All transactions are encrypted and secure.</p>
            <p class="mb-0">
                Questions? <a href="mailto:support@axiasms.com" class="text-primary font-weight-medium">Contact Support</a>
                &bull;
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-muted">Logout</a>
            </p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#billingSwitch').on('change', function() {
                const isAnnual = $(this).is(':checked');
                $('.price-term, .period-term').toggleClass('d-none', isAnnual);
                $('.price-annual, .period-annual').toggleClass('d-none', !isAnnual);
                $('#labelTerm, #labelAnnual').toggleClass('active');
            });

            $('#toggleComparison').on('click', function() {
                const $table = $('#comparisonTable');
                const isVisible = $table.is(':visible');
                $table.slideToggle(300);
                $(this).html(isVisible ?
                    '<i class="fas fa-chevron-down mr-1"></i> Show Full Comparison' :
                    '<i class="fas fa-chevron-up mr-1"></i> Hide Full Comparison'
                );
            });

            $('.faq-section .card-header .btn').on('click', function() {
                const icon = $(this).find('i.fa-chevron-down, i.fa-chevron-up');
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });
        });
    </script>
@endpush
