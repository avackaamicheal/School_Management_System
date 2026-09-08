@php
    // Expects: $childrenData (each with 'student', 'classLevel', 'section'), $prefix (string id prefix)
@endphp
<ul class="nav nav-pills mb-4" id="{{ $prefix }}Tabs" role="tablist"
    style="flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px;">
    @foreach ($childrenData as $data)
        @php $child = $data['student']; @endphp
        <li class="nav-item mr-2" role="presentation">
            <a class="child-pill {{ $loop->first ? 'active' : '' }}"
               id="{{ $prefix }}-tab-{{ $child->id }}"
               data-toggle="tab" href="#{{ $prefix }}-child-{{ $child->id }}"
               role="tab" aria-controls="{{ $prefix }}-child-{{ $child->id }}"
               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                <span class="pill-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</span>
                <span>
                    <span class="pill-name d-block">{{ $child->name }}</span>
                    <span class="pill-class d-block">
                        {{ $data['classLevel']->name ?? 'Class' }} {{ $data['section']->name ?? '' }}
                    </span>
                </span>
            </a>
        </li>
    @endforeach
</ul>
