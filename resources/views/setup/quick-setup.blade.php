@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Quick Setup</h1>
            <p class="text-muted mb-0">Choose a starting structure for your school</p>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('quick-setup.apply') }}" method="POST">
                @csrf

                <div class="row">
                    @foreach($presets as $key => $preset)
                        <div class="col-md-3 mb-3">
                            <label class="card h-100 shadow-sm preset-card" style="cursor: pointer; border-radius: 12px;">
                                <input type="radio" name="preset" value="{{ $key }}"
                                    class="d-none preset-radio" required>
                                <div class="card-body text-center">
                                    <i class="fas fa-school fa-2x text-primary mb-3"></i>
                                    <h5 class="font-weight-bold">{{ $preset['label'] }}</h5>
                                    <p class="text-muted text-sm mb-0">
                                        {{ implode(', ', $preset['classes']) }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    @endforeach

                    <div class="col-md-3 mb-3">
                        <label class="card h-100 shadow-sm preset-card" style="cursor: pointer; border-radius: 12px;">
                            <input type="radio" name="preset" value="scratch"
                                class="d-none preset-radio" required>
                            <div class="card-body text-center">
                                <i class="fas fa-pencil-alt fa-2x text-secondary mb-3"></i>
                                <h5 class="font-weight-bold">Start From Scratch</h5>
                                <p class="text-muted text-sm mb-0">
                                    I'll create my own classes and structure manually.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-arrow-right mr-1"></i> Continue
                    </button>
                </div>
            </form>

        </div>
    </section>
</div>

<style>
    .preset-card {
        border: 2px solid #e9ecef;
        transition: all 0.2s ease;
    }
    .preset-card:hover {
        border-color: #007bff;
        transform: translateY(-2px);
    }
    .preset-radio:checked + .card-body,
    input:checked ~ .card-body {
        background: #e7f1ff;
    }
</style>

<script>
document.querySelectorAll('.preset-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.preset-card').forEach(card => {
            card.style.borderColor = '#e9ecef';
            card.style.background = '';
        });
        this.closest('.preset-card').style.borderColor = '#007bff';
        this.closest('.preset-card').style.background = '#e7f1ff';
    });
});
</script>
@endsection
