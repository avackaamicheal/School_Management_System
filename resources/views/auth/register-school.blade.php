@extends('layouts.auth')

@section('title', 'Register Your School | Axia SMS')

@section('body_class', '')

@push('styles')
    <style>
        .sidebar-steps {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .sidebar-step {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .step-number {
            width: 33px;
            height: 33px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.7);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            transition: all .3s;
            flex-shrink: 0;
        }
        .sidebar-step.active .step-number {
            background: hsl(206, 94%, 87%);
            border-color: hsl(206, 94%, 87%);
            color: hsl(213, 96%, 18%);
        }
        .sidebar-step.done .step-number {
            background: #fff;
            border-color: #fff;
            color: hsl(243, 100%, 62%);
        }
        .sidebar-step.done .step-number::after {
            content: '\2713';
        }
        .step-text {
            display: flex;
            flex-direction: column;
        }
        .step-label {
            font-size: 12px;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .step-title {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .step-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: hsl(213, 96%, 18%);
            margin-bottom: .35rem;
        }
        .step-header p {
            color: hsl(231, 11%, 63%);
            margin-bottom: 2rem;
            font-size: 16px;
        }
        .step-body {
            flex: 1;
        }
        .step-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 2rem;
        }
        .btn-next, .btn-confirm {
            background: hsl(213, 96%, 18%);
            color: #fff;
            border: none;
            padding: .75rem 1.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 16px;
            cursor: pointer;
            transition: opacity .3s;
            font-family: inherit;
        }
        .btn-next:hover, .btn-confirm:hover {
            opacity: .8;
        }
        .btn-confirm {
            background: hsl(243, 100%, 62%);
        }
        .btn-previous {
            background: transparent;
            color: hsl(231, 11%, 63%);
            border: none;
            font-weight: 500;
            font-size: 16px;
            cursor: pointer;
            padding: .75rem 0;
            transition: color .3s;
            font-family: inherit;
        }
        .btn-previous:hover {
            color: hsl(213, 96%, 18%);
        }
        .registration-form fieldset {
            display: none;
        }
        .registration-form fieldset:first-child {
            display: block;
        }
        .error-summary {
            background: hsl(354, 84%, 57%);
            color: #fff;
            padding: .75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }
        .error-summary ul {
            margin: 0;
            padding-left: 1.25rem;
        }
        .thank-you {
            text-align: center;
            padding: 4rem 2rem;
        }
        .thank-you-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: hsl(206, 94%, 87%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 40px;
            color: hsl(213, 96%, 18%);
        }
        .thank-you h2 {
            font-size: 28px;
            font-weight: 700;
            color: hsl(213, 96%, 18%);
            margin-bottom: 1rem;
        }
        .thank-you p {
            color: hsl(231, 11%, 63%);
            max-width: 450px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .sidebar-steps {
                flex-direction: row;
                justify-content: center;
                gap: 1rem;
            }
            .step-text {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="multi-step-wrapper">
        <div class="multi-step-container">

            {{-- Sidebar --}}
            <div class="step-sidebar">
                <svg class="sidebar-bg" xmlns="http://www.w3.org/2000/svg" width="274" height="568" fill="none" viewBox="0 0 274 568" preserveAspectRatio="xMidYMid slice">
                    <mask id="a" width="274" height="568" x="0" y="0" maskUnits="userSpaceOnUse" style="mask-type:alpha">
                        <rect width="274" height="568" fill="#fff" rx="10"/>
                    </mask>
                    <g mask="url(#a)">
                        <path fill="#6259FF" fill-rule="evenodd" d="M-34.692 543.101C3.247 632.538 168.767 685.017 211.96 612.52c43.194-72.497-66.099-85.653-104.735-160.569-38.635-74.916-68.657-121.674-124.482-104.607-55.824 17.068-55.375 106.32-17.436 195.757Z" clip-rule="evenodd"/>
                        <path fill="#F9818E" fill-rule="evenodd" d="M233.095 601.153c60.679-28.278 92.839-143.526 41.875-171.528-50.965-28.003-57.397 47.579-108.059 75.987-50.662 28.408-82.14 50.207-69.044 88.241 13.096 38.034 74.549 35.578 135.228 7.3Z" clip-rule="evenodd"/>
                        <path stroke="#fff" stroke-linecap="round" stroke-linejoin="bevel" stroke-width="5" d="m165.305 469.097 10.607-10.806M209.461 474.581l-12.506-10.503M187.56 488.991l-6.908 14.798"/>
                        <path fill="#FFAF7E" d="M.305 546.891c37.003 0 67-29.997 67-67s-29.997-67-67-67-67 29.997-67 67 29.997 67 67 67Z"/>
                    </g>
                </svg>

                <div class="sidebar-steps" id="step-indicator">
                    <div class="sidebar-step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-text">
                            <span class="step-label">STEP 1</span>
                            <span class="step-title">School</span>
                        </div>
                    </div>
                    <div class="sidebar-step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-text">
                            <span class="step-label">STEP 2</span>
                            <span class="step-title">Account</span>
                        </div>
                    </div>
                    <div class="sidebar-step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-text">
                            <span class="step-label">STEP 3</span>
                            <span class="step-title">Submit</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="step-content">

                @if(session('registered'))

                    {{-- Thank you --}}
                    <div class="thank-you">
                        <div class="thank-you-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2>Registration Complete!</h2>
                        <p>Your school has been registered successfully. You can now set up your subscription and start managing your school.</p>
                        <a href="{{ route('subscription.index') }}" class="btn-confirm">
                            Continue to Subscription <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                @else

                    {{-- Error summary --}}
                    @if($errors->any())
                        <div class="error-summary">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('school.register.store') }}" method="POST" class="registration-form">
                        @csrf

                        {{-- Step 1: School Information --}}
                        <fieldset>
                            <div class="step-header">
                                <h2>School Information</h2>
                                <p>Tell us about your school</p>
                            </div>
                            <div class="step-body">
                                <div class="form-group">
                                    <label>
                                        School Name
                                        @error('school_name') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="text" name="school_name"
                                        class="form-control @error('school_name') input-error @enderror"
                                        value="{{ old('school_name') }}"
                                        placeholder="e.g. Saint Murumba College"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>
                                        School Email
                                        @error('school_email') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="email" name="school_email"
                                        class="form-control @error('school_email') input-error @enderror"
                                        value="{{ old('school_email') }}"
                                        placeholder="info@school.com"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>
                                        Phone Number
                                        @error('school_phone') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="text" name="school_phone"
                                        class="form-control @error('school_phone') input-error @enderror"
                                        value="{{ old('school_phone') }}"
                                        placeholder="e.g. 08012345678"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>
                                        School Address
                                        @error('school_address') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <textarea name="school_address"
                                        class="form-control @error('school_address') input-error @enderror"
                                        rows="2"
                                        placeholder="Full school address"
                                        required>{{ old('school_address') }}</textarea>
                                </div>
                            </div>
                            <div class="step-footer">
                                <div></div>
                                <button type="button" class="btn-next btn-next-step">Next Step <i class="fas fa-arrow-right ml-1"></i></button>
                            </div>
                        </fieldset>

                        {{-- Step 2: Admin Account --}}
                        <fieldset>
                            <div class="step-header">
                                <h2>Admin Account</h2>
                                <p>Set up your administrator profile</p>
                            </div>
                            <div class="step-body">
                                <div class="form-group">
                                    <label>
                                        Principal's Name
                                        @error('principal_name') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="text" name="principal_name"
                                        class="form-control @error('principal_name') input-error @enderror"
                                        value="{{ old('principal_name') }}"
                                        placeholder="e.g. Mr. John Smith"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>
                                        Your Full Name
                                        @error('admin_name') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="text" name="admin_name"
                                        class="form-control @error('admin_name') input-error @enderror"
                                        value="{{ old('admin_name') }}"
                                        placeholder="e.g. Jane Smith"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>
                                        Your Email Address
                                        @error('admin_email') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="email" name="admin_email"
                                        class="form-control @error('admin_email') input-error @enderror"
                                        value="{{ old('admin_email') }}"
                                        placeholder="you@email.com"
                                        required>
                                </div>
                            </div>
                            <div class="step-footer">
                                <button type="button" class="btn-previous btn-previous-step"><i class="fas fa-arrow-left mr-1"></i> Go Back</button>
                                <button type="button" class="btn-next btn-next-step">Next Step <i class="fas fa-arrow-right ml-1"></i></button>
                            </div>
                        </fieldset>

                        {{-- Step 3: Security & Submit --}}
                        <fieldset>
                            <div class="step-header">
                                <h2>Account Security</h2>
                                <p>Choose a strong password for your admin account</p>
                            </div>
                            <div class="step-body">
                                <div class="form-group">
                                    <label>
                                        Password
                                        @error('admin_password') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="password" name="admin_password"
                                        class="form-control @error('admin_password') input-error @enderror" required>
                                    <small class="text-muted">Minimum 8 characters.</small>
                                </div>
                                <div class="form-group">
                                    <label>
                                        Confirm Password
                                        @error('admin_password_confirmation') <span class="error-text">{{ $message }}</span> @enderror
                                    </label>
                                    <input type="password" name="admin_password_confirmation"
                                        class="form-control @error('admin_password_confirmation') input-error @enderror" required>
                                </div>
                            </div>
                            <div class="step-footer">
                                <button type="button" class="btn-previous btn-previous-step"><i class="fas fa-arrow-left mr-1"></i> Go Back</button>
                                <button type="submit" class="btn-confirm"><i class="fas fa-check mr-1"></i> Confirm</button>
                            </div>
                        </fieldset>

                    </form>

                @endif

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        jQuery(document).ready(function($) {

            var $form = $('.registration-form');
            var $fieldsets = $form.find('fieldset');
            var $steps = $('#step-indicator .sidebar-step');

            $fieldsets.hide().first().show();

            $form.find('input, textarea').on('focus', function() {
                $(this).removeClass('input-error');
            });

            function goToStep(index) {
                $steps.each(function(i) {
                    var stepNum = i + 1;
                    $(this).removeClass('active done');
                    if (stepNum === index) {
                        $(this).addClass('active');
                    } else if (stepNum < index) {
                        $(this).addClass('done');
                    }
                });
            }

            function validateFieldset($fieldset) {
                var valid = true;
                $fieldset.find('.form-control[required]').each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).addClass('input-error');
                        valid = false;
                    } else {
                        $(this).removeClass('input-error');
                    }
                });
                return valid;
            }

            $('.btn-next-step').on('click', function() {
                var $current = $(this).closest('fieldset');
                if (!validateFieldset($current)) return;

                var $next = $current.next('fieldset');
                if ($next.length) {
                    $current.fadeOut(300, function() {
                        $next.fadeIn(300);
                        goToStep($next.index() + 1);
                    });
                }
            });

            $('.btn-previous-step').on('click', function() {
                var $current = $(this).closest('fieldset');
                var $prev = $current.prev('fieldset');
                if ($prev.length) {
                    $current.fadeOut(300, function() {
                        $prev.fadeIn(300);
                        goToStep($prev.index() + 1);
                    });
                }
            });

            $form.on('submit', function(e) {
                var allValid = true;
                $fieldsets.find('.form-control[required]').each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).addClass('input-error');
                        allValid = false;
                    } else {
                        $(this).removeClass('input-error');
                    }
                });

                if (!allValid) {
                    e.preventDefault();
                    var $firstError = $fieldsets.find('.input-error').first().closest('fieldset');
                    var targetIndex = $firstError.length ? $fieldsets.index($firstError) + 1 : 1;

                    $fieldsets.hide();
                    $firstError.length ? $firstError.show() : $fieldsets.first().show();
                    goToStep(targetIndex);

                    $('html, body').animate({
                        scrollTop: $('.step-content').offset().top - 20
                    }, 300);

                    $fieldsets.find('.input-error').first().focus();
                }
            });

        });
    </script>
@endpush
