@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm"
         style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 200px; max-width: 90vw;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-check mr-1"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm"
         style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 200px; max-width: 90vw;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-ban mr-1"></i> {{ session('error') }}
    </div>
@endif

<div id="ajax-alert-container"
     style="position: fixed; top: 76px; right: 20px; z-index: 9999; min-width: 200px; max-width: 90vw;"></div>
