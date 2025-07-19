@extends('layouts.app')

@section('content')
    <main id="main-content" role="main" aria-label="Student progress tracking">
        <h1 class="visually-hidden">Student Progress Tracker</h1>
        <div id="students-container" role="list" aria-label="List of students">
            @foreach ($students as $studentId => $student)
                <div class="card shadow-sm" data-student-id="{{ $studentId }}">
                    <div class="card-body">
                        {{-- Student Name and Course --}}
                        <div id="top-row" class="d-flex justify-content-between align-items-center pb-1">
                            <div id="left-top-row">
                                <h2 class="h5 card-title" id="student-{{ $studentId }}-name">{{ $student['name'] }}</h2>
                                <p class="card-subtitle mb-2 text-muted" id="student-{{ $studentId }}-course">
                                    Course: {{ $student['course']['name'] }}
                                </p>
                            </div>
                            <div id="right-top-row" class="d-flex justify-content-end d-md-block d-none">
                                <span id="completion-tick-{{ $studentId }}"
                                    style="display: {{ $student['course']['progressCompleted'] == 100 ? 'block' : 'none' }};">
                                    <span class="visually-hidden">Course Fully Complete</span>
                                    <span class="badge bg-success rounded-pill">
                                        <span> Course Complete</span>
                                    </span>
                                </span>
                            </div>
                        </div>



                        {{-- Progress Bar  --}}
                        <div id="progress-row" class="pb-1" role="status" aria-live="polite">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold" id="progress-label-{{ $studentId }}">Progress</span>
                                <span class="visually-hidden">Current progress: </span>
                                <span class="visually-hidden">{{ $student['course']['progressCompleted'] }} percent
                                    complete</span>
                            </div>
                            <div class="progress position-relative" role="progressbar"
                                aria-labelledby="progress-label-{{ $studentId }}"
                                aria-describedby="progress-desc-{{ $studentId }}"
                                aria-valuenow="{{ $student['course']['progressCompleted'] }}" aria-valuemin="0"
                                aria-valuemax="100" style="height: 30px;">
                                <div class="progress-bar progress-bar-striped d-flex align-items-center justify-content-center"
                                    style="width: {{ $student['course']['progressCompleted'] }}%; min-width: 2em;"
                                    role="presentation">
                                    <span class="visually-hidden">{{ $student['course']['progressCompleted'] }}%
                                        complete</span>
                                    <span aria-hidden="true">{{ $student['course']['progressCompleted'] }}%</span>
                                </div>
                            </div>
                            <div id="progress-desc-{{ $studentId }}" class="visually-hidden">
                                Progress for {{ $student['name'] }} in {{ $student['course']['name'] }} is
                                {{ $student['course']['progressCompleted'] }} percent complete.
                            </div>
                        </div>

                        {{-- Units  --}}
                        <div id="units-row" class="pt-2">
                            <h3 class="h6" id="units-heading-{{ $studentId }}">Units</h3>
                            <div role="region" aria-labelledby="units-heading-{{ $studentId }}">
                                <ul class="list-group">
                                    @foreach ($student['course']['units'] as $unitIndex => $unit)
                                        <li class="list-group-item d-flex justify-content-between align-items-center {{ $unit['completed'] ? 'list-group-item-success' : '' }} unit-item"
                                            style="cursor: pointer;" onmouseover="this.style.cursor='hand';"
                                            onmouseout="this.style.cursor='pointer';" role="listitem" tabindex="0"
                                            data-student-id="{{ $studentId }}" data-unit-index="{{ $unitIndex }}"
                                            aria-label="{{ $unit['name'] }}, {{ $unit['completed'] ? 'completed' : 'not completed' }}">
                                            <div>
                                                <span class="fw-bold me-2">{{ $unit['id'] }}</span>
                                                <span>{{ $unit['name'] }}</span>
                                            </div>
                                            @if ($unit['completed'])
                                                <span class="badge bg-success rounded-pill">
                                                    <span class="visually-hidden">Status: </span>Complete
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">
                                                    <span class="visually-hidden">Status: </span>Incomplete
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
            @endforeach
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.unit-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const studentId = this.dataset.studentId;
                        const unitIndex = this.dataset.unitIndex;
                        const isCompleted = this.classList.contains('list-group-item-success');
                        const card = this.closest('.card');
                        const progressBar = card.querySelector('.progress-bar');

                        // Toggle visual state
                        this.classList.toggle('list-group-item-success');

                        // Remove any existing badge first to prevent duplicates
                        const existingBadge = this.querySelector('.badge');
                        if (existingBadge) {
                            existingBadge.remove();
                        }

                        // Create and append the appropriate badge
                        const badge = document.createElement('span');
                        badge.className = 'badge rounded-pill ' + (isCompleted ? 'bg-secondary' :
                            'bg-success');
                        badge.textContent = isCompleted ? 'Incomplete' : 'Complete';
                        this.querySelector('div').after(badge);

                        fetch('{{ route('update.progress') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    changes: [{
                                        studentId: parseInt(studentId),
                                        unitIndex: parseInt(unitIndex),
                                        completed: !isCompleted
                                    }]
                                }),
                                credentials: 'same-origin'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network Error');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    // Use server-calculated progress
                                    const newProgress = data.progress;

                                    // Update progress bar
                                    progressBar.style.width = `${newProgress}%`;
                                    progressBar.setAttribute('aria-valuenow', newProgress);
                                    progressBar.textContent = `${newProgress}%`;

                                    // Show/hide completion tick
                                    const completionTick = document.getElementById(
                                        `completion-tick-${studentId}`);
                                    if (completionTick) {
                                        completionTick.style.display = newProgress === 100 ?
                                            'block' : 'none';
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    });
                });
            });
        </script>

        <style>
            .progress-bar {
                background: rgb(255, 159, 6);
                color: black;
                font-weight: bold;
                background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
                background-size: 1.5rem 1.5rem;
            }

            /* Visually hidden but accessible to screen readers */
            .visually-hidden {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
        </style>
    @endsection
