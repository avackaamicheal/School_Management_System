<div class="card-tools">
    <form action="{{ $route }}" method="GET" class="d-flex">
        <div class="input-group input-group-sm" style="width: 250px;">
            <input type="text"
                name="search"
                class="form-control float-right"
                placeholder="{{ $placeholder }}"
                value="{{ request('search') }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-default">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ $route }}" class="btn btn-default">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>




{{--
<div class="card card-default shadow-sm mb-3">
    <div class="card-body py-3">
        <form action="{{ $route }}" method="GET" id="searchFilterForm">
            <div class="row align-items-end">

                Search Input
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text"
                            name="search"
                            class="form-control border-left-0"
                            placeholder="{{ $placeholder }}"
                            value="{{ request('search') }}"
                            autocomplete="off">
                        @if(request('search'))
                            <div class="input-group-append">
                                <a href="{{ $route }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                Class Filter
                @if($showClass && $classLevels)
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="class_id" class="form-control"
                            onchange="document.getElementById('searchFilterForm').submit()">
                            <option value="">All Classes</option>
                            @foreach($classLevels as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                Section Filter
                @if($showSection && $sections)
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="section_id" class="form-control"
                            onchange="document.getElementById('searchFilterForm').submit()">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                Gender Filter
                @if($showGender)
                    <div class="col-md-1 mb-2 mb-md-0">
                        <select name="gender" class="form-control"
                            onchange="document.getElementById('searchFilterForm').submit()">
                            <option value="">Gender</option>
                            <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>
                                Male
                            </option>
                            <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>
                                Female
                            </option>
                        </select>
                    </div>
                @endif

                Status Filter
                @if($showStatus && count($statusOptions))
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="status" class="form-control"
                            onchange="document.getElementById('searchFilterForm').submit()">
                            <option value="">All Status</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                Term Filter
                @if($showTerm && $terms)
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="term_id" class="form-control"
                            onchange="document.getElementById('searchFilterForm').submit()">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}"
                                    {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                Submit + Clear
                <div class="col-md mb-2 mb-md-0 d-flex">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request()->hasAny(['search', 'class_id', 'section_id', 'gender', 'status', 'term_id']))
                        <a href="{{ $route }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>

            </div>

            Active filter badges
            @if(request()->hasAny(['search', 'class_id', 'section_id', 'gender', 'status']))
                <div class="mt-2">
                    @if(request('search'))
                        <span class="badge badge-primary p-2 mr-1">
                            <i class="fas fa-search mr-1"></i>
                            "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('class_id') && $classLevels)
                        <span class="badge badge-info p-2 mr-1">
                            Class: {{ $classLevels->find(request('class_id'))?->name }}
                        </span>
                    @endif
                    @if(request('section_id') && $sections)
                        <span class="badge badge-info p-2 mr-1">
                            Section: {{ $sections->find(request('section_id'))?->name }}
                        </span>
                    @endif
                    @if(request('gender'))
                        <span class="badge badge-secondary p-2 mr-1">
                            Gender: {{ request('gender') }}
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="badge badge-warning p-2 mr-1">
                            Status: {{ request('status') }}
                        </span>
                    @endif
                </div>
            @endif

        </form>
    </div>
</div> --}}
